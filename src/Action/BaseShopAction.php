<?php

namespace App\Action;

use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseShopAction
{
    public function __construct(
        public readonly EntityManagerInterface $entityManager,
        public readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        public string $content = '',
    ) {
    }

    protected function initValidationAction(mixed $classToValidate): void
    {
        foreach ($classToValidate as $object) {
            $this->validateObject($object);
        }
    }

    protected function validateObject(mixed $object): void
    {
        $this->checkErrors($this->validator->validate(
            $this->serializer->deserialize(
                $this->content,
                $object,
                'json'
            )
        ));
    }

    protected function checkErrors(ConstraintViolationListInterface $errors): void
    {
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                throw new UnprocessableEntityHttpException($error->getMessage());
            }
        }
    }


    /**
     * @param array<string, mixed>  $data
     * @param class-string          $className
     * @param array<string, string> $params
     */
    protected function getObjectDatabase(array $data, string $className, array $params): null|object
    {
        /** @var class-string $className * */
        $printFormatRepository = $this->entityManager->getRepository($className);

        return $printFormatRepository->findOneBy([
            $params['key'] => $data[
                $params['field']
            ],
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function getCurrentDateTimeEurope(): \DateTimeInterface
    {
        return new \DateTime('NOW', new \DateTimeZone('Europe/Paris'));
    }

    protected function createItemAction(Cart $cart, array $itemData): Item
    {
        $item = new Item();
        $printFormat = $this->getObjectDatabase($itemData,
            PrintFormat::class,
            [
                'key' => 'name',
                'field' => 'printFormat',
            ]
        );
        $item->setQuantity($itemData['quantity'])
            ->setImage($itemData['image'])
            ->setPrintFormat($printFormat)
            ->setUnitPrice($itemData['unitPrice'])
            ->setUnitPreTaxPrice($itemData['unitPreTaxPrice'])
            ->setPreTaxPrice($itemData['preTaxPrice'])
            ->setTaxPrice($itemData['taxPrice'])
            ->setCart($cart);

        return $item;
    }
}
