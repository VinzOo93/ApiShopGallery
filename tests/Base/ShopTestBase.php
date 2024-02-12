<?php

namespace App\Tests\Base;

class ShopTestBase extends ApiTestBase
{

    protected function initShopTest()
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }
}
