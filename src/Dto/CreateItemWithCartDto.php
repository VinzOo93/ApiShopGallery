<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateItemWithCartDto
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public int $quantity = 2;

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    public string $image = 'a07ed184-c9aa-4729-aa25-70571f0fb11a';

    #[Assert\Choice(
        choices: ['30x20 cm', '60x40 cm', '80x65 cm'],
        message: "Veuillez respecter strictement ces valeurs '30x20 cm', '60x40 cm', '80x65 cm.'"
    )]
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
}
