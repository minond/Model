<?php

namespace Efficio\Tests\Dataset;

use Efficio\Dataset\Model;
use Efficio\Dataset\Storage\Model\SessionStorage;

class BasicModel extends Model
{
    protected $first_name;
    protected $last_name;
}

class UserProps extends Model
{
    protected $first_name;
    protected $last_name;
    protected $age;
    protected $colors = [];
}

class UserFuncs extends Model
{
    public $set = false;
    public $get = false;
    protected $first_name;

    public function setFirstName($fn)
    {
        $this->set = true;
        $this->first_name = $fn;
    }

    public function getFirstName()
    {
        $this->get = true;
        return $this->first_name;
    }
}

class BasicSessionModel extends BasicModel
{
    use SessionStorage;
}
