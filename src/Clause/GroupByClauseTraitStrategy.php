<?php

namespace Greg\Orm\Clause;

interface GroupByClauseTraitStrategy
{
    /**
     * @param string $column
     *
     * @return $this
     */
    public function groupBy(string $column);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function groupByRaw(string $sql, string ...$params);

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return $this
     */
    public function groupByLogic(string $sql, array $params = []);

    /**
     * @return bool
     */
    public function hasGroupBy(): bool;

    /**
     * @return array
     */
    public function getGroupBy(): array;

    /**
     * @return $this
     */
    public function clearGroupBy();
}