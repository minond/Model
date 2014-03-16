<?php

namespace Efficio\Dataset;

use ArrayAccess;
use Countable;
use JsonSerializable;

interface Collection extends ArrayAccess, Countable, JsonSerializable
{
    /**
     * @param object|string $model_class
     * @return boolean
     */
    public function isCollectionOf($model_class);

    /**
     * @return string
     */
    public function collectionOf();
}

