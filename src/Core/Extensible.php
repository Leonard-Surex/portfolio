<?php
    namespace Core;

    /**
     * Extensible
     *
     * Extensible provides a simple way to add extension methods to a class.
     */

    namespace Core;

    trait Extensible
    {
        private static $methodTable = array();

        /**
         * __call
         *
         * Magic method called when a method is not found in the class
         * @param string    $name           The name of the method being called.
         * @param array     $args           Parameters being passed to the called method.
         */
        public function __call($name, $args)
        {
            self::methodDispatcher($this, $name, $args);
        }

        /**
         * addMethod
         *
         * Add additional methods to a class with addMethod.
         * @param string    $methodName     The name of the method being added.
         * @param Clousure  $method         The method being added to the class.
         */
        public static function addMethod(string $methodName, Clousure $method)
        {
            $class = get_called_class();        
            $table =& self::$methodTable;
            if (!array_key_exists($class, $table))        
                $table[$class] = array();                

            $table[$class][$methodName] = $method;
        }

        /**
         * methodDispatcher
         *
         * Calls an extended method.
         * @param Extensible    $instance       The class the method being called is attached to.
         * @param string        $name           The name of the method being called.
         * @param array         $args           Parameters being passed to the called method.
         */
        private static function methodDispatcher(Extensible $instance, string $name, array $args)
        {
            $table =& self::$methodTable;        
            $class = get_class($instance);
            do {
                if (array_key_exists($class, $table) 
                        && array_key_exists($name, $table[$class]))
                    break;

                $class = get_parent_class($class);
            } while ($class !== false);

            if ($class === false)
                throw new Exception("Method not found");

            $func = $table[$class][$name];
            array_unshift($args, $instance);

            return call_user_func_array($func, $args);
        }
    }