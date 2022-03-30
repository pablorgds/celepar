## Central de Segurança Celepar LightPHP 5.1

Pacote desenvolvido para autenticação/autorização utilizando a Central de Segurança e Sentinela com o LightPHP 5.1.
Com este componente, é possivel se autenticar via protocolo oAuth2 com a Central de Segurança da Celepar, sem necessidade de possuir os dados do usuário na sua aplicação, pois ele utiliza os dados da base do Sentinela e da Central de Segurança.
Ao se autenticar, os dados são extendidos para a Classe Auth do laravel, podendo assim utilizar normalmente os seus métodos.

Ao utilizar este componente, serão criadas automaticamente as rotas abaixo:
- auth/login
- logout

[Documentacao do Componente da Central de Segurança](http://plataformadesenvolvimento.celepar.parana/index.php/Light-central-seguranca)

## Instalacao

### 1. Dependencia

Instalar atraves do [composer](https://getcomposer.org/).
Para que o package seja adicionado automaticamente ao seu arquivo `composer.json` execute o seguinte comando:

```shell
composer require celepar/light-central-seguranca
```

### 2 Configuracões

Para usar o componente como autenticador em sua aplicacao Light-PHP5, siga os seguintes passos:

#### 2.1
Registre o package no seu arquivo `config/app.php`, adicionando o seguinte codigo no fim da sessao `providers`

```php
// file START ommited
    'providers' => [
        // other providers ommited
        Celepar\Light\CentralSeguranca\CentralSegurancaServiceProvider::class,
    ],
// file END ommited
```

#### 2.2
Altere o driver de autenticação no arquivo `config/auth.php`.
Alterar de 'eloquent' para 'central-seguranca'

```php
// file START ommited
'driver' => 'central-seguranca',
// file END ommited
```

#### 2.3
Para proteger rotas utilizando o Middleware do componente, registre o middleware no arquivo `app/Http/Kernel.php`. adicionando o codigo abaixo no fim do array `protected $routeMiddleware`:

```php
// file START ommited
    protected $routeMiddleware = [
        // other middlewares ommited
        'central-seguranca' => \Celepar\Light\CentralSeguranca\Middleware\Authorize::class,
    ];
// file END ommited
```

Para verificação de token válido a cada requisição, registre o middleware no arquivo `app/Http/Kernel.php`. adicionando o codigo abaixo no fim do array `protected $middleware`:

```php
// file START ommited
    protected $middleware = [
        // other middlewares ommited
        \Celepar\Light\CentralSeguranca\Middleware\VerifyToken::class,
    ];
// file END ommited
```

Mais sobre middlewares do laravel no endereço - http://laravel.com/docs/5.1/middleware

#### 2.4
Realize um publish dos arquivos do componente com o comando abaixo:

```shell
php artisan vendor:publish --provider="Celepar\Light\CentralSeguranca\CentralSegurancaServiceProvider"
```
Esté comando irá criar o arquivo `config/central-seguranca.php`


#### 2.5
Edite o arquivo `config/central-seguranca.php` com as credenciais da sua aplicação cadastradas no Sentinela para cada ambiente. (Para realizar o cadastro no Sentinela, entrar em contato com Patrik no email pmenezes@celepar.pr.gov.br)

```php
// file START ommited

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

    //Rota para onde será redirecionado após o login.
    // COLOCAR SOMENTE O NOME DA ROTA. Ex: 'admin';
    'redirectAfterLogin' => '',

// file END ommited
```

## Utilização

### 1. Rotas autenticadas
Para permitir acesso somente a usuarios autenticados em determinados rotas, pode ser utizado o middleware Auth do proprio laravel, como no exemplo abaixo (app/Http/routes.php):

```php
// file START ommited
Route::group(['middleware' => 'auth'], function () {
    //Adicionar aqui todas as rotas desejadas
});
// file END ommited
```

### 2. Permissão em rotas
Algumas vezes temos rotas que são permitidas somente para alguns grupos de usuários, neste caso podemos utilizar o Middleware do componente.
Basta utilizar conforme o exemplo abaixo (app/Http/routes.php):

```php
// file START ommited
Route::group(['middleware' => 'auth'], function () {

    Route::get('teste', [
        'middleware'=> 'central-seguranca:<grupo1>|<grupo2>',
        'uses'=>'TesteController@index'
    ]);

});
// file END ommited
```
Neste caso, somente usuários do grupo1 e grupo2 terão acesso a esta rota. Podem ser passados diversos grupos separados por | ou somente um grupo.
Para criar grupos e associar usários a grupos, utilizamos o Sentinela (endereço de desenvolvimento https://www.sentinela.desenvolvimento.eparana.parana/sentinela/)

### 3. Permissão em recursos
Para realizar o controle de permissões à recursos, pode ser utilizado o componente de autorização do próprio láravel, chamado Gate.
[Documentação Gate](http://laravel.com/docs/5.1/authorization)
Logo abaixo tempos um simples exemplo de como fazer um controle de permissões à recursos.

#### 3.1
Registre a sua regra de permissão (Abilities) no arquivo `app/Providers/AuthServiceProvider.php` no método boot. Exemplo abaixo.

```php
// file START ommited
public function boot(GateContract $gate)
{
   // other gates ommited

   $gate->define('exibir-texto', function($user){
       return in_array('grupo1',$user->groups);
   });
}
// file END ommited
```
No exemplo acima, definimos que usuários do grupo1 tem acesso a permissão (Ability) de exibir-texto.
Após definida a permissão, podemos utiliza-la na aplicação basicamente de 2 formas, no código php da aplicação (Controllers/Models) ou no template da aplicação (blade)

#### 3.2
Exemplo simples de utilização no código php da aplicação, no caso diretamente em uma rota (app/Http/routes.php)

```php
// file START ommited

get('teste', function(){
        if(Gate::denies('exibir-texto')){
            return 'Sem permissão para exibir-texto';
        }
        return 'Você tem permissão para exibir-texto';
    });
// file END ommited
```
No exemplo acima usuários que tiverem a permissão 'exibir-texto', no caso os usuários do grupo1,
exibirão a mensagem 'Você tem permissão para exibir-texto'.
E usuários sem a permissão exibirão a mensagem 'Sem permissão para exibir-texto'.

#### 3.3
Exemplo simples de utilização no template (blade)

```
// file START ommited
@can('exibir-texto')
    Você tem permissão de ver isto!
@endcan
// file END ommited
```
No exemplo acima usuários que tiverem a permissão 'exibir-texto', no caso os usuários do grupo1, exibirão a mensagem 'Você tem permissão de ver isto!'