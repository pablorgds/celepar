<?php

namespace Celepar\Light\CentralSeguranca\Middleware;

use Closure;
use Session;
use Messenger;

class Authorize {


    public function handle($request, Closure $next, $grupos)
    {
        //Para requisições que vierem de Ajax
        if ($request->ajax()) {
            if($this->validaGrupos($grupos)){
                return $next($request);
            }
            return response('Unauthorized.', 401);

            //Para requisições que NÃO vierem de Ajax
        } else {
            if($this->validaGrupos($grupos)){
                return $next($request);
            }

            Messenger::error('Seu usuário não tem permissão para acessar o recurso solicitado.',false);
            return \Redirect::back();
        }
    }


    public function validaGrupos($grupos){
        //Caso seja passado mais de um grupo por |
        if( strpos($grupos, '|') ){
            $grupos = explode('|',$grupos);

            if(count(array_intersect($grupos,Session::get('central-seguranca-grupos'))) > 0){
                return true;
            }
        }

        //caso seja passado apenas um grupo
        if ( in_array($grupos, Session::get('central-seguranca-grupos'))) {
            return true;
        }
        return false;
    }
}