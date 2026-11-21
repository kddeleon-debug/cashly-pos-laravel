@props(['messages'])
@if ($messages)
    @foreach ((array) $messages as $message)
        <span {{ $attributes->merge(['class' => 'error']) }}>{{ $message }}</span>
    @endforeach
@endif
