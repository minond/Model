<?php

namespace Efficio\Tests\Dataset;

use PDO;
use PHPUnit_Framework_TestCase;
use ReflectionMethod;
use Efficio\Dataset\Collection\JitCollection;
use Efficio\Tests\Dataset\FixedUsers;
use Efficio\Tests\Dataset\Post;

require_once './tests/mocks/models.php';
require_once './tests/mocks/collections.php';

class JitCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PDO
     */
    protected $conn;

    /**
     * create a new in memory PDO connection and create a posts table for the
     * Post test model
     */
    public function setUp()
    {
        $this->conn = new PDO('sqlite::memory:');
        $this->conn->exec(file_get_contents('./tests/mocks/posts.sql'));
        Post::setConnection($this->conn);
    }

    public function tearDown()
    {
        $this->conn = null;
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage A model class is required when creating a Collection object
     */
    public function testPassingNoClassToConstructorThrowsError()
    {
        new JitCollection;
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Model class 'invalid' not found
     */
    public function testPassingAnInvalidClassToConstructorThrowsError()
    {
        new JitCollection('invalid');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Efficio\Tests\Dataset\JitCollectionTest is not an instance of Efficio\Dataset\Model
     */
    public function testPassingANonModelClassToConstructorThrowsError()
    {
        new JitCollection(__class__);
    }

    public function testModelClassNamesCanBePassedToContructor()
    {
        $coll = new JitCollection('Efficio\Tests\Dataset\Post');
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testModelObjectsCanBePassedToContructor()
    {
        $coll = new JitCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testArrayOfModelObjectsCanBePassedToContructor()
    {
        $coll = new JitCollection([
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

        $coll = new JitCollection($models);
        $this->assertEquals(count($models), count($coll));
    }

    public function testIsCollectionOfMethod()
    {
        $coll = new JitCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertTrue($coll->isCollectionOf('Efficio\Tests\Dataset\Post'));
    }

    public function testCollectionOfMethod()
    {
        $coll = new JitCollection(new \Efficio\Tests\Dataset\Post);
        $this->assertEquals('Efficio\Tests\Dataset\Post', $coll->collectionOf());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid model of class "Efficio\Tests\Dataset\UserProps". This is a collection of "Efficio\Tests\Dataset\Post" models
     */
    public function testSettingOffsetWithInvalidModelThrowsException()
    {
        $posts = new JitCollection('Efficio\Tests\Dataset\Post');
        $posts[] = new \Efficio\Tests\Dataset\UserProps;
    }

    public function testAllowsStringsToBeSet()
    {
        $posts = new JitCollection('Efficio\Tests\Dataset\Post');
        $posts[] = 'new \Efficio\Tests\Dataset\UserProps';
    }

    public function testStrinfigyingJustReturnsIds()
    {
        $this->expectOutputString('efficio.dataset.collection.jitcollection[9]{ 1, 2, 3, 4, 5, 6, 7, 8, 9 }');
        $coll = new JitCollection([
            Post::create([ 'id' => 1 ]),
            Post::create([ 'id' => 2 ]),
            Post::create([ 'id' => 3 ]),
            Post::create([ 'id' => 4 ]),
            5,
            6,
            7,
            8,
            9,
        ]);

        echo $coll;
    }

    public function testConvertingToJsonJustReturnsIds()
    {
        $coll = new JitCollection([
            Post::create([ 'id' => 1 ]),
            Post::create([ 'id' => 2 ]),
            Post::create([ 'id' => 3 ]),
            Post::create([ 'id' => 4 ]),
            5,
            6,
            7,
            8,
            9,
        ]);

        $this->assertEquals('[1,2,3,4,5,6,7,8,9]', json_encode($coll));
    }

    public function testGettingAModelReturnsModelsEventWhenCollectionOnlyHasAnId()
    {
        $coll = new JitCollection('Efficio\Tests\Dataset\Post');
        $posts = [];
        $len = 10;

        for ($i = 0; $i < $len; $i++) {
            $post = new Post;
            $post->label = uniqid();
            $post->save();
            $posts[] = $post;
            $coll[] = $post->id;
        }

        for ($i = 0; $i < $len; $i++) {
            $post = $coll[ $i ];
            $this->assertTrue(is_object($post));
            $this->assertEquals($posts[ $i ]->label, $post->label);
        }
    }

    public function testPreloadingModelsOnlyPreloadsSelected()
    {
        $coll = new JitCollection('Efficio\Tests\Dataset\Post');
        $get = new ReflectionMethod('ArrayObject', 'offsetGet');
        $get = $get->getClosure($coll);
        $len = 20;
        $from = 3;
        $to = 13;

        for ($i = 0; $i < $len; $i++) {
            $post = new Post;
            $post->label = uniqid();
            $post->save();
            $coll[] = $post->id;
        }

        $coll->preLoad($from, $to);

        for ($i = 0; $i < $len; $i++) {
            $post = call_user_func($get, $i);

            if ($i >= $from && $i <= $to) {
                $this->assertTrue(is_object($post));
            } else {
                $this->assertTrue(is_string($post));
            }
        }
    }

    public function testModelIdsAreStored()
    {
        $coll = new JitCollection('Efficio\Tests\Dataset\Post');
        $get = new ReflectionMethod('ArrayObject', 'offsetGet');
        $get = $get->getClosure($coll);

        $post = new Post;
        $post->label = uniqid();
        $post->save();
        $post_id = $post->id;
        $coll[] = $post_id;

        $post = call_user_func($get, 0);
        $this->assertEquals($post_id, $post);
    }

    public function testRetrivedModelsAreSavedToModel()
    {
        $coll = new JitCollection('Efficio\Tests\Dataset\Post');
        $get = new ReflectionMethod('ArrayObject', 'offsetGet');
        $get = $get->getClosure($coll);

        $post = new Post;
        $post->label = uniqid();
        $post->save();
        $post_id = $post->id;
        $coll[] = $post_id;

        $post = $coll[ 0 ];
        $post = call_user_func($get, 0);
        $this->assertTrue(is_object($post));
        $this->assertEquals($post_id, $post->id);
    }
}
