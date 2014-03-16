<?php

namespace Efficio\Tests\Dataset;

use PHPUnit_Framework_TestCase;
use Efficio\Tests\Dataset\Storage\Model\StorageTest;

class FileStorageTest extends StorageTest
{
    public function setUp()
    {
        $this->model = new BasicFileModel;

        BasicFileModel::setDirectory('./tests/flat/');
        $dir = BasicFileModel::initStorageDirectory();

        foreach (scandir($dir) as $file) {
            if (is_file($dir . $file)) {
                unlink($dir . $file);
            }
        }
    }

    public function testFileDirectoriesCanBeCreated()
    {
        $base = './tests/flat/anotherone';

        if (!is_dir($base)) {
            mkdir($base);
        }

        BasicFileModel::setDirectory($base);
        $dir = BasicFileModel::initStorageDirectory();
        $this->assertTrue(is_dir($dir));
        $this->assertTrue(rmdir($dir));
    }
}
