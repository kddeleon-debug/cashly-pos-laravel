@if (session('success'))
    <div class="alert success" role="status"><x-icon name="check"/> <span>{{ session('success') }}</span><button type="button" class="alert-x" data-dismiss aria-label="Dismiss">&times;</button></div>
@endif
@if (session('error'))
    <div class="alert error" role="alert"><x-icon name="alert"/> <span>{{ session('error') }}</span><button type="button" class="alert-x" data-dismiss aria-label="Dismiss">&times;</button></div>
@endif
@if ($errors->any() && ! request()->routeIs('login', 'profile.edit', 'password.confirm'))
    <div class="alert error" role="alert"><x-icon name="alert"/>
        <span>@if ($errors->count() === 1){{ $errors->first() }}@else Please fix the {{ $errors->count() }} errors below and try again.@endif</span>
        <button type="button" class="alert-x" data-dismiss aria-label="Dismiss">&times;</button>
    </div>
@endif
