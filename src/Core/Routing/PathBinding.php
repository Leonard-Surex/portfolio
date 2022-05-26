<?php
    
    namespace Core\Routing;

    use Core\Enum\RequestMethod;

    /**
     * PathBinding
     *
     * A binding between a address to a controller method
     */
    class PathBinding
    {
        /**
         * Incoming Path
         */
        public string $pathStr;

        /**
         * Target controller that contains the method
         */
        public string $controller;

        /**
         * Target method to call when this path is requested
         */
        public string $method;

        /**
         * Request method this route accepts
         */
        public RequestMethod $requestMethod;

    }

