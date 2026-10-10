<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $primaryKey = 'ticket_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ticket_id',
        'pelapor_id',
        'aplikasi_id',
        'kategori_id',
        'pic_support_id',
        'permasalahan',
        'lampiran',
        'lampiran_support',
        'penyelesaian',
        'pencegahan',
        'status',
        'link_ticket',
        'template_laporan',
        'surat_balasan',
        'is_faq',
        'tanggal_input',
        'tanggal_penyelesaian',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
        'is_faq' => 'boolean',
        'tanggal_input' => 'datetime',
        'tanggal_penyelesaian' => 'datetime',
        'lampiran' => 'array',
        'lampiran_support' => 'array',
    ];

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pelapor_id', 'user_id');
    }

    public function aplikasi()
    {
        return $this->belongsTo(MasterAplikasi::class, 'aplikasi_id', 'aplikasi_id');
    }

    public function kategori()
    {
        return $this->belongsTo(MasterKategori::class, 'kategori_id', 'kategori_id');
    }

    public function picSupport()
    {
        return $this->belongsTo(User::class, 'pic_support_id', 'user_id');
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class, 'ticket_id', 'ticket_id')->orderBy('created_at', 'desc');
    }
    
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->ticket_id)) {
                $datePrefix = 'TKT-' . date('ymd');
                
                $lastTicket = self::where('ticket_id', 'like', $datePrefix . '%')
                                  ->orderBy('ticket_id', 'desc')
                                  ->first();
                
                if ($lastTicket) {
                    $lastNumber = (int) substr($lastTicket->ticket_id, 10);
                    $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $newNumber = '0001';
                }
                
                $model->ticket_id = $datePrefix . $newNumber;
            }
        });
    }
}
