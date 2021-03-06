<?php
    namespace App\Controllers;

    use Template\ITemplate;

    /**
     * HomeController
     *
     * Example controller for your homepage. 
     */
    class HomeController extends BaseController 
    {
        private ITemplate $template;

        public function __construct(ITemplate $template) {
            $this->template = $template;
        }

        public function index($param)
        {  
            echo $this->template->renderFrom("home.html", $this->headerModel());    
        }

        public function pathNotFound()
        {
            echo "<strong>Error 404</strong><br/>Page not found.";
        }
    }