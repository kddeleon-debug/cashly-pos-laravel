@extends('layouts.guest')
@section('title', 'Sign in')

@section('content')
<h1>Staff sign in</h1>
<p class="sub">Enter your credentials to access the system.</p>

<!-- Session Status -->
<x-auth-session-status :status="session('status')" />

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    <!-- Username -->
    <div class="field" style="margin-bottom:16px">
        <x-input-label for="username" value="Username" />
        <x-text-input id="username" type="text" name="username" :value="old('username')" :invalid="$errors->has('username')" required autofocus autocomplete="username" />
        <x-input-error :messages="$errors->get('username')" />
    </div>

    <!-- Password -->
    <div class="field" style="margin-bottom:22px">
        <x-input-label for="password" value="Password" />
        <x-text-input id="password" type="password" name="password" :invalid="$errors->has('password')" required autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" />
    </div>

    <x-primary-button class="block"><x-icon name="lock"/> Sign in</x-primary-button>
</form>

@if (app()->environment('local'))
    <div class="cred"><strong>Demo account</strong> (local only)<br>Username: <code>admin</code> · Password: <code>admin12345</code></div>
@endif
@endsection
