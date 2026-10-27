<?php

namespace Tests\Feature;

use App\Models\Divisi;
use App\Models\Petugas;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PetugasTest extends TestCase
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

    private function buatPetugas(): Petugas
    {
        return Petugas::create([
            'divisi_id' => $this->buatDivisi()->id,
            'nama_petugas' => 'Petugas Uji '.Str::uuid(),
            'status_aktif' => true,
        ]);
    }

    public function test_seluruh_aksi_memerlukan_login(): void
    {
        $petugas = $this->buatPetugas();
        $jumlahAwal = Petugas::count();
        $namaAwal = $petugas->nama_petugas;

        $data = [
            'divisi_id' => $petugas->divisi_id,
            'nama_petugas' => 'Tidak Boleh Tersimpan',
            'status_aktif' => '1',
        ];

        $this->get('/petugas')->assertRedirect(route('login'));
        $this->get('/petugas/create')->assertRedirect(route('login'));
        $this->get("/petugas/{$petugas->id}/edit")
            ->assertRedirect(route('login'));

        $this->post('/petugas', $data)
            ->assertRedirect(route('login'));

        $this->put("/petugas/{$petugas->id}", $data)
            ->assertRedirect(route('login'));

        $this->assertSame($jumlahAwal, Petugas::count());
        $this->assertSame($namaAwal, $petugas->fresh()->nama_petugas);
    }

    public function test_daftar_dan_form_dapat_dibuka(): void
    {
        $this->login();
        $petugas = $this->buatPetugas();

        $this->get('/petugas')
            ->assertOk()
            ->assertSee($petugas->nama_petugas)
            ->assertSee($petugas->divisi->nama_divisi);

        $this->get('/petugas/create')
            ->assertOk()
            ->assertSee('Tambah Petugas')
            ->assertSee($petugas->divisi->nama_divisi);

        $this->get("/petugas/{$petugas->id}/edit")
            ->assertOk()
            ->assertSee($petugas->nama_petugas);
    }

    public function test_petugas_dapat_ditambah_dan_spasi_dirapikan(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();
        $nama = 'Petugas Uji '.Str::uuid();

        $this->post('/petugas', [
            'divisi_id' => $divisi->id,
            'nama_petugas' => '  '.$nama.'   Tambahan  ',
            'status_aktif' => '1',
        ])->assertRedirect(route('petugas.index'));

        $this->assertDatabaseHas('petugas', [
            'divisi_id' => $divisi->id,
            'nama_petugas' => $nama.' Tambahan',
            'status_aktif' => 1,
        ]);
    }

    public function test_nama_sama_diperbolehkan(): void
    {
        $this->login();
        $petugas = $this->buatPetugas();
        $jumlahAwal = Petugas::count();

        $this->post('/petugas', [
            'divisi_id' => $petugas->divisi_id,
            'nama_petugas' => $petugas->nama_petugas,
            'status_aktif' => '1',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('petugas.index'));

        $this->assertSame($jumlahAwal + 1, Petugas::count());
    }

    public function test_nama_tidak_valid_ditolak(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();
        $jumlahAwal = Petugas::count();

        foreach (['   ', str_repeat('A', 151), ['bukan teks']] as $nama) {
            $this->post('/petugas', [
                'divisi_id' => $divisi->id,
                'nama_petugas' => $nama,
                'status_aktif' => '1',
            ])->assertSessionHasErrors('nama_petugas');
        }

        $this->assertSame($jumlahAwal, Petugas::count());
    }

    public function test_divisi_tidak_valid_ditolak(): void
    {
        $this->login();
        $idTidakAda = ((int) Divisi::max('id')) + 1;
        $jumlahAwal = Petugas::count();

        foreach (['', 'bukan-id', $idTidakAda] as $divisiId) {
            $this->post('/petugas', [
                'divisi_id' => $divisiId,
                'nama_petugas' => 'Petugas Pengujian',
                'status_aktif' => '1',
            ])->assertSessionHasErrors('divisi_id');
        }

        $this->assertSame($jumlahAwal, Petugas::count());
    }

    public function test_status_tidak_valid_ditolak(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();
        $jumlahAwal = Petugas::count();

        foreach (['', 'aktif', '2', ['1']] as $status) {
            $this->post('/petugas', [
                'divisi_id' => $divisi->id,
                'nama_petugas' => 'Petugas Pengujian',
                'status_aktif' => $status,
            ])->assertSessionHasErrors('status_aktif');
        }

        $this->assertSame($jumlahAwal, Petugas::count());
    }

    public function test_petugas_dapat_diubah_dan_dinonaktifkan(): void
    {
        $this->login();
        $petugas = $this->buatPetugas();
        $divisiBaru = $this->buatDivisi();

        $this->put("/petugas/{$petugas->id}", [
            'divisi_id' => $divisiBaru->id,
            'nama_petugas' => 'Nama Petugas Baru',
            'status_aktif' => '0',
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('petugas.index'));

        $petugas->refresh();

        $this->assertSame('Nama Petugas Baru', $petugas->nama_petugas);
        $this->assertSame($divisiBaru->id, $petugas->divisi_id);
        $this->assertFalse($petugas->status_aktif);
    }

    public function test_perubahan_tidak_valid_tidak_disimpan(): void
    {
        $this->login();
        $petugas = $this->buatPetugas();
        $sebelum = $petugas->fresh()->getAttributes();

        $this->put("/petugas/{$petugas->id}", [
            'divisi_id' => 'tidak-valid',
            'nama_petugas' => '',
            'status_aktif' => 'salah',
        ])->assertSessionHasErrors([
            'divisi_id',
            'nama_petugas',
            'status_aktif',
        ]);

        $this->assertSame(
            $sebelum,
            $petugas->fresh()->getAttributes()
        );
    }

    public function test_petugas_tidak_tersedia_menghasilkan_404(): void
    {
        $this->login();
        $divisi = $this->buatDivisi();
        $id = ((int) Petugas::max('id')) + 1;

        $this->get("/petugas/{$id}/edit")->assertNotFound();

        $this->put("/petugas/{$id}", [
            'divisi_id' => $divisi->id,
            'nama_petugas' => 'Tidak Tersimpan',
            'status_aktif' => '1',
        ])->assertNotFound();
    }

    public function test_route_hapus_tidak_tersedia(): void
    {
        $this->login();
        $petugas = $this->buatPetugas();

        $this->delete("/petugas/{$petugas->id}")
            ->assertStatus(405);

        $this->assertDatabaseHas('petugas', [
            'id' => $petugas->id,
        ]);
    }
}