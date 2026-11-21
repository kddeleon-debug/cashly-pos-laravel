@php $s = $product->stock_status; @endphp
<span class="badge {{ $s }}">
    @if ($s === 'out') Out of stock @elseif ($s === 'low') Low · {{ $product->stock_quantity }} left @else {{ $product->stock_quantity }} in stock @endif
</span>
