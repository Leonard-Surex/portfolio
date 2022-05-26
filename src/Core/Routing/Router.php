<?php
    
    namespace Core\Routing;

    use Core\Collections\Collection;
    use Core\Collections\Dictionary;
    use Core\Enum\RequestMethod;
    use Core\Injection\Injector;
    use Core\Routing\PathBinding;

    /**
     * Router
     *
     * Route a call to a path to a controller method.
     */
    class Router
    {
        /**
         * Collection of Paths to Route to
         */
        private static Collection $paths;

        /**
         * Collection of Special Paths to Route to
         */
        private static Dictionary $special;

        /**
         * add
         *
         * Adds a path binding to connect a path to a controller
         * @param string $pathStr                       The path to bind
         * @param string $controller                    The controller to route to
         * @param string $method                        The method to route to
         * @param RequestMethod $requestMethod          Valid request methods to this route
         */
        public static function add(string $pathStr, string $controller, string $method, RequestMethod $requestMethod=RequestMethod::All) {
            self::init();

            $path = new PathBinding();
            $path->pathStr = $pathStr;
            $path->controller = $controller;
            $path->method = $method;
            $path->requestMethod = $requestMethod;
            if (class_exists("App\\Controllers\\" . $controller . "Controller")) {
                self::$paths->add($path);
                return;
            } else {
                echo "Controller App\\Controllers\\{$controller}Controller not found.<br />";
                die;
            }
        }

        /**
         * add404
         *
         * Adds a path binding to route unknown paths to
         * @param string $controller                    The controller to route to
         * @param string $method                        The method to route to
         */
        public static function add404(string $controller, string $method) {
            self::init();

            $path = new PathBinding();
            $path->controller = $controller;
            $path->method = $method;

            self::$special->add('404', $path);
        }

        /**
         * callPath
         *
         * Adds a path binding to route unknown paths to
         */
        public static function callPath() {
            self::init();

            $requestPath = parse_url($_SERVER['REQUEST_URI']);

            $found = false;

            self::$paths->forEach(function($path, $index) use ($requestPath, &$found) {
                if (!$found) {
                    $found = self::compare($requestPath["path"], $path->pathStr);
                }
            });

            if (!isset($found) || !$found) {
                self::show404();
            }
        }

        /**
         * compare
         *
         * returns true if this path matches this route
         */
        private static function compare(string $actualPath, string $storedPath): bool {

            $actualTokens = explode("/", trim($actualPath, '\\/'));
            $storedTokens = explode("/", trim($storedPath, '\\/'));

            $params = array();

            if (sizeof($actualTokens) != sizeof($storedTokens)) {
                return false;
            }

            foreach($actualTokens as $index => $actualToken) {
                if (strlen($storedTokens[$index]) == 0 || $storedTokens[$index][0] != "{") {
                    if (strcmp($storedTokens[$index], trim($actualToken, '/\\')) != 0) {
                        return false;
                    }
                } else {
                    $trimmed = trim($storedTokens[$index], '{}');
                    $params[$trimmed] = $actualToken;
                }
            }

            $foundPath = null;

            self::$paths->foreach(function($path, $index) use (&$foundPath, $storedPath) {
                if ($path->requestMethod == RequestMethod::All || $path->requestMethod == $_SERVER['REQUEST_METHOD']) {
                    if (trim($path->pathStr, '\\/') == trim($storedPath, '\\/')) {
                        $foundPath = $path;
                    }
                }
            });

            if ($foundPath == null) {
                show404();                
                return true;
            }

            $controllerName = "App\\Controllers\\" . $foundPath->controller . "Controller";
            $controller = Injector::new($controllerName);

            if (!method_exists($controller, $foundPath->method)) {
                // method not found
                echo "Method {$foundPath->method} not found in controller {$foundPath->controller}.";
                die;
            };

            $method = $foundPath->method;
            $controller->$method($params);

            return true;
        }

        private static function show404() {
            $path = self::$special->get('404');
            if ($path == null) {
                echo "Error 404<br/>Page not found.";
            }

            $controllerName = "App\\Controllers\\" . $path->controller . "Controller";
            $controller = Injector::new($controllerName);
            $method = $path->method;
            $controller->$method([]);
        }

        private static function init() {
            if (!isset(self::$paths) || !isset(self::$special)) {
                self::$paths = new Collection();
                self::$special = new Dictionary();
            }
        }

    }