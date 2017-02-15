<?php

namespace Greg\Orm\Clause;

use Greg\Orm\ConditionsStrategy;
use Greg\Orm\Query\SelectQueryStrategy;

interface WhereClauseTraitStrategy
{
    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function where($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhere($column, $operator, $value = null);

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function whereMultiple(array $columns);

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orWhereMultiple(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereDate($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereDate($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereTime($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereTime($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereYear($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereYear($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereMonth($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereMonth($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereDay($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereDay($column, $operator, $value = null);

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function whereRelation($column1, $operator, $column2 = null);

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function orWhereRelation($column1, $operator, $column2 = null);

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function whereRelations(array $relations);

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orWhereRelations(array $relations);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNotNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNotNull(string $column);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function whereBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orWhereBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function whereNotBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orWhereNotBetween(string $column, int $min, int $max);

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function whereGroup(callable $callable);

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function orWhereGroup(callable $callable);

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function whereConditions(ConditionsStrategy $strategy);

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function orWhereConditions(ConditionsStrategy $strategy);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function whereRaw(string $sql, string ...$params);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function orWhereRaw(string $sql, string ...$params);

    /**
     * @param string $type
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function whereLogic(string $type, $sql, array $params);

    /**
     * @return bool
     */
    public function hasWhere(): bool;

    /**
     * @return array
     */
    public function getWhere(): array;

    /**
     * @return $this
     */
    public function clearWhere();

    /**
     * @param SelectQueryStrategy $sql
     *
     * @return $this
     */
    public function whereExists(SelectQueryStrategy $sql);

    /**
     * @param SelectQueryStrategy $sql
     *
     * @return $this
     */
    public function whereNotExists(SelectQueryStrategy $sql);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function whereExistsRaw(string $sql, string ...$params);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function whereNotExistsRaw(string $sql, string ...$params);
}