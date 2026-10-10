<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterKategori extends Model
{
    protected $table = 'master_kategoris';
    protected $primaryKey = 'kategori_id';

    protected $fillable = [
        'nama_kategori',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'kategori_id', 'kategori_id');
    }

    public function faqs()
    {
        return $this->hasMany(Faq::class, 'kategori_id', 'kategori_id');
    }
}
