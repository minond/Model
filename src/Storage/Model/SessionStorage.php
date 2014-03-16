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
    }

    /**
     * saves model to session. returns model's id
     * @throws Exception
     * @return string
     */
    public function save()
    {
        if (!session_id()) {
            // @codeCoverageIgnoreStart
            throw new \Exception('Cannot save to session');
            // @codeCoverageIgnoreEnd
        }

        if (!$this->id) {
            $this->id = uniqid();
        }

        static::$sess[ static::hash($this->id) ] = serialize($this);
        return $this->id;
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
     * unique class hash
     * @return string
     */
    public static function sessionHash()
    {
        return '__models__' . get_called_class();
    }

    /**
     * starts a new session
     */
    public static function init()
    {
        $key = self::sessionHash();

        // sessions is always created before tests
        // @codeCoverageIgnoreStart
        if (!session_id()) {
            session_start();
        }
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
     * @param callback $cb
     * @return mixed[]|Collection
     */
    public static function all(callable $cb = null)
    {
        $matches = [];
        static::init();

        foreach (static::$sess as $hash => & $ser) {
            $model = unserialize($ser);

            if (is_callable($cb)) {
                $matches[] = $cb($model);
            } else {
                $matches[] = $model;
            }

            unset($model);
            unset($ser);
        }

        if (!is_callable($cb)) {
            $matches = static::getCollection($matches);
        }

        return $matches;
    }

    /**
     * find models using a set of criteria
     * @param array $criteria
     * @param callback $cb
     * @return mixed[]|Collection
     */
    public static function findBy(array $criteria, callable $cb = null)
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
                if (is_callable($cb)) {
                    $matches[] = $cb($model);
                } else {
                    $matches[] = $model;
                }
            }

            unset($model);
            unset($ser);
        }

        if (!is_callable($cb)) {
            $matches = static::getCollection($matches);
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
