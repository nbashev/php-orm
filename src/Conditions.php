<?php

namespace Greg\Orm;

use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectStrategy;

class Conditions extends SqlAbstract
{
    /**
     * @var array[]
     */
    private $conditions = [];

    public function __construct(SqlDialectStrategy $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function column($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('AND', $column, $operator, $value);

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orColumn($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('OR', $column, $operator, $value);

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function columns(array $columns)
    {
        foreach ($columns as $column => $value) {
            $this->column($column, $value);
        }

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orColumns(array $columns)
    {
        foreach ($columns as $column => $value) {
            $this->orColumn($column, $value);
        }

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function date($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('AND', $column, $operator, $value,
            function (string $column) {
                return 'DATE(' . $column . ')';
            },

            function (string $value) {
                return $this->dialect()->dateString($value);
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orDate($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('OR', $column, $operator, $value,
            function (string $column) {
                return 'DATE(' . $column . ')';
            },

            function (string $value) {
                return $this->dialect()->dateString($value);
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function time($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('AND', $column, $operator, $value,
            function (string $column) {
                return 'TIME(' . $column . ')';
            },

            function (string $value) {
                return $this->dialect()->timeString($value);
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orTime($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('OR', $column, $operator, $value,
            function (string $column) {
                return 'TIME(' . $column . ')';
            },

            function (string $value) {
                return $this->dialect()->timeString($value);
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function year($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('AND', $column, $operator, $value,
            function (string $column) {
                return 'YEAR(' . $column . ')';
            },

            function (string $value) {
                return (int) $value;
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orYear($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('OR', $column, $operator, $value,
            function (string $column) {
                return 'YEAR(' . $column . ')';
            },

            function (string $value) {
                return (int) $value;
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function month($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('AND', $column, $operator, $value,
            function (string $column) {
                return 'MONTH(' . $column . ')';
            },

            function (string $value) {
                return (int) $value;
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orMonth($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('OR', $column, $operator, $value,
            function (string $column) {
                return 'MONTH(' . $column . ')';
            },

            function (string $value) {
                return (int) $value;
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function day($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('AND', $column, $operator, $value,
            function (string $column) {
                return 'DAY(' . $column . ')';
            },

            function (string $value) {
                return (int) $value;
            }
        );

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orDay($column, $operator, $value = null)
    {
        if (func_num_args() < 3) {
            $value = $operator;

            $operator = null;
        }

        $this->addColumn('OR', $column, $operator, $value,
            function (string $column) {
                return 'DAY(' . $column . ')';
            },

            function (string $value) {
                return (int) $value;
            }
        );

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function relation($column1, $operator, $column2 = null)
    {
        if (func_num_args() < 3) {
            $column2 = $operator;

            $operator = null;
        }

        $this->addRelation('AND', $column1, $operator, $column2);

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function orRelation($column1, $operator, $column2 = null)
    {
        if (func_num_args() < 3) {
            $column2 = $operator;

            $operator = null;
        }

        $this->addRelation('OR', $column1, $operator, $column2);

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function relations(array $relations)
    {
        foreach ($relations as $column1 => $column2) {
            $this->relation($column1, $column2);
        }

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orRelations(array $relations)
    {
        foreach ($relations as $column1 => $column2) {
            $this->orRelation($column1, $column2);
        }

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function is(string $columnName)
    {
        $this->addCondition('AND', $this->dialect()->quoteName($columnName) . ' = 1');

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function orIs(string $columnName)
    {
        $this->addCondition('OR', $this->dialect()->quoteName($columnName) . ' = 1');

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function isNot(string $columnName)
    {
        $this->addCondition('AND', $this->dialect()->quoteName($columnName) . ' = 0');

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function orIsNot(string $columnName)
    {
        $this->addCondition('OR', $this->dialect()->quoteName($columnName) . ' = 0');

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function isNull(string $columnName)
    {
        $this->addCondition('AND', $this->dialect()->quoteName($columnName) . ' IS NULL');

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function orIsNull(string $columnName)
    {
        $this->addCondition('OR', $this->dialect()->quoteName($columnName) . ' IS NULL');

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function isNotNull(string $columnName)
    {
        $this->addCondition('AND', $this->dialect()->quoteName($columnName) . ' IS NOT NULL');

        return $this;
    }

    /**
     * @param string $columnName
     *
     * @return $this
     */
    public function orIsNotNull(string $columnName)
    {
        $this->addCondition('OR', $this->dialect()->quoteName($columnName) . ' IS NOT NULL');

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function between(string $column, int $min, int $max)
    {
        $this->addCondition('AND', $this->dialect()->quoteName($column) . ' BETWEEN ? AND ?', [$min, $max]);

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orBetween(string $column, int $min, int $max)
    {
        $this->addCondition('OR', $this->dialect()->quoteName($column) . ' BETWEEN ? AND ?', [$min, $max]);

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function notBetween(string $column, int $min, int $max)
    {
        $this->addCondition('AND', $this->dialect()->quoteName($column) . ' NOT BETWEEN ? AND ?', [$min, $max]);

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orNotBetween(string $column, int $min, int $max)
    {
        $this->addCondition('OR', $this->dialect()->quoteName($column) . ' NOT BETWEEN ? AND ?', [$min, $max]);

        return $this;
    }

    /**
     * @param callable|Conditions|WhereClauseStrategy|HavingClauseStrategy $conditions
     *
     * @return $this
     */
    public function conditions($conditions)
    {
        $this->addCondition('AND', $this->prepareConditions($conditions));

        return $this;
    }

    /**
     * @param callable|Conditions|WhereClauseStrategy|HavingClauseStrategy $conditions
     *
     * @return $this
     */
    public function orConditions($conditions)
    {
        $this->addCondition('OR', $this->prepareConditions($conditions));

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function raw(string $sql, string ...$params)
    {
        $this->addCondition('AND', '(' . $this->dialect()->quote($sql) . ')', $params);

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function orRaw(string $sql, string ...$params)
    {
        $this->addCondition('OR', '(' . $this->dialect()->quote($sql) . ')', $params);

        return $this;
    }

    /**
     * @param string $logic
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function addCondition(string $logic, $sql, array $params = [])
    {
        $this->conditions[] = [
            'logic'  => $logic,
            'sql'    => $sql,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function has(): bool
    {
        return (bool) $this->conditions;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        return $this->conditions;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->conditions = [];

        return $this;
    }

    /**
     * @return array
     */
    public function toSql(): array
    {
        $sql = $params = [];

        foreach ($this->conditions as $condition) {
            $condition = $this->prepareCondition($condition);

            $sql[] = ($sql ? $condition['logic'] . ' ' : '') . $condition['sql'];

            $params = array_merge($params, $condition['params']);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->toSql()[0];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @param string        $logic
     * @param array         $columns
     * @param null|string   $operator
     * @param array         $values
     * @param callable|null $columnCallable
     * @param callable|null $valueCallable
     *
     * @throws SqlException
     *
     * @return $this
     */
    private function addRow(string $logic, array $columns, ?string $operator, array $values,
                              callable $columnCallable = null, callable $valueCallable = null)
    {
        $this->prepareRowLogic($columns, $operator, $values);

        if ($operator == 'IN') {
            $valuesSql = $this->prepareBindKeys($values, count($columns));

            $values = array_merge(...$values);
        } else {
            if (count($values) !== count($columns)) {
                throw new SqlException('Wrong row values count in condition. Expected ' . count($columns) . ', got ' . count($values));
            }

            $valuesSql = $this->prepareBindKeys($values);
        }

        //$sql = $this->prepareColumns($columns, $columnCallable, true) . ' ' . $operator . ' ' . $valuesSql;
        $sql = $this->prepareColumns($columns, $columnCallable) . ' ' . $operator . ' ' . $valuesSql;

        $values = $this->prepareValues($values, $valueCallable);

        $this->addCondition($logic, $sql, $values);

        return $this;
    }

    /**
     * @param string $logic
     * @param $column
     * @param null|string $operator
     * @param $value
     * @param callable|null $columnCallable
     * @param callable|null $valueCallable
     *
     * @throws SqlException
     *
     * @return $this
     */
    private function addColumn(string $logic, $column, ?string $operator, $value,
                                 callable $columnCallable = null, callable $valueCallable = null)
    {
        $column = $this->prepareRow($column);

        if (is_array($column)) {
            $this->addRow($logic, $column, $operator, (array) $value, $columnCallable, $valueCallable);

            return $this;
        }

        $value = $this->prepareValue($value);

        $operator = $this->prepareOperator($operator, $value);

        $sql = $this->prepareColumn($column, $columnCallable) . ' ' . $operator . ' ' . $this->prepareBindKeys($value);

        $this->addCondition($logic, $sql, (array) $this->prepareValues($value, $valueCallable));

        return $this;
    }

    /**
     * @param string      $logic
     * @param array       $columns1
     * @param null|string $operator
     * @param array       $columns2
     *
     * @throws SqlException
     *
     * @return $this
     */
    private function addRowRelation(string $logic, array $columns1, ?string $operator, array $columns2)
    {
        $operator = strtoupper($operator);

        $columns2 = array_values($columns2);

        foreach ($columns2 as $key => &$column2) {
            $column2 = $this->prepareRow($column2);

            if (!$operator) {
                $operator = is_array($column2) ? 'IN' : '=';
            }

            if ($operator === 'IN' and !is_array($column2)) {
                if ($key !== 0) {
                    throw new SqlException('Second row column should be an array in relation on key #' . $key . '.');
                }

                $operator = '=';
            }

            if ($operator !== 'IN' and is_array($column2)) {
                throw new SqlException('Second row column could not be an array in relation on key #' . $key . ' for operator `' . $operator . '`.');
            }

            if ($operator === 'IN' and count($column2) !== count($columns2)) {
                throw new SqlException('Wrong second row column count in relation on key #' . $key . '.');
            }
        }
        unset($value);

        if ($operator !== 'IN' and count($columns2) !== count($columns1)) {
            throw new SqlException('Wrong second row columns count in relation. Expected ' . count($columns1) . ', got ' . count($columns2));
        }

        //$sql = $this->prepareColumns($columns1, true) . ' ' . $operator . ' ' . $this->prepareColumns($columns2, true);
        $sql = $this->prepareColumns($columns1) . ' ' . $operator . ' ' . $this->prepareColumns($columns2);

        $this->addCondition($logic, $sql);

        return $this;
    }

    /**
     * @param string $logic
     * @param $column1
     * @param null|string $operator
     * @param $column2
     *
     * @throws SqlException
     *
     * @return $this
     */
    private function addRelation(string $logic, $column1, ?string $operator, $column2)
    {
        $column1 = $this->prepareRow($column1);

        $column2 = $this->prepareRow($column2);

        if (is_array($column1)) {
            $this->addRowRelation($logic, $column1, $operator, (array) $column2);

            return $this;
        }

        if (!$operator) {
            $operator = is_array($column2) ? 'IN' : '=';
        }

        if ($operator === 'IN' and !is_array($column2)) {
            $operator = '=';
        }

        if ($operator !== 'IN' and is_array($column2)) {
            throw new SqlException('Second column could not be an array in relation for operator `' . $operator . '`.');
        }

        $column2 = is_array($column2) ? $this->prepareColumns($column2) : $this->dialect()->quoteName($column2);

        $sql = $this->dialect()->quoteName($column1) . ' ' . $operator . ' ' . $column2;

        $this->addCondition($logic, $sql);

        return $this;
    }

    /**
     * @param array       $columns
     * @param null|string $operator
     * @param array       $values
     *
     * @throws SqlException
     *
     * @return $this
     */
    private function prepareRowLogic(array &$columns, ?string &$operator, array &$values)
    {
        $values = array_values($values);

        foreach ($values as $key => &$value) {
            $value = $this->prepareRow($value);

            $operator = $this->prepareRowOperator($operator, $value, $key);

            if ($operator === 'IN' and count($value) !== count($columns)) {
                throw new SqlException('Wrong row value count in condition on key #' . $key . '.');
            }
        }
        unset($value);

        return $this;
    }

    /**
     * @param string        $column
     * @param callable|null $columnCallable
     *
     * @return string
     */
    private function prepareColumn(string $column, callable $columnCallable = null): string
    {
        $column = $this->dialect()->quoteName($column);

        if ($columnCallable) {
            $column = call_user_func_array($columnCallable, [$column]);
        }

        return $column;
    }

    /**
     * @param null|string $operator
     * @param $value
     * @param int $key
     *
     * @throws SqlException
     *
     * @return string
     */
    private function prepareRowOperator(?string $operator, $value, int $key): string
    {
        if (!$operator) {
            $operator = is_array($value) ? 'IN' : '=';
        }

        if ($operator === 'IN' and !is_array($value)) {
            if ($key !== 0) {
                throw new SqlException('Row value should be an array in condition on key #' . $key . '.');
            }

            $operator = '=';
        }

        if ($operator !== 'IN' and is_array($value)) {
            throw new SqlException('Row value could not be an array in condition on key #' . $key . ' for operator `' . $operator . '`.');
        }

        return $operator;
    }

    /**
     * @param null|string $operator
     * @param $value
     *
     * @throws SqlException
     *
     * @return string
     */
    private function prepareOperator(?string $operator, $value): string
    {
        $operator = strtoupper($operator);

        if (!$operator) {
            $operator = is_array($value) ? 'IN' : '=';
        }

        if ($operator === 'IN' and !is_array($value)) {
            $operator = '=';
        }

        if ($operator !== 'IN' and is_array($value)) {
            throw new SqlException('Value could not be an array in condition for operator `' . $operator . '`.');
        }

        return $operator;
    }

    /**
     * @param $value
     *
     * @return array|string
     */
    private function prepareRow($value)
    {
        if (is_array($value) and count($value) === 1) {
            $value = array_shift($value);
        }

        return $value;
    }

    /**
     * @param $value
     *
     * @return array|string
     */
    private function prepareValue($value)
    {
        if (is_array($value)) {
            foreach ($value as &$val) {
                if (is_array($val) and count($val) === 1) {
                    $val = array_shift($val);
                }
            }
            unset($val);

            if (count($value) === 1) {
                $value = array_shift($value);
            }
        }

        return $value;
    }

    /**
     * @param array         $columns
     * @param callable|null $columnCallable
     *
     * @return string
     */
    private function prepareColumns(array $columns, callable $columnCallable = null): string
    {
        foreach ($columns as &$column) {
            if (is_array($column)) {
                $column = $this->prepareColumns($column, $columnCallable);
            } else {
                $column = $this->prepareColumn($column, $columnCallable);
            }
        }
        unset($column);

        return '(' . implode(', ', $columns) . ')';
    }

    /**
     * @param $values
     * @param callable|null $valueCallable
     *
     * @return array|string
     */
    private function prepareValues($values, callable $valueCallable = null)
    {
        if (is_array($values)) {
            foreach ($values as &$value) {
                if ($valueCallable) {
                    $value = call_user_func_array($valueCallable, [(string) $value]);
                }

                $value = (string) $value;
            }
            unset($value);

            $values = array_values($values);
        } else {
            if ($valueCallable) {
                $values = call_user_func_array($valueCallable, [(string) $values]);
            }

            $values = (string) $values;
        }

        return $values;
    }

    /**
     * @param array $condition
     *
     * @return array
     */
    private function prepareCondition(array $condition): array
    {
        if ($condition['sql'] instanceof self) {
            list($sql, $params) = $condition['sql']->toSql();

            $condition['sql'] = $sql ? '(' . $sql . ')' : null;

            $condition['params'] = $params;
        }

        if ($condition['sql'] instanceof WhereClauseStrategy) {
            list($sql, $params) = $condition['sql']->whereToSql(false);

            $condition['sql'] = $sql ? '(' . $sql . ')' : null;

            $condition['params'] = $params;
        }

        if ($condition['sql'] instanceof HavingClauseStrategy) {
            list($sql, $params) = $condition['sql']->havingToSql(false);

            $condition['sql'] = $sql ? '(' . $sql . ')' : null;

            $condition['params'] = $params;
        }

        return $condition;
    }

    /**
     * @param $value
     * @param int|null $rowLength
     *
     * @return string
     */
    private function prepareBindKeys($value, int $rowLength = null): string
    {
        if (is_array($value)) {
            $result = '(' . implode(', ', array_fill(0, count($value), '?')) . ')';

            if ($rowLength) {
                $result = '(' . implode(', ', array_fill(0, $rowLength, $result)) . ')';
            }

            return $result;
        }

        return '?';
    }

    private function prepareConditions($conditions)
    {
        if (is_callable($conditions)) {
            $callable = $conditions;

            $conditions = new self($this->dialect());

            call_user_func_array($callable, [$conditions]);
        }

        if (!($conditions instanceof self)
            and !($conditions instanceof WhereClauseStrategy)
            and !($conditions instanceof HavingClauseStrategy)) {
            throw new \Exception('Unknown conditions format.');
        }

        return $conditions;
    }
}
