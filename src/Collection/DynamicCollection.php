<?php

namespace Efficio\Dataset\Collection;

use Exception;
use InvalidArgumentException;
use Efficio\Dataset\Model;

/**
 * a collection of Models
 */
class DynamicCollection extends ArrayCollection
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
        } elseif (is_array($model_class)) {
            $models = $model_class;
            $model_class = count($models) ? get_class($models[0]) : false;
        } elseif (is_object($model_class)) {
            $model_class = get_class($model_class);
            $models = func_get_args();
        }

        if (!$model_class) {
            throw new InvalidArgumentException(
                'A model class is required when creating a Collection object');
        } elseif (!class_exists($model_class)) {
            throw new Exception("Model class '$model_class' not found");
        } elseif (!(new $model_class instanceof Model)) {
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
}
