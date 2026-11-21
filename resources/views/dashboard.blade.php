@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-head">
    <div>
        <h1>Hello, {{ \Illuminate\Support\Str::of(auth()->user()->full_name)->before(' ') }} 👋</h1>
        <p>Here’s what’s happening in your store today.</p>
    </div>
    <a class="btn" href="{{ route('sales.create') }}"><x-icon name="cart"/> Record a sale</a>
</div>

<div class="grid stats">
    <div class="card stat"><div class="ico green"><x-icon name="peso"/></div><div><div class="v">{{ peso($revenueToday) }}</div><div class="l">Revenue today</div></div></div>
    <div class="card stat"><div class="ico"><x-icon name="receipt"/></div><div><div class="v">{{ $salesToday }}</div><div class="l">Sales today</div></div></div>
    <div class="card stat"><div class="ico pink"><x-icon name="box"/></div><div><div class="v">{{ $productCount }}</div><div class="l">Products</div></div></div>
    <div class="card stat"><div class="ico amber"><x-icon name="users"/></div><div><div class="v">{{ $customerCount }}</div><div class="l">Customers</div></div></div>
</div>

<div class="grid two-col">
    <div class="card">
        <div class="card-head"><h2>Recent sales</h2><a href="{{ route('sales.index') }}">View all</a></div>
        @if ($recentSales->isEmpty())
            <div class="empty"><x-icon name="receipt"/><div>No sales yet.</div></div>
        @else
        <div class="table-wrap"><table>
            <thead><tr><th>Product</th><th>Customer</th><th class="num">Total</th></tr></thead>
            <tbody>
            @foreach ($recentSales as $sale)
                <tr>
                    <td><div class="cell"><div><strong>{{ $sale->product->name }}</strong><small>{{ $sale->quantity }} × · {{ $sale->created_at->format('M d, g:i A') }}</small></div></div></td>
                    <td>{{ $sale->customer->full_name ?? 'Walk-in' }}</td>
                    <td class="num"><strong>{{ peso($sale->total_price) }}</strong></td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
        @endif
    </div>

    <div class="card">
        <div class="card-head"><h2>Stock alerts</h2><a href="{{ route('products.index') }}">Manage</a></div>
        @forelse ($lowStock as $product)
            <div class="cell" style="padding:14px 22px;border-bottom:1px solid var(--line);justify-content:space-between">
                <div class="cell">
                    @include('products._thumb', ['product' => $product])
                    <div><strong>{{ $product->name }}</strong><small>{{ peso($product->price) }}</small></div>
                </div>
                @include('products._stock', ['product' => $product])
            </div>
        @empty
            <div class="empty"><x-icon name="check"/><div>All products are well stocked.</div></div>
        @endforelse
        <div style="padding:16px 22px;color:var(--muted);font-size:13px">Lifetime revenue: <strong style="color:var(--ink)">{{ peso($revenueTotal) }}</strong></div>
    </div>
</div>
@endsection
