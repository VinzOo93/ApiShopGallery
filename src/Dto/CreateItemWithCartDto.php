<?php

namespace App\Dto;

final class CreateItemWithCartDto
{
    public int $quantity = 2;

    public string $image = 'a07ed184-c9aa-4729-aa25-70571f0fb11a';

    public string $printFormat = '30x20 cm';

    public string $unitPrice = '480.00';

    public string $unitPreTaxPrice = '400.00';

    public string $preTaxPrice = '800.00';

    public string $taxPrice = '160.00';
}
