<?php

return [

    // ClientId da aplicacao.
    'clientId'=>[
        'local'=>'',
        'homologacao'=>'',
        'producao'=>'',
    ],

    // ClientSecret da aplicacao.
    'clientSecret'=>[
        'local'=>'',
        'homologacao'=>'',
        'producao'=>'',
    ],

    // Debug para gerar logs de requisições. Por enquanto está gerando somente logs no middlewere de API
    'debug' => false,

    //Rota para onde será redirecionado após o login.
    //COLOCAR SOMENTE A URL DA ROTA. Ex: '/admin';
    //Caso queira redirecionar para a raiz da app, deixar em branco. Ex: ''
    'redirectAfterLogin' => '',

    //Rota para redirecionar o usuário quando ele não tiver nenhum grupo do sistema
    //Deixar vazio '' para exibir a tela padrão do componente de acesso negado
    'routeAccessDenied' => '',

    #####################################################################################
    #####################################################################################

    /*OS PARÂMETROS ABAIXO NÃO SÃO NECESSÁRIOS ALERAR*/

    //Escopo cadastrado no Sentinela
    'scope' => 'light.cs.auth',

    //Verificação do certificado digital do CentralSeguranca pelo Apache.
    'verify_certificate'=>[
        'local'=>false,
        'homologacao'=>true,
        'producao'=>true,
    ],

    //url para pegar o code de acesso
    'urlAuthorize'=>[
        'local'=>'http://auth-cs.desenvolvimento.celepar.parana/centralautenticacao/api/v1/authorize',
        'homologacao'=>'https://auth-cs-hml.identidadedigital.pr.gov.br/centralautenticacao/api/v1/authorize',
        'producao'=>'https://auth-cs.identidadedigital.pr.gov.br/centralautenticacao/api/v1/authorize',
    ],

    //url para pegar o acessToken
    'urlAccessToken'=>[
        'local'=>'http://auth-cs.desenvolvimento.celepar.parana/centralautenticacao/api/v1/token',
        'homologacao'=>'https://auth-cs-hml.identidadedigital.pr.gov.br/centralautenticacao/api/v1/token',
        'producao'=>'https://auth-cs.identidadedigital.pr.gov.br/centralautenticacao/api/v1/token',
    ],
    //url para pegar os dados do usuario
    'urlResourceOwnerDetails'=>[
        'local'=>'http://cidadao-cs.desenvolvimento.celepar.parana/centralcidadao/api/v1/cidadaos/autenticado',
        'homologacao'=>'https://cidadao-cs-hml.identidadedigital.pr.gov.br/centralcidadao/api/v1/cidadaos/autenticado',
        'producao'=>'https://cidadao-cs.identidadedigital.pr.gov.br/centralcidadao/api/v1/cidadaos/autenticado',
    ],

    'urlCertificateAuthorize'=>[
        //'local'=>'',
        'homologacao'=>'https://certauth-cs-hml.identidadedigital.pr.gov.br/centralautenticacao/api/v1/certificate/authorize',
        'producao'=>'https://certauth-cs.identidadedigital.pr.gov.br/centralautenticacao/api/v1/certificate/authorize',
    ],

];