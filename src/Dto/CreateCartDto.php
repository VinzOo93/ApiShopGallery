<?php

namespace App\Dto;

final class CreateCartDto
{
    public string $subtotal = '800.00';

    public string $taxes = '160.00';

    public string $shipping = '5.00';

    public string $total = '965.00';

    /** @var array <string,mixed> */
    public array $items = [
        [
            'quantity' => 2,
            'image' => 'a07ed184-c9aa-4729-aa25-70571f0fb11a',
            'printFormat' => '30x20 cm',
            'unitPrice' => '480.00',
            'unitPreTaxPrice' => '400.00',
            'preTaxPrice' => '800.00',
            'taxPrice' => '960.00',
        ],
    ];
}
