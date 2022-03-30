<?php

namespace Celepar\Light\Macros;

use Illuminate\Support\Facades\Facade;

class MacrosFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'cform'; //type registrado no Service Container
    }

}