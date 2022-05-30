<?php
    namespace Template\Operations;

    use Template\MemoryManager\IMemoryManager;
    use Template\MemoryManager\MemoryManager;
    use Template\Operations\Operation;
    use Template\Processor\IProcessor;
    use Template\Processor\Processor;
    use Template\Parser\LineParser;
    use Template\Parser\TokenParser;


    class IncludeOp extends Operation
    {
        public function process(IMemoryManager $memoryManager, IProcessor $processor): mixed
        {
            $token = $memoryManager->getToken();
            if ($token !== 'include') {
                return null;
            }                                   
            $memoryManager->progress();

            $templateName = $this->getValue($memoryManager, $processor);
            $memoryManager->progress();

            if ($memoryManager->getToken() == "using") {
                $memoryManager->progress();
                $params = $this->getValue($memoryManager, $processor);
            } else {
                $params = [];
            }

            $fileManager = $processor->getFileManager();
            $includedTemplate = $fileManager->read($templateName);

            if ($includedTemplate != null) {
                $lines = LineParser::parse($includedTemplate);

                $code = $code = [];
                foreach ($lines as $line)
                {                
                    $code = array_merge($code, TokenParser::parse($line));
                }

                $childMemoryManager = new MemoryManager($code, $params);            
                $childProcessor = new Processor($childMemoryManager, $fileManager, $processor->getConfig());
                
                $data =  $childProcessor->run();                        

                return $data;
            }
        }
    }