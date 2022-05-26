<?php
    namespace Core\Services\HookService;

    interface IHookService
    {
        function addHook(string $name, callable $method);

        function callHooks(string $name, array $parameters);
    }