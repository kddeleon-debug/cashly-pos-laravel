@extends('layouts.app')
@section('title', 'Add product')
@section('content')
<div class="page-head"><div><h1>Add product</h1><p>Create a new item in your catalogue.</p></div></div>
<div class="card card-pad" style="max-width:760px">
    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @include('products._form')
    </form>
</div>
@endsection
