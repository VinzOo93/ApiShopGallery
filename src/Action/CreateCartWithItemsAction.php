<?php

namespace App\Action;

use _PHPStan_c997ea9ee\Nette\Schema\ValidationException;
use App\Dto\CreateCartDto;
use App\Dto\CreateItemWithCartDto;
use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use App\Repository\PrintFormatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CreateCartWithItemsAction.
 */
class CreateCartWithItemsAction
{
    private const OBJECT_DTO = [
        CreateCartDto::class,
        CreateItemWithCartDto::class,
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly SerializerInterface $serializer,
        private string $content = '',
    ) {
    }

    /**
     * __invoke.
     */
    public function __invoke(Request $request): Cart
    {
        $this->content = $request->getContent();

        foreach (self::OBJECT_DTO as $object) {
            $this->validateObject($object);
        }

        $this->entityManager->beginTransaction();
        try {
            $cartData = json_decode($this->content, true);

            $cart = new Cart();

            /** @var \DateTimeInterface $date */
            $date = new \DateTime('NOW', new \DateTimeZone('Europe/Paris'));

            $cart->setSubtotal($cartData['subtotal'])
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setTaxes($cartData['taxes'])
                ->setShipping($cartData['shipping'])
                ->setTotal($cartData['total'])
                ->setToken($cartData['token']);

            foreach ($cartData['items'] as $itemData) {
                $item = new Item();
                $printFormat = $this->getPrintFormat($itemData);

                $item->setQuantity($itemData['quantity'])
                    ->setImage($itemData['image'])
                    ->setPrintFormat($printFormat)
                    ->setUnitPrice($itemData['unitPrice'])
                    ->setUnitPreTaxPrice($itemData['unitPreTaxPrice'])
                    ->setPreTaxPrice($itemData['preTaxPrice'])
                    ->setTaxPrice($itemData['taxPrice'])
                    ->setCart($cart);

                $this->entityManager->persist($item);
                $cart->addItem($item);
            }
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $cart;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, "impossible to create Cart $e");
        }
    }

    /**
     * getPrintFormat.
     *
     * @param array<string, string> $itemData
     * @return ?PrintFormat
     */
    private function getPrintFormat(array $itemData): ?PrintFormat
    {
        /** @var PrintFormatRepository $printFormatRepository */
        $printFormatRepository = $this->entityManager->getRepository(PrintFormat::class);

        return $printFormatRepository->findOneBy(['name' => $itemData['printFormat']]);
    }

    /**
     * validateObject.
     */
    private function validateObject(mixed $object): void
    {
        $this->checkErrors($this->validator->validate(
            $this->serializer->deserialize(
                $this->content,
                $object,
                'json'
            )
        ));
    }

    /**
     * getErrors.
     */
    private function showErrors(ConstraintViolationListInterface $errors): void
    {
        $errorsArray = [];

        foreach ($errors as $error) {
            $errorsArray[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
            ];
        }

        new Response(json_encode(['errors' => $errorsArray]), Response::HTTP_BAD_REQUEST);
    }

    /**
     * getErrors.
     */
    private function checkErrors(ConstraintViolationListInterface $errors): void
    {
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[$error->getPropertyPath()][] = $error->getMessage();
        }
        throw new ValidationException(new Response(json_encode(['errors' => $errorsArray])));
    }
}
