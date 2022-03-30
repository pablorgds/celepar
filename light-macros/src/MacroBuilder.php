<?php namespace Celepar\Light\Macros;

use Event;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Request;
use Illuminate\View\View;

class MacroBuilder extends \Collective\Html\FormBuilder
{
    //Variavel para armezenar os erros da validator
    private $errors;

    private $scriptsPath = 'vendor/macros/js/';
    private $cssPath = 'vendor/macros/css/';

    public function __construct(\Collective\Html\HtmlBuilder $html, \Illuminate\Routing\UrlGenerator $url, \Illuminate\Contracts\View\Factory $view,  $csrfToken)
    {
        parent::__construct($html, $url, $view, $csrfToken);
        $this->errors = \Session::get('errors', new MessageBag());

        if (is_null($this->model)) {
            $this->model( app('form')->model ); //obtenho o model do Form::model
        }
    }

    public function model($model, array $options = array())
    {
        $this->setModel($model);
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Macro para criação de campos tipo input text
     * @author: jakjr / pmenezes
     * @param string $name Nome do componente
     * @param null $value
     * @param array $options parâmentros opcionais de customização da tag html
     * @param array $params parâmetros opcionais
     * @return string
     */
    public function text($name, $value = null, $options=array(), $params=array())
    {
        if (isset($options['data-mask'])) {
            $this->insertScript('jquery.inputmask.bundle.min.js');
            $this->insertScript('mask.js');
        }

        return $this->makeInput('text', $name, $value, $options, $params);
    }

    public function password($name, $value = null, $options=array(), $params=array())
    {
        return $this->makeInput('password', $name, $value, $options, $params);
    }

    public function email($name, $value = null, $options=array(), $params=array())
    {
        return $this->makeInput('email', $name, $value, $options, $params);
    }

    public function tel($name, $value = null, $options=array(), $params=array())
    {
        return $this->makeInput('tel', $name, $value, $options, $params);
    }

    public function number($name, $value = null, $options=array(), $params=array())
    {
        return $this->makeInput('number', $name, $value, $options, $params);
    }

    public function date($name, $value = null, $options=array(), $params=array())
    {
        return $this->makeInput('date', $name, $value, $options, $params);
    }

    public function time($name, $value = null, $options=array(), $params=array())
    {
        return $this->makeInput('time', $name, $value, $options, $params);
    }

    public function url($name, $value = null, $options=array(), $params=array())
    {
        return $this->makeInput('url', $name, $value, $options, $params);
    }

    protected function makeInput($type, $name, $value = null, $options=array(), $params=array())
    {
        $params = array_merge($this->makeDefaultValues(), $params);

        $this->setDefaultValue($options['id'], $name);

        $this->setDefaultValue($params['label'], "*$name");
        $tagLabel = $this->makeTagLabel($options, $params);

        if (isset($options['class'])) {
            $options = array_merge($options, ['class'=>"form-control {$options['class']}"] );
        } else {
            $options = array_merge($options, ['class'=>"form-control"]);
        }

        $tagText = $this->input($type, $name, $value, $options);

        $tagText = $this->getIconBlock($params, $tagText);

        return $this->getHtmlBlock($name, $tagLabel, $tagText, $params);

    }


    /**
     * Macro para criação de campos tipo textarea
     * @author: jakjr / pmenezes
     * @param string $name Nome do componente
     * @param null $value
     * @param array $options parâmentros opcionais de customização da tag html
     * @param array $params parâmetros opcionais
     * @return string
     */
    public function textarea($name, $value = null, $options = array(), $params = array())
    {
        //Unificando valores passados pela view com valores padrões
        $params = array_merge($this->makeDefaultValues(), $params);

        $this->setDefaultValue($options['id'], $name);
        $this->setDefaultValue($params['label'], "*$name");

        $tagLabel = $this->makeTagLabel($options, $params);

        if (isset($options['class'])) {
            $options = array_merge($options, ['class'=>"form-control {$options['class']}"] );
        }
        else {
            $options = array_merge($options, ['class'=>"form-control"]);
        }

        $tagTextArea = parent::textarea($name, $value, $options);
        $tagTextArea = $this->getIconBlock($params, $tagTextArea);

        return $this->getHtmlBlock($name, $tagLabel, $tagTextArea, $params);
    }



    /**
     * Macro para criação de campos tipo textarea com tinyMce
     * @author: jakjr
     * @param string $name Nome do componente
     * @param null $value
     * @param array $options parâmentros opcionais de customização da tag html
     * @param array $params parâmetros opcionais
     * @return string
     */
    public function tinyMce($name, $value = null, $options = array(), $params = array())
    {
        $this->insertScriptOnHead('tinymce/tinymce.min.js');
        $this->insertScriptOnHead('tinymce/config.js');

        //Unificando valores passados pela view com valores padrões
        $params = array_merge($this->makeDefaultValues(), $params);

        $this->setDefaultValue($options['id'], $name);
        $this->setDefaultValue($params['label'], "*$name");

        $tagLabel = $this->makeTagLabel($options, $params);

        if (isset($options['class'])) {
            $options = array_merge($options, ['class'=>"form-control {$options['class']}"] );
        }
        else {
            $options = array_merge($options, ['class'=>"form-control tinymce"]);
        }

        $tagTextArea = parent::textarea($name, $value, $options);
        $tagTextArea = $this->getIconBlock($params, $tagTextArea);

        return $this->getHtmlBlock($name, $tagLabel, $tagTextArea, $params);
    }


    /**
     * Macro para criação de campos de seleção
     * instruções para utilizar o multiple:
     * adicionar o javascript e css:
     * {{ HTML::script('assets/plugins/jquery.multi-select.js') }}
     * {{ HTML::style('assets/css/multi-select.css') }}
     * o nome do select deve terminar com []
     * no array options deve ser passado os elementos: 'class'=>'multi-select', 'multiple'
     * @author: jakjr / pmenezes
     * @param string $name Nome do componente
     * @param array $list array contendo os valores do campo de seleção a serem listados
     * @param null $selected
     * @param array $options parâmentros opcionais de customização da tag html
     * @param array $params parâmetros opcionais
     * @return string
     */
    public function select($name, $list = array(), $selected = null, $options=array(), $params=array())
    {
        //Unificando valores passados pela view com valores padrões
        $params = array_merge($this->makeDefaultValues(), $params);

        $this->setDefaultValue($options['id'], $name);
        $this->setDefaultValue($params['label'], "*$name");

        $tagLabel = $this->makeTagLabel($options, $params);

        if (isset($options['class'])) {
            $options = array_merge($options, ['class'=>"form-control {$options['class']}"] );
        }
        else {
            $options = array_merge($options, ['class'=>"form-control"]);
        }

        $tagSelect = parent::select($name, $list, $selected, $options);
        $tagSelect = $this->getIconBlock($params, $tagSelect);

        return $this->getHtmlBlock($name, $tagLabel, $tagSelect, $params);
    }

    /**
     * alias para a criação de um multiselect
     * @param $name
     * @param array $list
     * @param null $selected
     * @param array $options
     * @param array $params
     * @return string
     */
    public function multiselect($name, $list = array(), $selected = null, $options=array(), $params=array())
    {
        $this->insertScript('jquery.multi-select.js');
        $this->insertCss('multi-select.css');

        $options = array_merge($options, ['class'=>'multi-select', 'multiple']);
        return $this->select($name, $list, $selected, $options, $params);
    }

    /**
     * Macro para criação de campos tipo checkbox
     * @author: jakjr / pmenezes
     * @param string $name Nome do componente
     * @param int $value
     * @param null $checked
     * @param array $options parâmentros opcionais de customização da tag html
     * @param array $params parâmetros opcionais
     * @internal param array $list array contendo os valores dos campos checkbox a serem exibidos
     * @return string
     */
    public function checkbox($name, $value = 1, $checked = null, $options = array(), $params = array())
    {
        //Unificando valores passados pela view com valores padrões
        $params = array_merge($paramsDefault = $this->makeDefaultValues(), $params);

        $this->setDefaultValue($options['id'], $name);
        $this->setDefaultValue($params['label'], "*$name");

        $options = array_merge($options, array('class'=>'light'));

        $tag = parent::checkbox($name, $value, $checked, $options);

        if ($params['inline']) {
            return <<<MACRO
<div class="checkbox light-inline">
    <label>
        {$tag} {$params['label']}
    </label>
</div>
MACRO;
        }
        else {
            $tagLabel = parent::label($options['id'], $params['label'], array('class'=>'col-md-3 control-label'));

            $transformedKey = $this->transformKey($name);
            $errorSpan = $this->getError($transformedKey) ? "<span class='help-block'>{$this->getError($transformedKey)}</span>" : '';
            $helpSpan = $params['help'] ? "<span class='help-block'>{$params['help']}</span>" : '';

            return <<<MACRO
<div class="form-group {$this->getErrorClass($transformedKey)}">
   	$tagLabel
    <div class="col-md-9 light-checkebox">
   	    $tag
        $errorSpan
        $helpSpan
    </div>
</div>
MACRO;
            return $block;
        }
    }

    /**
     * Macro para criação de campos tipo checkbox groups do bootstrap
     * http://getbootstrap.com/javascript/#buttons-checkbox-radio
     * @author: roberson.faria
     * @param string $name Nome do componente
     * @param array $list
     * @param array $options parâmentros opcionais de customização da tag html
     * @param array $params parâmetros opcionais
     * @internal param array $list array contendo os valores dos campos checkbox a serem exibidos
     * @return string
     */
    public function checkboxToggle($name, $list, $options=array(), $params=array())
    {
        $params = array_merge($this->makeDefaultValues(['checkedValue'=>null]), $params );

        $this->setDefaultValue($options['id'], $name);

        $this->setDefaultValue($params['label'], "*$name");

        $tagLabel = parent::label($options['id'], $params['label'], array('class'=>'col-md-3 control-label'));

        $tagRadio = '<div class="btn-group" data-toggle="buttons">';
        foreach ($list as $key => $text){
            $checked = false;
            $labelClass = '';

            if($key == $params['checkedValue']){
                $checked = true;
                $labelClass = ' active';
            } else {
                if ($key == $this->getValueAttribute($name)) {
                    $labelClass = ' active';
                }
            }

            $tagRadio .= "<label class='btn btn-primary$labelClass'>";
            $tagRadio .= parent::checkbox($name, $key, $checked, $options);
            $tagRadio .= " $text";
            $tagRadio .= '</label>';
        }
        $tagRadio .= '</div>';

        return $this->getHtmlBlock($name, $tagLabel, $tagRadio, $params);
    }

    /**
     * Macro para criação de campos tipo radio
     * @author: jakjr / pmenezes
     * @param string $name Nome do componente
     * @param array $list array contendo os valores dos campos radio a serem exibidos
     * @param array $params parâmetros opcionais
     * @param array $options parâmentros opcionais de customização da tag html
     * @return string
     */
    public function radioList($name, $list, $options=array(), $params=array())
    {
        //Unificando valores passados pela view com valores padrões
        $params = array_merge($this->makeDefaultValues(['checkedValue'=>null]), $params );

        $this->setDefaultValue($options['id'], $name);

        $this->setDefaultValue($params['label'], "*$name");

        $tagLabel = parent::label($options['id'], $params['label'], array('class'=>'col-md-3 control-label'));

        //$tagRadio = '<div class="light radio-list">';
        $tagRadio = '';

        foreach ($list as $key => $value){
            $checked = false;
            if($key == $params['checkedValue']){
                $checked = true;
            }

            $tagRadio .= '<label class="radio-inline">
								<span>';
            $tagRadio .= parent::radio($name, $key, $checked, $options);
            $tagRadio .= 		'</span>
							'. $value .'
						</label>';
        }
        //$tagRadio .= '</div>';

        $tagRadio = $this->getIconBlock($params, $tagRadio);

        return $this->getHtmlBlock($name, $tagLabel, $tagRadio, $params);
    }


    /**
     * Macro para criação de radio groups do bootstrap
     * http://getbootstrap.com/javascript/#buttons-checkbox-radio
     */
    public function radioToggle($name, $list, $options=array(), $params=array())
    {
        $params = array_merge($this->makeDefaultValues(['checkedValue'=>null]), $params );

        $this->setDefaultValue($options['id'], $name);

        $this->setDefaultValue($params['label'], "*$name");

        $tagLabel = parent::label($options['id'], $params['label'], array('class'=>'col-md-3 control-label'));

        $tagRadio = '<div class="btn-group" data-toggle="buttons">';
        foreach ($list as $key => $text){
            $checked = false;
            $labelClass = '';

            if($key == $params['checkedValue']){
                $checked = true;
                $labelClass = ' active';
            } else {
                if ($key == $this->getValueAttribute($name)) {
                    $labelClass = ' active';
                }
            }

            $tagRadio .= "<label class='btn btn-default$labelClass'>";
            $tagRadio .= parent::radio($name, $key, $checked, $options);
            $tagRadio .= " $text";
            $tagRadio .= '</label>';
        }
        $tagRadio .= '</div>';

        return $this->getHtmlBlock($name, $tagLabel, $tagRadio, $params);
    }



    /**
     * Macro para criação de campos tipo file
     * @author: jakjr / pmenezes
     * @param string $name Nome do componente
     * @param array $params parâmetros opcionais
     * @param array $options parâmentros opcionais de customização da tag html
     * @return string
     */
    public function file($name, $options = array(), $params=array())
    {
        //Unificando valores passados pela view com valores padrões
        $params = array_merge($this->makeDefaultValues(), $params);

        $this->setDefaultValue($options['id'], $name);
        $this->setDefaultValue($params['label'], "*$name");

        $tagLabel = parent::label($options['id'], $params['label'], array('class'=>'col-md-3 control-label'));

        $tagFile = parent::file($name, $options);
        $tagFile = $this->getIconBlock($params, $tagFile);

        return $this->getHtmlBlock($name, $tagLabel, $tagFile, $params);

    }

    /**
     * Macro para criação de campos input com autocomplete restrito
     * @author: jakjr
     * @param string $name Nome do componente
     * @param null $value
     * @param array $options parâmentros opcionais de customização da tag html
     * @param array $params parâmetros opcionais
     * @return string
     */
    public function autocompleteRestrict($name, $value = null, $options=array(), $params=array())
    {
        //Inserindo valores padrões da tag
        $paramsDefault = $this->makeDefaultValues();

        //Unificando valores passados pela view com valores padrões
        $params = array_merge($paramsDefault, $params);

        $this->setDefaultValue($options['id'], $name);
        $this->setDefaultValue($params['label'], "*$name");
        $tagLabel = $this->makeTagLabel($options, $params);

        if (isset($options['class'])) {
            $options = array_merge($options, ['class'=>"form-control {$options['class']}"] );
        }
        else {
            $options = array_merge($options, ['class'=>"form-control"]);
        }

        $tagTextTarget = parent::text("{$name}_target", $value, $options);
        $tagTextConcrete = parent::text($name, null, array('class'=>'hide'));

        $tagTextTarget =
            "<div class='input-group'>
            <span class='input-group-addon'>
                <i id='{$name}_spinner' class='fa fa-spinner'></i>
            </span>
            $tagTextTarget
        </div>";

        $helpSpan = $params['help'] ? "<span for=$name class='help-block'>{$params['help']}</span>" : '';

        return <<<MACRO
<div class="form-group {$this->getErrorClass($name)}">
   	$tagLabel
    <div class="col-md-9">
   	    $tagTextTarget
        $tagTextConcrete
        <span class="help-block hide" id="{$name}_msg">* Campo inválido. Selecione um item da listagem.</span>
        $helpSpan
    </div>
</div>
MACRO;
        return $block;
    }


    /**
     * Retorna um bloco html contendo um icone, quando este for especificado
     * @param $params
     * @param $tagHtml
     * @return string
     */
    private function getIconBlock($params, $tagHtml)
    {
        if ($params['icon']) {
            return "
    		<div class='input-group'>
        		<span class='input-group-addon'>
    	        	<i class='{$params['icon']}'></i>
    		    </span>
    		    $tagHtml
    		</div>";
        }
        return $tagHtml;
    }

    /**
     * Retorna uma string que representa um classe de erro, quando existir um erro
     * @param $name
     * @return string
     */
    private function getErrorClass($name)
    {
        if ($this->errors->first($name)) {
            return 'has-error';
        }
    }

    /**
     * Retorna o erro relativo a uma tag html
     * @param $name
     * @return mixed
     */
    private function getError($name)
    {
        $this->errors = \Session::get('errors', new MessageBag());
        return $this->errors->first($name);
    }

    private function setDefaultValue(&$field, $defaultValue)
    {
        if (!isset($field)) {
            $field = $defaultValue;
        }
    }

    /**
     * Retorna um bloco html contendo o bloco do componente a ser exibido
     * @param $name
     * @param $tagLabel
     * @param $tagComponent
     * @param $params
     * @return string
     */
    private function getHtmlBlock($name, $tagLabel, $tagComponent, $params)
    {
        if (isset($params['inline']) && $params['inline']) {
            return $this->getInlineHtmlBlock($name, $tagLabel, $tagComponent, $params);
        }
        else {
            return $this->getHorizontalHtmlBlock($name, $tagLabel, $tagComponent, $params);
        }
    }

    /**
     * retorna um bloco horizontal
     * @param $name
     * @param $tagLabel
     * @param $tagComponent
     * @param $params
     * @return string
     */
    private function getHorizontalHtmlBlock($name, $tagLabel, $tagComponent, $params)
    {
        //https://github.com/laravel/laravel/commit/3c36c0ed220d0718d07f8840e0fe75e34b38164a
        $transformedKey = $this->transformKey($name);

        $errorSpan = '';
        if ($e = $this->getError($transformedKey)) {
            $errorSpan = "<span for=$name class='help-block'>$e</span>";
        }
        $helpSpan = $params['help'] ? "<span class='help-block'>{$params['help']}</span>" : '';

        return <<<MACRO
<div class="form-group {$this->getErrorClass($transformedKey)}">
   	$tagLabel
    <div class="col-md-9">
   	    $tagComponent
        $errorSpan
        $helpSpan
    </div>
</div>
MACRO;
        return $block;
    }

    /**
     * retorna um bloco in-line
     * @param $name
     * @param $tagLabel
     * @param $tagComponent
     * @param $params
     * @return string
     */
    private function getInlineHtmlBlock($name, $tagLabel, $tagComponent, $params)
    {
        //https://github.com/laravel/laravel/commit/3c36c0ed220d0718d07f8840e0fe75e34b38164a
        $transformedKey = $this->transformKey($name);

        $errorSpan = '';
        if ($e = $this->getError($transformedKey)) {
            $errorSpan = "<span class='help-block'>$e</span>";
        }

        $helpSpan = $params['help'] ? "<span for=$name class='help-block'>{$params['help']}</span>" : '';

        return <<<MACRO
<div class="form-group {$this->getErrorClass($transformedKey)}">
   	$tagLabel
   	$tagComponent
    $errorSpan
    $helpSpan
</div>
MACRO;
        return $block;
    }

    /**
     * abre o bloco inline
     * @param string $label
     * @return string
     */
    public function openInline($label='')
    {
        $block = <<<MACRO
<div class="form-group">
    <label class="col-md-3 control-label">$label</label>
    <div class="col-md-9">
        <div class="form-inline">
MACRO;
        return $block;
    }

    /**
     * fecha o bloco inline
     * @return string
     */
    public function closeInline()
    {
        return <<<MACRO
        </div>
    </div>
</div>
MACRO;
    }

    /**
     * retorna um array contendo os default values para a tag
     * @param array $addonValues
     * @return array
     */
    private function makeDefaultValues($addonValues=array())
    {
        return array_merge(array(
            //'id'=>null, //movido para o parametro options
            'label'=>null,
            //'defaultValue'=>null,
            //'placeHolder'=>null, //movido para o parametro options
            'icon'=>null,
            'help'=>null,
            'inline'=>false),
            $addonValues);
    }

    private function makeTagLabel($options, $params)
    {
        if ($params['inline']) {
            return parent::label($options['id'], $params['label'], array('class'=>'sr-only'));
        }
        else {
            return parent::label($options['id'], $params['label'], array('class'=>'col-md-3 control-label'));
        }
    }

    /**
     * Macro para criação do neoCep
     * @author: pmenezes
     * @param array $params parâmetros opcionais
     * @param array $campos
     * @return string
     */
    public function neocep($params=array(),$campos=array('all'))
    {

        $paramsDefault = array(	'defaultValueCEP'=>null,
            'defaultValueUF'=>null,
            'defaultValueMunicipio'=>null,
            'defaultValueEndereco'=>null,
            'defaultValueNumero'=>null,
            'defaultValueBairro'=>null,
            'defaultValueComplemento'=>null
        );

        $params = array_merge($paramsDefault, $params);

        $listUF = array(''=>'---');
        $listMunicipio = array(''=>'---');

        $block =  $this->openInline('CEP')
            .$this->text('cep', $params['defaultValueCEP'], ['placeholder'=>'Pesquise...', 'data-mask'=>'99999999'], ['inline'=>true, 'label'=>'CEP'])
            .'&nbsp;<button type="button" class="btn btn-success" id="neoCepBuscarPorCep"><i class="fa fa-search"></i></button>'
            .$this->closeInline();

        if( in_array('uf', $campos) || in_array('municipio', $campos) || in_array('all', $campos) ){
            $block .= $this->select('uf', $listUF, $params['defaultValueUF'], ['style'=>'width:auto'], ['label'=>'UF']);
            $block .= $this->select('municipio', $listMunicipio, $params['defaultValueMunicipio'], ['style'=>'width:auto'], ['label'=>'Município']);
        }

        $block .= $this->text('endereco', $params['defaultValueEndereco'], ['style'=>'width:60%'], ['label'=>'Endereço']);

        if( in_array('numero', $campos) || in_array('all', $campos) ){
            $block .= $this->text('numero', $params['defaultValueNumero'], ['data-mask'=>'9999'], ['label'=>'Número']);
        }
        if( in_array('bairro', $campos) || in_array('all', $campos) ){
            $block .= $this->text('bairro', $params['defaultValueBairro'], ['style'=>'width:60%'], ['label'=>'Bairro']);
        }
        if( in_array('complemento', $campos) || in_array('all', $campos) ){
            $block .= $this->text('complemento', $params['defaultValueComplemento'], ['style'=>'width:60%'], ['label'=>'Complemento']);
        }

        return $block;
    }


    /**
     * Macro para criação de campos de filtro de pesquisas no header da tabela
     * @author: pmenezes
     * @param $name
     * @param null $value
     * @param array $options
     * @return string
     */
    public function textFilter($name, $value = null, $options=array())
    {
        $value = app('keeper')->get("filter.$name");

        if (isset($options['class'])) {
            $options = array_merge($options, ['class'=>"form-control {$options['class']}"] );
        }
        else {
            $options = array_merge($options, ['class'=>"form-control"]);
        }

        $this->insertScript('filters.js');

        if (isset($options['data-mask'])) {
            $this->insertScript('jquery.inputmask.bundle.min.js');
            $this->insertScript('mask.js');
            if ($options['data-mask'] == 'date') {
                $this->insertScript('datepicker.js');
            }
        }

        $tagText = parent::text('filter['.$name.']', $value, $options);

        return <<<MACRO
<div class="input-icon right">
	<i class="fa fa-search tooltips" id="filter-search" style="right: 30px;" title="Filtrar"></i>
	<i class="fa fa-times tooltips" id="filter-clean" title="Limpar Filtros"></i>
    $tagText
</div>
MACRO;
        return $block;
    }

    /**
     * Macro para criação de um filtro no format de checkbox
     * @param $name
     * @param int $value
     * @param null $checked
     * @param array $options
     * @param array $params
     * @return string
     * @author João Alfredo Knopik Junior
     */
    public function checkboxFilter($name, $value = 1, $checked = null, $options = array(), $params = array())
    {
        if ( $checkedByFilters = \Filters::get($name) ) {
            $checked = $checkedByFilters;
        }

        if (isset($params['class'])) {
            $options = array_merge($options, ['class'=>"form-control {$params['class']}"] );
        }
        else {
            $options = array_merge($options, ['class'=>"form-control"]);
        }

        return parent::checkbox('filter['.$name.']', $value, $checked, $options);
    }

    /**
     * Gera um botão submit estilizado (Criar)
     * @return string
     * @author João Alfredo Knopik Junior
     */
    public function create($label=null)
    {
        if (is_null($label)) {
            $label = 'Criar';
        }
        return parent::submit($label, ['class'=>'btn btn-success']);
    }

    /**
     * Gera um botão submit estilizado (Salvar)
     * @return string
     * @author João Alfredo Knopik Junior
     */
    public function update()
    {
        return $this->create('Salvar');
    }

    /**
     * Gera um botão submit com o método DELETE
     * @param $url
     * @return string
     * @author João Alfredo Knopik Junior
     */
    public function destroy($url)
    {
        $this->insertScript('rails.js');
        return <<<MACRO
<a href="$url" data-method="delete" class="btn btn-danger" role="button">Apagar</a>
MACRO;
    }

    /**
     * Gera um botão DELETE, com um modal de confirmação.
     * @param $url
     * @param null $item
     * @param null $header
     * @return string
     * @author João Alfredo Knopik Junior
     */
    public function confirm($url, $item=null, $header=null)
    {
        $this->insertScript('rails.js');

        if (is_null($item)) {
            $body = 'Tem certeza que deseja realizar esta ação?';
        } else {
            $body = "Tem certeza que deseja realizar esta ação no registro <b><u>$item</u></b>?";
        }

        if (is_null($header)) {
            $header = 'Confirmação';
        }

        return <<<MACRO
<button type="button" class="btn btn-danger" data-toggle="modal" data-target=".confirm-modal">Apagar</button>
<div class="modal confirm-modal" tabindex="-1" role="dialog" aria-labelledby="confirm-modal" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">$header</h4>
            </div>
            <div class="modal-body">
                <h4>$body</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" style="margin: -6px 0px 0px 0px" data-dismiss="modal">Não</button>
                <a href="$url" data-method="delete" class="btn btn-danger" role="button">Sim</a>
            </div>
        </div>
    </div>
</div>
MACRO;
    }

    /**
     * Gera um botão GET
     * Caso não seja passado nenhuma url, funcionará como o back do browser.
     * @param null $url
     * @return string
     * @author João Alfredo Knopik Junior
     */
    public function back($url=null)
    {
        if (is_null($url)) {
            $url = Request::header('referer');
        }
        return link_to($url, 'Voltar', ['class'=>'btn btn-default']);
    }

    /**
     * insere um arquivo JavaScript no momento que a view é renderizada
     * @param $scriptName
     * @author João Alfredo Knopik Junior
     */
    protected function insertScript($scriptName)
    {
        $scriptFullName = $this->scriptsPath . $scriptName;

        Event::listen('composing: layout::master.javascript', function(View $view) use ($scriptFullName) {

            if ($view->offsetExists('scriptCollection')) {
                $scriptCollection = $view->getData()['scriptCollection'];
            } else {
                $scriptCollection = new Collection();
            }

            if (! $scriptCollection->contains($scriptFullName)) {
                $scriptCollection->push($scriptFullName);
            }

            $view->with('scriptCollection', $scriptCollection);
        });
    }

    /**
     * insere um arquivo JavaScript no momento que a view é renderizada, no HEAD do HTML
     * @param $scriptName
     * @author João Alfredo Knopik Junior
     */
    protected function insertScriptOnHead($scriptName)
    {
        $scriptFullName = $this->scriptsPath . $scriptName;

        Event::listen('composing: layout::master.js-head', function(View $view) use ($scriptFullName) {

            if ($view->offsetExists('scriptHeadCollection')) {
                $scriptHeadCollection = $view->getData()['scriptHeadCollection'];
            } else {
                $scriptHeadCollection = new Collection();
            }

            if (! $scriptHeadCollection->contains($scriptFullName)) {
                $scriptHeadCollection->push($scriptFullName);
            }

            $view->with('scriptHeadCollection', $scriptHeadCollection);
        });
    }


    /**
     * Inseri um arquivo CSS no momento que a view é renderizada
     * @param $cssName
     * @author João Alfredo Knopik Junior
     */
    protected function insertCss($cssName)
    {
        $cssFullName = $this->cssPath . $cssName;
        Event::listen('composing: layout::master.css', function(View $view) use ($cssFullName) {
            if ($view->offsetExists('cssCollection')) {
                $cssCollection = $view->getData()['cssCollection'];
            }
            else {
                $cssCollection = new Collection();
            }

            if (! $cssCollection->contains($cssFullName)) {
                $cssCollection->push($cssFullName);
            }

            $view->with('cssCollection', $cssCollection);
        });
    }
}