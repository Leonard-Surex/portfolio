<?php    
    require_once('vendor/autoload.php');
    require_once('src/Config/Routes.php');
    require_once('src/Config/Bindings.php');
    require_once('src/Config/Database.php');

    use Core\Routing\Router;

    session_start();    
    Router::callPath();