@php $editing = isset($product); @endphp
<div class="form-grid">
    <div class="field full">
        <label for="name">Product name <span class="req">*</span></label>
        <input class="input @error('name') is-invalid @enderror" id="name" name="name" maxlength="100" value="{{ old('name', $product->name ?? '') }}" required>
        @error('name') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="price">Price <span class="req">*</span></label>
        <div class="input-prefix"><span>₱</span>
            <input class="input @error('price') is-invalid @enderror" id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required>
        </div>
        @error('price') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="stock_quantity">Stock quantity <span class="req">*</span></label>
        <input class="input @error('stock_quantity') is-invalid @enderror" id="stock_quantity" name="stock_quantity" type="number" step="1" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required>
        @error('stock_quantity') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="field full">
        <span class="label">Product image</span>
        <div class="upload">
            <div class="preview" id="imgPreview">
                @if ($editing && $product->image_url) <img src="{{ $product->image_url }}" alt="">@else <x-icon name="image"/> @endif
            </div>
            <div style="flex:1">
                <input class="input @error('image') is-invalid @enderror" type="file" name="image" accept="image/png,image/jpeg,image/webp" data-preview="imgPreview">
                <div class="hint" style="margin-top:6px">JPG, PNG or WEBP · max 2 MB. It is cropped to a square and resized to 600×600 for display.</div>
                @if ($editing && $product->image)
                    <label class="check" style="margin-top:8px"><input type="checkbox" name="remove_image" value="1"> Remove current image</label>
                @endif
            </div>
        </div>
        @error('image') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>
<div class="form-actions">
    <button class="btn" type="submit"><x-icon name="check"/> {{ $editing ? 'Save changes' : 'Add product' }}</button>
    <a class="btn ghost" href="{{ route('products.index') }}">Cancel</a>
</div>
