<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\Petugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PetugasController extends Controller
{
    public function index(): View
    {
        return view('petugas.index', [
            'daftarPetugas' => Petugas::with('divisi')
                ->orderBy('nama_petugas')
                ->orderBy('id')
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('petugas.form', [
            'petugas' => new Petugas(['status_aktif' => true]),
            'daftarDivisi' => Divisi::orderBy('nama_divisi')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Petugas::create($this->validasi($request));

        return redirect()->route('petugas.index')
            ->with('status', 'Petugas berhasil ditambahkan.');
    }

    public function edit(Petugas $petugas): View
    {
        return view('petugas.form', [
            'petugas' => $petugas,
            'daftarDivisi' => Divisi::orderBy('nama_divisi')->get(),
        ]);
    }

    public function update(
        Request $request,
        Petugas $petugas
    ): RedirectResponse {
        $petugas->update($this->validasi($request));

        return redirect()->route('petugas.index')
            ->with('status', 'Petugas berhasil diperbarui.');
    }

    private function validasi(Request $request): array
    {
        $nama = $request->input('nama_petugas');

        if (is_string($nama)) {
            $request->merge([
                'nama_petugas' => preg_replace('/\s+/u', ' ', trim($nama)),
            ]);
        }

        return $request->validate([
            'nama_petugas' => ['required', 'string', 'max:150'],
            'divisi_id' => ['required', 'integer', 'exists:divisi,id'],
            'status_aktif' => ['required', 'boolean'],
        ], [
            'nama_petugas.required' => 'Nama petugas wajib diisi.',
            'nama_petugas.string' => 'Nama petugas harus berupa teks.',
            'nama_petugas.max' => 'Nama petugas maksimal 150 karakter.',
            'divisi_id.required' => 'Divisi wajib dipilih.',
            'divisi_id.integer' => 'Pilihan divisi tidak valid.',
            'divisi_id.exists' => 'Divisi yang dipilih tidak tersedia.',
            'status_aktif.required' => 'Status wajib dipilih.',
            'status_aktif.boolean' => 'Status yang dipilih tidak valid.',
        ]);
    }
}