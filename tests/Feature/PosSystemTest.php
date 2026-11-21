<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PosSystemTest extends TestCase
{
    use RefreshDatabase;

    private function staff(array $attrs = []): User
    {
        return User::create(array_merge([
            'username' => 'tester',
            'full_name' => 'Test Staff',
            'password' => 'secret123',
        ], $attrs));
    }

    private function product(array $attrs = []): Product
    {
        return Product::create(array_merge(['name' => 'Widget', 'price' => 10.50, 'stock_quantity' => 10], $attrs));
    }

    // ---------- Authentication ----------

    public function test_guests_are_redirected_from_every_management_page(): void
    {
        foreach (['/', '/products', '/products/create', '/customers', '/customers/create', '/staff', '/staff/create', '/sales', '/sales/create', '/profile'] as $url) {
            $this->get($url)->assertRedirect('/login');
        }
        $this->post('/sales', [])->assertRedirect('/login');
        $this->post('/products', [])->assertRedirect('/login');
    }

    public function test_staff_can_log_in_and_out(): void
    {
        $this->staff();

        $this->post('/login', ['username' => 'tester', 'password' => 'wrong'])->assertSessionHasErrors('username');
        $this->assertGuest();

        $this->post('/login', ['username' => 'tester', 'password' => 'secret123'])->assertRedirect('/');
        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_passwords_are_hashed(): void
    {
        $user = $this->staff();

        $this->assertNotSame('secret123', $user->fresh()->password);
        $this->assertTrue(Hash::check('secret123', $user->fresh()->password));
    }

    public function test_password_confirmation_screen_works(): void
    {
        $this->actingAs($this->staff());

        $this->get('/confirm-password')->assertOk();
        $this->post('/confirm-password', ['password' => 'wrong'])->assertSessionHasErrors('password');
        $this->post('/confirm-password', ['password' => 'secret123'])->assertSessionHasNoErrors();
        $this->assertNotNull(session('auth.password_confirmed_at'));
    }

    public function test_login_is_rate_limited_after_five_failures(): void
    {
        $this->staff();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['username' => 'tester', 'password' => 'nope']);
        }

        $this->post('/login', ['username' => 'tester', 'password' => 'secret123'])->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_logged_in_staff_are_redirected_away_from_login(): void
    {
        $this->actingAs($this->staff())->get('/login')->assertRedirect('/');
    }

    public function test_profile_can_be_updated(): void
    {
        Storage::fake('public');
        $user = $this->staff();
        $this->actingAs($user);

        $this->get('/profile')->assertOk();

        $this->patch('/profile', [
            'full_name' => 'Renamed Me', 'username' => 'renamed',
            'avatar' => UploadedFile::fake()->image('a.jpg', 400, 400),
        ])->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('renamed', $user->username);
        Storage::disk('public')->assertExists($user->avatar);

        $this->patch('/profile', ['full_name' => '', 'username' => 'x'])->assertSessionHasErrors(['full_name', 'username']);
    }

    public function test_password_can_be_changed_from_profile(): void
    {
        $user = $this->staff();
        $this->actingAs($user);

        $this->put('/password', ['current_password' => 'wrong', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->put('/password', ['current_password' => 'secret123', 'password' => 'newpass123', 'password_confirmation' => 'newpass123'])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));
    }

    // ---------- Products ----------

    public function test_product_crud_with_image_upload(): void
    {
        Storage::fake('public');
        $this->actingAs($this->staff());

        $this->post('/products', [
            'name' => 'Coffee', 'price' => '99.50', 'stock_quantity' => 5,
            'image' => UploadedFile::fake()->image('photo.png', 1200, 800),
        ])->assertRedirect('/products');

        $product = Product::firstWhere('name', 'Coffee');
        $this->assertNotNull($product);
        Storage::disk('public')->assertExists($product->image);
        $this->assertStringEndsWith('.jpg', $product->image);

        // resized to the 600x600 display size
        [$w, $h] = getimagesizefromstring(Storage::disk('public')->get($product->image));
        $this->assertSame([600, 600], [$w, $h]);

        $oldImage = $product->image;
        $this->put("/products/{$product->id}", ['name' => 'Coffee II', 'price' => '120', 'stock_quantity' => 7])->assertRedirect('/products');
        $this->assertSame('Coffee II', $product->fresh()->name);
        Storage::disk('public')->assertExists($oldImage); // untouched when no new file

        $this->delete("/products/{$product->id}")->assertRedirect('/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($oldImage);
    }

    public function test_product_validation_and_file_restrictions(): void
    {
        Storage::fake('public');
        $this->actingAs($this->staff());

        $this->post('/products', ['name' => '', 'price' => 'abc', 'stock_quantity' => -1])
            ->assertSessionHasErrors(['name', 'price', 'stock_quantity']);

        $this->post('/products', ['name' => 'X', 'price' => 1, 'stock_quantity' => 1, 'image' => UploadedFile::fake()->create('evil.php', 10, 'text/x-php')])
            ->assertSessionHasErrors('image');

        $this->post('/products', ['name' => 'X', 'price' => 1, 'stock_quantity' => 1, 'image' => UploadedFile::fake()->image('big.jpg')->size(5000)])
            ->assertSessionHasErrors('image');

        $this->assertSame(0, Product::count());
    }

    public function test_product_with_sales_cannot_be_deleted(): void
    {
        $user = $this->staff();
        $product = $this->product();
        $this->actingAs($user)->post('/sales', ['product_id' => $product->id, 'quantity' => 1]);

        $this->delete("/products/{$product->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('products', ['id' => $product->id]);
    }

    // ---------- Customers ----------

    public function test_customer_crud(): void
    {
        $this->actingAs($this->staff());

        $this->post('/customers', ['full_name' => 'Ana Reyes', 'email' => 'ana@example.com', 'phone' => '0917 123 4567'])->assertRedirect('/customers');
        $customer = Customer::firstWhere('email', 'ana@example.com');
        $this->assertNotNull($customer);

        $this->post('/customers', ['full_name' => 'Dup', 'email' => 'ana@example.com'])->assertSessionHasErrors('email');
        $this->post('/customers', ['full_name' => 'Bad', 'email' => 'not-an-email'])->assertSessionHasErrors('email');

        $this->put("/customers/{$customer->id}", ['full_name' => 'Ana R.', 'email' => 'ana@example.com'])->assertRedirect('/customers');
        $this->assertSame('Ana R.', $customer->fresh()->full_name);

        $this->delete("/customers/{$customer->id}")->assertRedirect('/customers');
        $this->assertDatabaseCount('customers', 0);
    }

    public function test_customer_avatar_upload(): void
    {
        Storage::fake('public');
        $this->actingAs($this->staff());

        $this->post('/customers', [
            'full_name' => 'Pic Person', 'email' => 'pic@example.com',
            'avatar' => UploadedFile::fake()->image('p.png', 640, 480),
        ])->assertRedirect('/customers');

        $customer = Customer::firstWhere('email', 'pic@example.com');
        Storage::disk('public')->assertExists($customer->avatar);

        $old = $customer->avatar;
        $this->put("/customers/{$customer->id}", ['full_name' => 'Pic Person', 'email' => 'pic@example.com', 'remove_avatar' => 1]);
        $this->assertNull($customer->fresh()->avatar);
        Storage::disk('public')->assertMissing($old);

        $this->post('/customers', ['full_name' => 'Bad', 'email' => 'bad@example.com', 'avatar' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])
            ->assertSessionHasErrors('avatar');
    }

    // ---------- Staff ----------

    public function test_staff_crud_with_avatar_and_hashed_password(): void
    {
        Storage::fake('public');
        $me = $this->staff();
        $this->actingAs($me);

        $this->post('/staff', [
            'username' => 'newbie', 'full_name' => 'New Hire',
            'password' => 'password1', 'password_confirmation' => 'password1',
            'avatar' => UploadedFile::fake()->image('me.jpg', 500, 300),
        ])->assertRedirect('/staff');

        $new = User::firstWhere('username', 'newbie');
        $this->assertTrue(Hash::check('password1', $new->password));
        Storage::disk('public')->assertExists($new->avatar);
        [$w, $h] = getimagesizefromstring(Storage::disk('public')->get($new->avatar));
        $this->assertSame([256, 256], [$w, $h]);

        // blank password on edit keeps the old hash
        $hash = $new->password;
        $this->put("/staff/{$new->id}", ['username' => 'newbie', 'full_name' => 'Renamed'])->assertRedirect('/staff');
        $this->assertSame($hash, $new->fresh()->password);
        $this->assertSame('Renamed', $new->fresh()->full_name);

        $this->post('/staff', ['username' => 'tester', 'full_name' => 'Dup', 'password' => 'password1', 'password_confirmation' => 'password1'])
            ->assertSessionHasErrors('username');
        $this->post('/staff', ['username' => 'weak', 'full_name' => 'Weak', 'password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');

        // deleting needs a recent password confirmation (Breeze password.confirm)
        $this->delete("/staff/{$new->id}")->assertRedirect(route('password.confirm'));
        $this->assertDatabaseHas('users', ['id' => $new->id]);

        $this->withSession(['auth.password_confirmed_at' => time()]);

        // cannot delete yourself, can delete others
        $this->delete("/staff/{$me->id}")->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $me->id]);
        $this->delete("/staff/{$new->id}")->assertRedirect('/staff');
        $this->assertDatabaseMissing('users', ['id' => $new->id]);
    }

    // ---------- Sales ----------

    public function test_recording_a_sale_decreases_stock_and_stores_transaction(): void
    {
        $user = $this->staff();
        $customer = Customer::create(['full_name' => 'Buyer', 'email' => 'b@example.com']);
        $product = $this->product(['price' => 10.50, 'stock_quantity' => 10]);

        $this->actingAs($user)->post('/sales', [
            'product_id' => $product->id, 'customer_id' => $customer->id, 'quantity' => 3,
        ])->assertRedirect('/sales/create')->assertSessionHas('success');

        $this->assertSame(7, $product->fresh()->stock_quantity);
        $this->assertDatabaseHas('sales', [
            'product_id' => $product->id, 'customer_id' => $customer->id,
            'sold_by' => $user->id, 'quantity' => 3, 'total_price' => '31.50',
        ]);
    }

    public function test_customer_is_optional(): void
    {
        $user = $this->staff();
        $product = $this->product();

        $this->actingAs($user)->post('/sales', ['product_id' => $product->id, 'quantity' => 1])->assertSessionHasNoErrors();
        $this->assertNull(Sale::first()->customer_id);
    }

    public function test_cannot_sell_more_than_available_stock(): void
    {
        $user = $this->staff();
        $product = $this->product(['stock_quantity' => 2]);

        $this->actingAs($user)->post('/sales', ['product_id' => $product->id, 'quantity' => 3])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(2, $product->fresh()->stock_quantity);
        $this->assertSame(0, Sale::count());
    }

    public function test_can_sell_exactly_the_remaining_stock_but_not_one_more(): void
    {
        $user = $this->staff();
        $product = $this->product(['stock_quantity' => 2]);

        $this->actingAs($user)->post('/sales', ['product_id' => $product->id, 'quantity' => 2])->assertSessionHasNoErrors();
        $this->assertSame(0, $product->fresh()->stock_quantity);

        $this->post('/sales', ['product_id' => $product->id, 'quantity' => 1])->assertSessionHasErrors('quantity');
        $this->assertSame(0, $product->fresh()->stock_quantity);
    }

    public function test_sale_input_is_validated(): void
    {
        $this->actingAs($this->staff());
        $product = $this->product();

        $this->post('/sales', [])->assertSessionHasErrors(['product_id', 'quantity']);
        $this->post('/sales', ['product_id' => $product->id, 'quantity' => 0])->assertSessionHasErrors('quantity');
        $this->post('/sales', ['product_id' => $product->id, 'quantity' => -4])->assertSessionHasErrors('quantity');
        $this->post('/sales', ['product_id' => 9999, 'quantity' => 1])->assertSessionHasErrors('product_id');
        $this->post('/sales', ['product_id' => $product->id, 'customer_id' => 9999, 'quantity' => 1])->assertSessionHasErrors('customer_id');
    }

    public function test_sales_history_lists_transactions(): void
    {
        $user = $this->staff();
        $customer = Customer::create(['full_name' => 'Pat Buyer', 'email' => 'pat@example.com']);
        $product = $this->product(['name' => 'Gizmo']);
        $this->actingAs($user)->post('/sales', ['product_id' => $product->id, 'customer_id' => $customer->id, 'quantity' => 2]);

        $this->get('/sales')->assertOk()
            ->assertSee('Gizmo')->assertSee('Pat Buyer')->assertSee('Test Staff')->assertSee('21.00');
    }

    public function test_every_page_renders_for_a_logged_in_staff_member(): void
    {
        $user = $this->staff();
        $product = $this->product();
        $customer = Customer::create(['full_name' => 'C', 'email' => 'c@example.com']);
        $this->actingAs($user);

        foreach (['/', '/products', '/products/create', "/products/{$product->id}/edit", '/customers', '/customers/create',
            "/customers/{$customer->id}/edit", '/staff', '/staff/create', "/staff/{$user->id}/edit", '/profile', '/confirm-password', '/sales', '/sales/create'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
