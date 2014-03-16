<?php

namespace Efficio\Dataset\Access;

use InvalidArgumentException;

trait CollectionAccess
{
    /**
     * class name of models we're storing
     * @var string
     */
    protected $model_class;

    /**
     * @return string
     */
    public function __toString()
    {
        $models = [];

        for ($i = 0, $len = count($this); $i < $len; $i++) {
            $model = self::offsetGet($i);
            $models[] = (string) $model;
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
            $model = self::offsetGet($i);
            $models[] = $model->toArray();
        }

        return $models;
    }

    /**
     * @see ArrayObject::offsetSet
     * @param int $index
     * @param Model $model
     * @throws InvalidArgumentException
     */
    public function offsetSet($index, $model)
    {
        if (!($model instanceof $this->model_class)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid model of class "%s". This is a collection of "%s" models',
                is_object($model) ? get_class($model) : gettype($model),
                $this->model_class));
        }

        parent::offsetSet($index, $model);
    }

    /**
     * @see Efficio\Dataset\Collection::isCollectionOf
     */
    public function isCollectionOf($model_class)
    {
        $model_class = is_object($model_class) ?
            get_class($model_class) : $model_class;
        return $this->model_class === $model_class;
    }

    /**
     * @see Efficio\Dataset\Collection::collectionOf
     */
    public function collectionOf()
    {
        return $this->model_class;
    }
}
