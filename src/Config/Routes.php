<?php
    use Core\Routing\Router;
    use Core\Enum\RequestMethod;

    // Home Controller
    Router::add("", "Home", "index", RequestMethod::All);

    Router::add404("Home", "pathNotFound");