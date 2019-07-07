<?php

namespace Mydansun\Sendcloud\Facades;

class Sendcloud extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sendcloud';
    }
}