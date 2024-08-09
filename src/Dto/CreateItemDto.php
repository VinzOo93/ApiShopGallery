<?php

namespace App\Dto;

use App\Entity\Cart;
use App\Entity\PrintFormat;

final class CreateItemDto
{
    public ?string $image = 'a07ed184-c9aa-4729-aa25-70571f0fb11a';

    public ?PrintFormat $printFormat;

    public ?Cart $cart = null;

}
