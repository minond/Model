<?php

namespace Efficio\Tests\Dataset;

use PHPUnit_Framework_TestCase;
use Efficio\Tests\Dataset\Storage\Model\StorageTest;

require_once './tests/Dataset/Storage/Model/StorageTest.php';

class SessionStorageTest extends StorageTest
{
    public function setUp()
    {
        $this->model = new BasicSessionModel;
    }

    public function tearDown()
    {
        if (isset($_SESSION)) {
            $key = BasicSessionModel::sessionHash();
            unset($_SESSION[ $key ]);
        }
    }
}

