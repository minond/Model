<?php

namespace dataset;

/**
 * basic model with persistence methods
 */
class Model
{
    /**
     * property getter/setter flags
     */
    const P_GET = 'get';
    const P_SET = 'set';

    /**
     * model's unique identifier
     * @var mixed
     */
    protected $id;

    /**
     * setFirstName('strng') = first_name = 'string'
     * @param string $method
     * @param array $args
     * @throws Exception
     * @return mixed
     */
    final public function __call($method, $args)
    {
        $error = true;
        $ret = null;

        if (self::isLikePropertyGetSet($method)) {
            $prop = self::parsePropertyNameFromMethod($method);
            $type = self::parsePropertyActionFromMethod($method);

            if (property_exists($this, $prop)) {
                switch ($type) {
                    case self::P_SET:
                        list($this->{ $prop }) = $args;
                        $ret = $this->{ $prop };
                        $error = false;
                        break;

                    case self::P_GET:
                        $ret = $this->{ $prop };
                        $error = false;
                        break;
                }
            }
        }

        if ($error) {
            throw new \Exception(sprintf(
                'Invalid method %s called on class %s',
                $method, get_called_class()));
        }

        return $ret;
    }

    /**
     * first_name = 'string' = setFirstName('string')
     * @param string $prop
     * @param mixed $val
     * @throws Exception
     * @return mixed
     */
    final public function __set($prop, $val)
    {
        if (property_exists($this, $prop)) {
            $setter = self::generateSetterMethodName($prop);

            if (method_exists($this, $setter)) {
                $this->{ $setter }($val);
            } else {
                $this->{ $prop } = $val;
            }
        } else {
            throw new \Exception(sprintf(
                'Cannot set invalid property %s on class %s',
                $prop, get_called_class()));
        }

        return $val;
    }

    /**
     * $? = first_name = $? = getFirstName()
     * @param string $prop
     * @param mixed $val
     * @throws Exception
     * @return mixed
     */
    final public function __get($prop)
    {
        $ret = null;

        if (property_exists($this, $prop)) {
            $getter = self::generateGetterMethodName($prop);

            if (method_exists($this, $getter)) {
                $ret = $this->{ $getter }();
            } else {
                $ret = $this->{ $prop };
            }
        } else {
            throw new \Exception(sprintf(
                'Cannot get invalid property %s on class %s',
                $prop, get_called_class()));
        }

        return $ret;
    }

    /**
     * saves a model. returns save success
     * @throws Exception
     * @return boolean
     */
    public function save()
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * deletes a model. returns delete success
     * @return boolean
     */
    public function delete()
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * find a model using a unique identifier
     * @param mixed $id
     * @return Model
     */
    public static function find($id)
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * find models using a set of criteria
     * @param array $criteria
     * @return Model[]
     */
    public static function findBy(array $criteria)
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * @param array $data
     * @return Model
     */
    public static function create(array $data)
    {
        $class = get_called_class();
        $model = new $class;

        foreach ($data as $prop => $val) {
            $model->{ $prop } = $val;
        }

        return $model;
    }

    /**
     * returns a model's hash
     * @param mixed $id
     * @return string
     */
    public static function hash($id)
    {
        return get_called_class() . '_' . $id;
    }

    /**
     * @param string $method
     * @return boolean
     */
    final protected static function isLikePropertyGetSet($method)
    {
        $method = strtolower($method);
        return strpos($method, self::P_GET) === 0 ||
            strpos($method, self::P_SET) === 0;
    }

    /**
     * @param string $method
     * @return boolean
     */
    final protected static function parsePropertyActionFromMethod($method)
    {
        $type = null;

        if (strpos($method, self::P_SET) === 0) {
            $type = self::P_SET;
        } else if (strpos($method, self::P_GET) === 0) {
            $type = self::P_GET;
        }

        return $type;
    }

    /**
     * first_name = setFirstName
     * @param string $prop
     * @return string
     */
    final protected static function generateSetterMethodName($prop)
    {
        return self::P_SET . str_replace('_', '', $prop);
    }

    /**
     * first_name = getFirstName
     * @param string $prop
     * @return string
     */
    final protected static function generateGetterMethodName($prop)
    {
        return self::P_GET . str_replace('_', '', $prop);
    }

    /**
     * @param string $method
     * @return boolean
     */
    final protected static function parsePropertyNameFromMethod($method)
    {
        return strtolower(preg_replace(
            ['/^get|^set/', '/(\w)([A-Z])/'],
            ['', '$1_$2'], $method));
    }

    /**
     * declares the base Storage trait
     * @param string $storage
     * @throws \Exception
     */
    final public static function saveto($storage)
    {
        $ns = __NAMESPACE__;

        if (trait_exists("{$ns}\Storage")) {
            throw new \Exception('Storage has already been defined');
        } else if (!trait_exists($storage)) {
            throw new \Exception("Invalid storage trait: {$storage}");
        }

        eval("namespace {$ns}; trait Storage { use {$storage}; }");
    }
}
