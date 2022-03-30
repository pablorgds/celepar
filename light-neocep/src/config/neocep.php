<?php
return [
    /**
     * Codificação de município a ser utilizada por padrão, as opções são:
     * 0-CORREIOS
     * 1-SERPRO
     * 2-IBGE
     *
     * Obs: Esse parametro identifica o valor default, mas ainda é possível setar um diferente em cada metodo caso seja necessário
     */
    "codificacao-padrao" => 0,

    /**
     * Tempo em milisegundos que o cache irá ser persistido.
     *
     * O cache é aplicado somente nas funções de pais, uf e localidades(cidades)
     */
    "time-cache" => 1440,

    /**
     * Url de acesso ao serviço rest do Neocep Celepar
     *
     * Os parâmetros abaixo já vem configurado corretamente e só deve ser alterado em caso de alteração da URL na gic.
     *
     * ATENÇÂO: O pacote está preparado para trabalhar com a versão que vem configurado na URL do serviço, alterar a versão na URL pode causar erros no pacote
     */
    "url-rest-service" => [
        "local" => "http://www.gic.desenvolvimento.eparana.parana/cep/api/v1.0/enderecamento/",
        "homologacao" => "http://www.gic.homologacao.eparana.parana/cep/api/v1.0/enderecamento/",
        "producao" => "http://www.neocep.pr.gov.br/cep/api/v1.0/enderecamento/"
    ],
];