<?php
    namespace App\Models;

    use Core\Collections\Dictionary;

    class DataProperty
    {
        public string $name;

        public string $type;

        public string $property;

        public Dictionary $attributes;

        public string $parentType;

        public function __construct(string $name, string $property, string $parentType)
        {
            $this->name = $name;
            $this->property = $property;
            $this->parentType = $parentType;

            $this->attributes = new Dictionary();
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

        public function isType(string $type)
        {
            $this->type = $type;

            return $this;
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
    }