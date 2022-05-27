<?php
    use Core\Injection\Injector;

    Injector::bind("Template\\ITemplate", "Template\\Template")
            ->singleton()
            ->value("config", [
                    'template_path' => getcwd() . "\\src\\Templates",
                    'sandbox' => false,                
            ]);