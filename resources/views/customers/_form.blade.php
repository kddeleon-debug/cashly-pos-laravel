@php $editing = isset($customer); @endphp
<div class="form-grid">
    <div class="field full">
        <label for="full_name">Full name <span class="req">*</span></label>
        <input class="input @error('full_name') is-invalid @enderror" id="full_name" name="full_name" maxlength="100" value="{{ old('full_name', $customer->full_name ?? '') }}" required>
        @error('full_name') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="email">Email <span class="req">*</span></label>
        <input class="input @error('email') is-invalid @enderror" id="email" name="email" type="email" maxlength="100" value="{{ old('email', $customer->email ?? '') }}" required>
        @error('email') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="phone">Phone</label>
        <input class="input @error('phone') is-invalid @enderror" id="phone" name="phone" maxlength="20" value="{{ old('phone', $customer->phone ?? '') }}" placeholder="Optional">
        @error('phone') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field full">
        <span class="label">Photo / avatar</span>
        <div class="upload">
            <div class="preview round" id="avatarPreview">
                @if ($editing && $customer->avatar_url) <img src="{{ $customer->avatar_url }}" alt=""> @else <x-icon name="users"/> @endif
            </div>
            <div style="flex:1">
                <input class="input @error('avatar') is-invalid @enderror" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" data-preview="avatarPreview">
                <div class="hint" style="margin-top:6px">Optional · JPG, PNG or WEBP · max 2 MB. Cropped to a square (256×256).</div>
                @if ($editing && $customer->avatar)
                    <label class="check" style="margin-top:8px"><input type="checkbox" name="remove_avatar" value="1"> Remove current photo</label>
                @endif
            </div>
        </div>
        @error('avatar') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>
<div class="form-actions">
    <button class="btn" type="submit"><x-icon name="check"/> {{ $editing ? 'Save changes' : 'Add customer' }}</button>
    <a class="btn ghost" href="{{ route('customers.index') }}">Cancel</a>
</div>
