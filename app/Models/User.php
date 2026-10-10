<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'posisi',
        'locale',
        'instansi_id',
        'is_verified',
        'avatar',
        'nik',
        'whatsapp',
        'spesialisasi',
        'two_factor',
        'otp_method',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'role' => UserRole::class,
        'is_verified' => 'boolean',
        'two_factor' => 'boolean',
    ];

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id', 'instansi_id');
    }

    public function ticketsReported()
    {
        return $this->hasMany(Ticket::class, 'pelapor_id', 'user_id');
    }

    public function ticketsHandled()
    {
        return $this->hasMany(Ticket::class, 'pic_support_id', 'user_id');
    }
}
