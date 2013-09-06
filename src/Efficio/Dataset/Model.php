<?php

namespace Efficio\Dataset;

use Efficio\Utilitatis\Word;

/**
 * basic model with persistence methods
 */
class Model implements \JsonSerializable
{
    /**
     * property getter/setter/add/remove flags
     */
    const P_GET = 'get';
    const P_SET = 'set';
    const P_ADD = 'add';
    const P_REMOVE = 'remove';

    /**
     * custom find functions
     */
    const F_FINDBY = 'findBy';
    const F_FINDONEBY = 'findOneBy';

    /**
     * default primary key's label
     */
    const DEFAULT_PRIMARY_KEY = 'id';

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
    public function __call($method, $args)
    {
        $error = true;
        $ret = null;

        if (self::isLikePropertyGetSet($method)) {
            $type = self::parsePropertyActionFromMethod($method);
            $prop = self::parsePropertyNameFromMethod($method, $type);

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

                    case self::P_ADD:
                        if (is_array($this->{ $prop })) {
                            array_push($this->{ $prop }, $args[0]);
                            $ret = count($this->{ $prop });
                            $error = false;
                        }
                        break;

                    case self::P_REMOVE:
                        if (is_array($this->{ $prop })) {
                            $copy = [];
                            $vrem = $args[0];

                            foreach ($this->{ $prop } as $index => & $value) {
                                if ($value !== $vrem) {
                                    $copy[] = $value;
                                }

                                unset($value);
                            }

                            $this->{ $prop } = $copy;
                            $ret = count($this->{ $prop });
                            $error = false;
                            unset($copy);
                        }
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
     * findByFirstName = findBy([ 'first_name' => ? ])
     * @param string $method
     * @param array $args
     * @throw Exception
     * @return Model|Model[]
     */
    public static function __callStatic($method, $args)
    {
        $error = true;
        $matches = [];
        $filter = [];

        if (self::isLikeFindByCall($method)) {
            $type = self::parsePropertyActionFromMethod($method);
            $prop = self::parsePropertyNameFromMethod($method);

            if (property_exists(get_called_class(), $prop)) {
                $filter[ $prop ] = $args[0];

                switch ($type) {
                    case self::F_FINDBY:
                        $error = false;
                        $matches = static::findBy($filter);
                        break;

                    case self::F_FINDONEBY:
                        $error = false;
                        $matches = static::findOneBy($filter);
                        break;
                }
            }
        }

        if ($error) {
            throw new \Exception(sprintf(
                'Invalid static method %s called on class %s',
                $method, get_called_class()));
        }

        return $matches;
    }

    /**
     * first_name = 'string' = setFirstName('string')
     * @param string $prop
     * @param mixed $val
     * @throws Exception
     * @return mixed
     */
    public function __set($prop, $val)
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
    public function __get($prop)
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
     * to string
     * @return string
     */
    public function __toString()
    {
        return str_replace('\\', '.', strtolower(get_called_class())) .
            ':' . $this->id;
    }

    /**
     * shortcut method. updates properties and saves a model
     * @param array $updates
     * @param boolean $save
     */
    public function update(array $updates, $save = false)
    {
        foreach ($updates as $field => $value) {
            $this->__set($field, $value);
        }

        if ($save) {
            $this->save();
        }

        return $this;
    }

    /**
     * saves a model. returns save success
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     * @return boolean
     */
    public function delete()
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * returns all models
     * @codeCoverageIgnore
     * @return Model[]
     */
    public static function all()
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * find a model using a unique identifier
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     * @param array $criteria
     * @param callback $cb
     * @return mixed[]|Model[]
     */
    public static function findBy(array $criteria, callable $cb = null)
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * find a model using a set of criteria
     * @codeCoverageIgnore
     * @param array $criteria
     * @return Model[]
     */
    public static function findOneBy(array $criteria)
    {
        throw new \Exception(sprintf('Cannot call undefined method %s::%s',
            get_called_class(), __FUNCTION__));
    }

    /**
     * @param array $data
     * @param boolean $save
     * @return Model
     */
    public static function create(array $data, $save = false)
    {
        $class = get_called_class();
        $model = new $class;

        foreach ($data as $prop => $val) {
            $model->__set($prop, $val);
        }

        if ($save) {
            $model->save();
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
     * @see JsonSerializable
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /*
     * @return array
     */
    public function toArray()
    {
        $data = [];

        // excludes private properties
        foreach (get_object_vars($this) as $prop => $val) {
            $data[ $prop ] = $this->__get($prop);
        }

        return $data;
    }

    /**
     * @return array
     */
    public static function getFields()
    {
        return array_keys(get_object_vars(new static));
    }

    /**
     * @param string $method
     * @return boolean
     */
    final public static function isLikePropertyGetSet($method)
    {
        $method = strtolower($method);
        return strpos($method, self::P_GET) === 0 ||
            strpos($method, self::P_SET) === 0 ||
            strpos($method, self::P_ADD) === 0 ||
            strpos($method, self::P_REMOVE) === 0;
    }

    /**
     * @param string $method
     * @return boolean
     */
    final public static function isLikeFindByCall($method)
    {
        $method = strtolower($method);
        return strpos($method, strtolower(self::F_FINDBY)) === 0 ||
            strpos($method, strtolower(self::F_FINDONEBY)) === 0;
    }

    /**
     * @param string $method
     * @return boolean
     */
    final public static function parsePropertyActionFromMethod($method)
    {
        $type = null;

        if (strpos($method, self::P_SET) === 0) {
            $type = self::P_SET;
        } else if (strpos($method, self::P_GET) === 0) {
            $type = self::P_GET;
        } else if (strpos($method, self::P_ADD) === 0) {
            $type = self::P_ADD;
        } else if (strpos($method, self::P_REMOVE) === 0) {
            $type = self::P_REMOVE;
        } else if (strpos($method, self::F_FINDBY) === 0) {
            $type = self::F_FINDBY;
        } else if (strpos($method, self::F_FINDONEBY) === 0) {
            $type = self::F_FINDONEBY;
        }

        return $type;
    }

    /**
     * first_name = setFirstName
     * @param string $prop
     * @return string
     */
    final public static function generateSetterMethodName($prop)
    {
        return self::P_SET . str_replace('_', '', $prop);
    }

    /**
     * first_name = getFirstName
     * @param string $prop
     * @return string
     */
    final public static function generateGetterMethodName($prop)
    {
        return self::P_GET . str_replace('_', '', $prop);
    }

    /**
     * @param string $method
     * @param string $type, default = null
     * @return boolean
     */
    final public static function parsePropertyNameFromMethod($method, $type = null)
    {
        $prop = strtolower(preg_replace(
            ['/^get|^set|^add|^remove|^findBy|^findOneBy/', '/(\w)([A-Z])/'],
            ['', '$1_$2'], $method));

        switch ($type) {
            // addRole => $roles[]
            case self::P_ADD:
            case self::P_REMOVE:
                $word = new Word;
                $prop = $word->pluralize($prop);
                break;

            // setName = $name
            default:
                break;
        }

        return $prop;
    }

    /**
     * declares the base Storage trait
     * @param string $storage
     * @throws \Exception
     */
    final public static function saveto($storage, $tname = 'Storage')
    {
        $ns = __NAMESPACE__ . '\Storage\Model';
        $ss = '\\';

        if (trait_exists("{$ss}{$ns}{$ss}{$tname}")) {
            throw new \Exception("{$ss}{$ns}{$ss}{$tname} has already been defined");
        }

        if (!trait_exists($storage)) {
            throw new \Exception("Invalid storage trait: {$storage}");
        }

        eval("namespace {$ns}; trait {$tname} { use {$storage}; }");
    }
}
