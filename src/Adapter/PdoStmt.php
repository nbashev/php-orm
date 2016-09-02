<?php

namespace Greg\Orm\Adapter;

use Greg\Support\Arr;
use Greg\Support\Str;

class PdoStmt extends \PDOStatement implements StmtInterface
{
    /**
     * \PDOStatement require it to be protected
     */
    protected function __construct() {}

    /**
     * @var PdoAdapter|null
     */
    protected $adapter = null;

    public function bindParams(array $params)
    {
        $k = 1;

        foreach($params as $key => $param) {
            $param = $param !== null ? (array)$param : [$param];

            array_unshift($param, is_int($key) ? $k++ : $key);

            $this->bindValue(...$param);
        }

        return $this;
    }

    public function fetchColumn($column = 0)
    {
        if (Str::isNaturalNumber($column)) {
            return parent::fetchColumn($column);
        }

        $row = $this->fetchAssoc();

        return $row ? Arr::get($row, $column) : null;
    }

    public function fetchAllColumn($column = 0)
    {
        return array_column($this->fetchAll(), $column);
    }

    public function fetchPairs($key = 0, $value = 1)
    {
        $pairs = [];

        foreach($this->fetchAll() as $row) {
            $pairs[$row[$key]] = $row[$value];
        }

        return $pairs;
    }

    public function fetchAssoc()
    {
        return $this->fetch(\PDO::FETCH_ASSOC);
    }

    public function fetchAssocAll()
    {
        return $this->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function fetchAssocAllGenerator()
    {
        while ($record = $this->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    public function execute($params = null)
    {
        $this->getAdapter()->fire($this->queryString);

        return $this->callParent(__FUNCTION__, func_get_args());
    }

    protected function callParent($method, array $args = [])
    {
        try {
            return $this->_callParent($method, $args);
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 2006) {
                $this->getAdapter()->reconnect();

                return $this->_callParent($method, $args);
            }
            throw $e;
        }
    }

    protected function _callParent($method, array $args = [])
    {
        $result = call_user_func_array(['parent', $method], $args);

        if ($result === false) {
            $this->errorCheck();
        }

        return $result;
    }

    public function errorCheck()
    {
        $errorInfo = $this->errorInfo();

        // Bind or column index out of range
        if ($errorInfo[1] and $errorInfo[1] != 25) {
            throw new \Exception($errorInfo[2]);
        }

        return $this;
    }

    public function nextRows()
    {
        return $this->nextRowset();
    }

    public function setAdapter(PdoAdapter $adapter)
    {
        $this->adapter = $adapter;

        return $this;
    }

    public function getAdapter()
    {
        return $this->adapter;
    }
}