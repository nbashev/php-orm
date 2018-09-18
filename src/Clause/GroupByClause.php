<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectAbstract;
use Greg\Orm\SqlAbstract;

class GroupByClause extends SqlAbstract implements ClauseStrategy, GroupByClauseStrategy
{
    use GroupByClauseTrait;

    public function __construct(SqlDialect $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialectAbstract();
        }

        $this->setDialect($dialect);
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function toSql(bool $useClause = true): array
    {
        return $this->groupByToSql($useClause);
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function toString(bool $useClause = true): string
    {
        return $this->groupByToString($useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
