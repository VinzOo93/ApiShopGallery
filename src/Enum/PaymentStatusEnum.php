<?php

namespace App\Enum;

enum PaymentStatusEnum: string
{
    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case EXPIRED = 'EXPIRED';
    case REFUSED = 'REFUSED';

}
