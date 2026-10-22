@extends('layouts.admin')
@section('title', 'Edit Product')
@section('subtitle', $product->name)
@section('content')
<div class="max-w-4xl">@include('products._form')</div>
@endsection