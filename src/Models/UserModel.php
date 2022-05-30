<?php
    namespace App\Models;

    use Core\Database\TableValidator;

    class UserModel extends DataModel
    {
        protected static string $table = "user";

        public function __construct()
        {
            $this->column('userid', 'userId')->isType('int(11)')->primary()->autoIncrement()->notNull();
            $this->column('name')->isType('VARCHAR(100)')->notNull();
            $this->column('email')->isType('VARCHAR(256)')->notNull();
            $this->column('password')->isType('VARCHAR(128)')->notNull();
            $this->column('salt')->isType('VARCHAR(128)')->notNull();
            $this->column('reset')->isType('VARCHAR(64)');            

            if (ENVIRONMENT_CONFIG['mode'] == 'dev') {
                TableValidator::checkTable($this);
            }
        }

        public function login($password)
        {
            $hash = hash('sha512', $password . $this->salt);
            return ($hash === $this->password) ? true : false;
        }
    }