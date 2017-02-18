<?php

namespace Greg\Orm\Clause;

use Greg\Orm\DialectStrategy;
use Greg\Orm\QueryException;

trait OrderByClauseTrait
{
    /**
     * @var array[]
     */
    private $orderBy = [];

    /**
     * @param string      $column
     * @param string|null $type
     *
     * @throws QueryException
     *
     * @return $this
     */
    public function orderBy(string $column, string $type = null)
    {
        if ($type) {
            $type = strtoupper($type);

            if (!in_array($type, ['ASC', 'DESC'])) {
                throw new QueryException('Wrong ORDER type for statement.');
            }
        }

        $this->orderByLogic($this->dialect()->quoteName($column), $type);

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orderAsc(string $column)
    {
        $this->orderBy($column, 'ASC');

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orderDesc(string $column)
    {
        $this->orderBy($column, 'DESC');

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function orderByRaw(string $sql, string ...$params)
    {
        $this->orderByLogic($this->dialect()->quoteSql($sql), null, $params);

        return $this;
    }

    /**
     * @param string      $sql
     * @param null|string $type
     * @param array       $params
     *
     * @return $this
     */
    public function orderByLogic(string $sql, ?string $type, array $params = [])
    {
        $this->orderBy[] = [
            'sql'    => $sql,
            'type'   => $type,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOrderBy(): bool
    {
        return (bool) $this->orderBy;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return $this
     */
    public function clearOrderBy()
    {
        $this->orderBy = [];

        return $this;
    }

    /**
     * @param bool $useClause
     * @return array
     */
    public function orderByToSql(bool $useClause = true): array
    {
        $sql = $params = [];

        foreach ($this->orderBy as $orderBy) {
            $sql[] = $orderBy['sql'] . ($orderBy['type'] ? ' ' . $orderBy['type'] : '');

            $orderBy['params'] && $params = array_merge($params, $orderBy['params']);
        }

        $sql = implode(', ', $sql);

        if ($sql and $useClause) {
            $sql = 'ORDER BY ' . $sql;
        }

        return [$sql, $params];
    }

    /**
     * @param bool $useClause
     * @return string
     */
    public function orderByToString(bool $useClause = true): string
    {
        return $this->orderByToSql($useClause)[0];
    }

    /**
     * @return DialectStrategy
     */
    abstract public function dialect(): DialectStrategy;
}
