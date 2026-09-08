<?php

namespace Jeffersongoncalves\Trustpilot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffersongoncalves\Trustpilot\Trustpilot
 */
class Trustpilot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-trustpilot';
    }
}
