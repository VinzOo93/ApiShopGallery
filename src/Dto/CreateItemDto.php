<?php

namespace App\Dto;

use App\Entity\Cart;

final class CreateItemDto
{
    public array $item = [
        'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
        'printFormat' => '30x20 cm',
        'unitPrice' => '240.00',
        'unitPreTaxPrice' => '200.00',
    ];

    public Cart $cart;
}
