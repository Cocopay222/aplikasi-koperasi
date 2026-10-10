<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';
    protected $primaryKey = 'faq_id';

    protected $fillable = [
        'kategori_id',
        'pertanyaan',
        'jawaban',
        'visibility',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke MasterKategori
     */
    public function kategori()
    {
        return $this->belongsTo(MasterKategori::class, 'kategori_id', 'kategori_id');
    }

    /**
     * Scope: hanya FAQ yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: hanya FAQ public (visible untuk Pelapor & Support)
     */
    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    /**
     * Scope: hanya FAQ internal (visible untuk Support saja)
     */
    public function scopeInternal($query)
    {
        return $query->where('visibility', 'internal');
    }
}
