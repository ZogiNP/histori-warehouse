@extends('layouts.app')

@section('title', 'Form Petugas — Histori Warehouse')

@section('content')
    <h1>{{ $petugas->exists ? 'Ubah Petugas' : 'Tambah Petugas' }}</h1>

    @if ($daftarDivisi->isEmpty())
        <p class="error" role="alert">
            Belum ada divisi. Tambahkan divisi sebelum menyimpan petugas.
        </p>
        <p>
            <a href="{{ route('divisi.create') }}">Tambah Divisi</a>
        </p>
    @endif

    <form
        method="POST"
        action="{{ $petugas->exists
            ? route('petugas.update', $petugas)
            : route('petugas.store') }}"
    >
        @csrf

        @if ($petugas->exists)
            @method('PUT')
        @endif

        <label for="nama_petugas">Nama Petugas</label>
        <input
            id="nama_petugas"
            name="nama_petugas"
            type="text"
            value="{{ old('nama_petugas', $petugas->nama_petugas) }}"
            maxlength="150"
            required
            @error('nama_petugas')
                aria-invalid="true"
                aria-describedby="nama_petugas_error"
            @enderror
        >

        @error('nama_petugas')
            <p class="error" id="nama_petugas_error" role="alert">
                {{ $message }}
            </p>
        @enderror

        <p>
            <label for="divisi_id">Divisi</label>
            <select
                id="divisi_id"
                name="divisi_id"
                required
                @error('divisi_id')
                    aria-invalid="true"
                    aria-describedby="divisi_id_error"
                @enderror
            >
                <option value="">Pilih divisi</option>

                @foreach ($daftarDivisi as $divisi)
                    <option
                        value="{{ $divisi->id }}"
                        @selected(
                            (string) old('divisi_id', $petugas->divisi_id)
                            === (string) $divisi->id
                        )
                    >
                        {{ $divisi->nama_divisi }}
                    </option>
                @endforeach
            </select>
        </p>

        @error('divisi_id')
            <p class="error" id="divisi_id_error" role="alert">
                {{ $message }}
            </p>
        @enderror

        <p>
            <label for="status_aktif">Status</label>
            <select
                id="status_aktif"
                name="status_aktif"
                required
                @error('status_aktif')
                    aria-invalid="true"
                    aria-describedby="status_aktif_error"
                @enderror
            >
                <option value="1"
                    @selected((string) old('status_aktif', (int) $petugas->status_aktif) === '1')
                >
                    Aktif
                </option>
                <option value="0"
                    @selected((string) old('status_aktif', (int) $petugas->status_aktif) === '0')
                >
                    Nonaktif
                </option>
            </select>
        </p>

        @error('status_aktif')
            <p class="error" id="status_aktif_error" role="alert">
                {{ $message }}
            </p>
        @enderror

        <div class="aksi">
            <button type="submit" @disabled($daftarDivisi->isEmpty())>
                Simpan
            </button>
            <a href="{{ route('petugas.index') }}">Batal</a>
        </div>
    </form>
@endsection