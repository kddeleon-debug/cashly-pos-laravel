@extends('layouts.guest')
@section('title', 'Confirm password')

@section('content')
<h1>Confirm your password</h1>
<p class="sub">This is a secure area. Please confirm your password before continuing.</p>

<form method="POST" action="{{ route('password.confirm') }}" novalidate>
    @csrf
    <div class="field" style="margin-bottom:22px">
        <x-input-label for="password" value="Password" />
        <x-text-input id="password" type="password" name="password" :invalid="$errors->has('password')" required autofocus autocomplete="current-password" />
        <x-input-error :messages="$errors->get('password')" />
    </div>
    <x-primary-button class="block"><x-icon name="shield"/> Confirm</x-primary-button>
    <p style="text-align:center;margin-top:14px"><a href="{{ route('dashboard') }}">Cancel</a></p>
</form>
@endsection
