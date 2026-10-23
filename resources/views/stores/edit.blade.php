@extends('layouts.admin')
@section('title', 'Edit Customer')
@section('subtitle', $store->store_name)
@section('content')
@include('stores._form')
@endsection