<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Divisi extends Model
{
    protected $table = 'divisi';

    protected $fillable = ['nama_divisi'];

    public function petugas(): HasMany
    {
        return $this->hasMany(Petugas::class, 'divisi_id');
    }

    public function jenisAktivitas(): HasMany
    {
        return $this->hasMany(JenisAktivitas::class, 'divisi_id');
    }

    public function historiBarang(): HasMany
    {
        return $this->hasMany(HistoriBarang::class, 'divisi_id');
    }
}