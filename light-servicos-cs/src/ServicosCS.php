<?php
/**
 * Created by PhpStorm.
 * User: roberson.faria
 * Date: 07/11/16
 * Time: 17:41
 */

namespace Celepar\Light\ServicosCS;

use \GuzzleHttp\Client;
use \App;

class ServicosCS
{
    private $debug = false;

    /**
     * Pega a url da central de segurança no arquivo de config e adiciona o caminho da api
     * @return string url da api
     * @throws \Exception Falta da url da central do cidadão no arquivo de configuração da central de segurança
     */
    private function getUrlAPI(){
        if(config('central-seguranca.urlCentralCidadao.'.App::environment())){
            return config('central-seguranca.urlCentralCidadao.'.App::environment()).'/api/';
        }else{
            $msg = "Favor adicionar a seguinte configuração no final do seu arquivo de configs da central de seguranca (config/central-seguranca.php):\n"
                    ."'urlCentralCidadao'=>[
        'local'=>'http://cidadao-cs.desenvolvimento.celepar.parana/centralcidadao',
        'homologacao'=>'https://cidadao-cs-hml.identidadedigital.pr.gov.br/centralcidadao',
        'producao'=>'https://cidadao-cs.identidadedigital.pr.gov.br/centralcidadao',
    ]";
            throw new \Exception($msg);
        }
    }

    /**
     * Obtem o token para o sistema de acordo com o scopo passado
     * @param string $scopo
     * @return string Token
     * @throws \Exception
     */
    public function obterTokenAplicacao($scopo, $ambiente = null)
    {
        if(is_null($ambiente))
            $ambiente = App::environment();

        if($this->debug){
            echo "<pre>";
        }
        $clientID = config('central-seguranca.clientId.'.$ambiente);
        $clientSecret = config('central-seguranca.clientSecret.'.$ambiente);
        $clientBase64 = base64_encode($clientID.':'.$clientSecret);

        $url = config('central-seguranca.urlAccessToken.'.$ambiente);


        $client = new Client(['exceptions' => false]);
        $res = $client->request('POST', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Basic '.$clientBase64,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'query'=>[
                'grant_type'=>'client_credentials',
                'scope'=>$scopo
            ]
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 401:
                throw new \Exception('Acesso não autorizado, certifique-se que cadastrou o escopo para sua aplicação. Escopo = '.$scopo);
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Obtem os dados do usuário autenticado na aplicação.
     * @return Object Os dados da pessoa autenticada.
     * @throws \Exception
     */
    public function obterUsuarioAutenticado()
    {
        if($this->debug){
            echo "<pre>";
        }
//        $url = config('central-seguranca.urlAccessToken.'.App::environment());
        $url = $this->getUrlAPI().'v1/cidadaos/autenticado';

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer ' . \Session::get('userAccessToken')
            ]
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço');
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Verificar Existência de Cidadão.
     * @param string $cpf
     * @return bool
     * @throws \Exception
     */
    public function verificarExistenciaCidadao($cpf){
        $accessToken = $this->obterTokenAplicacao('central.cidadao.consultar');

        $url = $this->getUrlAPI().'v1/cidadaos';

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ],
            'query' => [
                'cpf' => $cpf,
            ]
        ]);

        switch ($res->getStatusCode()) {
            case 200:
                return true;
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço');
                break;
            case 404:
                return false;
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Pesquisar usuários vinculados a um Sistema.

     * @param int $pagina Número a partir do qual a busca será realizada. ex: 50 > a busca será feita a partir do registro 50.
     * @param int $qtdeRegistros Quantidade de registros a serem retornados.
     * @return Object Contendo a lista com os usuários do sistema e as informações de paginação.
     * @throws \Exception
     */
    public function usuariosSistema($pagina = 1,$qtdeRegistros = 10){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.sistema.get');

        $url = $this->getUrlAPI()."v1/cidadaos/sistema/{$pagina}/{$qtdeRegistros}";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ]
        ]);

        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 404:
                return null;
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Pesquisar Por Nome usuários vinculados a um Sistema.
     *
     * @param array $pesquisa
     * @param int $pagina Número a partir do qual a busca será realizada. ex: 50 > a busca será feita a partir do registro 50.
     * @param int $qtdeRegistros Quantidade de registros a serem retornados.
     * @return Object Contendo a lista com os usuários do sistema e as informações de paginação.
     * @throws \Exception
     */
    public function usuariosPorNomeNoSistema($nome = null, $pagina = 1,$qtdeRegistros = 10){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.sistema.get centralcidadao.v1.cidadaos.sistema.nome.get');

        $filtro=null;
        if(!is_null($nome))
        {
            $filtro = "/nome/{$nome}";
        }
        $url = $this->getUrlAPI()."v1/cidadaos/sistema{$filtro}/{$pagina}/{$qtdeRegistros}";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ]
        ]);

        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 404:
                throw new \Exception($res->getBody()->getContents(),404);
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode(),$res->getStatusCode());
                break;
        }
    }

    /**
     * Obter usuários pela lista de CPF.
     * @param array $cpf lista de CPFs a serem pesquisados. ex: ["12345678993", "78945612312"]
     * @param int $pagina Número a partir do qual a busca será realizada. ex: 50 > a busca será feita a partir do registro 50.
     * @param int $qtdeRegistros Quantidade de registros a serem retornados.
     * @return Object Uma lista de dados do tipo CidadaoEntity.
     * @throws \Exception
     */
    public function usuariosPorCpf(Array $cpf,$pagina = 1,$qtdeRegistros = 10){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.cpf.get');

        $url = $this->getUrlAPI()."v1/cidadaos/cpf/{$pagina}/{$qtdeRegistros}";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('POST', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ],
            'json'=>$cpf
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 404:
                return null;
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Pesquisar usuário por CPF.
     * @param string $cpf CPF do usuário a ser pesquisado.
     * @return Object Objeto com informações do usuário pesquisado.
     * @throws \Exception
     */
    public function usuarioPorCpf($cpf){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.cpf.get');

        $url = $this->getUrlAPI()."v1/cidadaos/cpf/{$cpf}";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ]
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 404:
                return null;
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Pesquisar usuários pelo nome.
     * @param string $nome Nome a ser pesquisado. Mínimo de 3 caracteres. A pesquisa ocorrerá por qualquer parte do nome. (%nome%).
     * @param int $pagina Número a partir do qual a busca será realizada. ex: 50 > a busca será feita a partir do registro 50.
     * @param int $qtdeRegistros Quantidade de registros a serem retornados.
     * @return Object Uma lista de usuários.
     * @throws \Exception
     */
    public function usuarioPorNome($nome,$pagina = 1,$qtdeRegistros = 10){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.nome.get');

        $url = $this->getUrlAPI()."v1/cidadaos/nome/{$nome}/{$pagina}/{$qtdeRegistros}";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ]
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 404:
                return null;
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Pesquisar usuários por email.
     * @param string $email Email do usuário a ser pesquisado.
     * @return Object Usuário pesquisado.
     * @throws \Exception
     */
    public function usuarioPorEmail($email){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.email.get');

        $url = $this->getUrlAPI()."v1/cidadaos/email/{$email}";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ]
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 404:
                return null;
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Obter usuários vinculados a um Grupo.
     * @param string $nomeGrupo Nome do grupo | A busca não é parcial, ou seja, a grafia do nome deve igual ao cadastrado. | O serviço não é case sensitive. exemplo - ADM%20meu%20grupo.
     * @param int $pagina Número a partir do qual a busca será realizada. ex: 50 > a busca será feita a partir do registro 50.
     * @param int $qtdeRegistros Quantidade de registros a serem retornados.
     * @return Object Contendo a lista com os usuários do sistema e as informações de paginação.
     * @throws \Exception
     */
    public function usuarioVinculadosGrupo($nomeGrupo,$pagina = 1,$qtdeRegistros = 10){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.grupo.get');

        $url = $this->getUrlAPI()."v1/cidadaos/grupo/{$nomeGrupo}/{$pagina}/{$qtdeRegistros}";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ]
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 404:
                return null;
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Solicitar Auto Cadastro.
     * @param array $email Emails no formato Array. ex: ["fulano@celepar.pr.gov.br", "joaops@celepar.pr.gov.br"]
     * @return mixed True caso envie todos as solicitações | Object com os e-mails inválidos (não enviou solicitação).
     * @throws \Exception
     */
    public function solicitarAutoCadastro(Array $email){
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadaos.autocadastro.post');

        $url = $this->getUrlAPI()."v1/cidadaos/autoCadastro";

        $client = new Client(['exceptions' => false]);
        $res = $client->request('POST', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ],
            'json'=>$email
        ]);
        switch ($res->getStatusCode()) {
            case 200:
                return true;
                break;
            case 403:
                return json_decode($res->getBody()->getContents());
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Vincular usuários a um grupo.
     * @param array $cpf Lista de CPFs no formato Array.
     * @param string $nomeGrupo Nome do grupo onde os usuários serão vinculados.
     * @return bool|mixed
     * @throws \Exception
     */
    public function vincularUsuarioGrupo(Array $cpf, $nomeGrupo)
    {
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.grupos.cidadaos.put');

        $url = $this->getUrlAPI().'v1/grupos/'.$nomeGrupo.'/cidadaos';

        $client = new Client(['exceptions' => false]);
        $res = $client->request('PUT', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ],
            'json' => $cpf
        ]);

        switch ($res->getStatusCode()) {
            case 200:
                return true;
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            case 500:
                return json_decode($res->getBody()->getContents());
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Desvincular usuários de um grupo.
     * @param array $cpf Lista de CPFs no formato Array. ex: ["12345678911", "98765432140"]
     * @param string $nomeGrupo nome do grupo de onde os usuários serão desvinculados.
     * @return bool
     * @throws \Exception
     */
    public function desvincularUsuarioGrupo(Array $cpf, $nomeGrupo)
    {
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.grupos.cidadaos.delete');

        $url = $this->getUrlAPI().'v1/grupos/'.$nomeGrupo.'/cidadaos';

        $client = new Client(['exceptions' => false]);
        $res = $client->request('DELETE', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ],
            'json' => $cpf
        ]);

        switch ($res->getStatusCode()) {
            case 200:
                return true;
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            default:
                throw new \Exception('Erro desconhecido. Código da resposta: '.$res->getStatusCode());
                break;
        }
    }

    /**
     * Obter grupos vinculados ao usuário por CPF
     * @param string $cpf cpf do usuário de quem se deseja obter os grupos. ex: "02752374941"
     * @return array Lista de grupos do usuario
     * @throws \Exception
     */
    public function gruposPorCpf($cpf)
    {
        $accessToken = $this->obterTokenAplicacao('centralcidadao.v1.cidadao.grupos.cpf.get');

        $url = $this->getUrlAPI().'v1/cidadaos/grupos/cpf/'.$cpf;

        $client = new Client(['exceptions' => false]);
        $res = $client->request('GET', $url, [
            'debug'=>$this->debug,
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken->access_token
            ]
        ]);

        switch ($res->getStatusCode()) {
            case 200:
                return json_decode($res->getBody()->getContents());
                break;
            case 403:
                throw new \Exception('Não tem permissão para consumir esse serviço.');
                break;
            default:
                throw new \Exception($res->getBody()->getContents(),$res->getStatusCode());
                break;
        }
    }
}