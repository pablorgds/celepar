<?php

namespace Celepar\Light\Neocep;

use Celepar\Light\Neocep\Contracts\NeocepInterface;
use Celepar\Light\Neocep\Exceptions\NeocepException;
use Illuminate\Routing\Controller as BaseController;
use GuzzleHttp\Client;
use Exception;
use Cache;
use Response;

class NeocepRest extends BaseController implements NeocepInterface
{
    protected function connect($service,$args = [])
    {
        try {
            $client = new Client();
            $url = config('neocep.url-rest-service')[app()->environment()] . $service . "/" . implode("/",$args)  ;
            $response = $client->request('GET', $url);
            $content = $response->getBody()->getContents();
            return json_decode(iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($content)));
        }catch (Exception $e){
            switch($e->getCode()){
                case 500:
                    throw new NeocepException('Erro ao consultar serviço rest do NeoCep.',500);
                    break;
                case 404:
                    throw new NeocepException('Parametros incompletos ou serviço não encontrado no NeoCep.',404);
                    break;
                default:
                    throw new NeocepException('Erro inesperado ao conectar ao serviço REST do NeoCep. Exception:'.$e->getMessage(),$e->getCode());
                    break;
            }
        }
    }

    /**
     * Lista Países
     *
     * @return array - contendo as seguintes informações (Sigla,Nome,Sigla3Letras)
     */
    public function paises()
    {
        return Cache::remember('neocep-paises',config('neocep.time-cache'),function(){
            return (Array) $this->connect('paises');
        });
    }

    /**
     * Lista unidades de federação (UF)
     *
     * @return array - contendo as seguintes informações (Sigla,Pais,Nome)
     */
    public function ufs()
    {
        return Cache::remember('neocep-ufs',config('neocep.time-cache'),function() {
            return (Array) $this->connect('ufs');
        });
    }

    /**
     * Lista localidades
     *
     * @param $siglaUf - Sigla da Unidade Federativa
     * @param string $tipoLocalidade - Código de identificação do tipo de localidade (M-Município, P-Povoado, D-Distrito e R-Região). Default = M
     * @param int $tipoCodificacaoLocalidadeRetorno - Solicita retorno de chave codificada de acordo com as tabelas (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0
     * @return array - contendo as seguintes informações (cep,uf,situacao,nome,chave,tipoLocalidade,localidadeSuperior)
     */
    public function localidades($siglaUf, $tipoLocalidade = "M", $tipoCodificacaoLocalidadeRetorno = null)
    {
        if(is_null($tipoCodificacaoLocalidadeRetorno)){
            $tipoCodificacaoLocalidadeRetorno = config('neocep.codificacao-padrao');
        }
        $nameCache = 'neocep-ufs'.$siglaUf.$tipoLocalidade.$tipoCodificacaoLocalidadeRetorno;
        return Cache::remember($nameCache,config('neocep.time-cache'),function() use ($siglaUf, $tipoLocalidade, $tipoCodificacaoLocalidadeRetorno) {
            $this->validaTipoCodificacao($tipoCodificacaoLocalidadeRetorno);
            return (Array) $this->connect('localidades', [$siglaUf, $tipoLocalidade, $tipoCodificacaoLocalidadeRetorno]);
        });
    }

    /**
     * Obter localidade
     *
     * @param $chaveLocalidade - Código da localidade
     * @param int $tipoCodificacaoLocalidade - Codificação de município a ser utilizada (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0
     * @param int $tipoCodificacaoLocalidadeRetorno - Solicita retorno de chave codificada de acordo com as tabelas (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0
     * @return array - contendo as seguintes informações (cep,uf,situacao,nome,chave,tipoLocalidade,localidadeSuperior)
     */
    public function localidade($chaveLocalidade, $tipoCodificacaoLocalidade = null, $tipoCodificacaoLocalidadeRetorno = null)
    {
        if(is_null($tipoCodificacaoLocalidade)){
            $tipoCodificacaoLocalidade = config('neocep.codificacao-padrao');
        }
        if(is_null($tipoCodificacaoLocalidadeRetorno)){
            $tipoCodificacaoLocalidadeRetorno = config('neocep.codificacao-padrao');
        }
        $this->validaTipoCodificacao($tipoCodificacaoLocalidade);
        $this->validaTipoCodificacao($tipoCodificacaoLocalidadeRetorno);
        return (Array) $this->connect('localidade',[$chaveLocalidade,$tipoCodificacaoLocalidade,$tipoCodificacaoLocalidadeRetorno]);
    }

    /**
     * Listar Bairros
     *
     * @param $chaveBairro - Código do bairro (Código dos correios)
     * @return array - contendo as seguintes informações (abreviatura,localidade,nome,chave)
     */
    public function bairros($chaveBairro)
    {
        return (Array) $this->connect('bairros',[$chaveBairro]);
    }

    /**
     * Localiza logradouro por nome
     *
     * @param $nomeLogradouro - Nome total ou parcial do logradouro desejado.
     * @param $chaveLocalidade - Código da localidade
     * @param $tipoCodificacaoLocalidade - Informa o tipo de codificação da chave localidade a ser utilizada na pesquisa (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0
     * @return array - contendo as seguintes informações (complemento,cep,tipo,indicadorTipo,nome,chave,bairroInicial,bairroFinal,paridadeLadoSeccionamento,numeroInicial,numeroFinal)
     */
    public function logradouro($nomeLogradouro, $chaveLocalidade, $tipoCodificacaoLocalidade = null)
    {
        if(is_null($tipoCodificacaoLocalidade)){
            $tipoCodificacaoLocalidade = config('neocep.codificacao-padrao');
        }
        $this->validaTipoCodificacao($tipoCodificacaoLocalidade);
        return (Array) $this->connect('logradouro',[$nomeLogradouro,$chaveLocalidade,$tipoCodificacaoLocalidade]);
    }

    /**
     * Recupera dados de endereco referente ao cep informado.
     *
     * @param $cep - Código de endereçamento postal
     * @param $tipoCodificacaoLocalidadeRetorno - Solicita retorno de chave codificada da localidade, de acordo com as tabelas (0-CORREIOS, 1-SERPRO e 2-IBGE). Default = 0
     * @return array - contendo as seguintes informações (numeroInicial,bairro,complemento,cep,uf,tipoCep,localidade,tipo,nome,numeroFinal,isCepGenerico)
     */
    public function endereco($cep, $tipoCodificacaoLocalidadeRetorno = null)
    {
        if(is_null($tipoCodificacaoLocalidadeRetorno)){
            $tipoCodificacaoLocalidadeRetorno = config('neocep.codificacao-padrao');
        }
        $cep = $this->validaFormataCep($cep);
        $this->validaTipoCodificacao($tipoCodificacaoLocalidadeRetorno);
        $endereco = $this->connect('endereco', [$cep, $tipoCodificacaoLocalidadeRetorno]);

        if(count($endereco) > 0) {
            return (Array)$endereco[0];
        }else{
            return [];
        }
    }

    /**
     * Valida se o tipo de codificação passado é um tipo válido
     *
     * @param $tipoCodificacao - 0-CORREIOS, 1-SERPRO e 2-IBGE
     * @throws NeocepException
     */
    protected function validaTipoCodificacao($tipoCodificacao){
        if($tipoCodificacao != 0 && $tipoCodificacao != 1 && $tipoCodificacao != 2){
            throw new NeocepException('Tipo de codificação da localidade não é válido. Os valores aceitos são 0-CORREIOS, 1-SERPRO e 2-IBGE. O valor padrão(caso não seja informado) é 0 (zero).', '002');
        }
    }

    /**
     * Valida se o cep passado é válido, caso seja, retira pontos e traços para enviar ao serviço somente os numeros
     *
     * @param $cep - Código de endereçamento postal
     * @return mixed - CEP sem nenhuma formatação
     * @throws NeocepException - Erro caso não consiga deixar o cep em um formato válido
     */
    protected function validaFormataCep($cep){
        $cep = str_replace(['.','-',' '], "", trim($cep));
        if(!preg_match("/^[0-9]{8}$/", $cep)){
            throw new NeocepException('Formato de cep inválido. Formatos aceitos: 99.999-999, 99999-999 ou ainda 99999999','001');
        }
        return $cep;
    }
}