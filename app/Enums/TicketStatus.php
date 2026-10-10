<?php

namespace App\Enums;

enum TicketStatus: string
{
    case OPEN = 'Open';
    case PENDING = 'Pending';
    case PROSES = 'Proses';
    case REVIEW = 'In Review';
    case WAITING = 'Waiting';
    case DONE = 'Done';
}
