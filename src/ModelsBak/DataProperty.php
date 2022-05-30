<?php
    namespace App\Models;

    use Core\Collections\Dictionary;
    use App\Models\DataModel;

    class DataProperty {
        public string $name;

        public string $property;

        public string $type;

        public Dictionary $attributes;

        public string $joinTable;

        public DataProperty $source;

        public DataProperty $target;

        public bool $connection = false;

        public DataModel $parent;

        public function __construct(string $name, DataModel $table, string $property, DataModel $parent)
        {
            $this->name = $name;
            $this->property = $property;
            $this->table = $table;
            $this->parent = $parent;
            $this->attributes = new Dictionary();
        }

        public function isType(string $type)
        {
            $this->type = $type;

            return $this;
        }

        public function setAttribute(string $attributeName, mixed $value)
        {
            $this->attributes->add($attributeName, $value);

            return $this;
        }

        public function getAttribute(string $attributeName)
        {
            return $this->attributes->get($attributeName);
        }

        public function primary(): self
        {
            $this->attributes->add('KEY', 'PRIMARY');

            return $this;
        }

        public function autoIncrement(): self
        {
            $this->attributes->add('AUTO_INCREMENT', '');

            return $this;
        }

        public function notNull(): self
        {
            $this->attributes->add('NULL', 'NOT');

            return $this;
        }

        public function default($default): self
        {
            $this->attributes->add('DEFAULT', $default);

            return $this;
        }

        public function refersTo(DataProperty $target): self
        {
            $this->target = $target;

            return $this;
        }

        public function manyToMany(string $joinTable, DataProperty $target, DataProperty $source): self
        {
            $this->joinTable = $joinTable;
            $this->target = $target;
            $this->source = $source;
            $this->connection = true;

            return $this;
        }
    }