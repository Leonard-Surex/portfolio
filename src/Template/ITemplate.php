<?php
    namespace Template;

    use Template\FileManager\FileManager;
    use Template\FileManager\IFileManager;
    use Template\MemoryManager\MemoryManager;
    use Template\MemoryManager\IMemoryManager;
    use Template\Parser\LineParser;
    use Template\Parser\TokenParser;
    use Template\Processor\Processor;
    use Template\Processor\IProcessor;

    interface Template
    {     
        public function __call(string $method, array $params): mixed;
     
        public function block($label): ?string;
     
        public static function __callStatic(string $method, array $params): mixed;
     
        public static function _render(string $script, $params=[], array $config = []): string;
     
        public function _irender(string $script, $params=[], array $config = []): string;
     
        public static function _renderFrom(string $template, array $params=[], array $config = []): string;
     
        public function _irenderFrom(string $template, array $params=[], array $config = []): string;
    }