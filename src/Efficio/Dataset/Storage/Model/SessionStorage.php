<?php

namespace Efficio\Dataset\Storage\Model;

trait SessionStorage
{
    /**
     * reference to storage in session
     * @var array
     */
    protected static $sess;

    /**
     * starts a new session
     */
    public function __construct()
    {
        static::init();
        $this->id = uniqid();
    }

    /**
     * saves model to session
     * @throws Exception
     * @return boolean
     */
    public function save()
    {
        if (!session_id()) {
            throw new \Exception('Cannot save to session');
        }

        static::$sess[ static::hash($this->id) ] = serialize($this);
        return true;
    }

    /**
     * deletes a model. returns delete success
     * @return boolean
     */
    public function delete()
    {
        unset(static::$sess[ static::hash($this->id) ]);
        return true;
    }

    /**
     * starts a new session
     */
    public static function init()
    {
        $key = '__models__';

        if (!session_id())
            // sessions is always created before tests
            // @codeCoverageIgnoreStart
            session_start();
            // @codeCoverageIgnoreEnd

        if (!isset($_SESSION[ $key ])) {
            $_SESSION[ $key ] = [];
        }

        static::$sess = & $_SESSION[ $key ];
    }

    /**
     * find a model using a unique identifier
     * @param mixed $id
     * @return Model
     */
    public static function find($id)
    {
        $hash = static::hash($id);
        static::init();

        return isset(static::$sess[ $hash ]) ?
            unserialize(static::$sess[ $hash ]) : null;
    }

    /**
     * find models using a set of criteria
     * @param array $criteria
     * @return Model[]
     */
    public static function findBy(array $criteria)
    {
        $matches = [];
        static::init();

        foreach (static::$sess as $hash => & $ser) {
            $model = unserialize($ser);
            $match = true;

            foreach ($criteria as $field => $value) {
                if ($model->{ $field } !== $value) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $matches[] = $model;
            }

            unset($model);
            unset($ser);
        }

        return $matches;
    }

    /**
     * find a model using a set of criteria
     * @param array $criteria
     * @return Model
     */
    public static function findOneBy(array $criteria)
    {
        static::init();

        foreach (static::$sess as $hash => & $ser) {
            $model = unserialize($ser);
            $match = true;

            foreach ($criteria as $field => $value) {
                if ($model->{ $field } !== $value) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                return $model;
            }

            unset($model);
            unset($ser);
        }
    }
}
