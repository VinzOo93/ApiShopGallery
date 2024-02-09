<?php

namespace App\Dto;

final class CreateCartDto
{
    public string $subtotal = "800.00";

    public string $taxes = "200.00";

    public string $shipping = "5.00";

    public string $total = "1005.00";

    /** @var CreateItemWithCartDto[] */
    public array $items = [];
}
