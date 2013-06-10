<?php

namespace Efficio\Tests\Dataset;

use Efficio\Dataset\Model;
use PHPUnit_Framework_TestCase;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'models.php';

class ModelTest extends PHPUnit_Framework_TestCase
{
    public function testSettingAndGettingProperties()
    {
        $val = 'Marcos';
        $model = new UserProps;
        $model->first_name = $val;
        $this->assertEquals($val, $model->first_name);
    }

    public function testSettingAndGettingFunctions()
    {
        $val = 'Marcos';
        $model = new UserProps;
        $model->setFirstName($val);
        $this->assertEquals($val, $model->getFirstName());
    }

    public function testAddingToAnArray()
    {
        $model = new UserProps;
        $model->addColor('red');
        $model->addColor('blue');
        $this->assertEquals(['red', 'blue'], $model->colors);
    }

    public function testRemovingFromAnArray()
    {
        $model = new UserProps;
        $model->addColor('red');
        $model->addColor('blue');
        $this->assertEquals(['red', 'blue'], $model->colors);
        $model->removeColor('blue');
        $this->assertEquals(['red'], $model->colors);
        $model->removeColor('blue');
        $this->assertEquals(['red'], $model->colors);
        $model->removeColor('red');
        $this->assertEquals([], $model->colors);
    }

    public function testSettingAndGettingPropertiesUsingProperties()
    {
        $val = 'Marcos';
        $model = new UserFuncs;
        $model->first_name = $val;
        $this->assertEquals($val, $model->first_name);
        $this->assertTrue($model->get);
        $this->assertTrue($model->set);
    }

    public function testSettingAndGettingPropertiesUsingFunctions()
    {
        $val = 'Marcos';
        $model = new UserFuncs;
        $model->setFirstName($val);
        $this->assertEquals($val, $model->getFirstName());
        $this->assertTrue($model->get);
        $this->assertTrue($model->set);
    }

    /**
     * @expectedException Exception
     */
    public function testSettingInvalidPropertiesTriggersError()
    {
        $model = new UserFuncs;
        $model->invalid_property = 1;
    }

    /**
     * @expectedException Exception
     */
    public function testGettingInvalidPropertiesTriggersError()
    {
        $model = new UserFuncs;
        $a = $model->invalid_property;
    }

    /**
     * @expectedException Exception
     */
    public function testCallingInvalidSetterTriggersError()
    {
        $model = new UserFuncs;
        $model->setSetSetSet(true);
    }

    /**
     * @expectedException Exception
     */
    public function testCallingInvalidGetterTriggersError()
    {
        $model = new UserFuncs;
        $model->getGetGetGet(true);
    }

    public function testSetterMethodGenerator()
    {
        $this->assertEquals('setfirstname', Model::generateSetterMethodName('first_name'));
    }

    public function testGetterMethodGenerator()
    {
        $this->assertEquals('getfirstname', Model::generateGetterMethodName('first_name'));
    }

    public function testMethodToPropertyParserForRegularProperties()
    {
        $this->assertEquals('first_name', Model::parsePropertyNameFromMethod('getFirstName'));
        $this->assertEquals('first_name', Model::parsePropertyNameFromMethod('setFirstName'));
        $this->assertEquals('first_name', Model::parsePropertyNameFromMethod('FirstName'));
    }

    public function testMethodToPropertyParserForArrays()
    {
        $this->assertEquals('roles', Model::parsePropertyNameFromMethod('addRole', Model::P_ADD));
        $this->assertEquals('roles', Model::parsePropertyNameFromMethod('removeRole', Model::P_REMOVE));
        $this->assertEquals('roles', Model::parsePropertyNameFromMethod('setRoles'));
        $this->assertEquals('roles', Model::parsePropertyNameFromMethod('getRoles'));
    }

    public function testMethodTypeParser()
    {
        $this->assertEquals(Model::P_GET, Model::parsePropertyActionFromMethod('getRoles'));
        $this->assertEquals(Model::P_SET, Model::parsePropertyActionFromMethod('setRoles'));
        $this->assertEquals(Model::P_ADD, Model::parsePropertyActionFromMethod('addRole'));
        $this->assertEquals(Model::P_REMOVE, Model::parsePropertyActionFromMethod('removeRole'));
        $this->assertEquals(Model::F_FINDONEBY, Model::parsePropertyActionFromMethod('findOneByFirstName'));
        $this->assertEquals(Model::F_FINDBY, Model::parsePropertyActionFromMethod('findByFirstName'));
        $this->assertEquals(null, Model::parsePropertyActionFromMethod('invalidCall'));
    }

    public function testFindByFinderFunction()
    {
        $this->assertTrue(Model::isLikeFindByCall('findBy'));
        $this->assertTrue(Model::isLikeFindByCall('findById'));
        $this->assertTrue(Model::isLikeFindByCall('findOneById'));
        $this->assertFalse(Model::isLikeFindByCall('_findOneById'));
    }

    public function testPropertyGetSetFinderFunction()
    {
        $this->assertTrue(Model::isLikePropertyGetSet('getFirstName'));
        $this->assertTrue(Model::isLikePropertyGetSet('setFirstName'));
        $this->assertTrue(Model::isLikePropertyGetSet('addRole'));
        $this->assertTrue(Model::isLikePropertyGetSet('removeRole'));
        $this->assertFalse(Model::isLikePropertyGetSet('_removeRole'));
    }

    public function testModelHashStringDontOverlap()
    {
        $this->assertNotEquals(UserFuncs::hash(1), UserProps::hash(1));
    }

    public function testCreateStaticMethodSetsAllGiveProperties()
    {
        $model = UserProps::create([
            'first_name' => 'Marcos',
            'last_name' => 'Minond',
            'age' => 24,
        ]);

        $this->assertEquals('Marcos', $model->first_name);
        $this->assertEquals('Minond', $model->last_name);
        $this->assertEquals(24, $model->age);
    }

    /**
     * @expectedException Exception
     */
    public function testPassingInvalidPropertyToCreateMethodTriggersError()
    {
        $model = UserProps::create([
            'first_name' => 'Marcos',
            'last_name' => 'Minond',
            'age' => 24,
            'invalid' => 24,
        ]);
    }

    public function testStandardToStringIncludesClassAndModelsId()
    {
        $model = new UserProps;
        $model->setId('id');
        $expected = str_replace('\\', '.', strtolower(get_class($model))) .
            ':' . $model->getId();

        $this->assertEquals($expected, (string) $model);
    }

    public function testJsonEncodingModelsCanBeDecoded()
    {
        $model = new BasicModel;
        $model->first_name = 'FirstName';
        $model->last_name = 'LastName';
        $model->id = 'ID';
        $json = json_encode($model);
        $deco = json_decode($json, true);
        $this->assertEquals([
            'first_name' => 'FirstName',
            'last_name' => 'LastName',
            'id' => 'ID',
        ], $deco);
    }

    public function testCreatingStorageTraits()
    {
        Model::saveTo('\Efficio\Dataset\Storage\Model\SessionStorage', 'ValidStorage');
        $this->assertTrue(trait_exists('\Efficio\Dataset\Storage\Model\ValidStorage'));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage \Efficio\Dataset\Storage\Model\ErrorStorage has already been defined
     */
    public function testRecreatingStorageTraitsWithSameNameTriggersError()
    {
        Model::saveTo('\Efficio\Dataset\Storage\Model\SessionStorage', 'ErrorStorage');
        Model::saveTo('\Efficio\Dataset\Storage\Model\SessionStorage', 'ErrorStorage');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid storage trait: \Efficio\Dataset\Storage\Model\InvalidSessionStorage
     */
    public function testCreatingStorageTraitsUsingInvalidStorageDriversTriggersErrors()
    {
        Model::saveTo('\Efficio\Dataset\Storage\Model\InvalidSessionStorage');
    }
}
