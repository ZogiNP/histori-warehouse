<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'barang';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
    ];

    public function historiBarang(): HasMany
    {
        return $this->hasMany(HistoriBarang::class, 'barang_id');
    }
}