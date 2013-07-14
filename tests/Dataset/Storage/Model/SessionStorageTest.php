<?php

namespace Efficio\Tests\Dataset;

use PHPUnit_Framework_TestCase;

require_once './tests/mocks/models.php';

class SessionStorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    public $model;

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

    public function testNewModelsGetAnId()
    {
        $this->assertNotNull($this->model->id);
    }

    public function testModelsCanBeSaved()
    {
        $this->assertTrue($this->model->save());
    }

    public function testModelsCanBeFoundById()
    {
        $first_name = 'Marcos';
        $this->model->first_name = $first_name;
        $this->model->save();
        $model = BasicSessionModel::find($this->model->id);
        $this->assertEquals($first_name, $model->first_name);
    }

    public function testModelsCanBeDeleted()
    {
        $id = $this->model->id;
        $this->model->save();
        $this->assertTrue($this->model->delete());
    }

    public function testModelsThatHaveBeenDeletedCanNotBeRetrieved()
    {
        $id = $this->model->id;
        $this->model->save();
        $this->model->delete();
        $this->assertNull(BasicSessionModel::find($id));
    }

    public function testFindByFunction()
    {
        $model1 = new BasicSessionModel;
        $model2 = new BasicSessionModel;
        $model3 = new BasicSessionModel;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->save();
        $model2->save();
        $model3->save();

        $models = BasicSessionModel::findBy([
            'last_name' => 'findbyfunctiontest',
        ]);

        $this->assertEquals(3, count($models));
    }

    public function testFindByFunctionTriggersCallBackPerResult()
    {
        $models = 0;

        $model1 = new BasicSessionModel;
        $model2 = new BasicSessionModel;
        $model3 = new BasicSessionModel;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->save();
        $model2->save();
        $model3->save();

        BasicSessionModel::findBy([
            'last_name' => 'findbyfunctiontest',
        ], function(BasicSessionModel $model) use(& $models) {
            $models++;
        });

        $this->assertEquals(3, $models);
    }

    public function testFindByFunctionTriggersCallBackPerResultAndReturnsAnArrayOrCallbackResponses()
    {
        $model1 = new BasicSessionModel;
        $model2 = new BasicSessionModel;
        $model3 = new BasicSessionModel;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->first_name = 1;
        $model2->first_name = 2;
        $model3->first_name = 3;
        $model1->save();
        $model2->save();
        $model3->save();

        $responses = BasicSessionModel::findBy([
            'last_name' => 'findbyfunctiontest',
        ], function(BasicSessionModel $model) {
            return $model->first_name;
        });

        sort($responses);
        $this->assertEquals([1, 2, 3], $responses);
    }

    public function testFindByFunctionReturnsEmptyArrayOnNoMatches()
    {
        $model1 = new BasicSessionModel;
        $model2 = new BasicSessionModel;
        $model3 = new BasicSessionModel;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->save();
        $model2->save();
        $model3->save();

        $models = BasicSessionModel::findBy([
            'last_name' => 'findbyfunctiontesttfdjskfldasjfdkls',
        ]);

        $this->assertEquals(0, count($models));
    }

    public function testFindOneByFunction()
    {
        $first_name = 'Marcos';
        $last_name = 'Minond';
        $id = $this->model->id;
        $this->model->first_name = $first_name;
        $this->model->last_name = $last_name;
        $this->model->save();

        $model = BasicSessionModel::findOneBy([
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);

        $this->assertEquals($id, $model->id);
    }

    public function testFindOneByFunctionWithNoResultsReturnsNull()
    {
        $first_name = 'Marcos';
        $last_name = 'Minond';
        $this->model->save();

        $model = BasicSessionModel::findOneBy([
            'first_name' => 'invalid' . uniqid(),
            'last_name' => 'invalid' . uniqid(),
        ]);

        $this->assertNull($model);
    }
}
