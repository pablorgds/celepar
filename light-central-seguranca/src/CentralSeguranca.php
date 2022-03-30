<?php

namespace Celepar\Light\CentralSeguranca;

use App;
use Auth;
use Session;
use GuzzleHttp;
use League\OAuth2\Client\Token\AccessToken;
use URL;

class CentralSeguranca
{

    //Retorna um novo Provider
    public static function newProvider($loginWithCertificate)
    {
        //Caso usuário esteja tentando logar com um certificado digital, pega o endpoint do authorize por Certificado Digital
        if($loginWithCertificate == 'certauth'){
            $urlAuthorize = config('central-seguranca.urlCertificateAuthorize.'.App::environment());
        }
        else{
            $urlAuthorize = config('central-seguranca.urlAuthorize.'.App::environment());
        }

        return new CentralSegurancaProvider([
            'clientId' => config('central-seguranca.clientId.'.App::environment()),
            'clientSecret' => config('central-seguranca.clientSecret.'.App::environment()),
            'redirectUri' => URL::to('/auth/login'),
            'urlAuthorize' => $urlAuthorize,
            'urlAccessToken' => config('central-seguranca.urlAccessToken.'.App::environment()),
            'urlResourceOwnerDetails' => config('central-seguranca.urlResourceOwnerDetails.'.App::environment()),
            'scopes' => config('central-seguranca.scope'),
        ]);
    }

    //Loga o usuário na aplicação com os dados retornados da Central de Segurança
    public static function loginUser(AccessToken $accessToken, $resourceOwner)
    {

        $user = new User();

        $user->setAuthIdentifier($resourceOwner['cpf']);
        $user->setName($resourceOwner['nome']);
        $user->setLogin($resourceOwner['login']);
        $user->setEmail($resourceOwner['email']);

        //Dados extras da Central do Cidadao
        $user->setIdCidadao($resourceOwner['idCidadao']);
        $user->setRg($resourceOwner['rg']);
        $user->setOrgaoExpedidor($resourceOwner['orgaoExpedidor']);
        $user->setDtEmissaoRG($resourceOwner['dtEmissaoRG']);
        $user->setUfRg($resourceOwner['ufRg']);
        $user->setCpf($resourceOwner['cpf']);
        $user->setDataCadastroUsuario($resourceOwner['dataCadastroUsuario']);
        $user->setTentativasSenhaErrada($resourceOwner['tentativasSenhaErrada']);
        $user->setDataTrocaSenha($resourceOwner['dataTrocaSenha']);
        $user->setUsuarioMainFrame($resourceOwner['usuarioMainFrame']);
        $user->setTelefone($resourceOwner['telefone']);
        $user->setCelular($resourceOwner['celular']);
        $user->setDataHoraSenhaErrada($resourceOwner['dataHoraSenhaErrada']);
        $user->setIndicativoUsuarioAtivo($resourceOwner['indicativoUsuarioAtivo']);
        $user->setIndicativoBloqueio($resourceOwner['indicativoBloqueio']);
        $user->setNmMae($resourceOwner['nmMae']);
        $user->setDtNascimento($resourceOwner['dtNascimento']);
        $user->setNivelConfiabilidadeCadastro($resourceOwner['nivelConfiabilidadeCadastro']);
        $user->setCodSistemaNivelCadastroUsuario($resourceOwner['codSistemaNivelCadastroUsuario']);
        $user->setGroup(CentralSeguranca::treatGroups($accessToken->getValues()));

        Auth::login($user);

        Session::put('userAccessToken', $accessToken->getToken());
        Session::put('userRefreshToken', $accessToken->getRefreshToken());

        //@TODO melhorar para tirar essas variaveis da sessao, ainda estao sendo utilizadas no Middleware do componente
        Session::put('central-seguranca-usuario', serialize($user));
        Session::put('central-seguranca-grupos', CentralSeguranca::treatGroups($accessToken->getValues()));

    }

    public static function logout()
    {

        Auth::logout();

        Session::flush();

        $url = config('central-seguranca.urlAuthorize.'.App::environment()).
            '?response_type=code&client_id='.config('central-seguranca.clientId.'.App::environment()).
            '&redirect_uri='.URL::to('/auth/login').
            '&scope='.config('central-seguranca.scope').
            '&force_login=true';

        return $url;
    }

    //Valida se o token ainda existe na central de segurança.
    //Caso o AccessToken esteja expirado, utiliza o refreshToken para pegar um novo AccessToken
    //Caso o RefreshToken já esteja expirado, então retorna false para que seja redirecionado para a tela de login
    public static function isTokenValid()
    {

        $client = new GuzzleHttp\Client(['exceptions' => false]);
        $res = $client->request('GET', config('central-seguranca.urlAccessToken.' . App::environment()), [
            'headers' => [
                'Authorization' => 'Bearer ' . Session::get('userAccessToken')
            ]
        ]);

        if ($res->getStatusCode() == 200) {
            return true;
        }
        elseif(CentralSeguranca::updateAccessToken()){
            return true;
        }
        return false;
    }

    //Tenta atualizar o AccessToken com o RefreshToken
    public static function updateAccessToken()
    {
        $clientID = config('central-seguranca.clientId.'.App::environment());
        $clientSecret = config('central-seguranca.clientSecret.'.App::environment());
        $clientBase64 = base64_encode($clientID.':'.$clientSecret); //OTliY2ZjZDc1NGE5OGNlODljYjg2ZjczYWNjMDQ2NDU6MTIzNDU2Nzg=

        $url = config('central-seguranca.urlAccessToken.' . App::environment()).'?grant_type=refresh_token&refresh_token='.Session::get('userRefreshToken');

        $client = new GuzzleHttp\Client(['exceptions' => false]);
        $res = $client->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Basic '.$clientBase64,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);


        if ($res->getStatusCode() == 200) {
            $body = json_decode($res->getBody()->getContents());

            //seta o novo accessToken e RefreshToken na sessão do usuário e retoma o fluxo
            Session::put('userAccessToken',$body->access_token);
            Session::put('userRefreshToken',$body->refresh_token);

            return true;
        }

        return false;
    }

    //método para tratar os grupos retornados pelo CentralSeguranca, já que ele retorna em outro formato
    public static function treatGroups($valores){
        $grupos = $valores['groups'];

        foreach($grupos as $grupo){
            $gruposTratados[] = $grupo['nome'];
        }
        return $gruposTratados;
    }

    //Valida os grupos do usuario desta aplicaçao
    public static function isGroupsValid(AccessToken $accessToken){
        $array = $accessToken->getValues();

        if(count($array['groups']) < 1){
            return false;
        }
        return true;
    }
}