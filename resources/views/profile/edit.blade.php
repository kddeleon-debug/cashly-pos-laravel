@extends('layouts.app')
@section('title', 'Profile')

@section('content')
<div class="page-head"><div><h1>My profile</h1><p>Update your account details and password.</p></div></div>

<div class="grid" style="max-width:760px">
    <div class="card card-pad">
        @include('profile.partials.update-profile-information-form')
    </div>
    <div class="card card-pad">
        @include('profile.partials.update-password-form')
    </div>
</div>
@endsection
