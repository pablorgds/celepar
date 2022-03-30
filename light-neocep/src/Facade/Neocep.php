<?php

namespace Celepar\Light\Neocep\Facade;

use Illuminate\Support\Facades\Facade;

class Neocep extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'neocep'; // the IoC binding.
    }
}
