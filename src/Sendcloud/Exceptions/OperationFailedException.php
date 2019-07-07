<?php

namespace Mydansun\Sendcloud\Exceptions;

class OperationFailedException extends \Exception
{
    protected $returnMessage;
    protected $statusCode;

    public function __construct($statusCode, $returnMessage)
    {
        $this->statusCode = intval($statusCode);
        $this->returnMessage = strval($returnMessage);
        parent::__construct("Sendcloud Failed [{$this->statusCode}]：{$this->returnMessage}", 0, null);
    }

    /**
     * @return string
     */
    public function getReturnMessage(): string
    {
        return $this->returnMessage;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}