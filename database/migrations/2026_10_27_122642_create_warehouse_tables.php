<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_divisi', 100)->unique();
            $table->timestamps();
        });

        Schema::create('petugas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('divisi_id')
                ->constrained('divisi')
                ->restrictOnDelete();

            $table->string('nama_petugas', 150);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->index(['divisi_id', 'status_aktif']);
            $table->index('nama_petugas');
        });

        Schema::create('barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 100)->unique();
            $table->string('nama_barang', 200)->index();
            $table->string('satuan', 30);
            $table->timestamps();
        });

        Schema::create('jenis_aktivitas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('divisi_id')
                ->constrained('divisi')
                ->restrictOnDelete();

            $table->string('nama_aktivitas', 100);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->unique(['divisi_id', 'nama_aktivitas']);
        });

        Schema::create('histori_barang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('barang_id')
                ->constrained('barang')
                ->restrictOnDelete();

            $table->foreignId('divisi_id')
                ->constrained('divisi')
                ->restrictOnDelete();

            $table->foreignId('petugas_id')
                ->constrained('petugas')
                ->restrictOnDelete();

            $table->foreignId('jenis_aktivitas_id')
                ->constrained('jenis_aktivitas')
                ->restrictOnDelete();

            // Akun login yang digunakan, berbeda dari petugas operasional.
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->decimal('jumlah', 15, 3)->nullable();
            $table->string('kondisi_barang', 150)->nullable();
            $table->string('nomor_referensi', 150)->nullable()->index();
            $table->text('keterangan');
            $table->dateTime('waktu_aktivitas');
            $table->timestamps();
            $table->softDeletes();

            $table->index('waktu_aktivitas');
            $table->index(['barang_id', 'waktu_aktivitas']);
            $table->index(['divisi_id', 'waktu_aktivitas']);
            $table->index(['petugas_id', 'waktu_aktivitas']);
            $table->index(['jenis_aktivitas_id', 'waktu_aktivitas']);
        });

        // Jumlah boleh kosong, tetapi tidak boleh negatif.
        DB::statement(
            'ALTER TABLE histori_barang
             ADD CONSTRAINT histori_jumlah_nonnegatif
             CHECK (jumlah IS NULL OR jumlah >= 0)'
        );

        Schema::create('bukti_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('histori_barang_id')
                ->constrained('histori_barang')
                ->restrictOnDelete();

            $table->string('nama_file_asli');
            $table->string('nama_file_storage')->unique();
            $table->string('path_file', 500);
            $table->string('disk_storage', 50)->default('warehouse_private');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran_file');
            $table->string('jenis_media', 20)->default('foto');
            $table->timestamps();
        });

        Schema::create('log_perubahan', function (Blueprint $table) {
            $table->id();

            $table->foreignId('histori_barang_id')
                ->constrained('histori_barang')
                ->restrictOnDelete();

            $table->foreignId('petugas_id')
                ->nullable()
                ->constrained('petugas')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('jenis_perubahan', 50);
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->dateTime('waktu_perubahan');
            $table->timestamps();

            $table->index(
                ['histori_barang_id', 'waktu_perubahan'],
                'log_histori_waktu_index'
            );
        });
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Rollback warehouse dinonaktifkan untuk melindungi histori. '
            .'Gunakan migration koreksi yang ditinjau terlebih dahulu.'
        );
    }
};