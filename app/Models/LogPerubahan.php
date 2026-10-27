<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogPerubahan extends Model
{
    protected $table = 'log_perubahan';

    // Isi log nantinya ditetapkan oleh mekanisme audit di server.
    protected $guarded = ['*'];

    protected function casts(): array
    {
        return [
            'data_sebelum' => 'array',
            'data_sesudah' => 'array',
            'waktu_perubahan' => 'datetime',
        ];
    }

    public function historiBarang(): BelongsTo
    {
        return $this->belongsTo(
            HistoriBarang::class,
            'histori_barang_id'
        )->withTrashed();
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'petugas_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}