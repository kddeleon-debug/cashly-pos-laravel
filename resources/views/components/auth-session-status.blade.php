@props(['status'])
@if ($status)
    <div {{ $attributes->merge(['class' => 'alert success']) }}><x-icon name="check"/> <span>{{ $status }}</span></div>
@endif
