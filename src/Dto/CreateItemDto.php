<?php

namespace App\Dto;

use App\Entity\Cart;

final class CreateItemDto
{
    public array $item = [
        'quantity' => 2,
        'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
        'printFormat' => '30x20 cm',
        'unitPrice' => '480.00',
        'unitPreTaxPrice' => '400.00',
        'preTaxPrice' => '800.00',
        'taxPrice' => '960.00',
    ];

    public Cart $cart;
}
