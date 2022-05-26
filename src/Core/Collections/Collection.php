<?php

    namespace Core\Collections;

    use Core\Collections\Iterate;

    /**
     * Collection
     *
     * Improved collection container.
     */
    class Collection extends Iterate
    {
        /**
         * All values
         */
        public array $_items = [];
                

        /**
         * count
         *
         * returns the number of items in the collection
         * @param mixed $item           The item to add to the collection.
         */
        public function count(): int {
            return count($this->_items);
        }

        /**
         * add
         *
         * Add an item to the bottom of a collection.
         * @param mixed $item           The item to add to the collection.
         */
        public function add(mixed $item): static
        {
            array_push($this->_items, $item);
            return $this;
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
            $this->add($source->get($index));
            return $this;
        }

        /**
         * getIndex
         *
         * Returns the real index based on a numerical index
         */
        protected function getIndex(int $indexNumber): mixed
        {
            return $indexNumber;
        }        

        /**
         * set
         *
         * Overwrite a value at a specific location in the Collection
         * @param int $index            The index of the item to set
         * @param mixed $value          The value to set
         */
        public function set(int $index, mixed $value): static
        {            
            $this->_items[$index] = $value;        
        }

        /**
         * get
         *
         * Returns the value in the collection at the index location
         * @param int $index            The index of the item to fetch.
         */
        public function get(mixed $index): mixed
        {            
            return $this->_items[$index];     
        }

        /**
         * first
         *
         * fetches the first item in the collection or null if the collection is empty.
         */
        public function first(): mixed
        {
            if (count($this->_items) == 0) {
                return null;
            }
            return $this->_items[0];
        }

        /**
         * last
         *
         * fetches the last item in the collection or null if the collection is empty.
         */
        public function last(): mixed
        {
            $count = count($this->_items);
            if ($count == 0) {
                return null;
            }
            return $this->_items[$count-1];
        }

        /**
         * __debugInfo
         *
         * returns debug info for the collection
         */
        public function __debugInfo(): array
        {
            return $this->_items;
        }

        /**
         * __toString
         *
         * returns a string representation of this collection
         */
        public function __toString(): string
        {
            return json_encode($this->_items);
        }

        /**
         * array
         *
         * returns the collection array
         */
        public function array(): array
        {
            return $this->_items;
        }

        /**
         * join
         *
         * Returns a collection based on combining collections
         */        
        public static function join(self $arr1, self $arr2, callable $method): static
        {
            $result = new Collection();

            $arr1->forEach(function($item, $index) use ($arr2, $method) {
                $arr2->forEach(function($item2, $index2) use ($method) {
                    if ($method($item, $item2, $index, $index2)) {
                        $binding = new Collection();
                        $binding->add($item)->add($item2);
                        $result->add($binding);
                    }
                });
            });
        }

        /**
         * toCollection
         *
         * Convert an array to a Collection
         * @param array $array          The array to convert
         */
        public static function toCollection(array $array): Collection
        {
            $collection = new Collection();
            foreach ($array as $item) {
                $collection->add($item);
            }
            return $collection;
        }
    }