<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petugas extends Model
{
    protected $table = 'petugas';

    protected $fillable = [
        'divisi_id',
        'nama_petugas',
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
        return $this->hasMany(HistoriBarang::class, 'petugas_id');
    }

    public function logPerubahan(): HasMany
    {
        return $this->hasMany(LogPerubahan::class, 'petugas_id');
    }
}