@extends('layouts.admin')
@section('title', 'Add Product')
@section('subtitle', 'Create a new product and its variants')
@section('content')
<div class="max-w-4xl">@include('products._form')</div>
@endsection