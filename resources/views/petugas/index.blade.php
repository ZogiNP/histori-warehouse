@extends('layouts.app')

@section('title', 'Data Petugas — Histori Warehouse')

@section('content')
    <h1>Data Petugas</h1>
    <p>Petugas nonaktif tetap disimpan untuk penelusuran histori.</p>

    <a class="tombol" href="{{ route('petugas.create') }}">
        Tambah Petugas
    </a>

    <div class="tabel">
        <table>
            <thead>
                <tr>
                    <th scope="col">No.</th>
                    <th scope="col">Nama Petugas</th>
                    <th scope="col">Divisi</th>
                    <th scope="col">Status</th>
                    <th scope="col">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPetugas as $petugas)
                    <tr>
                        <td>{{ $daftarPetugas->firstItem() + $loop->index }}</td>
                        <td>{{ $petugas->nama_petugas }}</td>
                        <td>{{ $petugas->divisi->nama_divisi }}</td>
                        <td>{{ $petugas->status_aktif ? 'Aktif' : 'Nonaktif' }}</td>
                        <td>
                            <a href="{{ route('petugas.edit', $petugas) }}">
                                Ubah
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada petugas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="aksi" aria-label="Navigasi halaman">
        @if ($daftarPetugas->previousPageUrl())
            <a href="{{ $daftarPetugas->previousPageUrl() }}">
                Sebelumnya
            </a>
        @endif

        <span>
            Halaman {{ $daftarPetugas->currentPage() }}
            dari {{ $daftarPetugas->lastPage() }}
        </span>

        @if ($daftarPetugas->hasMorePages())
            <a href="{{ $daftarPetugas->nextPageUrl() }}">
                Berikutnya
            </a>
        @endif
    </div>
@endsection