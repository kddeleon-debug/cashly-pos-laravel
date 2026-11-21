@extends('layouts.app')
@section('title', 'Edit staff')
@section('content')
<div class="page-head"><div><h1>Edit staff member</h1><p>Update {{ $user->full_name }}.</p></div></div>
<div class="card card-pad" style="max-width:760px">
    <form method="POST" action="{{ route('staff.update', $user) }}" enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        @include('staff._form')
    </form>
</div>
@endsection
