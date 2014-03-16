<?php

namespace Efficio\Tests\Dataset;

use PHPUnit_Framework_TestCase;
use Efficio\Tests\Dataset\Storage\Model\StorageTest;

class SessionStorageTest extends StorageTest
{
    public function setUp()
    {
        if (isset($_SESSION)) {
            $key = BasicSessionModel::sessionHash();
            unset($_SESSION[ $key ]);
        }

        $this->model = new BasicSessionModel;
    }
}
