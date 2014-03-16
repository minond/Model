<?php

namespace Efficio\Dataset\Access;

use Exception;
use Efficio\Utilitatis\Word;

/**
 * basic model with persistence methods
 */
trait GetterSetter
{
    /**
     * property getter/setter/add/remove flags
     */
    protected static $P_GET = 'get';
    protected static $P_SET = 'set';
    protected static $P_IS = 'is';
    protected static $P_ADD = 'add';
    protected static $P_REMOVE = 'remove';

    /**
     * custom find functions
     */
    protected static $F_FINDBY = 'findBy';
    protected static $F_FINDONEBY = 'findOneBy';

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
                    case static::$P_SET:
                        list($this->{ $prop }) = $args;
                        $ret = $this->{ $prop };
                        $error = false;
                        break;

                    case static::$P_GET:
                        $ret = $this->{ $prop };
                        $error = false;
                        break;

                    case static::$P_IS:
                        if (count($args)) {
                            list($this->{ $prop }) = $args;
                        }

                        $ret = !!$this->{ $prop };
                        $error = false;
                        break;

                    case static::$P_ADD:
                        if (is_array($this->{ $prop })) {
                            array_push($this->{ $prop }, $args[0]);
                            $ret = count($this->{ $prop });
                            $error = false;
                        }
                        break;

                    case static::$P_REMOVE:
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
            throw new Exception(sprintf(
                'Invalid method %s called on class %s',
                $method,
                get_called_class()
            ));
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
                    case static::$F_FINDBY:
                        $error = false;
                        $matches = call_user_func(
                            ['static', static::$F_FINDBY], $filter);
                        break;

                    case static::$F_FINDONEBY:
                        $error = false;
                        $matches = call_user_func(
                            ['static', static::$F_FINDONEBY], $filter);
                        break;
                }
            }
        }

        if ($error) {
            throw new Exception(sprintf(
                'Invalid static method %s called on class %s',
                $method,
                get_called_class()
            ));
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
            throw new Exception(sprintf(
                'Cannot set invalid property %s on class %s',
                $prop,
                get_called_class()
            ));
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
    public function & __get($prop)
    {
        $ret = null;

        if (property_exists($this, $prop)) {
            $getter = self::generateGetterMethodName($prop);

            if (method_exists($this, $getter)) {
                $ret = $this->{ $getter }();
            } else {
                $ret = & $this->{ $prop };
            }
        } else {
            throw new Exception(sprintf(
                'Cannot get invalid property %s on class %s',
                $prop,
                get_called_class()
            ));
        }

        return $ret;
    }

    /**
     * @param string $method
     * @return boolean
     */
    final public static function isLikePropertyGetSet($method)
    {
        $method = strtolower($method);
        $prefixes = [
            static::$P_GET,
            static::$P_SET,
            static::$P_IS,
            static::$P_ADD,
            static::$P_REMOVE,
        ];

        foreach ($prefixes as $prefix) {
            if (strpos($method, $prefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $method
     * @return boolean
     */
    final public static function isLikeFindByCall($method)
    {
        $method = strtolower($method);
        return strpos($method, strtolower(static::$F_FINDBY)) === 0 ||
            strpos($method, strtolower(static::$F_FINDONEBY)) === 0;
    }

    /**
     * @param string $method
     * @return boolean
     */
    final public static function parsePropertyActionFromMethod($method)
    {
        $type = null;

        if (strpos($method, static::$P_SET) === 0) {
            $type = static::$P_SET;
        } else if (strpos($method, static::$P_GET) === 0) {
            $type = static::$P_GET;
        } else if (strpos($method, static::$P_IS) === 0) {
            $type = static::$P_IS;
        } else if (strpos($method, static::$P_ADD) === 0) {
            $type = static::$P_ADD;
        } else if (strpos($method, static::$P_REMOVE) === 0) {
            $type = static::$P_REMOVE;
        } else if (strpos($method, static::$F_FINDBY) === 0) {
            $type = static::$F_FINDBY;
        } else if (strpos($method, static::$F_FINDONEBY) === 0) {
            $type = static::$F_FINDONEBY;
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
        return static::$P_SET . str_replace('_', '', $prop);
    }

    /**
     * first_name = getFirstName
     * @param string $prop
     * @return string
     */
    final public static function generateGetterMethodName($prop)
    {
        return static::$P_GET . str_replace('_', '', $prop);
    }

    /**
     * @param string $method
     * @param string $type, default = null
     * @return boolean
     */
    final public static function parsePropertyNameFromMethod($method, $type = null)
    {
        $flags = [
            static::$P_GET,
            static::$P_SET,
            static::$P_IS,
            static::$P_ADD,
            static::$P_REMOVE,
            static::$F_FINDBY,
            static::$F_FINDONEBY,
        ];

        $prefix = implode('|', array_map(function($flag) {
            return sprintf('^%s', $flag);
        }, $flags));

        $prop = strtolower(preg_replace(
            [sprintf('/%s/', $prefix), '/(\w)([A-Z])/'],
            ['', '$1_$2'], $method));

        switch ($type) {
            // addRole => $roles[]
            case static::$P_ADD:
            case static::$P_REMOVE:
                $word = new Word;
                $prop = $word->pluralize($prop);
                break;

            // setName = $name
            default:
                break;
        }

        return $prop;
    }
}
