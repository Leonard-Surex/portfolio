<?php
    namespace App\Controllers;

    use Core\Database\Database;
    use App\Models\UserModel;
    use Template\ITemplate;

    /**
     * UserController
     */
    class UserController extends BaseController 
    {
        private ITemplate $template;

        public function __construct(ITemplate $template) {
            $this->template = $template;
        }

        public function index($param)
        {
            unset($_SESSION['user']);
            $response = $this->headerModel();

            if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
              $user = new UserModel();
              
              $user = $user->fetchAll("email=?", [$_POST['email']])->first();

              if ($user !== null && $user->login($_POST['password'])) {
                $_SESSION['user'] = $user;
                header('Location: ' . '/');
                die;
              }
              $response['message'] = 'Incorrect email or password.';
            }

            echo $this->template->renderFrom("/user/login.html", $response);
        }
    }