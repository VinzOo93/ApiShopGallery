<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use App\Dto\CreateCartDto;
use ApiPlatform\State\ProcessorInterface;

class CartRepresentationProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): CreateCartDto
    {
        return new CreateCartDto(
            $data->getSubtotal(),
            $data->getTaxes(),
            $data->getShipping(),
            $data->getTotal(),
            $data->getItem()
        );
    }
}
