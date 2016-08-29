<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

trait TableSelectQueryTrait
{
    /**
     * @return SelectQueryInterface
     * @throws \Exception
     */
    public function needSelectQuery()
    {
        if (!$this->query) {
            $this->select();
        } elseif (!($this->query instanceof SelectQueryInterface)) {
            throw new \Exception('Current query is not a SELECT statement.');
        }

        return $this->query;
    }

    public function selectQuery($column = null, $_ = null)
    {
        $query = $this->getStorage()->select(...func_get_args());

        $query->from($this);

        return $query;
    }

    public function select($column = null, $_ = null)
    {
        $this->query = $this->selectQuery(...func_get_args());

        return $this;
    }

    public function distinct($value = true)
    {
        $this->needSelectQuery()->distinct($value);

        return $this;
    }

    public function only($column, $_ = null)
    {
        return $this->columnsFrom($this, ...func_get_args());
    }

    public function selectFrom($table, $column = null, $_ = null)
    {
        $this->needSelectQuery()->from(...func_get_args());

        return $this;
    }

    public function columnsFrom($table, $column, $_ = null)
    {
        $this->needSelectQuery()->from(...func_get_args());

        return $this;
    }

    public function columns($column, $_ = null)
    {
        $this->needSelectQuery()->columns(...func_get_args());

        return $this;
    }

    public function column($column, $alias = null)
    {
        $this->needSelectQuery()->column($column, $alias);

        return $this;
    }

    public function columnRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->columnRaw(...func_get_args());

        return $this;
    }

    public function clearColumns()
    {
        $this->needSelectQuery()->clearColumns();

        return $this;
    }

    public function groupBy($column)
    {
        $this->needSelectQuery()->groupBy($column);

        return $this;
    }

    public function groupByRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->groupByRaw(...func_get_args());

        return $this;
    }

    public function hasGroupBy()
    {
        return $this->needSelectQuery()->hasGroupBy();
    }

    public function clearGroupBy()
    {
        $this->needSelectQuery()->clearGroupBy();

        return $this;
    }

    public function groupByToSql()
    {
        return $this->needSelectQuery()->groupByToSql();
    }

    public function groupByToString()
    {
        return $this->needSelectQuery()->groupByToString();
    }

    public function orderBy($column, $type = null)
    {
        $this->needSelectQuery()->orderBy($column, $type);

        return $this;
    }

    public function orderByRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->orderByRaw(...func_get_args());

        return $this;
    }

    public function hasOrderBy()
    {
        return $this->needSelectQuery()->hasOrderBy();
    }

    public function clearOrderBy()
    {
        $this->needSelectQuery()->clearOrderBy();

        return $this;
    }

    public function orderByToSql()
    {
        return $this->needSelectQuery()->orderByToSql();
    }

    public function orderByToString()
    {
        return $this->needSelectQuery()->orderByToString();
    }

    public function limit($number)
    {
        $this->needSelectQuery()->limit($number);

        return $this;
    }

    public function offset($number)
    {
        $this->needSelectQuery()->offset($number);

        return $this;
    }

    public function union($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->union(...func_get_args());

        return $this;
    }

    public function unionAll($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->unionAll(...func_get_args());

        return $this;
    }

    public function unionDistinct($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->unionAll(...func_get_args());

        return $this;
    }

    public function selectStmtToSql()
    {
        return $this->needSelectQuery()->selectStmtToSql();
    }

    public function selectStmtToString()
    {
        return $this->needSelectQuery()->selectStmtToString();
    }

    public function selectToSql()
    {
        return $this->needSelectQuery()->selectToSql();
    }

    public function selectToString()
    {
        return $this->needSelectQuery()->selectToString();
    }

    public function assoc()
    {
        return $this->needSelectQuery()->assoc();
    }

    public function assocAll()
    {
        return $this->needSelectQuery()->assocAll();
    }

    public function assocAllGenerator()
    {
        return $this->needSelectQuery()->assocAllGenerator();
    }

    public function col($column = 0)
    {
        return $this->needSelectQuery()->col($column);
    }

    public function one($column = 0)
    {
        return $this->needSelectQuery()->one($column);
    }

    public function pairs($key = 0, $value = 1)
    {
        return $this->needSelectQuery()->pairs($key, $value);
    }

    public function exists()
    {
        return $this->needSelectQuery()->exists();
    }

    public function selectCount($column = '*', $alias = null)
    {
        $this->needSelectQuery()->count($column, $alias);

        return $this;
    }

    public function selectMax($column, $alias = null)
    {
        $this->needSelectQuery()->max($column, $alias);

        return $this;
    }

    public function selectMin($column, $alias = null)
    {
        $this->needSelectQuery()->min($column, $alias);

        return $this;
    }

    public function selectAvg($column, $alias = null)
    {
        $this->needSelectQuery()->avg($column, $alias);

        return $this;
    }

    public function selectSum($column, $alias = null)
    {
        $this->needSelectQuery()->sum($column, $alias);

        return $this;
    }

    public function fetchCount($column = '*', $alias = null)
    {
        return $this->clearColumns()->selectCount($column, $alias)->one();
    }

    public function fetchMax($column, $alias = null)
    {
        return $this->clearColumns()->selectMax($column, $alias)->one();
    }

    public function fetchMin($column, $alias = null)
    {
        return $this->clearColumns()->selectMin($column, $alias)->one();
    }

    public function fetchAvg($column, $alias = null)
    {
        return $this->clearColumns()->selectAvg($column, $alias)->one();
    }

    public function fetchSum($column, $alias = null)
    {
        return $this->clearColumns()->selectSum($column, $alias)->one();
    }

    public function selectKeyValue()
    {
        if (!$columnName = $this->getNameColumn()) {
            throw new \Exception('Undefined column name for table `' . $this->getName() . '`.');
        }

        $this->needSelectQuery()
            ->column($this->concat($this->firstUniqueIndex(), ':'), 'key')
            ->column($columnName, 'value');

        return $this;
    }

    public function rowExists($column, $value)
    {
        return $this->selectQuery()->columnRaw(1)->where($column, $value)->exists();
    }

    public function row()
    {
        $query = $this->needSelectQuery();

        if ($query->hasColumns()) {
            throw new \Exception('You can not fetch as rows while you have custom SELECT columns.');
        }

        $query->columnsFrom($this, '*');

        return $this->newInstance()->___appendRowData($query->assoc());
    }

    protected function rowsQuery()
    {
        $query = $this->needSelectQuery();

        if ($query->hasColumns()) {
            throw new \Exception('You can not fetch as rows while you have custom SELECT columns.');
        }

        $query->columnsFrom($this, '*');

        return $query;
    }

    public function rows()
    {
        $query = $this->rowsQuery();

        $rows = $this->newInstance();

        foreach($query->assocAllGenerator() as $row) {
            $rows->___appendRowData($row);
        }

        return $rows;
    }

    public function chunk($count, callable $callable, $callOneByOne = false)
    {
        return $this->needSelectQuery()->chunk($count, $callable, $callOneByOne);
    }

    public function chunkRows($count, callable $callable, $callOneByOne = false)
    {
        $query = $this->rowsQuery();

        $newCallable = function ($data) use ($callable, $callOneByOne) {
            if ($callOneByOne) {
                $row = $this->newInstance()->___appendRowData($data);

                return call_user_func_array($callable, [$row]);
            }

            $rows = $this->newInstance();

            foreach($data as $item) {
                $rows->___appendRowData($item);
            }

            return call_user_func_array($callable, [$rows]);
        };

        return $query->chunk($count, $newCallable, $callOneByOne);
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();

    /**
     * @return TableInterface
     */
    abstract protected function newInstance();
}