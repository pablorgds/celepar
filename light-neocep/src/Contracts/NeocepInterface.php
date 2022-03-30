<?php
/**
 * Created by PhpStorm.
 * User: roberson.faria
 * Date: 22/12/15
 * Time: 12:32
 */

namespace Celepar\Light\Neocep\Contracts;


interface NeocepInterface
{

    public function paises();

    public function ufs();

    public function localidades($siglaUf, $tipoLocalidade, $tipoCodificacaoLocalidadeRetorno);

    public function localidade($chaveLocalidade, $tipoCodificacaoLocalidade, $tipoCodificacaoLocalidadeRetorno);

    public function bairros($chaveBairro);

    public function logradouro($nomeLogradouro, $chaveLocalidade, $tipoCodificacaoLocalidade);

    public function endereco($cep, $tipoCodificacaoLocalidadeRetorno);

}