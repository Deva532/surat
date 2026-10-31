@extends('layout.app')

@section('menuSuperadminSuratKeluar', 'active')
@section('title', 'Surat Keluar')

@section('content')
    @livewire('superadmin.suratkeluar.index')
@endsection