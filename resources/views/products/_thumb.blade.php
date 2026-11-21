@if ($product->image_url)
    <img class="thumb" src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
@else
    <span class="thumb placeholder">{{ mb_strtoupper(mb_substr($product->name, 0, 1)) }}</span>
@endif
