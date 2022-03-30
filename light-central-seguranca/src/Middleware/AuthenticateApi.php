<?php

namespace Celepar\Light\CentralSeguranca\Middleware;

use Closure;
use GuzzleHttp\Exception\RequestException;

class AuthenticateApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $scope = null)
    {

        if(config('central-seguranca.debug')) {
            \Log::debug('Requisição ', ['Authorization' => $request->header('Authorization'), 'scope' => $scope]);
        }

        try {
            if (empty($request->header('Authorization'))) {
                return response("Forbidden.", 403);
            }

            $client = new \GuzzleHttp\Client(['verify' => config('central-seguranca.verify_certificate.' . \App::environment()), 'exceptions' => true]);
            $res = $client->request('GET', config('central-seguranca.urlAccessToken.' . \App::environment()), [
                'headers' => [
                    'Authorization' => $request->header('Authorization'),
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ]
            ]);

            if ($res->getStatusCode() == 200) {
                $result = json_decode($res->getBody()->getContents());
                $request->session()->flash('tokenInfo', $result);

                if(config('central-seguranca.debug')) {
                    \Log::debug('Retorno Central de segurança ', (array)$result);
                }

                if(!is_null($scope)){
                    if(!isset($result->scope)){
                        return response("Forbidden.", 403);
                    }
                    if(count(array_intersect(explode(" ",$result->scope),explode(" ",$scope))) >0){
                        return $next($request);
                    }else {
                        return response("Escopo inválido: " . $scope, 403);
                    }
                }
                return $next($request);

            } else {
                return response($res->getBody()->getContents(), $res->getStatusCode());
            }
        }catch (RequestException $e){

            \Log::debug('Erro inesperado ', (array)json_decode($e->getResponse()->getBody()->getContents())->error);

            return response(json_decode($e->getResponse()->getBody()->getContents())->error, $e->getCode());
        }
        return response("Forbidden.", 403);
    }
}