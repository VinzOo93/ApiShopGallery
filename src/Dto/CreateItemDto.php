<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateItemDto
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public int $quantity = 2;

    #[Assert\NotBlank]
    public string $image = 'a07ed184-c9aa-4729-aa25-70571f0fb11a';

    #[Assert\NotBlank]
    public string $printFormat = '30x20 cm';

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $unitPrice = '480.00';

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $unitPreTaxPrice = '400.00';

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $preTaxPrice = '800.00';

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $taxPrice = '960.00';

    public int $cart;
}
