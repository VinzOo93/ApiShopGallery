<?php

namespace App\Action;

use App\Dto\CreateCartDto;
use App\Dto\CreateItemWithCartDto;
use App\Entity\Cart;
use App\Entity\Item;
use App\Entity\PrintFormat;
use App\Repository\PrintFormatRepository;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * CreateCartWithItemsAction
 */
class CreateCartWithItemsAction
{
    private const OBJECT_DTO = [
        CreateCartDto::class,
        CreateItemWithCartDto::class
    ];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private string $content = '',
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->content = $content;
    }

    /**
     * __invoke
     *
     * @param  Request $request
     * @return Cart
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

            /** @var Cart */
            $cart = new Cart();

            /** @var DateTimeInterface */
            $date = new DateTime('NOW', new DateTimeZone('Europe/Paris'));

            $cart->setSubtotal($cartData['subtotal'])
                ->setCreatedAt($date)
                ->setUpdatedAt($date)
                ->setTaxes($cartData['taxes'])
                ->setShipping($cartData['shipping'])
                ->setTotal($cartData['total']);

            $this->entityManager->persist($cart);
            $this->entityManager->flush();

            foreach ($cartData['items'] as $itemData) {
                /** @var Item */
                $item = new Item();

                /** @var PrintFormat */
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
            }
            $this->entityManager->flush();
            $this->entityManager->commit();

            return $cart;
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw new HttpException(
                Response::HTTP_UNPROCESSABLE_ENTITY,
                "impossible to create Cart $e"
            );
        }
    }

    /**
     * getPrintFormat
     *
     * @param  array $itemData
     * @return void
     */
    private function getPrintFormat(array $itemData)
    {
        /** @var PrintFormatRepository */
        $printFormatRepository = $this->entityManager->getRepository(PrintFormat::class);

        return $printFormatRepository->findOneBy(['name' => $itemData['printFormat']]);
    }

    /**
     * validateObject
     *
     * @return void
     */
    private function validateObject(mixed $object)
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
     * getErrors
     *
     * @param  ConstraintViolationListInterface $errors
     * @return Response
     */
    private function showErrors(ConstraintViolationListInterface $errors): Response
    {
        $errorsArray = [];

        foreach ($errors as $error) {
            $errorsArray[] = [
                'property' => $error->getPropertyPath(),
                'message' => $error->getMessage()
            ];
        }
        return new Response(json_encode($errorsArray), 400);
    }

    /**
     * getErrors
     *
     * @param  ConstraintViolationListInterface $errors
     * @return mixed
     */
    private function checkErrors(ConstraintViolationListInterface $errors)
    {
        return (count($errors) > 0) ? $this->showErrors($errors) : false;
    }
}
