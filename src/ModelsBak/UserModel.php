<?php
    namespace App\Models;

    use App\Models\DataModel;
    use Core\Database\IDatabase;

    class UserModel extends DataModel
    {
        public int $userId;

        public string $name;

        public string $email;

        public string $password;

        public string $salt;

        public ?string $reset;

        public function __construct(IDatabase $database)
        {
            parent::__construct($database);

            $this->table = "user";

            $this->column('userid', 'userId')->isType("int(11)")->primary()->autoIncrement()->notNull();
            $this->column('name')->isType("VARCHAR(100)")->notNull();
            $this->column('email')->isType("VARCHAR(256)")->notNull();
            $this->column('password')->isType("VARCHAR(128)")->notNull();
            $this->column('salt')->isType("VARCHAR(128)")->notNull();
            $this->column('reset')->isType("VARCHAR(64)");            

            if (ENVIRONMENT_CONFIG['mode'] == 'dev') {
                $this->checkTable();
            }
        }

        public function login($password)
        {
            $hash = hash('sha512', $password . $this->salt);
            return ($hash === $this->password) ? true : false;
        }
    }