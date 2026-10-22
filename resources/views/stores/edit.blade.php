@extends('layouts.admin')
@section('title', 'Edit Store')
@section('subtitle', $store->store_name)
@section('content')
@include('stores._form')
@endsection