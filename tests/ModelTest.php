<?php

namespace Greg\Orm\Tests;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Model;
use Greg\Orm\ModelTestingAbstract;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

class ModelTest extends ModelTestingAbstract
{
    public function testCanManageQuery()
    {
        $this->assertFalse($this->model->hasQuery());

        $this->assertEmpty($this->model->getQuery());

        $this->model->setQuery($this->model->newSelectQuery());

        $this->assertTrue($this->model->hasQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->getQuery());

        $this->assertInstanceOf(QueryStrategy::class, $this->model->query());

        $this->model->clearQuery();

        $this->assertEmpty($this->model->getQuery());

        $this->expectException(SqlException::class);

        $this->model->query();
    }

    public function testCanUseWhen()
    {
        $this->model->setQuery($this->model->newSelectQuery());

        $callable = function (Model $model) {
            $model->where('Column', 'foo');
        };

        $this->model->when(false, $callable);

        $this->assertEquals('SELECT * FROM `Table`', $this->model->toString());

        $this->model->when(true, $callable);

        $this->assertEquals('SELECT * FROM `Table` WHERE `Column` = ?', $this->model->toString());
    }

    public function testCanGetClausesSql()
    {
        $query = $this->model
            ->from('Table1')
            ->inner('Table2')
            ->where('Column', 'foo')
            ->having('Column', 'foo')
            ->orderBy('Column')
            ->groupBy('Column')
            ->limit(10)
            ->offset(10);

        $sql = 'FROM `Table1` INNER JOIN `Table2` WHERE `Column` = ?'
                . ' GROUP BY `Column` HAVING `Column` = ? ORDER BY `Column` LIMIT 10 OFFSET 10';

        $this->assertEquals($sql, $query->toString());
    }

    public function testCanTransformToString()
    {
        $query = $this->model->where('Column', 'foo');

        $this->assertEquals('WHERE `Column` = ?', (string) $query);
    }

    public function testCanGetClause()
    {
        $query = $this->model->where('Column', 'foo');

        $this->assertInstanceOf(WhereClause::class, $query->clause('WHERE'));
    }

    public function testCanThrowExceptionIfClauseNotExists()
    {
        $this->expectException(SqlException::class);

        $this->model->clause('FROM');
    }

    public function testCanDetermineIfClauseExists()
    {
        $this->assertFalse($this->model->hasClause('WHERE'));

        $query = $this->model->where('Column', 'foo');

        $this->assertTrue($query->hasClause('WHERE'));
    }

    public function testCanClearClause()
    {
        $query = $this->model->where('Column', 'foo');

        $query->clearClause('WHERE');

        $this->assertFalse($query->hasClause('WHERE'));
    }

    public function testCanThrowExceptionIfNameNotDefined()
    {
        /** @var Model $model */
        $model = new class() extends Model {
        };

        $this->expectException(\Exception::class);

        $model->name();
    }

    public function testCanThrowExceptionWhenDriverNotDefined()
    {
        /** @var Model $model */
        $model = new class() extends Model {
        };

        $this->expectException(\Exception::class);

        $model->driver();
    }

    public function testCanGetLabel()
    {
        $this->assertEquals('My Table', $this->model->label());
    }

    public function testCanGetFillable()
    {
        $this->assertEquals('*', $this->model->fillable());
    }

    public function testCanGetPrimary()
    {
        $this->mockDescribe();

        $this->assertEquals(['Id'], $this->model->primary());
    }

    public function testCanGetUnique()
    {
        $this->assertEquals([['SystemName']], $this->model->unique());
    }

    public function testCanGetFirstUnique()
    {
        $this->mockDescribe();

        $this->assertEquals(['Id'], $this->model->firstUnique());
    }

    public function testCanGetAutoIncrement()
    {
        $this->mockDescribe();

        $this->assertEquals('Id', $this->model->autoIncrement());
    }

    public function testCanGetNameColumn()
    {
        $this->assertEquals('Name', $this->model->nameColumn());
    }

    public function testCanGetCasts()
    {
        $this->assertEquals(['Active' => 'bool'], $this->model->casts());
    }

    public function testCanGetCast()
    {
        $this->assertEquals('bool', $this->model->cast('Active'));
    }

    public function testCanSelectPairs()
    {
        $this->mockDescribe();

        $this->driverMock->method('pairs')->willReturn([1 => 1, 2 => 2]);

        $this->assertEquals([1 => 1, 2 => 2], $this->model->pairs());
    }

    public function testCanThrowExceptionIfCanNotSelectPairs()
    {
        $this->expectException(\Exception::class);

        /** @var Model $model */
        $model = new class() extends Model {
        };

        $model->pairs();
    }

    public function testCanThrowExceptionIfCanNotSelectPairsWhenCustomSelect()
    {
        $this->mockDescribe();

        $query = $this->model->select(1);

        $this->expectException(\Exception::class);

        $query->pairs();
    }

    public function testCanCreateNewRow()
    {
        $this->mockDescribe();

        $driver = $this->driverMock;

        /** @var Model $row */
        $row = new class(['Id' => 1], $driver) extends Model {
            protected $name = 'Table';
        };

        $this->assertEquals(1, $row['Id']);
    }

    public function testCanGetFirstByCallable()
    {
        $this->mockDescribe();

        $driver = $this->driverMock;

        /** @var Model $rows */
        $rows = new class([], $driver) extends Model {
            protected $name = 'Table';
        };

        $rows->appendRecord([
                'Id' => 1,
            ])
            ->appendRecord([
                'Id' => 2,
            ]);

        $row = $rows->search(function (Model $row) {
            return $row['Id'] === 2;
        }, false);

        $this->assertEquals(2, $row['Id']);
    }

    public function testCanChunk()
    {
        $this->driverMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ],
            [
                ['Id' => 3],
                ['Id' => 4],
            ],
            [

            ]
        ));

        $count = 0;

        $this->model->chunk(2, function ($records) use (&$count) {
            ++$count;

            $this->assertCount(2, $records);
        });

        $this->assertEquals(2, $count);
    }

    public function testCanChunkOneByOne()
    {
        $this->driverMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ],
            [
                ['Id' => 3],
                ['Id' => 4],
            ],
            [

            ]
        ));

        $count = 0;

        $this->model->chunk(2, function ($records) use (&$count) {
            ++$count;

            $this->assertCount(1, $records);
        }, true, false);

        $this->assertEquals(4, $count);
    }

    public function testCanChunkYieldOneByOne()
    {
        $this->driverMock->expects($this->exactly(3))->method('fetchYield')->will($this->onConsecutiveCalls(
            (function () {
                yield ['Id' => 1];
                yield ['Id' => 2];
            })(),
            (function () {
                yield ['Id' => 1];
                yield ['Id' => 2];
            })(),
            (function () {
                if (false) {
                    yield;
                }
            })()
        ));

        $count = 0;

        $this->model->chunk(2, function ($records) use (&$count) {
            ++$count;

            $this->assertCount(1, $records);
        }, true);

        $this->assertEquals(4, $count);
    }

    public function testCanStopChunk()
    {
        $this->driverMock->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ]
        ));

        $count = 0;

        $this->model->chunk(2, function () use (&$count) {
            ++$count;

            return false;
        });

        $this->assertEquals(1, $count);
    }

    public function testCanStopChunk1By1()
    {
        $this->driverMock->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ]
        ));

        $count = 0;

        $this->model->chunk(2, function () use (&$count) {
            ++$count;

            return false;
        }, true, false);

        $this->assertEquals(1, $count);
    }

    public function testCanChunkRows()
    {
        $this->mockDescribe();

        $this->driverMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ],
            [
                ['Id' => 3],
                ['Id' => 4],
            ],
            [

            ]
        ));

        $count = 0;

        $this->model->chunkRows(2, function ($records) use (&$count) {
            ++$count;

            $this->assertCount(2, $records);
        });

        $this->assertEquals(2, $count);
    }

    public function testCanChunkRowsOneByOne()
    {
        $this->mockDescribe();

        $this->driverMock->expects($this->exactly(3))->method('fetchAll')->will($this->onConsecutiveCalls(
            [
                ['Id' => 1],
                ['Id' => 2],
            ],
            [
                ['Id' => 3],
                ['Id' => 4],
            ],
            [

            ]
        ));

        $count = 0;

        $this->model->chunkRows(2, function ($records) use (&$count) {
            ++$count;

            $this->assertCount(1, $records);
        }, true, false);

        $this->assertEquals(4, $count);
    }

    public function testCanFetch()
    {
        $this->driverMock->method('fetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetch());
    }

    public function testCanFetchOrFail()
    {
        $this->driverMock->method('fetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetchOrFail());
    }

    public function testCanThrowExceptionIfFetchFail()
    {
        $this->driverMock->method('fetch')->willReturn(null);

        $this->expectException(\Exception::class);

        $this->model->fetchOrFail();
    }

    public function testCanFetchAll()
    {
        $this->driverMock->method('fetchAll')->willReturn([['Id' => 1]]);

        $this->assertCount(1, $this->model->fetchAll());
    }

    public function testCanFetchColumn()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchColumn());
    }

    public function testCanFetchAllColumn()
    {
        $this->driverMock->method('columnAll')->willReturn([1, 2]);

        $this->assertEquals([1, 2], $this->model->fetchColumnAll());
    }

    public function testCanFetchAllColumnYield()
    {
        $this->driverMock->method('columnYield')->willReturn((function () {
            yield 1;
            yield 2;
        })());

        $generator = $this->model->fetchColumnYield();

        $this->assertInstanceOf(\Generator::class, $generator);

        $array = [1, 2];

        foreach ($generator as $column) {
            $this->assertEquals(array_shift($array), $column);
        }

        $this->assertEmpty($array);
    }

    public function testCanFetchPairs()
    {
        $this->driverMock->method('pairs')->willReturn([1 => 1, 2 => 2]);

        $this->assertEquals([1 => 1, 2 => 2], $this->model->fetchPairs());
    }

    public function testCanFetchPairsYield()
    {
        $this->driverMock->method('pairsYield')->willReturn((function () {
            yield 1 => 1;
            yield 2 => 2;
        })());

        $generator = $this->model->fetchPairsYield();

        $this->assertInstanceOf(\Generator::class, $generator);

        $array = [1 => 1, 2 => 2];

        foreach ($generator as $key => $value) {
            $this->assertEquals(key($array), current($array));

            next($array);
        }

        $this->assertFalse(next($array));
    }

    public function testCanFetchCount()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchCount());
    }

    public function testCanFetchMax()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMax('Column'));
    }

    public function testCanFetchMin()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchMin('Column'));
    }

    public function testCanFetchAvg()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchAvg('Column'));
    }

    public function testCanFetchSum()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertEquals(1, $this->model->fetchSum('Column'));
    }

    public function testCanFetchExists()
    {
        $this->driverMock->method('column')->willReturn(1);

        $this->assertTrue($this->model->exists());
    }

    public function testCanUpdate()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->update(['Column' => 'foo']));
    }

    public function testCanDelete()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->delete());

        $this->assertEquals(1, $this->model->delete('Table2'));
    }

    public function testThrowExceptionIfChunkCountIsLessThanZero()
    {
        $this->expectException(\Exception::class);

        $this->model->chunk(-1, function () {
        });
    }

    public function testCanGetColumns()
    {
        $this->mockDescribe();

        $this->assertCount(1, $this->model->columns());
    }

    public function testCanGetDetermineIfColumnExists()
    {
        $this->mockDescribe();

        $this->assertTrue($this->model->hasColumn('Id'));

        $this->assertFalse($this->model->hasColumn('Undefined'));
    }

    public function testCanGetColumn()
    {
        $this->mockDescribe();

        $this->assertNotEmpty($this->model->column('Id'));
    }

    public function testCanThrowExceptionIfColumnNotFound()
    {
        $this->mockDescribe();

        $this->expectException(\Exception::class);

        $this->model->column('Undefined');
    }

    public function testCanGetGuarded()
    {
        $this->assertEquals([], $this->model->guarded());
    }

    public function testCanSetDefaults()
    {
        $this->assertCount(0, $this->model->getDefaults());

        $this->model->setDefaults(['Active' => 1]);

        $this->assertCount(1, $this->model->getDefaults());
    }

    public function testCanInsert()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->insert(['Column' => 'foo']));
    }

    public function testCanInsertSelectWithDefaults()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->model->setDefaults(['Foo' => 'bar']);

        $this->assertEquals(1, $this->model->insertSelect(['Column'], $this->driverMock->select()->columns('Column')));
    }

    public function testCanInsertSelect()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->insertSelect(['Column'], $this->driverMock->select()->columns('Column')));
    }

    public function testCanInsertSelectRaw()
    {
        $this->driverMock->method('execute')->willReturn(1);

        $this->assertEquals(1, $this->model->insertSelectRaw(['Column'], $this->driverMock->select()->columns('Column')));
    }

    public function testCanInsertForEach()
    {
        $this->driverMock->expects($this->exactly(2))->method('execute')->willReturn(1);

        $this->model->insertForEach('Column', ['foo', 'bar']);
    }

    public function testCanFetchPagination()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetchYield')->willReturnOnConsecutiveCalls([
            ['Id' => 1],
            ['Id' => 2],
        ]);

        $this->driverMock->method('column')->willReturn(20);

        $pagination = $this->model->pagination(10, 10);

        $this->assertEquals(10, $pagination->rowsLimit());

        $this->assertEquals(10, $pagination->rowsOffset());

        $this->assertEquals(20, $pagination->rowsTotal());
    }

    public function testCanIterateRows()
    {
        $this->mockDescribe();

        $this->model->appendRecord(['Id' => 1]);

        $this->model->appendRecord(['Id' => 2]);

        $ids = [1, 2];

        foreach ($this->model as $row) {
            $this->assertEquals(array_shift($ids), $row['Id']);
        }
    }

    public function testCanAppendRecordReference()
    {
        $this->mockDescribe();

        $record = ['Id' => 1];

        $isNew = false;

        $modified = [];

        $this->model->appendRecordRef($record, $isNew, $modified);

        $this->assertEquals(1, $this->model['Id']);

        $record['Id'] = 2;

        $this->assertEquals(2, $this->model['Id']);

        $this->model['Id'] = 3;

        $this->assertEquals(3, $modified['Id']);
    }

    public function testCanGetUniqueForFirstUnique()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
            ],
            'primary' => [
            ],
        ]);

        $driver = $this->driverMock;

        /** @var Model $model */
        $model = new class([], $driver) extends Model {
            protected $name = 'Table';

            protected $unique = ['Id'];
        };

        $this->assertEquals(['Id'], $model->firstUnique());
    }

    public function testCanThrowExceptionIfFirstUniqueNotFound()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
            ],
            'primary' => [
            ],
        ]);

        $driver = $this->driverMock;

        /** @var Model $model */
        $model = new class([], $driver) extends Model {
            protected $name = 'Table';
        };

        $this->expectException(\Exception::class);

        $model->firstUnique();
    }

    public function testCanCreateNewRowUsingCreateMethod()
    {
        $this->mockDescribe();

        $row = $this->model->create(['Id' => 1]);

        $this->assertEquals(1, $row['Id']);
    }

    public function testCanFetchRow()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1]);

        $this->assertEquals($record, $this->model->fetchRow()->firstToArray());
    }

    public function testCanFetchEmptyRow()
    {
        $this->assertEmpty($this->model->fetchRow());
    }

    public function testCanFetchRowOrFail()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn(['Id' => 1]);

        $this->assertEquals(['Id' => 1], $this->model->fetchRowOrFail()->firstToArray());
    }

    public function testCanThrowExceptionIfFetchRowFail()
    {
        $this->expectException(\Exception::class);

        $this->model->fetchRowOrFail();
    }

    public function testCanFetchNoRows()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetchYield')->willReturn((function () {
            if (false) {
                yield;
            }
        })());

        $this->assertEquals([], $this->model->fetchRows()->toArray());
    }

    public function testCanFetchRows()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetchYield')->willReturn((function () {
            yield ['Id' => 1];

            yield ['Id' => 2];
        })());

        $this->assertEquals([['Id' => 1], ['Id' => 2]], $this->model->fetchRows()->toArray());
    }

    public function testCanFetchRowsYield()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetchYield')->willReturn((function () {
            yield ['Id' => 1];

            yield ['Id' => 2];
        })());

        $rows = [['Id' => 1], ['Id' => 2]];

        $generator = $this->model->fetchRowsYield();

        $this->assertInstanceOf(\Generator::class, $generator);

        foreach ($generator as $row) {
            $this->assertEquals(array_shift($rows), $row->firstToArray());
        }

        $this->assertEmpty($rows);
    }

    public function testCanFind()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->find(1);

        $this->assertEquals($record, $row->firstToArray());
    }

    public function testCanFindOrFail()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->findOrFail(1);

        $this->assertEquals($record, $row->firstToArray());
    }

    public function testCanThrowExceptionIfFindFail()
    {
        $this->mockDescribe();

        $this->expectException(\Exception::class);

        $this->model->findOrFail(1);
    }

    public function testCanGetFirst()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->first($record);

        $this->assertEquals($record, $row->firstToArray());
    }

    public function testCanGetFirstOrFail()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->firstOrFail($record);

        $this->assertEquals($record, $row->firstToArray());
    }

    public function testCanThrowExceptionIfGetFirstFail()
    {
        $this->mockDescribe();

        $this->expectException(\Exception::class);

        $this->model->firstOrFail(['Id' => 1]);
    }

    public function testCanGetFirstFromFirstOrNew()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->firstOrNew($record);

        $this->assertFalse($row->isNew());
    }

    public function testCanGetNewFromFirstOrNew()
    {
        $this->mockDescribe();

        $row = $this->model->firstOrNew(['Id' => 1]);

        $this->assertTrue($row->isNew());
    }

    public function testCanGetFirstFromFirstOrCreate()
    {
        $this->mockDescribe();

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1]);

        $row = $this->model->firstOrCreate($record);

        $this->assertFalse($row->isNew());
    }

    public function testCanGetNewFromFirstOrCreate()
    {
        $this->mockDescribe();

        $row = $this->model->firstOrCreate(['Id' => 1]);

        $this->assertFalse($row->isNew());
    }

    public function testCanErase()
    {
        $this->mockDescribe();

        $this->driverMock->expects($this->once())->method('execute')->with('DELETE FROM `Table` WHERE `Id` = ?', [1]);

        $this->model->erase(1);
    }

    public function testCanTruncate()
    {
        $this->driverMock->expects($this->once())->method('truncate')->with('Table');

        $this->model->truncate();
    }

    public function testCanThrowExceptionIfCanNotCombinePrimaryKeys()
    {
        $this->mockDescribe();

        $this->expectException(\Exception::class);

        $this->model->find([1, 2]);
    }

    public function testCanThrowExceptionIfCanNotFetchRows()
    {
        $this->expectException(\Exception::class);

        $this->model->select(1)->fetchRow();
    }

    public function testCanFetchRowAndTransformToNullValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'string',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1, 'Foo' => '']);

        $row = $this->model->fetchRow();

        $this->assertNull($row['Foo']);
    }

    public function testCanFetchRowAndTransformToFloatValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'string',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => true,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1, 'Foo' => '1.1']);

        $row = $this->model->fetchRow();

        $this->assertTrue(is_float($row['Foo']));
    }

    public function testCanFetchRowAndTransformToDatetimeValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'text',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->model->setCast('Foo', 'datetime');

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1, 'Foo' => '01.01.2017 18:00:00']);

        $row = $this->model->fetchRow();

        $this->assertEquals('2017-01-01 18:00:00', $row['Foo']);
    }

    public function testCanFetchRowAndTransformToDateValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'text',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->model->setCast('Foo', 'date');

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1, 'Foo' => '01.01.2017']);

        $row = $this->model->fetchRow();

        $this->assertEquals('2017-01-01', $row['Foo']);
    }

    public function testCanFetchRowAndTransformToTimeValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'text',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->model->setCast('Foo', 'time');

        $this->driverMock->method('fetch')->willReturn($record = ['Id' => 1, 'Foo' => '18:00']);

        $row = $this->model->fetchRow();

        $this->assertEquals('18:00:00', $row['Foo']);
    }

    public function testCanFetchRowAndTransformToSystemNameValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'text',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->model->setCast('Foo', 'systemName');

        $this->assertEquals('foo-bar', $this->model->prepareValue('Foo', 'Foo Bar.', true));
    }

    public function testCanFetchRowAndTransformToBooleanValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'tinyint',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->model->setCast('Foo', 'boolean');

        $this->assertTrue($this->model->prepareValue('Foo', 1));
    }

    public function testCanFetchRowAndTransformToArrayValue()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
                'Foo' => [
                    'name'    => 'Foo',
                    'type'    => 'string',
                    'null'    => true,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => false,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => false,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);

        $this->model->setCast('Foo', 'array');

        $this->assertEquals($value = ['Foo' => 'bar'], $this->model->prepareValue('Foo', json_encode($value)));

        $this->assertEquals(json_encode($value), $this->model->prepareValue('Foo', $value, true));
    }

    protected function mockDescribe()
    {
        $this->driverMock->method('describe')->willReturn([
            'columns' => [
                'Id' => [
                    'name'    => 'Id',
                    'type'    => 'int',
                    'null'    => false,
                    'default' => null,
                    'extra'   => [
                        'isInt'         => true,
                        'isFloat'       => false,
                        'isNumeric'     => false,
                        'autoIncrement' => true,
                    ],
                ],
            ],
            'primary' => [
                'Id',
            ],
        ]);
    }
}
