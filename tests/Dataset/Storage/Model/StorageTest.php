<?php

namespace Efficio\Tests\Dataset\Storage\Model;

use PHPUnit_Framework_TestCase;

abstract class StorageTest extends PHPUnit_Framework_TestCase
{
    protected $model;

    /**
     * shoudl set $model
     */
    public function setUp()
    {
        throw new \Exception('I need a Model');
    }

    final public function testModelsDontStartWithAnId()
    {
        $this->assertNull($this->model->id);
    }

    final public function testModelsCanBeSaved()
    {
        $this->assertNotNull($this->model->save());
    }

    final public function testModelsGetAnIdAfterBeignSaved()
    {
        $this->model->save();
        $this->assertNotNull($this->model->id);
    }

    final public function testModelsCanBeDeleted()
    {
        $this->model->save();
        $this->assertTrue($this->model->delete());
    }

    final public function testStartOutWithNoId()
    {
        $model = $this->model;
        $model = new $model;
        $this->assertNull($model->id);
    }

    final public function testModelsGetAnIdWhenSaved()
    {
        $model = $this->model;
        $model = new $model;
        $model->save();
        $this->assertNotNull($model->id);
    }

    final public function testDeletingModelsThatHaventBeenSavedJustReturnTrue()
    {
        $model = $this->model;
        $model = new $model;
        $this->assertTrue($model->delete());
    }

    final public function testIdDoesNotChangeUpdateUpdate()
    {
        $model = $this->model;
        $model = new $model;
        $model->first_name = uniqid();
        $model->save();
        $id = $model->id;
        $model->first_name = uniqid();
        $model->save();
        $this->assertEquals($id, $model->id);
    }

    final public function testModelsThatHaveBeenDeletedCanNotBeRetrieved()
    {
        $model = $this->model;
        $this->model->save();
        $id = $this->model->id;
        $this->assertTrue($this->model->delete());
        $this->assertNull($model::find($id));
    }

    final public function testModelsCanBeFoundById()
    {
        $model = $this->model;
        $first_name = 'Marcos';
        $this->model->first_name = $first_name;
        $this->model->save();
        $model = $model::find($this->model->id);
        $this->assertEquals($first_name, $model->first_name);
    }

    final public function testAllFunctionReturnsCallbackReturns()
    {
        $model = $this->model;

        $model1 = new $model;
        $model2 = new $model;
        $model3 = new $model;
        $model1->save();
        $model2->save();
        $model3->save();

        $ids = $model::all(function ($model) {
            return $model->id;
        });

        $this->assertEquals(3, count($ids));
        $this->assertEquals([$model1->id, $model2->id, $model3->id], $ids);
    }

    final public function testAllFunctionReturnsAllModelsWhenNoCallbackIsPassed()
    {
        $model = $this->model;

        $model1 = new $model;
        $model2 = new $model;
        $model3 = new $model;
        $model1->save();
        $model2->save();
        $model3->save();

        $models = $model::all();
        $this->assertEquals(3, count($models));
        $this->assertEquals($model1->id, $models[0]->id);
        $this->assertEquals($model2->id, $models[1]->id);
        $this->assertEquals($model3->id, $models[2]->id);
    }

    final public function testFindOneByFunctionWithNoResultsReturnsNull()
    {
        $model = $this->model;
        $first_name = 'Marcos';
        $last_name = 'Minond';
        $this->model->save();

        $model = $model::findOneBy([
            'first_name' => 'invalid' . uniqid(),
            'last_name' => 'invalid' . uniqid(),
        ]);

        $this->assertNull($model);
    }

    final public function testFindByFunction()
    {
        $model = $this->model;
        $model1 = new $model;
        $model2 = new $model;
        $model3 = new $model;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->save();
        $model2->save();
        $model3->save();

        $models = $model::findBy([
            'last_name' => 'findbyfunctiontest',
        ]);

        $this->assertEquals(3, count($models));
    }

    final public function testFindByFunctionTriggersCallBackPerResult()
    {
        $models = 0;
        $model = $this->model;

        $model1 = new $model;
        $model2 = new $model;
        $model3 = new $model;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->save();
        $model2->save();
        $model3->save();

        $model::findBy([
            'last_name' => 'findbyfunctiontest',
        ], function ($model) use(& $models) {
            $models++;
        });

        $this->assertEquals(3, $models);
    }

    final public function testFindByFunctionTriggersCallBackPerResultAndReturnsAnArrayOrCallbackResponses()
    {
        $model = $this->model;
        $model1 = new $model;
        $model2 = new $model;
        $model3 = new $model;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->first_name = 1;
        $model2->first_name = 2;
        $model3->first_name = 3;
        $model1->save();
        $model2->save();
        $model3->save();

        $responses = $model::findBy([
            'last_name' => 'findbyfunctiontest',
        ], function ($model) {
            return $model->first_name;
        });

        sort($responses);
        $this->assertEquals([1, 2, 3], $responses);
    }

    final public function testFindByFunctionReturnsEmptyArrayOnNoMatches()
    {
        $model = $this->model;
        $model1 = new $model;
        $model2 = new $model;
        $model3 = new $model;
        $model1->last_name = 'findbyfunctiontest';
        $model2->last_name = 'findbyfunctiontest';
        $model3->last_name = 'findbyfunctiontest';
        $model1->save();
        $model2->save();
        $model3->save();

        $models = $model::findBy([
            'last_name' => 'findbyfunctiontesttfdjskfldasjfdkls',
        ]);

        $this->assertEquals(0, count($models));
    }

    final public function testFindOneByFunction()
    {
        $model = $this->model;
        $first_name = 'Marcos';
        $last_name = 'Minond';
        $this->model->first_name = $first_name;
        $this->model->last_name = $last_name;
        $this->model->save();
        $id = $this->model->id;

        $model = $model::findOneBy([
            'first_name' => $first_name,
            'last_name' => $last_name,
        ]);

        $this->assertEquals($id, $model->id);
    }

    final public function testModelsAreNotSavedByDefaultWhenCallingTheUpdateMethod()
    {
        $model = $this->model;
        $model = $model::create([ 'first_name' => 'Marcos' ]);
        $model->update([ 'first_name' => 'NotMarcos' ]);
        $this->assertNull($model->id);
        $this->assertEquals('NotMarcos', $model->first_name);
    }

    final public function testModelsCanBeSavedWhenCallingTheUpdateMethod()
    {
        $model = $this->model;
        $model = $model::create([ 'first_name' => 'Marcos' ]);
        $model->update([ 'first_name' => 'NotMarcos' ], true);
        $this->assertNotNull($model->id);
        $this->assertEquals('NotMarcos', $model->first_name);
    }

    final public function testSavingModelsThatHaventBeenUpdated()
    {
        $model = $this->model;
        $model = $model::create([ 'first_name' => 'Marcos' ]);
        $model->save();
        $model->save();
    }

    final public function testModelsCanBeSavedUsingStaticCreateMethod()
    {
        $model = $this->model;
        $model = $model::create([ 'first_name' => 'Marcos' ], true);
        $this->assertNotNull($model->id);
    }

    final public function testModelsAreNotSavedByDefaultWhenUsingTheStaticCreateMethod()
    {
        $model = $this->model;
        $model = $model::create([ 'first_name' => 'Marcos' ]);
        $this->assertNull($model->id);
    }
}
