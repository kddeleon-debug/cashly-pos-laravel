@extends('layouts.app')
@section('title', 'Add customer')
@section('content')
<div class="page-head"><div><h1>Add customer</h1><p>Save a customer's contact details.</p></div></div>
<div class="card card-pad" style="max-width:720px">
    <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('customers._form')
    </form>
</div>
@endsection
