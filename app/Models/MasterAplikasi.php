<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAplikasi extends Model
{
    protected $table = 'master_aplikasis';
    protected $primaryKey = 'aplikasi_id';

    protected $fillable = [
        'nama_aplikasi',
        'deskripsi',
        'ebook',
        'link',
        'username',
        'password',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'ebook' => 'array',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'aplikasi_id', 'aplikasi_id');
    }

    public function instansis()
    {
        return $this->belongsToMany(Instansi::class, 'instansi_aplikasi', 'aplikasi_id', 'instansi_id');
    }
}
