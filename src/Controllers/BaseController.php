<?php
    namespace App\Controllers;

    /**
     * BaseController
     *
     * BaseController provides a convenient place for loading components
     * and performing functions that are needed by all your controllers.
     */
    class BaseController 
    {
        public function headerModel()
        {
            $response = [];

            $response['status'] = [];
            $response['status']['loggedIn'] = "no";

            if (isset($_SESSION['user'])) {
                $response['status']['loggedIn'] = "yes";
                $response['status']['user'] = $_SESSION['user'];
            }

            return $response;
        } 
    }