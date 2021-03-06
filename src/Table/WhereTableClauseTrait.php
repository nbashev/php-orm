<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;

trait WhereTableClauseTrait
{
    use TableClauseTrait;

    private $whereAppliers = [];

    /**
     * @param WhereClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignWhereAppliers(WhereClauseStrategy $strategy)
    {
        if ($this->whereAppliers) {
            $items = $strategy->getWhere();

            $strategy->clearWhere();

            foreach ($this->whereAppliers as $applier) {
                $clause = $this->connection()->where();

                call_user_func_array($applier, [$clause]);

                $strategy->whereConditions($clause);
            }

            if ($items) {
                $clause = $this->connection()->where();

                foreach ($items as $where) {
                    $clause->addWhere($where['logic'], $where['sql'], $where['params']);
                }

                $strategy->whereConditions($clause);
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function setWhereApplier(callable $callable)
    {
        $this->whereAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWhereAppliers(): bool
    {
        return (bool) $this->whereAppliers;
    }

    /**
     * @return callable[]
     */
    public function getWhereAppliers(): array
    {
        return $this->whereAppliers;
    }

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setWhereAppliers(array $appliers)
    {
        $this->whereAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearWhereAppliers()
    {
        $this->whereAppliers = [];

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function where($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->where(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orWhere($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhere(...func_get_args());

        return $instance;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function whereMultiple(array $columns)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereMultiple($columns);

        return $instance;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orWhereMultiple(array $columns)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereMultiple($columns);

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function whereDate($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereDate(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orWhereDate($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereDate(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function whereTime($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereTime(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orWhereTime($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereTime(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function whereYear($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereYear(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orWhereYear($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereYear(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function whereMonth($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereMonth(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orWhereMonth($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereMonth(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function whereDay($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereDay(...func_get_args());

        return $instance;
    }

    /**
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @return $this
     */
    public function orWhereDay($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereDay(...func_get_args());

        return $instance;
    }

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     *
     * @return $this
     */
    public function whereRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereRelation(...func_get_args());

        return $instance;
    }

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     *
     * @return $this
     */
    public function orWhereRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereRelation(...func_get_args());

        return $instance;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function whereRelations(array $relations)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereRelations($relations);

        return $instance;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orWhereRelations(array $relations)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereRelations($relations);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIs(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereIs($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIs(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereIs($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNot(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereIsNot($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNot(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereIsNot($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereIsNull($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereIsNull($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNotNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereIsNotNull($column);

        return $instance;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNotNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereIsNotNull($column);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function whereBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orWhereBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function whereNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereNotBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orWhereNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereNotBetween($column, $min, $max);

        return $instance;
    }

    /**
     * @param $strategy
     *
     * @return $this
     */
    public function whereConditions($strategy)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereConditions($strategy);

        return $instance;
    }

    /**
     * @param $strategy
     *
     * @return $this
     */
    public function orWhereConditions($strategy)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereConditions($strategy);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function whereRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function orWhereRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @return bool
     */
    public function hasWhere(): bool
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->hasWhere();
        }

        return false;
    }

    public function getWhere(): array
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->getWhere();
        }

        return [];
    }

    /**
     * @return $this
     */
    public function clearWhere()
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->clearWhere();
        }

        return $this;
    }

    /**
     * @param SelectQuery $sql
     *
     * @return $this
     */
    public function whereExists(SelectQuery $sql)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereExists($sql);

        return $instance;
    }

    /**
     * @param SelectQuery $sql
     *
     * @return $this
     */
    public function whereNotExists(SelectQuery $sql)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereNotExists($sql);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function whereExistsRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereExistsRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function whereNotExistsRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereNotExistsRaw($sql, ...$params);

        return $instance;
    }

    public function hasExists(): bool
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->hasExists();
        }

        return false;
    }

    public function getExists(): ?array
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->getExists();
        }

        return null;
    }

    /**
     * @return $this
     */
    public function clearExists()
    {
        if ($clause = $this->getWhereStrategy()) {
            $clause->clearExists();
        }

        return $this;
    }

    public function whereToSql(bool $useClause = true): array
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->whereToSql($useClause);
        }

        return ['', []];
    }

    public function whereToString(bool $useClause = true): string
    {
        return $this->whereToSql($useClause)[0];
    }

    public function whereClause(): WhereClause
    {
        /** @var WhereClause $clause */
        $clause = $this->clause('WHERE');

        return $clause;
    }

    public function hasWhereClause(): bool
    {
        return $this->hasClause('WHERE');
    }

    public function getWhereClause(): ?WhereClause
    {
        /** @var WhereClause $clause */
        $clause = $this->getClause('WHERE');

        return $clause;
    }

    public function whereStrategy(): WhereClauseStrategy
    {
        /** @var QueryStrategy|WhereClauseStrategy $query */
        if ($query = $this->getQuery()) {
//            $this->validateWhereStrategyInQuery($query);

            return $query;
        }

        return $this->whereClause();
    }

    public function getWhereStrategy(): ?WhereClauseStrategy
    {
        /** @var QueryStrategy|WhereClauseStrategy $query */
        if ($query = $this->getQuery()) {
//            $this->validateWhereStrategyInQuery($query);

            return $query;
        }

        return $this->getWhereClause();
    }

    /**
     * @return $this
     */
    public function intoWhereStrategy()
    {
        if (!$this->hasClause('WHERE')) {
            $this->setClause('WHERE', $this->connection()->where());
        }

        return $this;
    }

    private function whereStrategyInstance()
    {
        if ($query = $this->getQuery()) {
//            $this->validateWhereStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoWhereStrategy();
        }

        return $this->cleanClone()->setClause('WHERE', $this->connection()->where());
    }

//    private function validateWhereStrategyInQuery(QueryStrategy $query)
//    {
//        if (!($query instanceof WhereClauseStrategy)) {
//            throw new SqlException('Current query does not have a WHERE clause.');
//        }
//
//        return $this;
//    }

    private function getPreparedWhereClause()
    {
        if ($this->whereAppliers) {
            $clause = clone $this->intoWhereStrategy()->whereClause();

            $this->assignWhereAppliers($clause);
        } else {
            $clause = $this->getWhereClause();
        }

        return $clause;
    }
}
