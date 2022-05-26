<?php

    namespace Core\Collections;

    use Core\Collections\Collection;
    use Core\Collections\Iterate;

    /**
     * Dictionary
     *
     * Improved collection container for key value pairs.
     */
    class Dictionary extends Iterate
    {
        /**
         * All keys
         */
        public Collection $keys;

        /**
         * All values
         */
        public Collection $values;

        public function __construct() {
            $this->keys = new Collection();
            $this->values = new Collection();
        }

        /**
         * count
         *
         * Returns the number of items in the Dictionary
         */
        public function count(): int {
            return $this->keys->count();       
        }
        
        /**
         * get
         *
         * Returns the value of the dictionary at an index
         * @param mixed $index          The index of the item to return
         */
        public function get(mixed $index): mixed {          
            $indexPos = $this->getKeyPosition($index);

            return !isset($indexPos) ? null : $this->values->get($indexPos);
        }

        /**
         * getKeyPosition
         *
         * Returns the numeric position of the key, or null if it does not exist
         * @param int $indexNumber      The numerical order of the key to return
         */
        protected function getKeyPosition(mixed $index): ?int
        {
            unset($calculatedIndex);
            $this->keys->where(function ($item, $indexValue) use ($index, &$calculatedIndex) {
                if ($item == $index) {
                    $calculatedIndex = $indexValue;
                }
            });

            return $calculatedIndex;
        }


        /**
         * getIndex
         *
         * Get the index at a numerical position in the Dictionary
         * @param int $indexNumber      The numerical order of the key to return
         */
        protected function getIndex(int $indexNumber): mixed
        {
            return $this->keys->get($indexNumber);        
        }

        /**
         * addFrom
         *
         * Add an item from an Iterate of the same type to this one
         * @param mixed $index          The index of the item to add
         * @param self $source          The source Iterate
         */
        protected function addFrom(mixed $index, Iterate $source): static
        {
            if ($this->contains($index)) {
                throw new Exception('Can not add duplicate key to Dictionary.');
            }
            $this->add($index, $source->get($index));

            return $this;
        }

        /**
         * add
         *
         * Add an item to the bottom of a collection.
         * @param mixed $item           The item to add to the collection.
         */
        public function add(mixed $key, mixed $item): static
        {
            if ($this->keys->contains($key)) {
                throw new Exception('This key already exists in this Dictionary.');
            }

            $this->keys->add($key);
            $this->values->add($item);
            return $this;
        }

        /**
         * __toString
         *
         * returns a string representation of this collection
         */
        public function __toString(): string
        {
            return json_encode("[{$this->keys},{$this->values}]");
        }

    }