<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private ImageService $images) {}

    public function index(Request $request): View
    {
        $products = Product::query()
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->string('q')->trim().'%'))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'price', 'stock_quantity']);

        if ($request->hasFile('image')) {
            $data['image'] = $this->images->store($request->file('image'), 'products', 600);
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'price', 'stock_quantity']);

        if ($request->hasFile('image')) {
            $this->images->delete($product->image);
            $data['image'] = $this->images->store($request->file('image'), 'products', 600);
        } elseif ($request->boolean('remove_image')) {
            $this->images->delete($product->image);
            $data['image'] = null;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Sales keep a foreign key to the product, so a product with sales history cannot be deleted.
        if ($product->sales()->exists()) {
            return back()->with('error', "“{$product->name}” has sales on record and cannot be deleted. Set its stock to 0 instead.");
        }

        $this->images->delete($product->image);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
