<?php

namespace Greg\Orm\Query;

use Greg\Support\Arr;
use Greg\Support\DateTime;

trait ConditionsExprTrait
{
    protected $conditions = [];

    public function conditions(array $columns)
    {
        foreach ($columns as $column => $value) {
            $this->condition($column, $value);
        }

        return $this;
    }

    public function condition($column, $operator, $value = null)
    {
        return $this->addColumnLogic('AND', ...func_get_args());
    }

    public function orConditions(array $columns)
    {
        foreach ($columns as $column => $value) {
            $this->orCondition($column, $value);
        }

        return $this;
    }

    public function orCondition($column, $operator, $value = null)
    {
        return $this->addColumnLogic('OR', ...func_get_args());
    }

    public function conditionRel($column1, $operator, $column2 = null)
    {
        return $this->addRelationLogic('AND', ...func_get_args());
    }

    public function orConditionRel($column1, $operator, $column2 = null)
    {
        return $this->addRelationLogic('OR', ...func_get_args());
    }

    public function conditionIsNull($column)
    {
        return $this->addLogic('AND', $this->quoteNameExpr($column) . ' IS NULL');
    }

    public function orConditionIsNull($column)
    {
        return $this->addLogic('OR', $this->quoteNameExpr($column) . ' IS NULL');
    }

    public function conditionIsNotNull($column)
    {
        return $this->addLogic('AND', $this->quoteNameExpr($column) . ' IS NOT NULL');
    }

    public function orConditionIsNotNull($column)
    {
        return $this->addLogic('OR', $this->quoteNameExpr($column) . ' IS NOT NULL');
    }

    public function conditionBetween($column, $min, $max)
    {
        return $this->addLogic('AND', $this->quoteNameExpr($column) . ' BETWEEN ? AND ?', $min, $max);
    }

    public function orConditionBetween($column, $min, $max)
    {
        return $this->addLogic('OR', $this->quoteNameExpr($column) . ' BETWEEN ? AND ?', $min, $max);
    }

    public function conditionNotBetween($column, $min, $max)
    {
        return $this->addLogic('AND', $this->quoteNameExpr($column) . ' NOT BETWEEN ? AND ?', $min, $max);
    }

    public function orConditionNotBetween($column, $min, $max)
    {
        return $this->addLogic('OR', $this->quoteNameExpr($column) . ' NOT BETWEEN ? AND ?', $min, $max);
    }

    public function conditionDate($column, $date)
    {
        return $this->addLogic('AND', 'DATE(' . $this->quoteNameExpr($column) . ') = ?', DateTime::toDateString($date));
    }

    public function orConditionDate($column, $date)
    {
        return $this->addLogic('AND', 'DATE(' . $this->quoteNameExpr($column) . ') = ?', DateTime::toDateString($date));
    }

    public function conditionTime($column, $date)
    {
        return $this->addLogic('AND', 'TIME(' . $this->quoteNameExpr($column) . ') = ?', DateTime::toTimeString($date));
    }

    public function orConditionTime($column, $date)
    {
        return $this->addLogic('AND', 'TIME(' . $this->quoteNameExpr($column) . ') = ?', DateTime::toTimeString($date));
    }

    public function conditionYear($column, $year)
    {
        return $this->addLogic('AND', 'YEAR(' . $this->quoteNameExpr($column) . ') = ?', (int) $year);
    }

    public function orConditionYear($column, $year)
    {
        return $this->addLogic('OR', 'YEAR(' . $this->quoteNameExpr($column) . ') = ?', (int) $year);
    }

    public function conditionMonth($column, $month)
    {
        return $this->addLogic('AND', 'MONTH(' . $this->quoteNameExpr($column) . ') = ?', (int) $month);
    }

    public function orConditionMonth($column, $month)
    {
        return $this->addLogic('OR', 'MONTH(' . $this->quoteNameExpr($column) . ') = ?', (int) $month);
    }

    public function conditionDay($column, $day)
    {
        return $this->addLogic('AND', 'DAY(' . $this->quoteNameExpr($column) . ') = ?', (int) $day);
    }

    public function orConditionDay($column, $day)
    {
        return $this->addLogic('OR', 'DAY(' . $this->quoteNameExpr($column) . ') = ?', (int) $day);
    }

    public function conditionRaw($expr, $value = null, $_ = null)
    {
        $args = func_get_args();

        if (!is_callable($expr)) {
            $args[0] = $this->quoteExpr($expr);
        }

        return $this->addLogic('AND', ...$args);
    }

    public function orConditionRaw($expr, $value = null, $_ = null)
    {
        $args = func_get_args();

        if (!is_callable($expr)) {
            $args[0] = $this->quoteExpr($expr);
        }

        return $this->addLogic('OR', ...func_get_args());
    }

    public function hasConditions()
    {
        return (bool) $this->conditions;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function addConditions(array $conditions)
    {
        $this->conditions = array_merge($this->conditions, $conditions);

        return $this;
    }

    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function clearConditions()
    {
        $this->conditions = [];

        return $this;
    }

    protected function addRelationLogic($type, $column1, $operator, $column2 = null)
    {
        $args = func_get_args();

        array_shift($args);

        if (count($args) < 3) {
            $column1 = array_shift($args);

            $column2 = array_shift($args);

            $operator = null;
        }

        $column1 = $this->packColumns((array) $column1);

        $column2 = $this->packColumns((array) $column2);

        $expr = $column1 . ' ' . ($operator ?: '=') . ' ' . $column2;

        return $this->addLogic($type, $expr);
    }

    protected function packColumns(array $columns)
    {
        $columns = array_map([$this, 'quoteNameExpr'], $columns);

        if (count($columns) > 1) {
            $columns = '(' . implode(', ', $columns) . ')';
        } else {
            $columns = Arr::first($columns);
        }

        return $columns;
    }

    /**
     * Support formats:
     * col1 => 1
     * col1 => [1, 2]
     * [col1] => [1]
     * [col1] => [[1], [2]]
     * [col1, col2] => [1, 2]
     * [col1, col2] => [[1, 2], [3, 4]].
     *
     * @param $type
     * @param $column
     * @param $operator
     * @param null $value
     *
     * @throws \Exception
     *
     * @return $this
     */
    protected function addColumnLogic($type, $column, $operator, $value = null)
    {
        $args = func_get_args();

        array_shift($args);

        if (count($args) < 3) {
            $column = array_shift($args);

            $value = array_shift($args);

            $operator = null;
        }

        // Omg, don't change this! It just works! :))
        $column = (array) $column;

        $value = (array) $value;

        foreach ($value as &$val) {
            $val = (array) $val;
        }
        unset($val);

        if (($columnsCount = count($column)) > 1) {
            if (!$operator and count(Arr::first($value)) > 1) {
                $operator = 'IN';
            }

            if (strtoupper($operator) == 'IN') {
                foreach ($value as &$val) {
                    if (count($val) !== $columnsCount) {
                        throw new \Exception('Wrong row values count in condition.');
                    }
                }
                unset($val);

                $valueExpr = $this->prepareInForBind(count($value), $columnsCount);

                $value = array_merge(...$value);
            } else {
                foreach ($value as &$val) {
                    $val = (string) Arr::first($val);
                }
                unset($val);

                if (count($value) !== $columnsCount) {
                    throw new \Exception('Wrong row values count in condition.');
                }

                $valueExpr = $this->prepareForBind($value);
            }
        } else {
            foreach ($value as &$val) {
                $val = (string) Arr::first($val);
            }
            unset($val);

            if (!$operator) {
                if (count($value) > 1) {
                    $operator = 'IN';
                } else {
                    $value = Arr::first($value);
                }
            }

            $valueExpr = $this->prepareForBind($value);
        }
        // Omg end.

        $column = $this->packColumns($column);

        $expr = $column . ' ' . ($operator ?: '=') . ' ' . $valueExpr;

        return $this->addLogic($type, $expr, $value);
    }

    protected function addLogic($type, $expr, $param = null, $_ = null)
    {
        if (is_callable($expr)) {
            $conditionsExpr = $this->newConditions();

            call_user_func_array($expr, [$conditionsExpr]);

            $expr = $conditionsExpr;

            $params = [];
        } else {
            $params = is_array($param) ? $param : array_slice(func_get_args(), 2);
        }

        $this->conditions[] = [
            'logic'  => $type,
            'expr'   => $expr,
            'params' => $params,
        ];

        return $this;
    }

    protected function parseCondition(&$condition)
    {
        if ($condition['expr'] instanceof ConditionsExprInterface) {
            list($exprSql, $exprParams) = $condition['expr']->toSql();

            $condition['expr'] = $exprSql ? '(' . $exprSql . ')' : null;

            $condition['params'] = $exprParams;
        }

        return $this;
    }

    protected function newConditions()
    {
        return new ConditionsExpr();
    }

    protected function conditionsToSql()
    {
        $sql = $params = [];

        foreach ($this->conditions as $condition) {
            $this->parseCondition($condition);

            $sql[] = ($sql ? $condition['logic'] . ' ' : '') . $condition['expr'];

            $condition['params'] && $params = array_merge($params, $condition['params']);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    protected function conditionsToString()
    {
        return $this->conditionsToSql()[0];
    }
}