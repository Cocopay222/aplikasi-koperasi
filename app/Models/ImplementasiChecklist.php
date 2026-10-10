<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplementasiChecklist extends Model
{
    use HasFactory;

    protected $table = 'implementasi_checklists';

    protected $touches = ['implementasi'];

    protected $fillable = [
        'implementasi_id',
        'nama_item',
        'kategori',
        'status',
        'catatan',
    ];

    public function implementasi()
    {
        return $this->belongsTo(ImplementasiKoperasi::class, 'implementasi_id');
    }
}
