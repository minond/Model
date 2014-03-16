<?php

namespace Efficio\Tests\Dataset;

use Efficio\Dataset\Collection\DynamicCollection;
use PHPUnit_Framework_TestCase;

require_once './tests/mocks/models.php';

class DynamicCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A model class is required when creating a Collection object
     */
    public function testPassingNoClassToConstructorThrowsError()
    {
        new DynamicCollection;
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Model class 'invalid' not found
     */
    public function testPassingAnInvalidClassToConstructorThrowsError()
    {
        new DynamicCollection('invalid');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Efficio\Tests\Dataset\DynamicCollectionTest is not an instance of Efficio\Dataset\Model
     */
    public function testPassingANonModelClassToConstructorThrowsError()
    {
        new DynamicCollection(__class__);
    }

    public function testModelClassNamesCanBePassedToContructor()
    {
        $coll = new DynamicCollection('Efficio\Tests\Dataset\Post');
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testModelObjectsCanBePassedToContructor()
    {
        $coll = new DynamicCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testArrayOfModelObjectsCanBePassedToContructor()
    {
        $coll = new DynamicCollection([
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

        $coll = new DynamicCollection($models);
        $this->assertEquals(count($models), count($coll));
    }

    public function testIsCollectionOfMethod()
    {
        $coll = new DynamicCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testCollectionOfMethod()
    {
        $coll = new DynamicCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertEquals('Efficio\Tests\Dataset\Post', $coll->collectionOf());
    }

    public function testSettingOffsetWithValidModel()
    {
        $posts = new DynamicCollection('Efficio\Tests\Dataset\Post');
        $this->assertEquals(0, count($posts));
        $posts[] = new \Efficio\Tests\Dataset\Post;
        $this->assertEquals(1, count($posts));
        $posts[] = new \Efficio\Tests\Dataset\Post;
        $this->assertEquals(2, count($posts));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid model of class "Efficio\Tests\Dataset\UserProps". This is a collection of "Efficio\Tests\Dataset\Post" models
     */
    public function testSettingOffsetWithInvalidModelThrowsException()
    {
        $posts = new DynamicCollection('Efficio\Tests\Dataset\Post');
        $posts[] = new \Efficio\Tests\Dataset\UserProps;
    }

    public function testToStringMethodStringifiesAllModels()
    {
        $this->expectOutputString('efficio.dataset.collection.dynamiccollection[1]{ efficio.tests.dataset.post:1 }');
        $posts = new DynamicCollection('Efficio\Tests\Dataset\Post');
        $posts[] = \Efficio\Tests\Dataset\Post::create([ 'id' => 1 ]);
        echo $posts;
    }

    public function testJsonEncodingCollectionsEncodesModels()
    {
        $this->expectOutputString('[{"label":null,"created_date":null,"id":1}]');
        $posts = new DynamicCollection('Efficio\Tests\Dataset\Post');
        $posts[] = \Efficio\Tests\Dataset\Post::create([ 'id' => 1 ]);
        echo json_encode($posts);
    }
}
