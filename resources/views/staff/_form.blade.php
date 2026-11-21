@php $editing = isset($user); @endphp
<div class="form-grid">
    <div class="field">
        <label for="full_name">Full name <span class="req">*</span></label>
        <input class="input @error('full_name') is-invalid @enderror" id="full_name" name="full_name" maxlength="100" value="{{ old('full_name', $user->full_name ?? '') }}" required>
        @error('full_name') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="username">Username <span class="req">*</span></label>
        <input class="input @error('username') is-invalid @enderror" id="username" name="username" maxlength="50" autocomplete="off" value="{{ old('username', $user->username ?? '') }}" required>
        @error('username') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="field">
        <label for="password">Password @unless ($editing)<span class="req">*</span>@endunless</label>
        <input class="input @error('password') is-invalid @enderror" id="password" name="password" type="password" autocomplete="new-password" @unless ($editing) required @endunless>
        <span class="hint">{{ $editing ? 'Leave blank to keep the current password. ' : '' }}At least 8 characters with letters and numbers.</span>
        @error('password') <span class="error">{{ $message }}</span> @enderror
    </div>
    <div class="field">
        <label for="password_confirmation">Confirm password @unless ($editing)<span class="req">*</span>@endunless</label>
        <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" @unless ($editing) required @endunless>
    </div>

    <div class="field full">
        <span class="label">Avatar</span>
        <div class="upload">
            <div class="preview round" id="avatarPreview">
                @if ($editing && $user->avatar_url) <img src="{{ $user->avatar_url }}" alt="">@else <x-icon name="badge"/> @endif
            </div>
            <div style="flex:1">
                <input class="input @error('avatar') is-invalid @enderror" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" data-preview="avatarPreview">
                <div class="hint" style="margin-top:6px">JPG, PNG or WEBP · max 2 MB. It is cropped to a square and resized to 256×256.</div>
                @if ($editing && $user->avatar)
                    <label class="check" style="margin-top:8px"><input type="checkbox" name="remove_avatar" value="1"> Remove current avatar</label>
                @endif
            </div>
        </div>
        @error('avatar') <span class="error">{{ $message }}</span> @enderror
    </div>
</div>
<div class="form-actions">
    <button class="btn" type="submit"><x-icon name="check"/> {{ $editing ? 'Save changes' : 'Add staff member' }}</button>
    <a class="btn ghost" href="{{ route('staff.index') }}">Cancel</a>
</div>
