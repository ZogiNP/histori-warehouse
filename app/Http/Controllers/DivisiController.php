<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DivisiController extends Controller
{
    public function index(): View
    {
        return view('divisi.index', [
            'daftarDivisi' => Divisi::orderBy('nama_divisi')->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('divisi.form', [
            'divisi' => new Divisi(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Divisi::create($this->validasi($request));

        return redirect()->route('divisi.index')
            ->with('status', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Divisi $divisi): View
    {
        return view('divisi.form', compact('divisi'));
    }

    public function update(Request $request, Divisi $divisi): RedirectResponse
    {
        $divisi->update($this->validasi($request, $divisi));

        return redirect()->route('divisi.index')
            ->with('status', 'Divisi berhasil diperbarui.');
    }

    private function validasi(Request $request, ?Divisi $divisi = null): array
    {
        $nama = $request->input('nama_divisi');

        // Rapikan spasi hanya jika input memang berupa teks.
        if (is_string($nama)) {
            $request->merge([
                'nama_divisi' => preg_replace('/\s+/u', ' ', trim($nama)),
            ]);
        }

        $unik = Rule::unique('divisi', 'nama_divisi');

        if ($divisi !== null) {
            $unik->ignore($divisi);
        }

        return $request->validate([
            'nama_divisi' => [
                'required',
                'string',
                'max:100',
                $unik,
            ],
        ], [
            'nama_divisi.required' => 'Nama divisi wajib diisi.',
            'nama_divisi.string' => 'Nama divisi harus berupa teks.',
            'nama_divisi.max' => 'Nama divisi maksimal 100 karakter.',
            'nama_divisi.unique' => 'Nama divisi sudah digunakan.',
        ]);
    }
}