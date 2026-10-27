<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisAktivitas extends Model
{
    protected $table = 'jenis_aktivitas';

    protected $fillable = [
        'divisi_id',
        'nama_aktivitas',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return ['status_aktif' => 'boolean'];
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function historiBarang(): HasMany
    {
        return $this->hasMany(HistoriBarang::class, 'jenis_aktivitas_id');
    }
}