<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING    = 'pending';
    case PAID       = 'paid';
    case UNPAID     = 'unpaid';
    case FAILED     = 'failed';
}
