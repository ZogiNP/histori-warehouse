<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuktiMedia extends Model
{
    protected $table = 'bukti_media';

    // Metadata file nantinya ditetapkan oleh proses upload di server.
    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['ukuran_file' => 'integer'];
    }

    public function historiBarang(): BelongsTo
    {
        return $this->belongsTo(
            HistoriBarang::class,
            'histori_barang_id'
        )->withTrashed();
    }
}