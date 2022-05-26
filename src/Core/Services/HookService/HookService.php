<?php
    namespace Core\Services\HookService;

    use Core\Collections\Collection;
    use Core\Collections\Dictionary;

    class HookService implements IHookService
    {
        public Dictionary $_hooks;

        public function __construct()
        {
                $this->_hooks = new Dictionary();
        }

        public function addHook(string $name, callable $method)
        {
            $targetHooks = $this->_hooks->get($name);
            if ($targetHooks == null) {
                $targetHooks = new Collection();
                $this->_hooks->add($name, $targetHooks);                
            }
            $targetHooks->add($method);
        }

        public function callHooks(string $name, array $parameters)
        {
            $targetHooks = $this->_hooks->get($name);
            if ($targetHooks == null) {
                return;
            }

            $targetHooks->forEach(fn($method, $index) => $method($parameters));
        }
    }