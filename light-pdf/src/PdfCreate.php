<?php

namespace Celepar\Light\Pdf;

use Requests;


/**
 * Class PdfCreate
 * @package Celepar\Light\Pdf
 * @author Patrik Unterstell
 * Componente para utilizar o serviço de geração de PDF, localizado no servidor servicos.celepar.parana:80/servicos
 */
class PdfCreate {

    public static function make($html, $formats = array(), $options = array())
    {

        $data = [
            'encoding'=>'utf8',
            'html' => $html
        ];

        if(array_key_exists('header_html', $formats)){
            //verifica se existe DOCTYPE
            if(stripos($formats['header_html'], '<!DOCTYPE html>') === false){
                $data['header_html'] = '<!DOCTYPE html>'.$formats['header_html'];
            }
            else{
                $data['header_html'] = $formats['header_html'];
            }
            unset($formats['header_html']);
        }

        if(array_key_exists('footer_html', $formats)){
            //verifica se existe DOCTYPE
            if(stripos($formats['footer_html'], '<!DOCTYPE html>') === false){
                $data['footer_html'] = '<!DOCTYPE html>'.$formats['footer_html'];
            }
            else{
                $data['footer_html'] = $formats['footer_html'];
            }
            unset($formats['footer_html']);
        }

        //Insere as formatações passadas pelo usuario
        foreach ($formats as $k=>$v){
            $data[$k] = htmlentities($v);
        }

        //Faz a requisição ao servidor e recebe o conteúdo processado
        $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        $response = Requests::post(
            config('pdf.urlServidorPdf'),
            $headers,
            json_encode($data),
            ['timeout' => '120', 'connect_timeout' => '120']
        );

        //verifica se foi passado algum tipo de retorno
        if( array_key_exists('return', $options) ){
            if($options['return'] == 'json'){
                return $response->body;
            }
        }
        else{

            //Verifica se foi passado algum nome para o arquivo montado
            if( array_key_exists('fileName', $options) ){
                $fileName = $options['fileName'];
            }
            else{
                $fileName = 'relatorio.pdf';
            }

            //Verifica se foi passado como retornar o arquivo pdf (contentDisposition)
            if( array_key_exists('contentDisposition', $options) ){
                $contentDisposition = $options['contentDisposition'];
            }
            else{
                $contentDisposition = 'attachment; filename="'.$fileName.'"';
            }

            $result = json_decode($response->body, true);
            $headers = [];
            $headers['content-type'] = 'application/pdf';
            $headers['content-transfer-encoding'] = 'binary';
            $headers['content-disposition'] = $contentDisposition;
            return \Response::make(base64_decode($result['result']), 200, $headers);
        }

    }
}

?>