<?php
    namespace Core\Collections;

    abstract class Iterate
    {
        public function count(): int
        {
            throw new \Exception('Count not implemented in Iterate.');          
        }

        public function set(int $index, mixed $value) : self
        {
            throw new \Exception('set not implemented in Iterate.');
        }

        public function get(mixed $index): mixed
        {
            throw new \Exception('get not implemented in Iterate.');          
        }

        protected function getIndex(int $indexNumber): mixed
        {
            throw new \Exception('getIndex not implemented in Iterate.');          
        }

        protected function addFrom(mixed $index, Iterate $source): self
        {
            throw new \Exception('addFrom not implemented in Iterate.');          
        }

        /**
         * forEach
         *
         * Iterate through a collection against a function
         * @param callable $method      The function to call against each item in the collection.
         */
        public function forEach(callable $method): self
        {
            for ($i=0; $i<$this->count(); $i++) {
                $index = $this->getIndex($i);
                $c = $this->get($index);
                $method($this->get($index), $index, $this);
            }

            return $this;
        }

        /**
         * where
         *
         * Return a Collection filtered using the passed function. 
         * @param callable $method      The filter function that takes in the collection object and returns true if it is kept in the final list.
         */
        public function where(callable $method): self
        {            
            $list = new static();

            $this->forEach(function ($item, $index) use ($method, $list) {
                if ($method($item, $index, $this)) {                   
                    $list->addFrom($index, $this);
                }
            });

            return $list;
        }

        /**
         * indexOf
         *
         * returns the index of if the iterate contains the value, otherwise returns null
         * @param mixed $value          The value to be searched for in the Iterate
         */
        public function indexOf(mixed $value): mixed
        {
            $result = null;

            $this->forEach(function ($item, $index) use ($value, $result) {
                if ($item = $value) {
                    $result = $value;
                }
            });

            return $result;
        }

        /**
         * contains
         *
         * returns the true if the iterate contains the value, otherwise returns false
         * @param mixed $value          The value to be searched for in the Iterate
         */
        public function contains(mixed $value): mixed
        {
            return $this->indexOf($value) == null ? false : true;
        }


        /**
         * map
         *
         * Returns a list with an item for each item in the collection.
         * @param callable $method      The method that determines what value is returned for the given collection item.
         */
        public function map(callable $method): self
        {
            $list = new static();
            $this->forEach(function ($item, $index) use ($method, $list) {                
                $list->addFrom($index, $this);
                $list->set(getIndex($list->count() - 1), $method($item, $index, $this));
            });

            return $list;
        }

        /**
         * concat
         *
         * Appends a collection onto this collection.
         * @param Collection $list      The list to concatinate to this one.
         */
        public function concat(self $list): self
        {
            $list->forEach(function($item, $index) use ($list) {
                $this->addFrom($index, $list);
            });

            return $this;
        }

        /**
         * reduce
         *
         * Returns a value calculated with each item in the 
         * @param callable $method      The method that determines what value is returned for the given collection item.
         */
        public function reduce(callable $method, mixed $initial=0): mixed
        {
            $sum = $initial;
            
            $this->forEach(function ($item, $index) use (&$sum) {
                $sum = $method($sum, $item, $index, $this);
            });

            return $sum;
        }


    }