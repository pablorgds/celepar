<?php
/**
 * Created by PhpStorm.
 * User: roberson.faria
 * Date: 07/11/16
 * Time: 17:41
 */

namespace Celepar\Light\ServicosCS\Facade;


use Illuminate\Support\Facades\Facade;

class ServicosCS extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ServicosCS'; // the IoC binding.
    }

}