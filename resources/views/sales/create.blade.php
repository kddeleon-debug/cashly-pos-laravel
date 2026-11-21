@extends('layouts.app')
@section('title', 'Record sale')

@section('content')
<div class="page-head"><div><h1>Record sale</h1><p>Select a product, an optional customer and the quantity sold.</p></div></div>

<form method="POST" action="{{ route('sales.store') }}" id="saleForm" novalidate>
@csrf
<div class="sale-layout">
    <div class="card card-pad">
        <div class="form-grid">
            <div class="field full">
                <label for="product_id">Product <span class="req">*</span></label>
                <select class="input @error('product_id') is-invalid @enderror" id="product_id" name="product_id" required>
                    <option value="">Choose a product…</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                                data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}"
                                data-stock="{{ $product->stock_quantity }}"
                                data-image="{{ $product->image_url }}"
                                @selected(old('product_id') == $product->id)
                                @disabled($product->stock_quantity <= 0)>
                            {{ $product->name }} — {{ peso($product->price) }} ({{ $product->stock_quantity > 0 ? $product->stock_quantity.' in stock' : 'out of stock' }})
                        </option>
                    @endforeach
                </select>
                @error('product_id') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="field full">
                <label for="customer_id">Customer <span class="hint" style="font-weight:400">(optional — leave blank for walk-in)</span></label>
                <select class="input @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                    <option value="">Walk-in customer</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->full_name }} — {{ $customer->email }}</option>
                    @endforeach
                </select>
                @error('customer_id') <span class="error">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="quantity">Quantity <span class="req">*</span></label>
                <input class="input @error('quantity') is-invalid @enderror" id="quantity" name="quantity" type="number" min="1" step="1" value="{{ old('quantity', 1) }}" required>
                @error('quantity') <span class="error">{{ $message }}</span> @enderror
                <span class="error" id="stockWarn" hidden></span>
            </div>

            <div class="field">
                <span class="label">Sold by</span>
                <div class="cell" style="padding:6px 0">@include('partials.avatar', ['user' => auth()->user()])<strong>{{ auth()->user()->full_name }}</strong></div>
            </div>
        </div>

        <div class="form-actions">
            <button class="btn" type="submit"><x-icon name="check"/> Complete sale</button>
            <a class="btn ghost" href="{{ route('sales.index') }}">View sales history</a>
        </div>
    </div>

    <aside class="card summary">
        <h2 style="margin-bottom:16px;font-size:16px">Order summary</h2>
        <div class="pimg" id="sumImg"><x-icon name="image"/></div>
        <div class="row"><span>Product</span><b id="sumName">—</b></div>
        <div class="row"><span>Unit price</span><b id="sumPrice">—</b></div>
        <div class="row"><span>In stock</span><b id="sumStock">—</b></div>
        <div class="row"><span>Quantity</span><b id="sumQty">—</b></div>
        <div class="total"><span style="color:var(--muted)">Total</span><strong id="sumTotal">₱0.00</strong></div>
    </aside>
</div>
</form>
@endsection
