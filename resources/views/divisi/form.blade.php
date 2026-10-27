@extends('layouts.app')

@section('title', 'Form Divisi — Histori Warehouse')

@section('content')
    <h1>{{ $divisi->exists ? 'Ubah Divisi' : 'Tambah Divisi' }}</h1>

    <form
        method="POST"
        action="{{ $divisi->exists
            ? route('divisi.update', $divisi)
            : route('divisi.store') }}"
    >
        @csrf

        @if ($divisi->exists)
            @method('PUT')
        @endif

        <label for="nama_divisi">Nama Divisi</label>

        <input
            id="nama_divisi"
            name="nama_divisi"
            type="text"
            value="{{ old('nama_divisi', $divisi->nama_divisi) }}"
            maxlength="100"
            required
            @error('nama_divisi')
                aria-invalid="true"
                aria-describedby="nama_divisi_error"
            @enderror
        >

        @error('nama_divisi')
            <p class="error" id="nama_divisi_error" role="alert">
                {{ $message }}
            </p>
        @enderror

        <div class="aksi">
            <button type="submit">Simpan</button>
            <a href="{{ route('divisi.index') }}">Batal</a>
        </div>
    </form>
@endsection