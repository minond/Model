<?php

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

    public function testSettingAndGettingPropertiesUsingFunctions()
    {
        $val = 'Marcos';
        $model = new UserFuncs;
        $model->setFirstName($val);
        $this->assertEquals($val, $model->getFirstName());
        $this->assertTrue($model->get);
        $this->assertTrue($model->set);
    }
}
