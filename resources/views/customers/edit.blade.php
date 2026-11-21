@extends('layouts.app')
@section('title', 'Edit customer')
@section('content')
<div class="page-head"><div><h1>Edit customer</h1><p>Update {{ $customer->full_name }}.</p></div></div>
<div class="card card-pad" style="max-width:720px">
    <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        @include('customers._form')
    </form>
</div>
@endsection
