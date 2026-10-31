@extends('layout.app')

@section('menuSuperadminProfile', 'active')
@section('title', 'Profile')

@section('content')
    @livewire('superadmin.profile.index')
@endsection