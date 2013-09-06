<?php

namespace Efficio\Dataset\Storage\Model;

use PDO;
use PDOStatement;
use Efficio\Utilitatis\Word;

trait DatabaseStorage
{
    /**
     * @var PDO
     */
    protected static $conn;

    /**
     * tracks which fields have been updates
     * @var array
     */
    private $update_tracking = [];

    /**
     * {@inheritdoc}
     */
    public function __set($var, $val)
    {
        if ($var !== self::DEFAULT_PRIMARY_KEY)
            $this->update_tracking[] = $var;

        return parent::__set($var, $val);
    }

    /**
     * @param PDO $conn
     */
    public static function setConnection(PDO $conn)
    {
        static::$conn = $conn;
    }

    /**
     * @return PDO
     */
    public static function getConnection()
    {
        return static::$conn;
    }

    /**
     * create or updates a model to a database. returns model's id
     * @return int
     */
    public function save()
    {
        $data = [];
        $updates = [];

        if (count($this->update_tracking)) {
            $data = $this->toArray();

            foreach ($this->update_tracking as $field) {
                if (isset($data[ $field ])) {
                    $updates[ $field ] = $data[ $field ];
                }
            }

            if (!$this->id)
                self::generateInsertQuery($updates)->execute();
            else
                self::generateUpdateQuery($this->id, $updates)->execute();

            $this->update_tracking = [];
        }

        return $this->id ?: $this->id = static::$conn->lastInsertId();
    }

    /**
     * deletes a model. returns delete success
     * @return boolean
     */
    public function delete()
    {
        $ok = false;

        if ($this->id) {
            $query = self::generateDeleteQuery($this->id);
            $query->execute();
            $ok = $query->rowCount() >= 1;
        } else {
            $ok = true;
        }

        return $ok;
    }

    /**
     * @param Callable $db
     * @return Model[]
     */
    public static function all(Callable $cb = null)
    {
        $results = [];
        $query = self::generateSelectStatement([]);
        $query->execute();

        if ($cb) {
            while ($model = $query->fetchObject(get_called_class())) {
                $results[] = $cb($model);
                unset($model);
            }
        } else {
            $results = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
        }

        return $results;
    }

    /**
     * @param int $id
     * @return Model
     */
    public static function find($id)
    {
        $query = self::generateSelectStatement(['id' => $id]);
        $query->execute();
        return $query->fetchObject(get_called_class());
    }

    /**
     * find models using a set of criteria
     * @param array $criteria
     * @param Callable $cb
     * @return mixed[]|Model[]
     */
    public static function findBy(array $criteria, Callable $cb = null)
    {
        $results = [];
        $query = self::generateSelectStatement($criteria);
        $query->execute();

        if ($cb) {
            while ($model = $query->fetchObject(get_called_class())) {
                $results[] = $cb($model);
                unset($model);
            }
        } else {
            $results = $query->fetchAll(PDO::FETCH_CLASS, get_called_class());
        }

        return $results;
    }

    /**
     * find a model using a set of criteria
     * @param array $criteria
     * @return Model
     */
    public static function findOneBy(array $criteria)
    {
        $query = self::generateSelectStatement($criteria);
        $query->execute();
        return $query->fetchObject(get_called_class());
    }

    /**
     * convert a field name into a field placeholder
     * @param string $word
     * @return string
     */
    protected static function field($word)
    {
        return ':' . $word;
    }

    /**
     * returns model's table name
     * @return string
     */
    protected static function getTableName()
    {
        $class = get_called_class();
        $class = explode('\\', $class);
        $class = array_pop($class);
        $word = new Word;
        return $word->pluralize($class);
    }

    /**
     * generates a select query
     * @param array $filters
     * @param array $fields
     * @return PDOStatement
     */
    protected static function generateSelectStatement(array $filters, array $fields = null)
    {
        $sfilters = [];
        $fields = $fields ?: self::getFields();

        foreach ($filters as $field => $value) {
            $sfilters[] = sprintf(
                '%s = %s',
                $field,
                self::field($field)
            );
        }

        $query = sprintf(
            'select %s from %s',
            implode(', ', $fields),
            self::getTableName()
        );

        if (count($filters)) {
            $query .= sprintf(
                ' where %s',
                implode(' and ', $sfilters)
            );
        }

        $query = static::$conn->prepare($query);

        foreach ($filters as $field => & $value) {
            $query->bindParam(self::field($field), $value);
            unset($field);
            unset($value);
        }

        return $query;
    }

    /**
     * generates an insert query
     * @param array $fields
     * @return PDOStatement
     */
    protected static function generateInsertQuery($fields)
    {
        $query = static::$conn->prepare(sprintf(
            'insert into %s(%s) values (%s)',
            self::getTableName(),
            implode(', ', array_keys($fields)),
            implode(', ', array_map(['self', 'field'], array_keys($fields)))
        ));

        foreach ($fields as $field => & $value) {
            $query->bindParam(self::field($field), $value);
            unset($field);
        }

        return $query;
    }

    /**
     * generates an update query
     * @param mixed $id
     * @param array $fields
     * @return PDOStatement
     */
    protected static function generateUpdateQuery($id, $fields)
    {
        $updates = [];
        $query = null;

        foreach ($fields as $field => $value) {
            $updates[] = sprintf(
                '%s = %s',
                $field,
                self::field($field)
            );
        }

        $query = static::$conn->prepare(sprintf(
            'update %s set %s where `id` = %s',
            self::getTableName(),
            implode(', ', $updates),
            $id
        ));


        foreach ($fields as $field => & $value) {
            $query->bindParam(self::field($field), $value);
            unset($field);
        }

        return $query;
    }

    /**
     * generates a delete query
     * @param mixed $id
     * @return PDOStatement
     */
    protected static function generateDeleteQuery($id)
    {
        $query = static::$conn->prepare(sprintf(
            'delete from %s where `id` = %s',
            self::getTableName(),
            self::field('id')
        ));

        $query->bindParam(self::field('id'), $id);
        return $query;
    }
}
