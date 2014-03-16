<?php

namespace Efficio\Dataset\Collection;

use Exception;
use ArrayObject;
use InvalidArgumentException;
use Efficio\Dataset\Model;

/**
 * load a model when requested
 */
class JitCollection extends ArrayCollection
{
    /**
     * @param string|Model|Model[] $model_class
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct($model_class = '')
    {
        $models = [];

        if (!$model_class) {
            $model_class = $this->model_class;
        } else if (is_array($model_class)) {
            $models = $model_class;
            $model_class = count($models) ? get_class($models[0]) : false;
        } else if (is_object($model_class)) {
            $model_class = get_class($model_class);
            $models = func_get_args();
        }

        if (!$model_class) {
            throw new InvalidArgumentException(
                'A model class is required when creating a Collection object');
        } else if (!class_exists($model_class)) {
            throw new Exception("Model class '$model_class' not found");
        } else if (!(new $model_class instanceof Model)) {
            throw new InvalidArgumentException("$model_class is not an instance of Efficio\Dataset\Model");
        }

        if (!$this->model_class) {
            $this->model_class = $model_class;
        }

        foreach ($models as & $model) {
            $this[] = $model;
            unset($model);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $models = [];

        for ($i = 0, $len = count($this); $i < $len; $i++) {
            // from parent, so load autoload
            $model = parent::offsetGet($i);
            $models[] = is_object($model) ? $model->id : $model;
        }

        return sprintf('%s[%s]{ %s }', str_replace('\\', '.',
            strtolower(get_called_class())), count($models), implode(', ', $models));
    }

    /**
     * @see JsonSerializable::jsonSerialize
     */
    public function jsonSerialize()
    {
        $models = [];

        for ($i = 0, $len = count($this); $i < $len; $i++) {
            // from parent, so load autoload
            $model = parent::offsetGet($i);
            $models[] = is_object($model) ? $model->id : $model;
        }

        return $models;
    }

    /**
     * @param int $index
     * @return Model
     */
    public function offsetGet($index)
    {
        $model = parent::offsetGet($index);

        if (!is_object($model)) {
            $model_class = $this->model_class;
            $model = $model_class::find($model);
            self::offsetSet($index, $model);
        }

        return $model;
    }

    /**
     * @see ArrayObject::offsetSet
     * @param int $index
     * @param Model|string $model
     * @throws InvalidArgumentException
     */
    public function offsetSet($index, $model)
    {
        if (is_object($model) && !($model instanceof $this->model_class)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid model of class "%s". This is a collection of "%s" models',
                is_object($model) ? get_class($model) : gettype($model),
                $this->model_class));
        }

        // skip parent. go straight to array
        ArrayObject::offsetSet($index, $model);
    }

    /**
     * preload models
     * @param int $from
     * @param int $to
     * @return int
     */
    public function preLoad($from, $to)
    {
        foreach (range($from, $to) as $index) {
            // TODO: should load all at once
            self::offsetGet($index);
        }

        return $to - $from;
    }
}
