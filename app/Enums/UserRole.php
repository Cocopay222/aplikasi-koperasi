<?php

namespace App\Enums;

enum UserRole: string
{
    case PELAPOR = 'Pelapor';
    case SUPPORT = 'Support';
    case SUPERADMIN = 'Super Admin';
}
