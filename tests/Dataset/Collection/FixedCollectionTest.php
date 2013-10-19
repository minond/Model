<?php

namespace Efficio\Tests\Dataset;

use Efficio\Dataset\Collection\FixedCollection;
use Efficio\Tests\Dataset\FixedUsers;
use PHPUnit_Framework_TestCase;

require_once './tests/mocks/models.php';
require_once './tests/mocks/collections.php';

class FixedCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A model class is required when creating a Collection object
     */
    public function testPassingNoClassToConstructorThrowsError()
    {
        new FixedCollection;
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Model class 'invalid' not found
     */
    public function testPassingAnInvalidClassToConstructorThrowsError()
    {
        new FixedCollection('invalid');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Efficio\Tests\Dataset\FixedCollectionTest is not an instance of Efficio\Dataset\Model
     */
    public function testPassingANonModelClassToConstructorThrowsError()
    {
        new FixedCollection(__class__);
    }

    public function testModelClassNamesCanBePassedToContructor()
    {
        $coll = new FixedCollection('Efficio\Tests\Dataset\Post');
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testModelObjectsCanBePassedToContructor()
    {
        $coll = new FixedCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testArrayOfModelObjectsCanBePassedToContructor()
    {
        $coll = new FixedCollection([
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
        ]);
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testPassingArrayOfModelsToConstructorSavesModesToCollection()
    {
        $models = [
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
            new \Efficio\Tests\Dataset\Post,
        ];

        $coll = new FixedCollection($models);
        $this->assertEquals(count($models), count($coll));
    }

    public function testIsCollectionOfMethod()
    {
        $coll = new FixedCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testCollectionOfMethod()
    {
        $coll = new FixedCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertEquals('Efficio\Tests\Dataset\Post', $coll->collectionOf());
    }

    public function testArraySizeCanBePassedToConstructor()
    {
        $users = new FixedUsers(100);
        $this->assertEquals(100, count($users));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid model of class "Efficio\Tests\Dataset\UserProps". This is a collection of "Efficio\Tests\Dataset\Post" models
     */
    public function testSettingOffsetWithInvalidModelThrowsException()
    {
        $posts = new FixedCollection('Efficio\Tests\Dataset\Post');
        $posts[] = new \Efficio\Tests\Dataset\UserProps;
    }

    public function testToStringMethodStringifiesAllModels()
    {
        $this->expectOutputString('efficio.tests.dataset.fixedusers[100]{ , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , , ,  }');
        $users = new FixedUsers(100);
        echo $users;
    }
}

