<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DivisiTest extends TestCase
{
    private bool $transaksiDimulai = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertTrue(app()->environment('testing'));
        $this->assertSame(
            'histori_warehouse_testing',
            DB::selectOne('SELECT DATABASE() AS nama')->nama
        );

        DB::beginTransaction();
        $this->transaksiDimulai = true;
    }

    protected function tearDown(): void
    {
        try {
            if ($this->transaksiDimulai) {
                DB::rollBack();
            }
        } finally {
            parent::tearDown();
        }
    }

    private function login(): void
    {
        $this->actingAs(User::factory()->create());
    }

    private function buatDivisi(): Divisi
    {
        return Divisi::create([
            'nama_divisi' => 'Divisi Uji '.Str::uuid(),
        ]);
    }

    public function test_seluruh_aksi_divisi_memerlukan_login(): void
    {
        $divisi = $this->buatDivisi();
        $namaAwal = $divisi->nama_divisi;
        $jumlahAwal = Divisi::count();

        $this->get('/divisi')->assertRedirect(route('login'));
        $this->get('/divisi/create')->assertRedirect(route('login'));
        $this->get("/divisi/{$divisi->id}/edit")
            ->assertRedirect(route('login'));

        $this->post('/divisi', [
            'nama_divisi' => 'Tidak Boleh Tersimpan',
        ])->assertRedirect(route('login'));

        $this->put("/divisi/{$divisi->id}", [
            'nama_divisi' => 'Tidak Boleh Berubah',
        ])->assertRedirect(route('login'));

        $this->assertSame($jumlahAwal, Divisi::count());
        $this->assertSame($namaAwal, $divisi->fresh()->nama_divisi);
    }

    public function test_daftar_dan_form_divisi_dapat_dibuka(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();

        $this->get('/divisi')
            ->assertOk()
            ->assertSee($divisi->nama_divisi);

        $this->get('/divisi/create')
            ->assertOk()
            ->assertSee('Tambah Divisi');

        $this->get("/divisi/{$divisi->id}/edit")
            ->assertOk()
            ->assertSee('Ubah Divisi')
            ->assertSee($divisi->nama_divisi);
    }

    public function test_divisi_dapat_ditambah_dan_spasi_dirapikan(): void
    {
        $this->login();

        $nama = 'Divisi Uji '.Str::uuid();

        $this->post('/divisi', [
            'nama_divisi' => '  '.$nama.'   Tambahan  ',
        ])->assertRedirect(route('divisi.index'));

        $this->assertDatabaseHas('divisi', [
            'nama_divisi' => $nama.' Tambahan',
        ]);
    }

    public function test_nama_kosong_terlalu_panjang_dan_array_ditolak(): void
    {
        $this->login();
        $jumlahAwal = Divisi::count();

        foreach (['   ', str_repeat('A', 101), ['bukan teks']] as $nama) {
            $this->from('/divisi/create')->post('/divisi', [
                'nama_divisi' => $nama,
            ])->assertSessionHasErrors('nama_divisi');
        }

        $this->assertSame($jumlahAwal, Divisi::count());
    }

    public function test_nama_duplikat_ditolak_saat_menambah(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();
        $jumlahAwal = Divisi::count();

        $this->post('/divisi', [
            'nama_divisi' => $divisi->nama_divisi,
        ])->assertSessionHasErrors('nama_divisi');

        $this->assertSame($jumlahAwal, Divisi::count());
    }

    public function test_nama_divisi_dapat_diubah(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();
        $namaBaru = 'Nama Baru '.Str::uuid();

        $this->put("/divisi/{$divisi->id}", [
            'nama_divisi' => $namaBaru,
        ])->assertRedirect(route('divisi.index'));

        $this->assertSame($namaBaru, $divisi->fresh()->nama_divisi);
    }

    public function test_menyimpan_nama_sendiri_tidak_dianggap_duplikat(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();

        $this->put("/divisi/{$divisi->id}", [
            'nama_divisi' => $divisi->nama_divisi,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('divisi.index'));
    }

    public function test_nama_divisi_lain_ditolak_saat_mengubah(): void
    {
        $this->login();
        $pertama = $this->buatDivisi();
        $kedua = $this->buatDivisi();
        $namaAwal = $kedua->nama_divisi;

        $this->put("/divisi/{$kedua->id}", [
            'nama_divisi' => $pertama->nama_divisi,
        ])->assertSessionHasErrors('nama_divisi');

        $this->assertSame($namaAwal, $kedua->fresh()->nama_divisi);
    }

    public function test_divisi_tidak_tersedia_menghasilkan_404(): void
    {
        $this->login();
        $id = ((int) Divisi::max('id')) + 1;

        $this->get("/divisi/{$id}/edit")->assertNotFound();

        $this->put("/divisi/{$id}", [
            'nama_divisi' => 'Tidak Tersimpan',
        ])->assertNotFound();
    }

    public function test_route_hapus_tidak_tersedia(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();

        $this->delete("/divisi/{$divisi->id}")
            ->assertStatus(405);

        $this->assertDatabaseHas('divisi', [
            'id' => $divisi->id,
        ]);
    }
}