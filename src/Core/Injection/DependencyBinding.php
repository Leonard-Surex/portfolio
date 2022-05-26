<?php
    namespace Core\Injection;

    use Core\Collections\Dictionary;

    /**
     * DependencyBinding
     *
     * A binding between an interface and a concrete class or object
     */
    class DependencyBinding {
        /**
         * Concrete class to target
         */
        public string $target;

        /**
         * The interface to replace
         */
        public string $source;

        /**
         * Is this a singleton
         */
        public bool $isSingleton;

        /**
         * Singleton object instance
         */
        public $instance;

        /**
         * Additional parameters to pass to to the constructor
         */
        public Dictionary $values;

        /**
         * __construct
         *
         * Class constructor
         */
        public function __construct() {
            $values = new Dictionary();
        }

        /**
         * singleton
         *
         * This binding will use the same instance of the concrete object
         */
        public function singleton(): DependencyBinding {
            $this->isSingleton = true;
            return $this;
        }

        /**
         * specify
         *
         * Sets a specific instances to use is this binding is for a singleton
         * @param string    $instance       The object to bind
         */
        public function specify(mixed $instance): DependencyBinding {
            $this->instance = $instance;
            return $this;
        }

        /**
         * value
         *
         * Sets parameters to be used during the creation of this bindings concrete object
         * @param string $key               The parameter to be set
         * @param mixed $value              The value to set the parameter to
         */
        public function value(string $key, mixed $value): DependencyBinding {
            $this->values->add($key, $value);
            return $this;
        }

    }