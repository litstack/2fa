<?php

namespace Litstack\TwoFA\Support;

use Illuminate\Support\Facades\Facade;

class TwoFA extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lit.2fa';
    }
}
