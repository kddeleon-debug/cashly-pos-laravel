@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="page-head">
    <div><h1>Products</h1><p>Manage your catalogue, prices and stock levels.</p></div>
    <a class="btn" href="{{ route('products.create') }}"><x-icon name="plus"/> Add product</a>
</div>

<div class="card">
    <form class="toolbar" method="GET" action="{{ route('products.index') }}">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search products…">
        <button class="btn ghost" type="submit"><x-icon name="search"/> Search</button>
        @if (request('q')) <a href="{{ route('products.index') }}">Clear</a> @endif
    </form>

    @if ($products->isEmpty())
        <div class="empty"><x-icon name="box"/><div>No products found.</div></div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>Product</th><th class="num">Price</th><th>Stock</th><th>Added</th><th></th></tr></thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td><div class="cell">@include('products._thumb', ['product' => $product])<div><strong>{{ $product->name }}</strong><small>ID #{{ $product->id }}</small></div></div></td>
                <td class="num"><strong>{{ peso($product->price) }}</strong></td>
                <td>@include('products._stock', ['product' => $product])</td>
                <td>{{ $product->created_at->format('M d, Y') }}</td>
                <td>
                    <div class="actions">
                        <a class="btn ghost sm" href="{{ route('products.edit', $product) }}"><x-icon name="edit"/> Edit</a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" data-confirm="Delete “{{ $product->name }}”? This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn danger sm" type="submit"><x-icon name="trash"/> Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    @include('partials.pager-footer', ['paginator' => $products])
    @endif
</div>
@endsection
