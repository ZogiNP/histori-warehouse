<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoriBarang extends Model
{
    use SoftDeletes;

    protected $table = 'histori_barang';

    protected $fillable = [
        'barang_id',
        'divisi_id',
        'petugas_id',
        'jenis_aktivitas_id',
        'jumlah',
        'kondisi_barang',
        'nomor_referensi',
        'keterangan',
        'waktu_aktivitas',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:3',
            'waktu_aktivitas' => 'datetime',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function jenisAktivitas(): BelongsTo
    {
        return $this->belongsTo(
            JenisAktivitas::class,
            'jenis_aktivitas_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buktiMedia(): HasMany
    {
        return $this->hasMany(BuktiMedia::class, 'histori_barang_id');
    }

    public function logPerubahan(): HasMany
    {
        return $this->hasMany(LogPerubahan::class, 'histori_barang_id');
    }
}