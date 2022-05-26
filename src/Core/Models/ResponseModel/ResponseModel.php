<?php
    namespace Core\Models\ResponseModel;

    use Core\Collections\Collection;
    use Core\Collections\Dictionary;
    use Core\Models\ResponseModel\ResponseDataModel;

    class ResponseModel
    {
        public ResponseDataModel $preResponse;

        public ResponseDataModel $postResponse;

        public Collection $responses;

        public Dictionary $header;

        public function __construct()
        {
            $this->preResponse = new ResponseDataModel();
            $this->postResponse = new ResponseDataModel();
            $this->responses = new Collection();
            $this->header = new Dictionary();
        }

        public function addHeader(string $name, string $value): self
        {
            $header->add($name, $value);

            return $this;
        }

        public function addPreResponse(string $data): self
        {
            $this->preResponse->ResponseData = $data;

            return $this;
        }

        public function addPostResponse(string $data): self
        {
            $this->postResponse->ResponseData = $data;

            return $this;
        }

        public function addResponse(string $data): self
        {
            $response = new ResponseDataModel();
            $response->ResponseData = $data;
            $this->responses->add($response);

            return $this;
        }

        public function isJson(): self
        {
            return $this->header("Content-Type", "application/json");
        }

        public function isHtml(): self
        {
            return $this->header("Content-Type", "text/html");
        }

        public function redirect($url): self
        {
            return $this->header("Location", $url);
        }

    }