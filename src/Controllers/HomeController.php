<?php
    namespace App\Controllers;

    /**
     * HomeController
     *
     * Example controller for your homepage. 
     */
    class HomeController extends BaseController 
    {
        public function index($param)
        {  
            echo "hello!!!<br/>";     
        }

        public function pathNotFound()
        {
            echo "<strong>Error 404</strong><br/>Page not found.";
        }
    }