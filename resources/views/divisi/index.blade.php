@extends('layouts.app')

@section('title', 'Data Divisi — Histori Warehouse')

@section('content')
    <h1>Data Divisi</h1>
    <p>Kelola divisi yang digunakan untuk pencatatan aktivitas warehouse.</p>

    <a class="tombol" href="{{ route('divisi.create') }}">
        Tambah Divisi
    </a>

    <div class="tabel">
        <table>
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Nama Divisi</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarDivisi as $divisi)
                    <tr>
                        <td>{{ $daftarDivisi->firstItem() + $loop->index }}</td>
                        <td>{{ $divisi->nama_divisi }}</td>
                        <td>
                            <a href="{{ route('divisi.edit', $divisi) }}">
                                Ubah
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Belum ada divisi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="aksi" aria-label="Navigasi halaman">
        @if ($daftarDivisi->previousPageUrl())
            <a href="{{ $daftarDivisi->previousPageUrl() }}">Sebelumnya</a>
        @endif

        <span>
            Halaman {{ $daftarDivisi->currentPage() }}
            dari {{ $daftarDivisi->lastPage() }}
        </span>

        @if ($daftarDivisi->hasMorePages())
            <a href="{{ $daftarDivisi->nextPageUrl() }}">Berikutnya</a>
        @endif
    </div>
@endsection