@extends('layouts.app')
@section('title', 'Customers')

@section('content')
<div class="page-head">
    <div><h1>Customers</h1><p>People who buy from your store.</p></div>
    <a class="btn" href="{{ route('customers.create') }}"><x-icon name="plus"/> Add customer</a>
</div>

<div class="card">
    <form class="toolbar" method="GET" action="{{ route('customers.index') }}">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name, email or phone…">
        <button class="btn ghost" type="submit"><x-icon name="search"/> Search</button>
        @if (request('q')) <a href="{{ route('customers.index') }}">Clear</a> @endif
    </form>

    @if ($customers->isEmpty())
        <div class="empty"><x-icon name="users"/><div>No customers found.</div></div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>Customer</th><th>Phone</th><th>Purchases</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        @foreach ($customers as $customer)
            <tr>
                <td><div class="cell">@include('partials.avatar', ['user' => $customer, 'size' => 'lg'])<div><strong>{{ $customer->full_name }}</strong><small>{{ $customer->email }}</small></div></div></td>
                <td>{{ $customer->phone ?: '—' }}</td>
                <td><span class="badge neutral">{{ $customer->sales_count }}</span></td>
                <td>{{ $customer->created_at->format('M d, Y') }}</td>
                <td>
                    <div class="actions">
                        <a class="btn ghost sm" href="{{ route('customers.edit', $customer) }}"><x-icon name="edit"/> Edit</a>
                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm="Delete {{ $customer->full_name }}? This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn danger sm" type="submit"><x-icon name="trash"/> Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    @include('partials.pager-footer', ['paginator' => $customers])
    @endif
</div>
@endsection
