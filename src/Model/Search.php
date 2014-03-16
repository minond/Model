<?php

namespace Efficio\Dataset\Model;

interface Search
{
    /**
     * returns all models or the return value of the callback
     * @param Callable $db
     * @return mixed[]|Collection
     */
    public static function all(Callable $cb = null);

    /**
     * find a model using a unique identifier
     * @param mixed $id
     * @return Model
     */
    public static function find($id);

    /**
     * find models using a set of criteria
     * @param array $criteria
     * @param callback $cb
     * @return mixed[]|Collection
     */
    public static function findBy(array $criteria, callable $cb = null);

    /**
     * find a model using a set of criteria
     * @param array $criteria
     * @return Model
     */
    public static function findOneBy(array $criteria);
}
