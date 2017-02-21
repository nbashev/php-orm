<?php

namespace Greg\Orm;

use Greg\Support\Arr;

trait TableTrait
{
    use TableSqlTrait;

    protected $prefix;

    protected $name;

    protected $alias;

    protected $label;

    protected $columns = false;

    protected $fillable = '*';

    protected $guarded = [];

    protected $primary = false;

    protected $unique;

    protected $autoIncrement = false;

    protected $nameColumn;

    protected $casts = [];

    public function alias(): ?string
    {
        return $this->alias;
    }

    public function name(): string
    {
        if (!$this->name) {
            throw new \Exception('Table name is required in model.');
        }

        return $this->name;
    }

    public function fullName(): string
    {
        return $this->prefix . $this->name();
    }

    public function label(): ?string
    {
        return $this->label;
    }

    public function columns(): array
    {
        if ($this->columns === false) {
            $this->loadSchema();
        }

        return (array) $this->columns;
    }

    public function hasColumn(string $name): bool
    {
        if ($this->columns === false) {
            $this->loadSchema();
        }

        return isset($this->columns[$name]);
    }

    public function column(string $name): array
    {
        $this->validateColumn($name);

        return $this->columns[$name];
    }

    public function fillable()
    {
        return $this->fillable === '*' ? $this->fillable : (array) $this->fillable;
    }

    public function guarded()
    {
        return $this->guarded === '*' ? $this->guarded : (array) $this->guarded;
    }

    public function primary(): array
    {
        if ($this->primary === false) {
            $this->loadSchema();
        }

        return (array) $this->primary;
    }

    public function unique(): array
    {
        $keys = (array) $this->unique;

        foreach ($keys as &$key) {
            $key = (array) $key;
        }
        unset($key);

        return $keys;
    }

    public function firstUnique(): array
    {
        if ($name = $this->autoIncrement()) {
            return [$name];
        }

        if ($primary = $this->primary()) {
            return $primary;
        }

        if ($unique = (array) Arr::first($this->unique)) {
            return $unique;
        }

        throw new \Exception('No unique keys found in `' . $this->name() . '`.');
    }

    public function autoIncrement(): ?string
    {
        if ($this->autoIncrement === false) {
            $this->loadSchema();
        }

        return $this->autoIncrement;
    }

    public function nameColumn(): ?string
    {
        return $this->nameColumn;
    }

    public function casts(): array
    {
        return $this->casts;
    }

    public function cast(string $name): ?string
    {
        return $this->casts[$name] ?? null;
    }

    public function selectPairs()
    {
        if (!$columnName = $this->nameColumn()) {
            throw new QueryException('Undefined column name for table `' . $this->name() . '`.');
        }

        $instance = $this->selectQueryInstance();

        $instance->selectQuery()
            ->columnConcat($this->firstUnique(), ':', 'key')
            ->column($columnName, 'value');

        return $instance;
    }

    public function truncate()
    {
        return $this->driver()->truncate($this->fullName());
    }

    public function erase($key)
    {
        return $this->deleteQueryInstance()->whereMultiple($this->combineFirstUnique($key))->delete();
    }

    public function lastInsertId()
    {
        return $this->driver()->lastInsertId();
    }

    public function describe()
    {
        return $this->driver()->describe($this->fullName());
    }

    protected function validateColumn(string $name)
    {
        if ($this->columns === false) {
            $this->loadSchema();
        }

        if (!isset($this->columns[$name])) {
            throw new \Exception('Column `' . $name . '` not found in table `' . $this->name() . '`.');
        }

        return $this;
    }

    protected function combineFirstUnique($value)
    {
        $value = (array) $value;

        $keys = $this->firstUnique();

        if (count($keys) !== count($value)) {
            throw new \Exception('Unique keys count should be the same as values count.');
        }

        return array_combine($keys, $value);
    }

    protected function setColumn(string $name, string $type, bool $allowNull = false, ?string $default = null, array $extra = [])
    {
        $this->columns[$name] = [
            'name'      => $name,
            'type'      => $type,
            'allowNull' => $allowNull,
            'default'   => $default,
            'extra'     => $extra,
        ];

        return $this;
    }

    protected function loadSchema()
    {
        $schema = $this->describe();

        if ($this->columns === false) {
            $this->columns = $schema['columns'];
        }

        if ($this->primary === false) {
            $this->primary = $schema['primary'];
        }

        if ($this->autoIncrement === false) {
            $this->autoIncrement = null;

            foreach ($schema['columns'] as $column) {
                if ($column['extra']['autoIncrement'] ?? false) {
                    $this->autoIncrement = $column['name'];

                    break;
                }
            }
        }

        return $this;
    }
}
