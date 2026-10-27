<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BuktiMedia;
use App\Models\Divisi;
use App\Models\HistoriBarang;
use App\Models\JenisAktivitas;
use App\Models\LogPerubahan;
use App\Models\Petugas;
use Database\Seeders\DivisiSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class WarehouseBehaviorTest extends TestCase
{
    private bool $transaksiDimulai = false;

    protected function setUp(): void
    {
        parent::setUp();

        // Pemeriksaan dilakukan SEBELUM menulis data.
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

    private function buatHistori(): HistoriBarang
    {
        $divisi = Divisi::create([
            'nama_divisi' => 'Uji '.Str::uuid(),
        ]);

        $petugas = Petugas::create([
            'divisi_id' => $divisi->id,
            'nama_petugas' => 'Petugas Pengujian',
            'status_aktif' => true,
        ]);

        $barang = Barang::create([
            'kode_barang' => 'UJI-'.Str::uuid(),
            'nama_barang' => 'Barang Pengujian',
            'satuan' => 'pcs',
        ]);

        $aktivitas = JenisAktivitas::create([
            'divisi_id' => $divisi->id,
            'nama_aktivitas' => 'Pemeriksaan',
            'status_aktif' => true,
        ]);

        return HistoriBarang::create([
            'barang_id' => $barang->id,
            'divisi_id' => $divisi->id,
            'petugas_id' => $petugas->id,
            'jenis_aktivitas_id' => $aktivitas->id,
            'jumlah' => '2.500',
            'keterangan' => 'Data pengujian, bukan aktivitas nyata.',
            'waktu_aktivitas' => now(),
        ]);
    }

    public function test_relasi_dan_soft_delete_histori(): void
    {
        $histori = $this->buatHistori();

        $media = new BuktiMedia();
        $media->forceFill([
            'histori_barang_id' => $histori->id,
            'nama_file_asli' => 'contoh.jpg',
            'nama_file_storage' => Str::uuid().'.jpg',
            'path_file' => 'pengujian/contoh.jpg',
            'disk_storage' => 'warehouse_private',
            'mime_type' => 'image/jpeg',
            'ukuran_file' => 100,
            'jenis_media' => 'foto',
        ])->save();

        $log = new LogPerubahan();
        $log->forceFill([
            'histori_barang_id' => $histori->id,
            'petugas_id' => $histori->petugas_id,
            'jenis_perubahan' => 'dibuat',
            'data_sesudah' => ['jumlah' => '2.500'],
            'waktu_perubahan' => now(),
        ])->save();

        $this->assertTrue($histori->barang->exists);
        $this->assertTrue($histori->divisi->exists);
        $this->assertTrue($histori->petugas->exists);
        $this->assertTrue($histori->jenisAktivitas->exists);
        $this->assertSame('2.500', $histori->jumlah);
        $this->assertSame(1, $histori->buktiMedia()->count());
        $this->assertSame(1, $histori->logPerubahan()->count());

        // Hanya menandai data contoh pada database pengujian.
        $histori->delete();

        $this->assertNull(HistoriBarang::find($histori->id));
        $this->assertTrue(
            HistoriBarang::withTrashed()->findOrFail($histori->id)->trashed()
        );

        $this->assertTrue(
            $media->fresh()->historiBarang->trashed()
        );
        $this->assertTrue(
            $log->fresh()->historiBarang->trashed()
        );
        $this->assertSame(
            ['jumlah' => '2.500'],
            $log->fresh()->data_sesudah
        );
    }

    public function test_foreign_key_menolak_barang_tidak_tersedia(): void
    {
        $histori = $this->buatHistori();

        // ID ini milik data contoh yang belum ada dalam tabel barang.
        $idTidakAda = ((int) Barang::max('id')) + 1;

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('1452');

        DB::table('histori_barang')
            ->where('id', $histori->id)
            ->update(['barang_id' => $idTidakAda]);
    }

    public function test_jumlah_negatif_ditolak(): void
    {
        $histori = $this->buatHistori();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('4025');

        DB::table('histori_barang')
            ->where('id', $histori->id)
            ->update(['jumlah' => -1]);
    }

    public function test_barang_yang_dirujuk_tidak_bisa_dihapus(): void
    {
        $histori = $this->buatHistori();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('1451');

        // Mencoba menghapus barang contoh; foreign key harus menolak.
        $histori->barang->delete();
    }

    public function test_kode_barang_duplikat_ditolak(): void
    {
        $histori = $this->buatHistori();

        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('1062');

        Barang::create([
            'kode_barang' => $histori->barang->kode_barang,
            'nama_barang' => 'Barang Duplikat Pengujian',
            'satuan' => 'pcs',
        ]);
    }

    public function test_seeder_divisi_tidak_menggandakan_data(): void
    {
        $this->seed(DivisiSeeder::class);

        $sebelum = Divisi::orderBy('id')
            ->get(['id', 'nama_divisi'])
            ->toArray();

        $this->seed(DivisiSeeder::class);

        $sesudah = Divisi::orderBy('id')
            ->get(['id', 'nama_divisi'])
            ->toArray();

        $this->assertSame($sebelum, $sesudah);

        foreach ([
            'Penyiap Reseller',
            'Quality Control',
            'Penerima Barang',
        ] as $nama) {
            $this->assertSame(
                1,
                Divisi::where('nama_divisi', $nama)->count()
            );
        }
    }
}