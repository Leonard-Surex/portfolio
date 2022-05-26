<?php
    use Core\Injection\Injector;

    Injector::bind("Template\\ITemplate", "Template\\Template")->singleton();