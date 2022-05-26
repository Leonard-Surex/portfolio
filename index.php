<?php    
    require_once('vendor/autoload.php');
    require_once('src/Config/Routes.php');
    require_once('src/Config/Bindings.php');

    use Core\Routing\Router;

    Router::callPath();