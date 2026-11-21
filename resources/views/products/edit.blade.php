@extends('layouts.app')
@section('title', 'Edit product')
@section('content')
<div class="page-head"><div><h1>Edit product</h1><p>Update “{{ $product->name }}”.</p></div></div>
<div class="card card-pad" style="max-width:760px">
    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" novalidate>
        @csrf @method('PUT')
        @include('products._form')
    </form>
</div>
@endsection
