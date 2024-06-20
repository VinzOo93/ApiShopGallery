<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Item;
use Symfony\Contracts\Cache\ItemInterface;

class UpdateItemInExistingCartProcessor extends BaseShopProcessor implements ProcessorInterface
{
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if (!$context['previous_data'] instanceof ItemInterface) {
            return;
        }


    }
}
