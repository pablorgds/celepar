## Pacote de acesso a SERVICOS da central de segurança celepar LightPHP 5.1

Pacote desenvolvido para encapsular os serviços da central de segurança.

[Documentacao dos serviços para as aplicações](http://segurancaaplicacoes.celepar.parana/index.php/Servi%C3%A7os_para_as_Aplica%C3%A7%C3%B5es)

## Instalacão

### 1. Dependência

Instalar atraves do [composer](https://getcomposer.org/).
Para que o package seja adicionado automaticamente ao seu arquivo `composer.json` execute o seguinte comando:

```shell
composer require celepar/light-servicos-cs
```

### 2 Configurações

Para usar o componente siga os passos

#### 2.1
Registre o pacote no seu arquivo `config/app.php`, adicionando o seguinte codigo no fim da sessão `providers`

```php
    'providers' => [
        Celepar\Light\ServicosCS\ServicosCSServiceProvider::class,
    ],
```

#### 2.2
Registre o alias da facade no seu arquivo `config/app.php`, adicionando o seguinte codigo no fim da sessão `aliases`

```php
    'aliases' => [
        'ServicosCS'=> \Celepar\Light\ServicosCS\Facade\ServicosCS::class,
    ],
```

#### 2.3
Edite o arquivo `config/central-seguranca.php` adicionando a seguinte configuração:

```php
    'urlCentralCidadao'=>[
        'local'=>'http://cidadao-cs.desenvolvimento.celepar.parana/centralcidadao',
        'homologacao'=>'https://cidadao-cs-hml.identidadedigital.pr.gov.br/centralcidadao',
        'producao'=>'https://cidadao-cs.identidadedigital.pr.gov.br/centralcidadao',
    ]
```

## Utilização

### 1. Acessando os métodos pela facade
Para usar os serviços basta chamar os métodos da seguinte forma

```php
\ServicosCS::obterUsuarioAutenticado()
\ServicosCS::verificarExistenciaCidadao($cpf:string)
\ServicosCS::usuariosSistema([$pagina:int=1],[$qtdeRegistros:int=10])
\ServicosCS::usuariosPorCpf($cpf:array,[$pagina:int=1],[$qtdeRegistros:int=10])
\ServicosCS::usuarioPorCpf($cpf:string)
\ServicosCS::usuarioPorNome($nome:string,[$pagina:int=1],[$qtdeRegistros:int=10])
\ServicosCS::usuarioPorEmail($email:string)
\ServicosCS::usuarioVinculadosGrupo($nomeGrupo:string,[$pagina:int=1],[$qtdeRegistros:int=10])
\ServicosCS::solicitarAutoCadastro($email:array)
\ServicosCS::vincularUsuarioGrupo($cpf:array,$nomeGrupo:string)
\ServicosCS::desvincularUsuarioGrupo($cpf:array,$nomeGrupo:string)
\ServicosCS::gruposPorCpf($cpf:string)
```
