<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateCartDto
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $subtotal = "800.00";

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $taxes = "160.00";

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $shipping = "5.00";

    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    public string $total = "965.00";

    /** @var CreateItemWithCartDto[] */
    public array $items = [];
}
