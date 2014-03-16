<?php

namespace Efficio\Dataset\Storage\Model;

trait FileStorage
{
    /**
     * storage directory
     * @var string
     */
    protected static $dir;

    /**
     * saves model to session. returns model's id
     * @return string
     */
    public function save()
    {
        $file = self::initStorageDirectory();

        if (!$this->id) {
            $this->id = uniqid();
            touch($file . $this->id);
        }

        file_put_contents($file . $this->id, serialize($this));
        return $this->id;
    }

    /**
     * deletes a model. returns delete success
     * @return boolean
     */
    public function delete()
    {
        return !$this->id ? true :
            unlink(self::initStorageDirectory() . $this->id);
    }

    /**
     * storage directory setter
     * @param string $dir
     */
    public static function setDirectory($dir)
    {
        static::$dir = $dir;
    }

    /**
     * @param callback $cb
     * @return mixed[]|Collection
     */
    public static function all(callable $cb = null)
    {
        $dir = self::initStorageDirectory();
        $ret = [];

        foreach (scandir($dir) as $file) {
            if (is_file($dir . $file)) {
                $model = unserialize(file_get_contents($dir . $file));
                $ret[] = is_callable($cb) ? $cb($model) : $model;
                unset($model);
            }
        }

        if (!is_callable($cb)) {
            $ret = static::getCollection($ret);
        }

        return $ret;
    }

    /**
     * find a model using a unique identifier
     * @param mixed $id
     * @return Model
     */
    public static function find($id)
    {
        $dir = self::initStorageDirectory();
        return !file_exists($dir . $id) ? null :
            unserialize(file_get_contents($dir . $id));
    }

    /**
     * find models using a set of criteria
     * @param array $criteria
     * @param callback $cb
     * @return mixed[]|Collection
     */
    public static function findBy(array $criteria, callable $cb = null)
    {
        $dir = self::initStorageDirectory();
        $ret = [];

        foreach (scandir($dir) as $file) {
            if (is_file($dir . $file)) {
                $model = unserialize(file_get_contents($dir . $file));
                $match = false;

                foreach ($criteria as $field => $value) {
                    if ($model->{ $field } == $value) {
                        $match = true;
                    }
                }

                if ($match) {
                    $ret[] = is_callable($cb) ? $cb($model) : $model;
                }

                unset($model);
            }
        }

        if (!is_callable($cb)) {
            $ret = static::getCollection($ret);
        }

        return $ret;
    }

    /**
     * find a model using a set of criteria
     * @param array $criteria
     * @return Model
     */
    public static function findOneBy(array $criteria)
    {
        $dir = self::initStorageDirectory();

        foreach (scandir($dir) as $file) {
            if (is_file($dir . $file)) {
                $model = unserialize(file_get_contents($dir . $file));

                foreach ($criteria as $field => $value) {
                    if ($model->{ $field } == $value) {
                        return $model;
                    }
                }

                unset($model);
            }
        }

        return null;
    }

    /**
     * @return string
     */
    public static function initStorageDirectory()
    {
        $dir = static::$dir . DIRECTORY_SEPARATOR .
            preg_replace('/\W+/', '', get_called_class()) .
            DIRECTORY_SEPARATOR;

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        return $dir;
    }
}

