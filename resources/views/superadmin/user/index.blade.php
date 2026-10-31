@extends('layout.app')

@section('menuSuperadminUser', 'active')
@section('title', 'Data User')

@section('content')
    @livewire('superadmin.user.index')
@endsection