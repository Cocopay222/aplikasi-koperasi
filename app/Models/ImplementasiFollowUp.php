<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplementasiFollowUp extends Model
{
    use HasFactory;

    protected $table = 'implementasi_followups';

    protected $touches = ['implementasi'];

    protected $fillable = [
        'implementasi_id',
        'tanggal_followup',
        'tanggal_followup_berikutnya',
        'target_tanggal_tindakan',
        'jenis_tindakan',
        'pic_tindakan',
        'status_tindakan',
        'hasil_komunikasi',
        'kendala_koperasi',
        'komitmen_koperasi',
        'tindakan_berikutnya',
        'created_by',
    ];

    protected $casts = [
        'tanggal_followup' => 'date',
        'tanggal_followup_berikutnya' => 'date',
        'target_tanggal_tindakan' => 'date',
    ];

    public function implementasi()
    {
        return $this->belongsTo(ImplementasiKoperasi::class, 'implementasi_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
