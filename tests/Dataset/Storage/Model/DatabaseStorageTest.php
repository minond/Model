<?php

namespace Efficio\Tests\Dataset;

use PDO;
use PHPUnit_Framework_TestCase;

require_once './tests/mocks/models.php';

class DatabaseStorageTest extends PHPUnit_Framework_TestCase
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

    public function testConnectionsCanBeSet()
    {
        $conn = new PDO('sqlite::memory:');
        Post::setConnection($conn);
        $this->assertEquals($conn, Post::getConnection());
    }

    public function testDifferentConnectionsCanBeSetOnDifferentModels()
    {
        $conn1 = new PDO('sqlite::memory:');
        $conn2 = new PDO('sqlite::memory:');
        Post::setConnection($conn1);
        Comment::setConnection($conn2);
        $this->assertEquals($conn1, Post::getConnection());
        $this->assertEquals($conn2, Comment::getConnection());
    }

    public function testDifferentConnectionsCanBeSetOnParentChildClasses()
    {
        $conn1 = new PDO('sqlite::memory:');
        $conn2 = new PDO('sqlite::memory:');
        Post::setConnection($conn1);
        ImportantPost::setConnection($conn2);
        $this->assertEquals($conn1, Post::getConnection());
        $this->assertEquals($conn2, ImportantPost::getConnection());
    }

    public function testCreatingANewModelsReturnsTheirIds()
    {
        $post = new Post;
        $post->label = 'new post';
        $post->created_date = time();
        $id = $post->save();
        $this->assertEquals(1, (int) $id);
    }

    public function testSavingModelsWithAnIdRunAnUpdateStatement()
    {
        $post = new Post;
        $post->label = 'new post';
        $post->created_date = time();
        $post->save();
        $post->label = 'new post2';
        $id = $post->save();
        $this->assertEquals(1, (int) $id);
    }

    public function testModelsCanBeDeletedAndAreNotFoundOnSearchesAfterwards()
    {
        $post = new Post;
        $post->label = 'new post';
        $post->created_date = time();
        $id = $post->save();
        $this->assertTrue($post->delete());
        $this->assertFalse(Post::find($id));
    }

    public function testDeletingModelsThatHaventBeenSavedJustReturnTrue()
    {
        $post = new Post;
        $this->assertTrue($post->delete());
    }

    public function testModelsCanBeFoundByTheirId()
    {
        $post = new Post;
        $post->label = 'new post';
        $post->created_date = time();
        $id = $post->save();
        $this->assertTrue(Post::find($id) instanceof Post);
    }

    public function testModelsCanBeFoundByTheirFoundUsingProperties()
    {
        $label = uniqid();
        $post = new Post;
        $post->label = $label;
        $id = $post->save();
        $result = Post::findOneBy([ 'label' => $label ]);
        $this->assertTrue($result instanceof Post);
        $this->assertEquals($id, $result->id);
    }

    public function testMultipleModelsCanBeFoundByTheirFoundUsingProperties()
    {
        $label = uniqid();

        $post1 = new Post;
        $post1->label = $label;
        $id1 = $post1->save();

        $post2 = new Post;
        $post2->label = $label;
        $id2 = $post2->save();

        $results = Post::findBy([ 'label' => $label ]);
        $this->assertEquals(2, count($results));
        $this->assertTrue($results[0] instanceof Post);
        $this->assertTrue($results[1] instanceof Post);
        $this->assertEquals($id1, $results[0]->id);
        $this->assertEquals($id2, $results[1]->id);
    }

    public function testMultipleModelsCanBeFoundByTheirFoundUsingPropertiesAndAreSentToTheCallbackFunction()
    {
        $label = uniqid();
        $count = 0;
        $ids = [];
        $match = [];
        $returns = [ mt_rand(), mt_rand() ];

        $post1 = new Post;
        $post1->label = $label;
        $ids[] = $post1->save();

        $post2 = new Post;
        $post2->label = $label;
        $ids[] = $post2->save();

        $results = Post::findBy([ 'label' => $label ], function(Post $post) use(
            & $count,
            & $match,
            $returns,
            $ids
        ) {
            $match[] = (int) $ids[ $count ] === (int) $post->id;
            return $returns[ $count++ ];
        });

        $this->assertEquals([1, 2], $match);
        $this->assertEquals($returns, $results);
    }

    public function testModelsCanBeSavedUsingStaticCreateMethod()
    {
        $model = Post::create([ 'label' => 'Marcos' ], true);
        $this->assertNotNull($model->id);
    }

    public function testModelsAreNotSavedByDefaultWhenUsingTheStaticCreateMethod()
    {
        $model = Post::create([ 'label' => 'Marcos' ]);
        $this->assertNull($model->id);
    }

    public function testModelsAreNotSavedByDefaultWhenCallingTheUpdateMethod()
    {
        $model = Post::create([ 'label' => 'Marcos' ]);
        $model->update([ 'label' => 'NotMarcos' ]);
        $this->assertNull($model->id);
        $this->assertEquals('NotMarcos', $model->label);
    }

    public function testModelsCanBeSavedWhenCallingTheUpdateMethod()
    {
        $model = Post::create([ 'label' => 'Marcos' ]);
        $model->update([ 'label' => 'NotMarcos' ], true);
        $this->assertNotNull($model->id);
        $this->assertEquals('NotMarcos', $model->label);
    }
}
