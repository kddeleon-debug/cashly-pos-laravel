@extends('layouts.app')
@section('title', 'Staff')

@section('content')
<div class="page-head">
    <div><h1>Staff</h1><p>Accounts that can sign in and record sales.</p></div>
    <a class="btn" href="{{ route('staff.create') }}"><x-icon name="plus"/> Add staff</a>
</div>

<div class="card">
    <form class="toolbar" method="GET" action="{{ route('staff.index') }}">
        <input class="input" type="search" name="q" value="{{ request('q') }}" placeholder="Search name or username…">
        <button class="btn ghost" type="submit"><x-icon name="search"/> Search</button>
        @if (request('q')) <a href="{{ route('staff.index') }}">Clear</a> @endif
    </form>

    @if ($staff->isEmpty())
        <div class="empty"><x-icon name="badge"/><div>No staff found.</div></div>
    @else
    <div class="table-wrap"><table>
        <thead><tr><th>Staff member</th><th>Sales recorded</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        @foreach ($staff as $member)
            <tr>
                <td>
                    <div class="cell">
                        @include('partials.avatar', ['user' => $member, 'size' => 'lg'])
                        <div><strong>{{ $member->full_name }} @if ($member->is(auth()->user())) <span class="badge neutral">You</span> @endif</strong><small>&#64;{{ $member->username }}</small></div>
                    </div>
                </td>
                <td><span class="badge neutral">{{ $member->sales_count }}</span></td>
                <td>{{ $member->created_at->format('M d, Y') }}</td>
                <td>
                    <div class="actions">
                        <a class="btn ghost sm" href="{{ route('staff.edit', $member) }}"><x-icon name="edit"/> Edit</a>
                        @unless ($member->is(auth()->user()))
                        <form method="POST" action="{{ route('staff.destroy', $member) }}" data-confirm="Delete {{ $member->full_name }}? This cannot be undone.">
                            @csrf @method('DELETE')
                            <button class="btn danger sm" type="submit"><x-icon name="trash"/> Delete</button>
                        </form>
                        @endunless
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table></div>
    @include('partials.pager-footer', ['paginator' => $staff])
    @endif
</div>
@endsection
