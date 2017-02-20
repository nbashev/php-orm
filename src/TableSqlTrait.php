<?php

namespace Greg\Orm;

use Greg\Orm\Clause\ClauseStrategy;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\StatementStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Table\DeleteTableQueryTrait;
use Greg\Orm\Table\FromTableClauseTrait;
use Greg\Orm\Table\GroupByTableClauseTrait;
use Greg\Orm\Table\HavingTableClauseTrait;
use Greg\Orm\Table\InsertTableQueryTrait;
use Greg\Orm\Table\JoinTableClauseTrait;
use Greg\Orm\Table\LimitTableClauseTrait;
use Greg\Orm\Table\OffsetTableClauseTrait;
use Greg\Orm\Table\OrderByTableClauseTrait;
use Greg\Orm\Table\SelectTableQueryTrait;
use Greg\Orm\Table\UpdateTableQueryTrait;
use Greg\Orm\Table\WhereTableClauseTrait;

trait TableSqlTrait
{
    use DeleteTableQueryTrait,
        InsertTableQueryTrait,
        SelectTableQueryTrait,
        UpdateTableQueryTrait,

        FromTableClauseTrait,
        GroupByTableClauseTrait,
        HavingTableClauseTrait,
        JoinTableClauseTrait,
        LimitTableClauseTrait,
        OffsetTableClauseTrait,
        OrderByTableClauseTrait,
        WhereTableClauseTrait;

    /**
     * @var QueryStrategy|null
     */
    private $query;

    /**
     * @var ClauseStrategy[]
     */
    private $clauses = [];

    public function query(): QueryStrategy
    {
        if (!$this->query) {
            throw new QueryException('Query was not defined.');
        }

        return $this->query;
    }

    public function setQuery(QueryStrategy $query)
    {
        $this->query = $query;

        $this->clearClauses();

        return $this;
    }

    public function hasQuery(): bool
    {
        return (bool) $this->query;
    }

    public function getQuery(): ?QueryStrategy
    {
        return $this->query;
    }

    public function clearQuery()
    {
        $this->query = null;

        return $this;
    }

    public function clause(string $name): ClauseStrategy
    {
        if (!isset($this->clauses[$name])) {
            throw new QueryException('Clause ' . $name . ' was not defined.');
        }

        return $this->clauses[$name];
    }

    public function setClause(string $name, ClauseStrategy $query)
    {
        $this->clauses[$name] = $query;

        return $this;
    }

    public function hasClauses(): bool
    {
        return (bool) $this->clauses;
    }

    public function hasClause(string $name): bool
    {
        return isset($this->clauses[$name]);
    }

    public function getClauses(): array
    {
        return $this->clauses;
    }

    public function getClause(string $name): ?ClauseStrategy
    {
        return $this->clauses[$name] ?? null;
    }

    public function clearClauses()
    {
        $this->clauses = [];

        return $this;
    }

    public function clearClause(string $name)
    {
        unset($this->clauses[$name]);

        return $this;
    }

    public function chunk(int $count, callable $callable, bool $callOneByOne = false, bool $yield = true)
    {
        $this->chunkQuery($this->selectQueryInstance()->selectQuery(), $count, $callable, $callOneByOne, $yield);

        return $this;
    }

    public function fetch(): ?array
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery();

        return $instance->execute()->fetch();
    }

    public function fetchOrFail(): array
    {
        if (!$record = $this->fetch()) {
            throw new QueryException('Row was not found.');
        }

        return $record;
    }

    public function fetchAll(): array
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery();

        return $instance->execute()->fetchAll();
    }

    public function fetchYield()
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery();

        return $instance->execute()->fetchYield();
    }

    public function fetchColumn(string $column = '0'): string
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery();

        return $instance->execute()->column($column);
    }

    public function fetchColumnAll(string $column = '0'): array
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery();

        return $instance->execute()->columnAll($column);
    }

    public function fetchPairs(string $key = '0', string $value = '1'): array
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery();

        return $instance->execute()->pairs($key, $value);
    }

    public function fetchCount(string $column = '*', string $alias = null): int
    {
        return $this->clearSelect()->selectCount($column, $alias)->fetchColumn();
    }

    public function fetchMax(string $column, string $alias = null): int
    {
        return $this->clearSelect()->selectMax($column, $alias)->fetchColumn();
    }

    public function fetchMin(string $column, string $alias = null): int
    {
        return $this->clearSelect()->selectMin($column, $alias)->fetchColumn();
    }

    public function fetchAvg(string $column, string $alias = null): float
    {
        return $this->clearSelect()->selectAvg($column, $alias)->fetchColumn();
    }

    public function fetchSum(string $column, string $alias = null): string
    {
        return $this->clearSelect()->selectSum($column, $alias)->fetchColumn();
    }

    public function exists(): bool
    {
        return (bool) $this->clearSelect()->selectRaw(1)->fetchColumn();
    }

    public function update(array $columns = []): int
    {
        return $this->setValues($columns)->execute()->affectedRows();
    }

    public function delete(string ...$tables)
    {
        $instance = $this->deleteQueryInstance();

        $instance->deleteQuery();

        if ($tables) {
            $instance->rowsFrom(...$tables);
        }

        return $instance->execute()->affectedRows();
    }

    public function when(bool $condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }

    public function prepare(): StatementStrategy
    {
        return $this->prepareQuery($this->query());
    }

    public function execute(): StatementStrategy
    {
        return $this->executeQuery($this->query());
    }

    public function toSql(): array
    {
        if ($this->clauses and !$this->query) {
            return $this->clausesToSql();
        }

        $query = clone $this->query();

        if ($query instanceof FromClauseStrategy) {
            $this->assignFromAppliers($query);
        }

        if ($query instanceof JoinClauseStrategy) {
            $this->assignJoinAppliers($query);
        }

        if ($query instanceof WhereClauseStrategy) {
            $this->assignWhereAppliers($query);
        }

        if ($query instanceof HavingClauseStrategy) {
            $this->assignHavingAppliers($query);
        }

        if ($query instanceof OrderByClauseStrategy) {
            $this->assignOrderByAppliers($query);
        }

        if ($query instanceof GroupByClauseStrategy) {
            $this->assignGroupByAppliers($query);
        }

        if ($query instanceof LimitClauseStrategy) {
            $this->assignLimitAppliers($query);
        }

        if ($query instanceof OffsetClauseStrategy) {
            $this->assignOffsetAppliers($query);
        }

        return $query->toSql();
    }

    public function toString(): string
    {
        return $this->toSql()[0];
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    protected function clausesToSql()
    {
        $sql = $params = [];

        if ($clause = $this->hasFromAppliers() ? $this->fromClause() : $this->getFromClause()) {
            $clause = clone $clause;

            $this->assignFromAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasJoinAppliers() ? $this->joinClause() : $this->getJoinClause()) {
            $clause = clone $clause;

            $this->assignJoinAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasWhereAppliers() ? $this->whereClause() : $this->getWhereClause()) {
            $clause = clone $clause;

            $this->assignWhereAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasGroupByAppliers() ? $this->groupByClause() : $this->getGroupByClause()) {
            $clause = clone $clause;

            $this->assignGroupByAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasHavingAppliers() ? $this->havingClause() : $this->getHavingClause()) {
            $clause = clone $clause;

            $this->assignHavingAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        if ($clause = $this->hasOrderByAppliers() ? $this->orderByClause() : $this->getOrderByClause()) {
            $clause = clone $clause;

            $this->assignOrderByAppliers($clause);

            list($s, $p) = $clause->toSql();

            $sql[] = $s;

            $params = array_merge($params, $p);
        }

        $sql = implode(' ', $sql);

        if ($clause = $this->hasLimitAppliers() ? $this->limitClause() : $this->getLimitClause()) {
            $clause = clone $clause;

            $this->assignLimitAppliers($clause);

            $sql = $this->driver()->dialect()->addLimitToSql($sql, $clause->getLimit());
        }

        if ($clause = $this->hasOffsetAppliers() ? $this->offsetClause() : $this->getOffsetClause()) {
            $clause = clone $clause;

            $this->assignOffsetAppliers($clause);

            $sql = $this->driver()->dialect()->addOffsetToSql($sql, $clause->getOffset());
        }

        return [$sql, $params];
    }

    protected function prepareQuery(QueryStrategy $query): StatementStrategy
    {
        list($sql, $params) = $query->toSql();

        $stmt = $this->driver()->prepare($sql);

        if ($params) {
            $stmt->bindMultiple($params);
        }

        return $stmt;
    }

    protected function executeQuery(QueryStrategy $query): StatementStrategy
    {
        $stmt = $this->prepareQuery($query);

        $stmt->execute();

        return $stmt;
    }

    protected function chunkQuery(SelectQuery $query, int $count, callable $callable, bool $callOneByOne = false, bool $yield = true)
    {
        if ($count < 1) {
            throw new QueryException('Chunk count should be greater than 0.');
        }

        $offset = 0;

        while (true) {
            $stmt = $this->executeQuery($query->limit($count)->offset($offset));

            if ($callOneByOne) {
                $k = 0;

                foreach ($yield ? $stmt->fetchYield() : $stmt->fetchAll() as $record) {
                    if (call_user_func_array($callable, [$record]) === false) {
                        $k = 0;

                        break;
                    }

                    ++$k;
                }
            } else {
                $records = $stmt->fetchAll();

                $k = count($records);

                if ($records and call_user_func_array($callable, [$records]) === false) {
                    $k = 0;
                }
            }

            if ($k < $count) {
                break;
            }

            $offset += $count;
        }

        return $this;
    }

    abstract public function driver(): DriverStrategy;
}
