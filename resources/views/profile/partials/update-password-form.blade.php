<h2 style="margin-bottom:4px">Update password</h2>
<p class="hint" style="margin-bottom:20px">Use at least 8 characters with letters and numbers.</p>

<form method="POST" action="{{ route('password.update') }}" novalidate>
    @csrf @method('PUT')
    <div class="form-grid">
        <div class="field full">
            <x-input-label for="update_password_current_password" value="Current password" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" :invalid="$errors->updatePassword->has('current_password')" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" />
        </div>
        <div class="field">
            <x-input-label for="update_password_password" value="New password" />
            <x-text-input id="update_password_password" name="password" type="password" :invalid="$errors->updatePassword->has('password')" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" />
        </div>
        <div class="field">
            <x-input-label for="update_password_password_confirmation" value="Confirm new password" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" />
        </div>
    </div>
    <div class="form-actions">
        <x-primary-button><x-icon name="lock"/> Update password</x-primary-button>
        @if (session('status') === 'password-updated')
            <span class="badge ok" style="align-self:center">Saved.</span>
        @endif
    </div>
</form>
