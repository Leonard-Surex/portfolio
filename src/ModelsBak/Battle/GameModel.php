<?php
    namespace App\Models\Battle;

    use App\Models\DataModel;
    use App\Models\UserModel;
    use Core\Collections\Collection;
    use Core\Database\IDatabase;

    class GameModel extends DataModel
    {
        public int $gameId;

        public string $name;

        private Collection $players;

        public function __construct(IDatabase $database)
        {
            parent::__construct($database);

            $this->table = 'btl_game';

            $this->column('gameid', 'gameId')->isType('int(11)')->primary()->autoIncrement()->notNull();
            $this->column('name')->isType('VARCHAR(100)')->notNull();

            $player = new UserModel($database);
            $this->column('players')->manyToMany('game_user', $this->getProperty('gameid'), $player->getProperty('userid'));

            if (ENVIRONMENT_CONFIG['mode'] == 'dev') {
                $this->checkTable();
            }
        }
    }