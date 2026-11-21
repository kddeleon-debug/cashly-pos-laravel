@props(['disabled' => false, 'invalid' => false])
<input @disabled($disabled) {{ $attributes->merge(['class' => 'input'.($invalid ? ' is-invalid' : '')]) }}>
