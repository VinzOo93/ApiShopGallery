<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AcmeAssert;

final class CreateCartDto
{

    public string $subtotal = '800.00';

    public string $taxes = '160.00';


    public string $shipping = '5.00';

    public string $total = '965.00';

    /** @var CreateItemDto[] */
    public array $items = [];
}
