<?php

namespace App\DataFixtures\Payment;

use App\Enum\PaymentStatusEnum;
use App\Enum\PaymentTypeEnum;
use App\Factory\PaymentFactory;
use App\Repository\PaymentRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentFixtures extends Fixture
{

    public function __construct()
    {
        $this->referenceRepository = PaymentRepository::class;
    }

    public function load(ObjectManager $manager): void
    {
        PaymentFactory::findOrCreate([
            'status' => PaymentStatusEnum::PENDING,
            'type' => PaymentTypeEnum::PAYPAL,
        ]);
        $manager->flush();
    }



}
