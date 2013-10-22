<?php

namespace Efficio\Dataset;

use Efficio\Dataset\Collection\DynamicCollection;
use Efficio\Dataset\Access\GetterSetter;
use Efficio\Dataset\Model\Handling;
use Efficio\Dataset\Model\Search;
use Efficio\Dataset\Storage\Model\NullStorage;

use JsonSerializable;

/**
 * basic model with persistence methods
 */
class Model implements JsonSerializable, Handling, Search
{
    use GetterSetter;
    use NullStorage;

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
     * to string
     * @return string
     */
    public function __toString()
    {
        return str_replace('\\', '.', strtolower(get_called_class())) .
            ':' . ($this->id ?: spl_object_hash($this));
    }

    /**
     * @see Handling::update
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
     * @return array
     */
    public static function getFields()
    {
        return array_keys(get_object_vars(new static));
    }

    /**
     * TODO: should check for Model specific Collections
     * @param $items Model[]
     * @return DynamicCollection
     */
    public static function getCollection(array $items = [])
    {
        $coll = new DynamicCollection(get_called_class());

        foreach ($items as & $item) {
            $coll[] = $item;
            unset($item);
        }

        return $coll;
    }

    /**
     * declares the base Storage trait
     * @param string $storage
     * @throws \Exception
     */
    final public static function saveTo($storage, $tname = 'Storage')
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

