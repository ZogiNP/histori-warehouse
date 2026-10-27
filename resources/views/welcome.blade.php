@extends('layouts.app')

@section('title', 'Histori Warehouse')

@section('content')
    <h1>Selamat Datang di Histori Warehouse</h1>
    <p>Aplikasi histori aktivitas barang warehouse.</p>

    <a class="tombol" href="{{ route('divisi.index') }}">
        Kelola Data Divisi
    </a>
@endsection