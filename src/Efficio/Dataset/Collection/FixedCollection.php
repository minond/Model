<?php

namespace Efficio\Dataset\Collection;

use Exception;
use InvalidArgumentException;
use SplFixedArray;
use Efficio\Dataset\Collection;

/**
 * a collection of Models
 */
class FixedCollection extends SplFixedArray implements Collection
{
    use CollectionAccess;

    /**
     * @param int|string|Model|Model[] $model_class
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function __construct($model_class = '')
    {
        $models = [];
        $count = 0;

        if (is_int($model_class)) {
            $count = $model_class;
            $model_class = $this->model_class;
        } else if (!$model_class) {
            $model_class = $this->model_class;
        } else if (is_array($model_class)) {
            $models = $model_class;
            $count = count($models);
            $model_class = $count ? get_class($models[0]) : false;
        } else if (is_object($model_class)) {
            $model_class = get_class($model_class);
            $models = func_get_args();
            $count = count($models);
        }

        if (!$model_class) {
            throw new InvalidArgumentException(
                'A model class is required when creating a Collection object');
        } else if (!class_exists($model_class)) {
            throw new Exception("Model class '$model_class' not found");
        }

        if (!$this->model_class) {
            $this->model_class = $model_class;
        }
        $this->setSize($count);

        foreach ($models as $index => & $model) {
            $this[ $index ] = $model;
            unset($model);
        }
    }
}

