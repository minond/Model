<?php

namespace dataset\storage\model;

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
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['__models'])) {
            $_SESSION['__models'] = [];
        }

        static::$sess = & $_SESSION['__models'];
    }

    /**
     * find a model using a unique identifier
     * @param mixed $id
     * @return Model
     */
    public static function find($id)
    {
        static::init();

        if (isset(
            static::$sess,
            static::$sess[ static::hash($id) ]
        )) {
            return unserialize(static::$sess[ static::hash($id) ]);
        }
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
}
