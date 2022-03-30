<a name="topo"></a>
## NeoCEP Celepar LightPHP 5.1

Pacote desenvolvido para integração do NeoCEP Celepar com o LightPHP 5.1.

[Documentação NeoCEP](http://www.gic.desenvolvimento.eparana.parana/cep/)

* [Instalação](#instalacao)
    - [Dependência](#dependencia)
    - [Configuração Service Provider](#configuracao-service-provider)
    - [Publicando Arquivos](#publicando-arquivos)
* [Usando a Facade](#uso-facade)
    - [Exemplo chamada via Facade](#exemplo-facade)
* [Usando no Ajax/Json](#uso-ajax-json)
    - [Exemplo chamada via jQuery](#exemplo-jquery)
* [Métodos disponíveis](#metodos-disponiveis)
    - [Neocep::paises](#neocep-paises)
    - [Neocep::ufs](#neocep-ufs)
    - [Neocep::localidades](#neocep-localidades)
    - [Neocep::localidade](#neocep-localidade)
    - [Neocep::bairros](#neocep-bairros)
    - [Neocep::logradouro](#neocep-logradouro)
    - [Neocep::endereco](#neocep-endereco)
* [Exemplo de um formulário utilizando o serviço ajax](#exemplo-formulario)

<a name="instalacao"></a>
## Instalação

<a name="dependencia"></a>
#### Dependência

Instalar através do [composer](https://getcomposer.org/).
Para que o package seja adicionado automaticamente ao seu arquivo `composer.json` execute o seguinte comando:

```shell
composer require celepar/light-neocep
```

<p align="right"><a href="#topo">Topo</a></p>
<a name="configuracao-service-provider"></a>
#### Configuração Service Provider

Para usar o NeoCEP em sua aplicação Light-PHP5, é necessário registrar o package no seu arquivo `config/app.php`.
Adicione o seguinte código no fim da seção `providers`

```php
// file START ommited
    'providers' => [
        // other providers ommited
        Celepar\Light\Neocep\Providers\NeocepServiceProvider::class,
    ],
// file END ommited
```

<p align="right"><a href="#topo">Topo</a></p>
<a name="publicando-arquivos"></a>
#### Publicando arquivos

Para funcionar é necessário copiar o arquivo config/neocep.php para a pasta config

```shell
php artisan vendor:publish --provider="Celepar\Light\Neocep\Providers\NeocepServiceProvider"
```

<p align="right"><a href="#topo">Topo</a></p>
<a name="uso-facade"></a>
## Usando a Facade

É possível chamar o serviço do NeoCep nos seus Controllers, Middlewares, Providers e outras classes que achar necessário através da facade.

Para utilizar o NeoCep em suas classes basta importar a Facade na sua classe(Controller, Middleware, Providers, Requests, etc)

**Dica:** Todos os metodos estão descritos na seção de [Métodos disponíveis](#metodos-disponiveis).

<p align="right"><a href="#topo">Topo</a></p>
<a name="exemplo-facade"></a>
#### Exemplo chamada via Facade ####

Uso em um controller, arquivo `app\Http\Controllers\HomeController.php`:

```php
namespace App\Http\Controllers;

use Neocep;

class HomeController extends Controller{

   public function index(){
       $listaDePaises = Neocep::paises();
       $listaDeEstados = Neocep::ufs();
       $listaDeCidadesPR = Neocep::localidades('pr');
       $dadosCidadeCuritiba = Neocep::localidade(6015); //6015 é o código da cidade de curitiba
       $listaDeBairrosCuritiba = Neocep::bairros(6015); //6015 é o código da cidade de curitiba
       $listaDeEnderecos = Neocep::logradouro('mateus leme',6015); //6015 é o código da cidade de curitiba
       $dadosEnderecoMateusLeme = Neocep::endereco('80530010'); //80530010 é o cep da rua mateus leme
   }
}
```

**Observação:** O pacote já cria automáticamente o alias para acesso à Facade, não é necessário apontar o caminho completo da facade, apenas o nome.

<p align="right"><a href="#topo">Topo</a></p>
<a name="uso-ajax-json"></a>
## Usando no Ajax/Json ##

É possível chamar o serviço do NeoCep para uso em requisições Ajax e o retorno será um objeto Json.

Para utilizar o NeoCep em requisições ajax basta chamar as rotas conforme listagem abaixo:

| Metodo | URL | Descrição |
|--------|-----|-----------|
| GET | /neocep/paises | Lista Países |
| GET | /neocep/ufs | Lista unidades de federação (UF) |
| GET | /neocep/localidades/{siglaUf}/{tipoLocalidade?}/{tipoCodificacaoLocalidadeRetorno?} | Lista localidades |
| GET | /neocep/localidade/{chaveLocalidade}/{tipoCodificacaoLocalidade?}/{tipoCodificacaoLocalidadeRetorno?} | Obter localidade |
| GET | /neocep/bairros/{chaveLocalidade} | Listar Bairros |
| GET | /neocep/logradouro/{nomeLogradouro}/{chaveLocalidade}/{tipoCodificacaoLocalidade?} | Localiza logradouro por nome|
| GET | /neocep/endereco/{cep}/{tipoCodificacaoLocalidadeRetorno?} | Recupera dados de endereco referente ao cep informado. |

<small>
Legenda:<br>
{nomeParametro} = Parâmetro obrigatório<br>
{nomeParametro?} = Parâmetro opcional<br>
</small>

**Dica:** Todos os metodos estão descritos na seção de [Métodos disponíveis](#metodos-disponiveis).

<p align="right"><a href="#topo">Topo</a></p>
<a name="exemplo-jquery"></a>
#### Exemplo chamada via jQuery ####

Na sua view blade utilize a section('js') para escrever o código javascript como no exemplo abaixo `resources\views\welcome.blade.php`:

```php
@extends('layout::master')

@section('content')
    Seu conteudo ....
    <div id="endereco"></div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            cep = '80530010'
            $.ajax({
                url: '/neocep/endereco/'+cep,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    $("#endereco").append($('<div>Resultado da busca pelo CEP: '+cep+'</div>'));
                    $("#endereco").append($('<div>Logradouro: '+data.tipo+' '+data.nome+'</div>'));
                    $("#endereco").append($('<div>Bairro: '+data.bairro+'</div>'));
                    $("#endereco").append($('<div>CEP: '+data.cep+'</div>'));
                    $("#endereco").append($('<div>UF: '+data.uf+'</div>'));
                }
            });
        });
    </script>
@endsection

```

<p align="right"><a href="#topo">Topo</a></p>
<a name="metodos-disponiveis"></a>
## Métodos disponíveis ##

<a name="neocep-paises"></a>
#### Neocep::paises() ####
Lista Países<br>
**return** array - contendo as seguintes informações (Sigla,Nome,Sigla3Letras)

<p align="right"><a href="#topo">Topo</a></p>
<a name="neocep-ufs"></a>
#### Neocep::ufs() ####
Lista unidades de federação (UF)<br>
**return** array - contendo as seguintes informações (Sigla,Pais,Nome)

<p align="right"><a href="#topo">Topo</a></p>
<a name="neocep-localidades"></a>
#### Neocep::localidades(siglaUf, [tipoLocalidade = 0], [tipoCodificacaoLocalidadeRetorno = 0]) ####
Lista localidades<br>
**param** $siglaUf - Sigla da Unidade Federativa<br>
**param** string $tipoLocalidade - Código de identificação do tipo de localidade (M-Município, P-Povoado, D-Distrito e R-Região). Default = M<br>
**param** int $tipoCodificacaoLocalidadeRetorno - Solicita retorno de chave codificada de acordo com as tabelas (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0<br>
**return** array - contendo as seguintes informações (cep,uf,situacao,nome,chave,tipoLocalidade,localidadeSuperior)

<p align="right"><a href="#topo">Topo</a></p>
<a name="neocep-localidade"></a>
#### Neocep::localidade(chaveLocalidade, [tipoCodificacaoLocalidade = 0], [tipoCodificacaoLocalidadeRetorno = 0]) ####
Obter localidade<br>
**param** $chaveLocalidade - Código da localidade/cidade (Código dos correios)<br>
**param** int $tipoCodificacaoLocalidade - Codificação de município a ser utilizada (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0<br>
**param** int $tipoCodificacaoLocalidadeRetorno - Solicita retorno de chave codificada de acordo com as tabelas (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0<br>
**return** array - contendo as seguintes informações (cep,uf,situacao,nome,chave,tipoLocalidade,localidadeSuperior)

<p align="right"><a href="#topo">Topo</a></p>
<a name="neocep-bairros"></a>
#### Neocep::bairros(chaveLocalidade) ####
Listar Bairros<br>
**param** $chaveLocalidade - Código da localidade/cidade (Código dos correios)<br>
**return** array - contendo as seguintes informações (abreviatura,localidade,nome,chave)

<p align="right"><a href="#topo">Topo</a></p>
<a name="neocep-logradouro"></a>
#### Neocep::logradouro(nomeLogradouro, chaveLocalidade, [tipoCodificacaoLocalidade = 0]) ####
Localiza logradouro por nome<br>
**param** $nomeLogradouro - Nome total ou parcial do logradouro desejado.<br>
**param** $chaveLocalidade - Código da localidade<br>
**param** $tipoCodificacaoLocalidade - Informa o tipo de codificação da chave localidade a ser utilizada na pesquisa (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0<br>
**return** array - contendo as seguintes informações (complemento,cep,tipo,indicadorTipo,nome,chave,bairroInicial,bairroFinal,paridadeLadoSeccionamento,numeroInicial,numeroFinal)

<p align="right"><a href="#topo">Topo</a></p>
<a name="neocep-endereco"></a>
#### Neocep::endereco(cep, [tipoCodificacaoLocalidadeRetorno = 0]) ####
Recupera dados de endereco referente ao cep informado.<br>
**param** $cep - Código de endereçamento postal<br>
**param** $tipoCodificacaoLocalidadeRetorno - Solicita retorno de chave codificada da localidade, de acordo com as tabelas (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0<br>
**return** array - contendo as seguintes informações (numeroInicial,bairro,complemento,cep,uf,tipoCep,localidade,tipo,nome,numeroFinal,isCepGenerico)

<p align="right"><a href="#topo">Topo</a></p>

<a name="exemplo-formulario"></a>
## Exemplo de um formulário utilizando o serviço ajax ##

Para teste coloque o código abaixo em uma view (por exemplo <code>resources\views\welcome.blade.php</code>) em um projeto com o componente instalado.

```php
@extends('forms::basic')

@section('content')
    <div class="form-horizontal">
        {!! cForm::text('cep',null,['id'=>'neocep-cep','data-mask'=>'99999-999'],['label'=>'CEP']) !!}

        {!! cForm::text('logradouro',null,['id'=>'neocep-logradouro'],['label'=>'Endereço']) !!}

        {!! cForm::select('uf',[],null,['id'=>'neocep-uf'],['label'=>'Estado']) !!}

        {!! cForm::select('cidade',[],null,['id'=>'neocep-cidade'],['label'=>'Cidade']) !!}

        {!! cForm::select('bairro',[],null,['id'=>'neocep-bairro-combo'],['label'=>'Bairro']) !!}

        <div class="form-group">
            <div class="col-md-12" align="center">
                <button id="neocep-limpar" class="btn btn-primary">Limpar Campos</button>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        var neocep = { //criação de váriavel com as funções neocep
            ListarUF : function(ufSelecionado) { // função de busca de listagem de estados
                $("#neocep-uf").html('<option value="">Carregando ...</option>');
                $.ajax({
                    url: '/neocep/ufs/', //URL do serviço Neocep::ufs()
                    type: 'GET',
                    success: function (data) { //em caso de sucesso
                        $("#neocep-uf").html('<option value="">Selecione um estado</option>');
                        $.each(data,function(index,value){ //loop no resultado
                            selecionado = '';
                            if(ufSelecionado == value.sigla){
                                selecionado = ' selected';
                            }
                            $("#neocep-uf").append('<option value="'+value.sigla+'" '+selecionado+'>'+value.nome+' - '+value.sigla+'</option>');
                        });
                        if($("#neocep-cidade").length > 0) {
                            $("#neocep-cidade").html('<option value="">Selecione um estado</option>');
                        }
                        if($("#neocep-bairro-combo").length > 0) {
                            $("#neocep-bairro-combo").html('<option value="">Selecione um estado</option>');
                        }
                    }
                });
            },
            ListarCidades : function(siglaUf,idCidadeSelecionado){ // função de busca de listagem de cidades
                $("#neocep-cidade").html('<option value="">Carregando ...</option>');
                $.ajax({
                    url: '/neocep/localidades/'+siglaUf, //URL do serviço Neocep::localidades()
                    type: 'GET',
                    success: function (data) { //em caso de sucesso
                        $("#neocep-cidade").html('<option value="">Selecione uma cidade</option>');
                        $.each(data,function(index,value){ //loop no resultado
                            selecionado = '';
                            if(idCidadeSelecionado == value.chave){
                                selecionado = ' selected';
                            }
                            $("#neocep-cidade").append('<option value="'+value.chave+'" '+selecionado+'>'+value.nome+'</option>');
                        });
                        if($("#neocep-bairro-combo").length > 0) {
                            $("#neocep-bairro-combo").html('<option value="">Selecione uma cidade</option>');
                        }
                    }
                });
            },
            ListarBairros: function(idCidade,idBairroSelecionado){ // função de busca de listagem de bairros
                $("#neocep-bairro-combo").html('<option value="">Carregando ...</option>');
                $.ajax({
                    url: '/neocep/bairros/'+idCidade, //URL do serviço Neocep::bairros()
                    type: 'GET',
                    success: function (data) { //em caso de sucesso
                        $("#neocep-bairro-combo").html('<option value="">Selecione um bairro</option>');
                        $.each(data,function(index,value){ //loop no resultado
                            selecionado = '';
                            if(idBairroSelecionado == value.chave){
                                selecionado = ' selected';
                            }
                            $("#neocep-bairro-combo").append('<option value="'+value.chave+'" '+selecionado+'>'+value.nome+'</option>');
                        });
                    }
                });
            },
            BuscarCep: function(cep){ // função de busca de endereço pelo CEP
                $.ajax({
                    url: '/neocep/endereco/'+cep, //URL do serviço Neocep::endereco()
                    type: 'GET',
                    success: function (data) { //em caso de sucesso
                        $("#neocep-logradouro").val(data.tipo+' '+data.nome);
                        $("#neocep-uf").val(data.uf);
                        neocep.ListarCidades(data.uf,data.localidade);
                        neocep.ListarBairros(data.localidade, data.chave_bairro);
                    },
                    error: function(){ //em caso de erro
                        alert('Favor informar um CEP válido.');
                    }
                });
            },
            LimparCampos: function(){ //função que limpa os campos
                $("#neocep-cep").val('');
                $("#neocep-logradouro").val('');
                $("#neocep-uf").val('');
                $("#neocep-cidade").html('<option value="">Selecione um estado</option>');
                $("#neocep-bairro-combo").html('<option value="">Selecione um estado</option>');
            }
        }
        $(document).ready(function() { //script executado assim que terminar o carregamento dos elementos da pagina
            $("#neocep-cep").parent('div').append('<i id="neocep-busca-cep" class="fa fa-search" style="cursor: pointer"></i>'); //adiciona o icone de busca ao lado do campo CEP
            $("#neocep-cep").css('display','inline'); //muda o display do campo neocep-cep para que o icone de busca fique ao lado

            neocep.ListarUF(); //chama a função para carregar a combo de estado

            $("#neocep-uf").change(function(){ //caso seja selecionado um estado
                neocep.ListarCidades($("#neocep-uf").val()); //chama função para carregar as cidades do estado selecionado
            });
            $("#neocep-cidade").change(function() { //caso seja selecionado uma cidade
                neocep.ListarBairros($("#neocep-cidade").val()); //chama função para carregar os bairros da cidade selecionada
            });
            $("#neocep-busca-cep").click(function(){ //caso seje clicado no icone de busca de cep
                if($('#neocep-cep').val().replace("_","").length == 9) { //verifica se foi digitado um CEP
                    neocep.BuscarCep($('#neocep-cep').val()); // chama função para carregar os dados do cep informado
                }else{
                    alert('Favor informar um CEP válido.'); //informa erro na digitação do CEP
                }
            });
            $("#neocep-limpar").click(function(){ //caso seja clicado no botão de limpar campos
               neocep.LimparCampos(); //chma função que limpa os campos
            });
        });
    </script>
@endsection
```

<p align="right"><a href="#topo">Topo</a></p>