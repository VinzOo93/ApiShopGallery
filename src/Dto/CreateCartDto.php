<?php

namespace App\Dto;

use App\Entity\Item;

final class CreateCartDto
{
    public string $subtotal;

    public string $taxes;

    public string $shipping;

    public string $total;

    /** @var Item[] */
    public array $item = [];
}
