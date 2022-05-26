<?php
    namespace Core\Injection;

    use Core\Collections\Collection;
    use Core\Injection\DependencyBinding;

    /**
     * Injector
     *
     * Inversion of control service
     */
    class Injector {
        /**
         * Collection of bindings to use
         */
        private static Collection $bindings;

        /**
         * new
         *
         * Returns the requested object
         * @param string $type              The interface being requested
         */
        public static function new(string $type): mixed {
            $actualType = self::GetByType($type);
            $class = new \ReflectionClass($actualType ?? $type);
            $constructor = $class->getConstructor();

            if ($constructor != null) {
                $params = $constructor->getParameters();
                $paramObjects = new Collection();
                foreach ($params as $index => $param) {
                    $byValue = self::GetByValue($param->name, $type);
                    if ($byValue == null) {
                        $paramObjects->add(self::New($param->getType()->__toString()));
                    } else {
                        $paramObjects->add($byValue);
                    }
                }

                return $class->newInstanceArgs($paramObjects->array());
           } else {

               return $class->newInstanceArgs([]);
           }
        }

        /**
         * bind
         *
         * Binds an interface type to a concrete type
         * @param string $target             Interface to request
         * @param string $source             Concrete class to return
         */
        public static function bind(string $target, string $source): DependencyBinding {
            if (!isset(self::$bindings)) {
                self::$bindings = new Collection();
            }            
            $binding = new DependencyBinding();
            $binding->target = $target;
            $binding->source = $source;
            $binding->singleton = false;
            unset($binding->instance);
            self::$bindings->add($binding);

            return $binding;
        }
        
        /**
         * GetByType
         *
         * Returns the name of a concrete
         * @param string $target              The interface being requested
         */
        public static function GetByType(string $target): ?string {
            $type = self::$bindings->where(fn($binding, $index) => $binding->target == $target)->first();
            if ($type != null) {
                return $type->source;
            }
            return null;
        }
    
        /**
         * GetByType
         *
         * Returns a parameter to be called with a concrete constructor
         * @param string $key           Which parameter
         * @param string $target        The interface the parameter is being requested for
         */
        private static function GetByValue(string $key, string $target): mixed {            
            foreach (self::$bindings as $index => $binding) {
                if (strcmp($binding->target, $target) == 0) {
                    if (isset($binding->values[$key])) {
                        return $binding->values[$key];
                    }                    
                }
            }

            return null;
        }
    }