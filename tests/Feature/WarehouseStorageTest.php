<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class WarehouseStorageTest extends TestCase
{
    public function test_file_dapat_ditulis_dan_dibaca_di_storage_privat(): void
    {
        $this->assertTrue(app()->environment('testing'));

        $this->assertSame(
            storage_path('app/private/warehouse'),
            config('filesystems.disks.warehouse_private.root')
        );

        $disk = Storage::disk('warehouse_private');

        $path = 'pengujian-storage/'.Str::uuid().'.txt';
        $isi = 'Pemeriksaan storage Histori Warehouse.';

        $this->assertTrue($disk->put($path, $isi));
        $this->assertTrue($disk->exists($path));
        $this->assertSame($isi, $disk->get($path));
        $this->assertFileExists($disk->path($path));
    }
}