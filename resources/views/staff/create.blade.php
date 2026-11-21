@extends('layouts.app')
@section('title', 'Add staff')
@section('content')
<div class="page-head"><div><h1>Add staff member</h1><p>Create an account that can sign in to the POS.</p></div></div>
<div class="card card-pad" style="max-width:760px">
    <form method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('staff._form')
    </form>
</div>
@endsection
