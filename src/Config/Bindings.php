<?php
    use Core\Injection\Injector;

    Injector::bind("Template\\ITemplate", "Template\\Template")
            ->singleton()
            ->value("config", [
                    'template_path' => getcwd() . "\\src\\Templates",
                    'sandbox' => false,
            ]);
    
    Injector::bind("App\Services\BattleService\IBattleService", "App\Services\BattleService\BattleService")
            ->singleton();