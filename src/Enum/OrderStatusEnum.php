<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case PREPARATION = 'PREPARATION';
    case SENDING = 'SENDING';
    case DELIVERED = 'DELIVERED';
    case FINISHED = 'FINISHED';
    case CLAIM = 'CLAIM';
    case ABORTED = 'ABORTED';

}
