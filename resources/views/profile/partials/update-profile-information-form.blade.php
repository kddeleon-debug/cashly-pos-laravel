<h2 style="margin-bottom:4px">Profile information</h2>
<p class="hint" style="margin-bottom:20px">Update your name, username and avatar.</p>

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
    @csrf @method('PATCH')
    <div class="form-grid">
        <div class="field">
            <x-input-label for="full_name" value="Full name" />
            <x-text-input id="full_name" name="full_name" type="text" maxlength="100" :value="old('full_name', $user->full_name)" :invalid="$errors->has('full_name')" required />
            <x-input-error :messages="$errors->get('full_name')" />
        </div>
        <div class="field">
            <x-input-label for="username" value="Username" />
            <x-text-input id="username" name="username" type="text" maxlength="50" :value="old('username', $user->username)" :invalid="$errors->has('username')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" />
        </div>
        <div class="field full">
            <span class="label">Avatar</span>
            <div class="upload">
                <div class="preview round" id="avatarPreview">
                    @if ($user->avatar_url) <img src="{{ $user->avatar_url }}" alt=""> @else <x-icon name="badge"/> @endif
                </div>
                <div style="flex:1">
                    <input class="input @error('avatar') is-invalid @enderror" type="file" name="avatar" accept="image/png,image/jpeg,image/webp" data-preview="avatarPreview">
                    <div class="hint" style="margin-top:6px">JPG, PNG or WEBP · max 2 MB. Cropped to a square (256×256).</div>
                    @if ($user->avatar)
                        <label class="check" style="margin-top:8px"><input type="checkbox" name="remove_avatar" value="1"> Remove current avatar</label>
                    @endif
                </div>
            </div>
            <x-input-error :messages="$errors->get('avatar')" />
        </div>
    </div>
    <div class="form-actions">
        <x-primary-button><x-icon name="check"/> Save</x-primary-button>
        @if (session('status') === 'profile-updated')
            <span class="badge ok" style="align-self:center">Saved.</span>
        @endif
    </div>
</form>
