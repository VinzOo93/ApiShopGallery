<?php

namespace App\Dto;

final class CreateCartDto
{
    public string $subtotal;

    public string $taxes;

    public string $shipping;

    public string $total;

    /** @var CreateItemWithCartDto[] */
    public array $items = [];
}
