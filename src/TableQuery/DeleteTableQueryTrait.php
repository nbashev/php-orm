<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\WhereClauseInterface;

/**
 * Class TableDeleteQueryTrait.
 *
 * @method $this whereAre(array $columns);
 * @method $this where($column, $operator, $value = null);
 * @method $this orWhereAre(array $columns);
 * @method $this orWhere($column, $operator, $value = null);
 * @method $this whereRel($column1, $operator, $column2 = null);
 * @method $this orWhereRel($column1, $operator, $column2 = null);
 * @method $this whereIsNull($column);
 * @method $this orWhereIsNull($column);
 * @method $this whereIsNotNull($column);
 * @method $this orWhereIsNotNull($column);
 * @method $this whereBetween($column, $min, $max);
 * @method $this orWhereBetween($column, $min, $max);
 * @method $this whereNotBetween($column, $min, $max);
 * @method $this orWhereNotBetween($column, $min, $max);
 * @method $this whereDate($column, $date);
 * @method $this orWhereDate($column, $date);
 * @method $this whereTime($column, $date);
 * @method $this orWhereTime($column, $date);
 * @method $this whereYear($column, $year);
 * @method $this orWhereYear($column, $year);
 * @method $this whereMonth($column, $month);
 * @method $this orWhereMonth($column, $month);
 * @method $this whereDay($column, $day);
 * @method $this orWhereDay($column, $day);
 * @method $this whereRaw($expr, $value = null, $_ = null);
 * @method $this orWhereRaw($expr, $value = null, $_ = null);
 * @method $this hasWhere();
 * @method $this clearWhere();
 * @method $this whereExists($expr, $param = null, $_ = null);
 * @method $this whereNotExists($expr, $param = null, $_ = null);
 * @method $this whereToSql();
 * @method $this whereToString();
 * @method DeleteQueryInterface getQuery();
 */
trait DeleteTableQueryTrait
{
    protected function deleteQuery()
    {
        $query = $this->getDriver()->delete();

        $query->from($this);

        $this->applyWhere($query);

        return $query;
    }

    protected function newDeleteInstance()
    {
        return $this->newInstance()->intoDelete();
    }

    protected function checkDeleteQuery()
    {
        if (!($this->query instanceof DeleteQueryInterface)) {
            throw new \Exception('Current query is not a DELETE statement.');
        }

        return $this;
    }

    protected function needDeleteInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoDelete();
            }

            return $this->newDeleteInstance();
        }

        return $this->checkDeleteQuery();
    }

    protected function intoDeleteQuery()
    {
        $query = $this->deleteQuery();

        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                or !($clause instanceof JoinClauseInterface)
                or !($clause instanceof WhereClauseInterface)
                or !($clause instanceof OrderByClauseInterface)
                or !($clause instanceof LimitClauseInterface)
            ) {
                throw new \Exception('Current query is not a DELETE statement.');
            }
        }

        foreach ($this->clauses as $clause) {
            if ($clause instanceof FromClauseInterface) {
                $query->addFrom($clause->getFrom());

                continue;
            }

            if ($clause instanceof JoinClauseInterface) {
                $query->addJoins($clause->getJoins());

                continue;
            }

            if ($clause instanceof WhereClauseInterface) {
                $query->addWhere($clause->getWhere());

                continue;
            }

            if ($clause instanceof OrderByClauseInterface) {
                $query->addOrderBy($clause->getOrderBy());

                continue;
            }

            if ($clause instanceof LimitClauseInterface) {
                $query->setLimit($clause->getLimit());

                continue;
            }
        }

        return $query;
    }

    public function intoDelete()
    {
        $this->query = $this->intoDeleteQuery();

        $this->clearClauses();

        return $this;
    }

    /**
     * @return DeleteQueryInterface
     */
    public function getDeleteQuery()
    {
        $this->checkDeleteQuery();

        return $this->query;
    }

    public function fromTable($table, $_ = null)
    {
        $instance = $this->needDeleteInstance();

        $instance->getQuery()->fromTable(...func_get_args());

        return $instance;
    }

    public function delete($table = null, $_ = null)
    {
        $instance = $this->needDeleteInstance();

        if ($args = func_get_args()) {
            $instance->fromTable($args);
        }

        return $this->execQuery($instance->getQuery());
    }

    public function truncate()
    {
        return $this->getDriver()->truncate($this->fullName());
    }

    public function erase($key)
    {
        return $this->newDeleteInstance()->whereAre($this->combineFirstUniqueIndex($key))->delete();
    }

    /**
     * @return $this
     */
    abstract protected function newInstance();

    /**
     * @return DriverInterface
     */
    abstract public function getDriver();
}