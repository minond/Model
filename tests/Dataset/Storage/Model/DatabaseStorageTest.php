<?php

namespace Efficio\Tests\Dataset;

use PDO;
use Efficio\Tests\Dataset\Storage\Model\StorageTest;

class DatabaseStorageTest extends StorageTest
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
        $this->conn->exec(file_get_contents('./tests/mocks/basic.sql'));
        Post::setConnection($this->conn);
        BasicDatabaseModel::setConnection($this->conn);
        $this->model = new BasicDatabaseModel;
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
}

