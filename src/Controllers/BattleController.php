<?php
    namespace App\Controllers;

    use Template\ITemplate;
    use Core\Database\Database;
    use App\Services\BattleService\IBattleService;
    use App\Models\UserModel;
    use App\Models\Battle\GameModel;

    /**
     * HomeController
     *
     * Example controller for your homepage. 
     */
    class BattleController extends BaseController 
    {
        private ITemplate $template;

        private IBattleService $battleService;

        public function __construct(ITemplate $template, IBattleService $battleService) {
            $this->template = $template;
            $this->battleService = $battleService;
        }

        public function index($param)
        {

            $game = new GameModel(new Database());
            $game = $game->fetchAll('gameid = ?', [1])->first();

            echo $this->template->renderFrom("Battle\play.html", $this->headerModel());

            $players = $game->players;
        }
    }