<?php
    namespace Template\Processor;

    use Template\FileManager\IFileManager;
    use Template\MemoryManager\IMemoryManager;
    use Template\Operations\ForEachOp;
    use Template\Operations\IfOp;
    use Template\Operations\IncludeOp;
    use Template\Operations\Block;
    use Template\Operations\Show;
    use Template\Operations\Value;
    use Template\Processor\IProcessor;

    class Processor implements IProcessor
    {
        private IMemoryManager $memoryManager;

        private IFileManager $fileManager;

        private array $config;

        public function __construct(IMemoryManager $memoryManager, IFileManager $fileManager, array $config = [])
        {
            $this->memoryManager = $memoryManager;
            $this->fileManager = $fileManager;
            $this->config = $config;            

            $memoryManager
                    ->addOperation(new Value())
                    ->addOperation(new Block())
                    ->addOperation(new Show())
                    ->addOperation(new IfOp())
                    ->addOperation(new ForEachOp())
                    ->addOperation(new IncludeOp());
        }

        public function next(): mixed
        {
            foreach ($this->memoryManager->getOperations() as $operation)
            {
                if ($this->memoryManager->done()) {

                    return "";
                }

                $result = $operation->process($this->memoryManager, $this);

                if ($result !== null) {

                    return $result;
                }
            }

            return null;
        }

        public function run(): mixed        
        {
            $result = "";
            do
            {
                $singleResult = $this->next();
                if ($singleResult === null) {
                    $this->memoryManager->progress();
                } else {
                    if (!is_array($result)) {
                        if (is_array($singleResult)) {
                        } else {
                            $result .= $singleResult;
                        }                        
                    } else {
                    }
                }
            } while (!$this->memoryManager->done());

            return $result;
        }

        public function getFileManager(): IFileManager
        {
            return $this->fileManager;
        }

        public function getConfig(): array
        {
            return $this->config;
        }

        public function getMemoryManager() : IMemoryManager
        {
            return $this->memoryManager;
        }
    }