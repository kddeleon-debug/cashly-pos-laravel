@extends('layouts.app')
@section('title', 'Sales history')

@section('content')
<div class="page-head">
    <div><h1>Sales history</h1><p>Every transaction recorded in the system.</p></div>
    <a class="btn" href="{{ route('sales.create') }}"><x-icon name="cart"/> Record a sale</a>
</div>

<div class="grid stats" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
    <div class="card stat"><div class="ico green"><x-icon name="peso"/></div><div><div class="v">{{ peso($totalRevenue) }}</div><div class="l">Revenue {{ request()->hasAny(['q','from','to']) ? '(filtered)' : '(all time)' }}</div></div></div>
    <div class="card stat"><div class="ico"><x-icon name="box"/></div><div><div class="v">{{ number_format($totalUnits) }}</div><div class="l">Units sold</div></div></div>
    <div class="card stat"><div class="ico amber"><x-icon name="receipt"/></div><div><div class="v">{{ number_format($sales->total()) }}</div><div class="l">Transactions</div></div></div>
</div>

<div class="card">
    <form class="toolbar" method="GET" action="{{ route('sales.index') }}">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Product, customer or staff…">
        <input class="input" type="date" name="from" value="{{ request('from') }}" style="max-width:170px" aria-label="From date">
        <input class="input" type="date" name="to" value="{{ request('to') }}" style="max-width:170px" aria-label="To date">
        <button class="btn ghost" type="submit"><x-icon name="search"/> Filter</button>
        @if (request()->hasAny(['q','from','to'])) <a href="{{ route('sales.index') }}">Clear</a> @endif
        @error('to') <span class="error">{{ $message }}</span> @enderror
    </form>

    @if ($sales->isEmpty())
        <div class="empty"><x-icon name="receipt"/><div>No sales found.</div></div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>#</th><th>Product</th><th>Customer</th><th>Staff</th><th class="num">Qty</th><th class="num">Total</th><th>Date</th></tr></thead>
        <tbody>
        @foreach ($sales as $sale)
            <tr>
                <td style="color:var(--muted)">{{ $sale->id }}</td>
                <td><div class="cell">@include('products._thumb', ['product' => $sale->product])<strong>{{ $sale->product->name }}</strong></div></td>
                <td>@if ($sale->customer) <div class="cell">@include('partials.avatar', ['user' => $sale->customer]){{ $sale->customer->full_name }}</div> @else <span class="badge neutral">Walk-in</span> @endif</td>
                <td><div class="cell">@include('partials.avatar', ['user' => $sale->seller]){{ $sale->seller->full_name }}</div></td>
                <td class="num">{{ $sale->quantity }}</td>
                <td class="num"><strong>{{ peso($sale->total_price) }}</strong></td>
                <td>{{ $sale->created_at->format('M d, Y') }}<br><small style="color:var(--muted)">{{ $sale->created_at->format('g:i A') }}</small></td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    @include('partials.pager-footer', ['paginator' => $sales])
    @endif
</div>
@endsection
