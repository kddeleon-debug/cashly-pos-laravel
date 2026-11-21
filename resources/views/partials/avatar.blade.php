@php $big = ($size ?? 'md') === 'lg'; @endphp
@if ($user->avatar_url)
    <img class="avatar {{ $big ? 'lg' : '' }}" src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}">
@else
    <span class="avatar {{ $big ? 'lg' : '' }}">{{ $user->initials }}</span>
@endif
