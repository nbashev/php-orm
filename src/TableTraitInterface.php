<?php

namespace Greg\Orm;

use Greg\Orm\TableQuery\TableQueryTraitInterface;

interface TableTraitInterface extends TableQueryTraitInterface
{
    public function setPrefix($name);

    public function getPrefix();

    public function setName($name);

    public function getName();

    public function fullName();

    public function setAlias($name);

    public function getAlias();

    public function setLabel($name);

    public function getLabel();

    public function addColumn(Column $column);

    public function getColumns();

    public function getColumn($name);

    public function getColumnType($name);

    public function setCustomColumnType($key, $value);

    public function getCustomColumnType($key);

    public function getCustomColumnTypes();

    public function setNameColumn($name);

    public function getNameColumn();

    public function setAutoIncrement($columnName);

    public function getAutoIncrement();

    public function setPrimaryKeys($columnsNames);

    public function getPrimaryKeys();

    public function addUniqueKeys(array $keys);

    public function getUniqueKeys();

    public function getFirstUniqueKeys();

    public function firstUniqueIndex();

    public function combineFirstUniqueIndex($values);

    public function fixRowValueType(array $row, $clean = false, $reverse = false);
}
