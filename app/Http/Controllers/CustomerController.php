<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(Request $request): View
    {
        $term = $request->string('q')->trim();

        $customers = Customer::query()
            ->withCount('sales')
            ->when($term->isNotEmpty(), fn ($q) => $q->where(fn ($w) => $w
                ->where('full_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")))
            ->orderBy('full_name')
            ->paginate(12)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(CustomerRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['full_name', 'email', 'phone']);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $this->images->store($request->file('avatar'), 'customers', 256);
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer): RedirectResponse
    {
        $data = $request->safe()->only(['full_name', 'email', 'phone']);

        if ($request->hasFile('avatar')) {
            $this->images->delete($customer->avatar);
            $data['avatar'] = $this->images->store($request->file('avatar'), 'customers', 256);
        } elseif ($request->boolean('remove_avatar')) {
            $this->images->delete($customer->avatar);
            $data['avatar'] = null;
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($customer->sales()->exists()) {
            return back()->with('error', "{$customer->full_name} has purchases on record and cannot be deleted.");
        }

        $this->images->delete($customer->avatar);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
