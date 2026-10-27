<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WarehouseStructureTest extends TestCase
{
    public function test_tabel_dan_kolom_warehouse_tersedia(): void
    {
        $struktur = [
            'divisi' => [
                'id', 'nama_divisi', 'created_at', 'updated_at',
            ],
            'petugas' => [
                'id', 'divisi_id', 'nama_petugas', 'status_aktif',
            ],
            'barang' => [
                'id', 'kode_barang', 'nama_barang', 'satuan',
            ],
            'jenis_aktivitas' => [
                'id', 'divisi_id', 'nama_aktivitas', 'status_aktif',
            ],
            'histori_barang' => [
                'id', 'barang_id', 'divisi_id', 'petugas_id',
                'jenis_aktivitas_id', 'user_id', 'jumlah',
                'kondisi_barang', 'nomor_referensi', 'keterangan',
                'waktu_aktivitas', 'created_at', 'updated_at',
                'deleted_at',
            ],
            'bukti_media' => [
                'id', 'histori_barang_id', 'nama_file_asli',
                'nama_file_storage', 'path_file', 'disk_storage',
                'mime_type', 'ukuran_file', 'jenis_media',
                'created_at', 'updated_at',
            ],
            'log_perubahan' => [
                'id', 'histori_barang_id', 'petugas_id', 'user_id',
                'jenis_perubahan', 'data_sebelum', 'data_sesudah',
                'waktu_perubahan', 'created_at', 'updated_at',
            ],
        ];

        foreach ($struktur as $tabel => $kolom) {
            $this->assertTrue(
                Schema::hasColumns($tabel, $kolom),
                "Struktur tabel {$tabel} belum sesuai."
            );
        }
    }

    public function test_kode_barang_memiliki_index_unik(): void
    {
        $index = collect(Schema::getIndexes('barang'));

        $this->assertTrue(
            $index->contains(
                fn (array $item) =>
                    $item['unique']
                    && $item['columns'] === ['kode_barang']
            )
        );
    }

    public function test_index_filter_histori_tersedia(): void
    {
        $index = collect(Schema::getIndexes('histori_barang'));

        foreach ([
            ['waktu_aktivitas'],
            ['nomor_referensi'],
            ['barang_id', 'waktu_aktivitas'],
            ['divisi_id', 'waktu_aktivitas'],
            ['petugas_id', 'waktu_aktivitas'],
            ['jenis_aktivitas_id', 'waktu_aktivitas'],
        ] as $kolom) {
            $this->assertTrue(
                $index->contains(
                    fn (array $item) => $item['columns'] === $kolom
                ),
                'Index belum tersedia: '.implode(', ', $kolom)
            );
        }
    }

    public function test_konfigurasi_disk_foto_privat(): void
    {
        $disk = config('warehouse.media.disk');

        $this->assertSame('warehouse_private', $disk);
        $this->assertSame(
            storage_path('app/private/warehouse'),
            config("filesystems.disks.{$disk}.root")
        );
        $this->assertSame(
            'private',
            config("filesystems.disks.{$disk}.visibility")
        );
        $this->assertFalse(
            config("filesystems.disks.{$disk}.serve")
        );
        $this->assertTrue(
            config("filesystems.disks.{$disk}.throw")
        );
    }
}