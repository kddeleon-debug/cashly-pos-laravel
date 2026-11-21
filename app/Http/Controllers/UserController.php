<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Staff management (the "users" table). */
class UserController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(Request $request): View
    {
        $term = $request->string('q')->trim();

        $staff = User::query()
            ->withCount('sales')
            ->when($term->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w
                ->where('full_name', 'like', "%{$term}%")
                ->orWhere('username', 'like', "%{$term}%")))
            ->orderBy('full_name')
            ->paginate(12)
            ->withQueryString();

        return view('staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('staff.create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['username', 'full_name', 'password']); // password is hashed by the model cast

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->images->store($request->file('avatar'), 'avatars', 256);
        }

        User::create($data);

        return redirect()->route('staff.index')->with('success', 'Staff member added successfully.');
    }

    public function edit(User $user): View
    {
        return view('staff.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['username', 'full_name']);

        if ($request->filled('password')) {
            $data['password'] = $request->input('password');
        }

        if ($request->hasFile('avatar')) {
            $this->images->delete($user->avatar);
            $data['avatar'] = $this->images->store($request->file('avatar'), 'avatars', 256);
        } elseif ($request->boolean('remove_avatar')) {
            $this->images->delete($user->avatar);
            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->route('staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(Auth::user())) {
            return back()->with('error', 'You cannot delete your own account while signed in.');
        }

        if ($user->sales()->exists()) {
            return back()->with('error', "{$user->full_name} has recorded sales and cannot be deleted.");
        }

        $this->images->delete($user->avatar);
        $user->delete();

        return redirect()->route('staff.index')->with('success', 'Staff member deleted.');
    }
}
