<?php

namespace Efficio\Dataset\Storage\Model;

trait NullStorage
{
    /**
     * @see Handling::save
     * @codeCoverageIgnore
     * @throws Exception
     * @return boolean
     */
    public function save()
    {
        throw new \Exception(sprintf(
            'Cannot call undefined method %s::%s',
            get_called_class(),
            __FUNCTION__
        ));
    }

    /**
     * @see Handling::delete
     * @codeCoverageIgnore
     * @return boolean
     */
    public function delete()
    {
        throw new \Exception(sprintf(
            'Cannot call undefined method %s::%s',
            get_called_class(),
            __FUNCTION__
        ));
    }

    /**
     * @see Search::all
     * @codeCoverageIgnore
     * @param callable $db
     * @return mixed[]|Collection
     */
    public static function all(callable $cb = null)
    {
        throw new \Exception(sprintf(
            'Cannot call undefined method %s::%s',
            get_called_class(),
            __FUNCTION__
        ));
    }

    /**
     * find a model using a unique identifier
     * @codeCoverageIgnore
     * @param mixed $id
     * @return Model
     */
    public static function find($id)
    {
        throw new \Exception(sprintf(
            'Cannot call undefined method %s::%s',
            get_called_class(),
            __FUNCTION__
        ));
    }

    /**
     * @see Search::findBy
     * @codeCoverageIgnore
     * @param array $criteria
     * @param callable $cb
     * @return mixed[]|Collection
     */
    public static function findBy(array $criteria, callable $cb = null)
    {
        throw new \Exception(sprintf(
            'Cannot call undefined method %s::%s',
            get_called_class(),
            __FUNCTION__
        ));
    }

    /**
     * @see Search::findOneBy
     * @codeCoverageIgnore
     * @param array $criteria
     * @return Model
     */
    public static function findOneBy(array $criteria)
    {
        throw new \Exception(sprintf(
            'Cannot call undefined method %s::%s',
            get_called_class(),
            __FUNCTION__
        ));
    }
}
