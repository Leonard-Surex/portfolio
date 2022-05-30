<?php
    use Core\Routing\Router;
    use Core\Enum\RequestMethod;

    Router::add404("Home", "pathNotFound");
    
    // Home Controller
    Router::add("", "Home", "index", RequestMethod::All);

    // User Controller
    Router::add("/login", "User", "index", RequestMethod::All);

    // Battle Controller
    Router::add("/battle", "Battle", "index", RequestMethod::All);
    Router::add("/battle/findGame", "Battle", "findGame", RequestMethod::Get);
    Router::add("/battle/playRound/{game}", "Battle", "playRound", RequestMethod::Post);
