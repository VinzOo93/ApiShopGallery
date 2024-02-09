<?php

namespace App\Dto;

final class CreateItemDto
{
    public int $quantity = 2;

    public string $image;

    public string $printFormat;

    public string $unitPrice = '500.00';

    public string $unitPreTaxPrice = '400.00';

    public string $preTaxPrice = '800.00';

    public string $taxPrice = '200.00';

    public int $cart;
}
