<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\ParameterBag;

class JsonRequest extends ParameterBag
{
    public function __construct()
    {
        parent::__construct($this->processJsonRequest());
    }

    private function processJsonRequest(): array
    {
        $payload = trim(file_get_contents('php://input')) ?: '[]';
        $result = json_decode($payload, true);
        return json_last_error() ? [] : $result;
    }
}
