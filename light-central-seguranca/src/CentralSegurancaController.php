<?php

namespace Celepar\Light\CentralSeguranca;

use App;
use Session;
use Request;
use Redirect;
use \League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use GuzzleHttp;

class CentralSegurancaController extends \App\Http\Controllers\Controller
{

    public function login($loginWithCertificate = null)
    {
        $provider = CentralSeguranca::newProvider($loginWithCertificate);

        // Caso nao tenha um authorization code entao pegue um
        if (!Request::get('code') || !Session::get('oauth2state')) {
            // Pega a authorization URL do provider; isso retorna a urlAuthorize junto com os paramentros necessarios para buscar o code na CS
            $authorizationUrl = $provider->getAuthorizationUrl();

            // pega o state gerado na CS e grava na sessao.
            Session::set('oauth2state', $provider->getState());

            // Redireciona o usuario para a tela de login da CS (authorization URL).
            return Redirect::to($authorizationUrl);
            // Verifica o state gravado na sessao contra o state dado pela CS para evitar o CSRF ataque
        } elseif (empty(Request::get('state')) || (Request::get('state') !== Session::get('oauth2state'))) {
            Session::forget('oauth2state');
            return view('vendor.central-seguranca.error')->with('msgErroCS', 'State error');
        } else {
            try {
                // Tenta pegar um AccessToken usando o authorization code grant pego no passo anterior.
                $accessToken = $provider->getAccessToken1('authorization_code', [
                    'code' => Request::get('code')
                ]);

                //Valida se o usuário possui algum grupo de acesso nesta aplicação,
                //Caso não tenha, redireciona para tela de erro e nem busca os dados do usuário.
                if(!CentralSeguranca::isGroupsValid($accessToken)){

                    //Verifica se foi informado no config alguma rota caso o usuário não possua grupos neste sistema
                    if ($routeDenied = config('central-seguranca.routeAccessDenied')) {
                        $resourceOwner = $provider->getResourceOwner1($accessToken)->toArray();
                        unset($resourceOwner['senhaUsuario']);
                        Session::flash('UserData', $resourceOwner);
                        return redirect()->to($routeDenied);
                    }

                    return view('vendor.central-seguranca.access_denied');
                }

                // Com o AccessToken, buscamos os dados do usuario na CS
                $resourceOwner = $provider->getResourceOwner1($accessToken)->toArray();

                //Logamos o usuario no sistema
                CentralSeguranca::loginUser($accessToken, $resourceOwner);

                return redirect()->to(config('central-seguranca.redirectAfterLogin'));

            } catch (\Exception $e) {

                return view('vendor.central-seguranca.error')->with('msgErroCS', 'Erro ao autenticar/obter os dados do usuário');

            }
        }
    }

    public function logout()
    {
        $url = CentralSeguranca::logout();
        return Redirect::to($url);
    }

}