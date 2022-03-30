<?php
namespace Celepar\Gopmp;

/**
 * Classe de gera��o de documentos XML para o OpenGOP
 * @author Guilherme Striquer Bisotto gbisotto@celepar.pr.gov.br
 * @version 1.0
 * @license http://www.gnu.org/copyleft/gpl.html GPL
 * @package OpenGOPBuilder
 */
class OpenGOPBuilder extends \DOMDocument {

	/**
	 * @var DOMNode $elementOpenGOP Elemento raiz do documento
	 * @access private
	 */
	private $elementOpenGOP = null;

	/**
	 * @var DOMNode $elementTitle Elemento com o titulo do monitoramento
	 * @access private
	 */
	private $elementTitle   = null;
	/**
	 * @var array $testElements Array com os elementos de teste
	 * @access private
	 */
	private $testElements   = array();

	/**
	 * Construtora da classe OpenGOPBuilder
	 * @params string $version Vers�o do XML a ser gerada. Padr�o: 1.0
	 * @params string $encoding Condifica��o de caracteres do XML gerado. Padr�o: ISO-8859-1
	 * @return mixed Objeto da classe OpenGOPBuilder
	 * @access public
	 */
	public function __construct($version = '1.0', $encoding = 'ISO-8859-1'){
		parent::__construct($version, $encoding);
	}

	/**
	 * Seta o t�tulo do monitoramento.
	 * @params string $title T�tulo do monitoramento
	 * @return boolean True caso o elemento de titulo tenha sido criado com sucesso, false caso contr�rio
	 * @access public
	 */
	public function setTitle($title){
		$this->elementTitle = $this->createElement('titulo', $title);
		
		return ($this->elementTitle !== false) ? true : false;
	}

	/**
	 * Constr�i o documento DOMDocument que formar� o XML
	 * @return boolean True caso a �rvore tenha sido constru�da com sucesso e false caso contr�rio
	 * @access private
	 */
	private function buildTree(){
		if(!$this->elementTitle)
			return false;

		$this->elementOpenGOP = $this->createElement('opengop');
		if($this->elementOpenGOP === false)
			return false;

		$this->elementOpenGOP->appendChild($this->elementTitle);
		foreach($this->testElements as $element){
			$this->elementOpenGOP->appendChild($element);
		}
		
		/*echo "<pre>";
		print_r($this->elementTitle);
		echo "</pre>";
		die();*/
		
		$this->appendChild($this->elementOpenGOP);
		return true;
	}

	/**
	 * Adiciona um �tem de teste � arvore do documento
	 * @params string $testName O nome do teste
	 * @params string $description A descri��o do teste
	 * @params boolean $testValue Resultado do teste
	 * @params string $msgErro Mensagem de erro que ser� exibida caso o $testValue seja false
	 * @return boolean True caso o elemento tenha sido criado com sucesso e false caso contr�rio
	 * @access public
	 */
	public function addItem($testName, $description, $testValue, $msgErro){
		
		$testeElement = $this->createElement('teste');
		if($testeElement === false)
			return false;
		
		$nomeElement = $this->createElement('nome', $testName);
		if($nomeElement === false)
			return false;
		
		$descricaoElement = $this->createElement('descricao', $description);
		if($descricaoElement === false)
			return false;
		
		if($testValue){
			$statusElement = $this->createElement('status', 'OK');
			if($statusElement === false)
				return false;

			$msgErroElement = $this->createElement('msgerro');
			if($msgErroElement === false)
				return false;
		} else {
			$statusElement = $this->createElement('status', 'ERRO');
			if($statusElement === false)
				return false;

			$msgErroElement = $this->createElement('msgerro', $msgErro);
			if($msgErroElement === false)
				return false;
		}

		$testeElement->appendChild($nomeElement);
		$testeElement->appendChild($descricaoElement);
		$testeElement->appendChild($statusElement);
		$testeElement->appendChild($msgErroElement);

		array_push($this->testElements, $testeElement);
		
		return true;
	}

	/**
	 * Adiciona um �tem de teste em que o resultado foi positivo
	 * @params string $testName Nome do teste
	 * @params string $description Descri��o do teste
	 * @return boolean True caso o elemento tenha sido criado com sucesso e false caso contr�rio
	 * @access public
	 */
	public function addItemOK($testName, $description){

		$testeElement = $this->createElement('teste');
		if($testeElement === false)
			return false;

		$nomeElement = $this->createElement('nome', $testName);
		if($nomeElement === false)
			return false;

		$descricaoElement = $this->createElement('descricao', $description);
		if($descricaoElement === false)
			return false;

		$statusElement = $this->createElement('status', 'OK');
		if($statusElement === false)
			return false;

		$msgErroElement = $this->createElement('msgerro');
		if($msgErroElement === false)
			return false;

		$testeElement->appendChild($nomeElement);
		$testeElement->appendChild($descricaoElement);
		$testeElement->appendChild($statusElement);
		$testeElement->appendChild($msgErroElement);

		array_push($this->testElements, $testeElement);
		return true;
	}

	/**
	 * Adiciona um �tem de teste em que o resultado do teste foi negativo
	 * @params string $testName O nome do teste
	 * @params string $description A descri��o do teste
	 * @params string $msgErro A mensagem que dever� ser mostrada na console
	 * @return boolean True caso o elemento tenha sido criado com sucesso e false caso contr�rio
	 * @access public
	 */
	public function addItemComErro($testName, $description, $msgErro){

		$testeElement = $this->createElement('teste');
		if($testeElement === false)
			return false;

		$nomeElement = $this->createElement('nome', $testName);
		if($nomeElement === false)
			return false;

		$descricaoElement = $this->createElement('descricao', $description);
		if($descricaoElement === false)
			return false;

		$statusElement = $this->createElement('status', 'ERRO');
		if($statusElement === false)
			return false;

		$msgErroElement = $this->createElement('msgerro', $msgErro);
		if($msgErroElement === false)
			return false;

		$testeElement->appendChild($nomeElement);
		$testeElement->appendChild($descricaoElement);
		$testeElement->appendChild($statusElement);
		$testeElement->appendChild($msgErroElement);

		array_push($this->testElements, $testeElement);
		return true;
	}

	/**
	 * Constr�i e imprime o documento XML
	 * @return string Retorna o documento XML em forma de texto.
	 * @access public
	 */
	public function printXML(){
		if(!$this->buildTree()){
			$this->appendChild($this->createElement('opengop', "ERRO AO CONSTRUIR A ARVORE XML"));
		}

		@header('content-type: text/xml');
		echo $this->saveXML();
	}
}