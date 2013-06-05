<?php

use dataset\Model;

class UserProps extends Model
{
    protected $first_name;
}

class UserFuncs extends UserProps
{
    public $set = false;
    public $get = false;

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
