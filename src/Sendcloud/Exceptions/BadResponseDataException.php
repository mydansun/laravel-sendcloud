<?php

namespace Mydansun\Sendcloud\Exceptions;

class BadResponseDataException extends \RuntimeException
{
    public function __construct($responseData)
    {
        $message = "Invalid response data from sendcloud API " . json_encode($responseData);
        parent::__construct($message, 0, null);
    }
}