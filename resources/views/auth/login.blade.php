{{-- @extends('layout.app') --}}
@extends('layout.guest')

@section('menuAuth', 'active')
@section('title', 'Login')

@section('content')
    @livewire('auth.login')
@endsection

