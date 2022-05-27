<?php
    namespace Template;

    interface ITemplate
    {     
        public function __call(string $method, array $params): mixed;
     
        public function block($label): ?string;
     
        public static function __callStatic(string $method, array $params): mixed;
     
        public static function _render(string $script, $params=[], array $config = []): string;
     
        public function _irender(string $script, $params=[], array $config = []): string;
     
        public static function _renderFrom(string $template, array $params=[], array $config = []): string;
     
        public function _irenderFrom(string $template, array $params=[], array $config = []): string;
    }