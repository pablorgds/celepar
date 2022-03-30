<?php namespace Celepar\Light\Layout;

use Exception;
use Illuminate\View\View;

/**
 * Class LayoutDecoratorTrait
 * @package Light
 * @author João Alfredo Knopik Junior
 * @description Trait para simplificar o uso de variáveis de layout do LightPHP. Tendo métodos, a IDE pode executar o autocomplete.
 */
trait LayoutDecoratorTrait {

    private $view;

    public function setView($view)
    {
        $this->view = $view;
    }

    private function setViewIfValid($v)
    {
        if ($v instanceof View) {
            $this->setView($v);
        }
    }

    private function setViewValue($attribute, $value)
    {
        $this->isViewSetUp();
        $this->view->$attribute = $value;
    }

    private function isViewSetUp()
    {
        if (! $this->view instanceof View) {
            throw new Exception('no view setted');
        }
    }

    /*******************
     * PAGES
     ******************/
    public function setLayoutPageIcon($value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue('pageIcon', $value);
        return $this;
    }

    public function setLayoutPageTitle($value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue('pageTitle', $value);
        return $this;
    }

    public function setLayoutPageDescription($value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue('pageDesc', $value);
        return $this;
    }

    public function setLayoutPage($title=null, $icon=null, $description=null, $v=null)
    {
        if (! is_null($title)) {
            $this->setLayoutPageTitle($title, $v);
        }

        if (! is_null($icon)) {
            $this->setLayoutPageIcon($icon, $v);
        }

        if (! is_null($description)) {
            $this->setLayoutPageDescription($description, $v);
        }

        return $this;
    }


    /*******************
     * FORMS
     ******************/
    public function setLayoutFormTitle($value, $formNumber=null, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue("formTitle{$formNumber}", $value);
        return $this;
    }

    public function setLayoutFormIcon($value, $formNumber=null, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue("formIcon{$formNumber}", $value);
        return $this;
    }

    public function setLayoutForm($title=null, $icon=null, $formNumber=null, $v=null)
    {
        if (! is_null($title)) {
            $this->setLayoutFormTitle($title, $formNumber, $v);
        }

        if (! is_null($icon)) {
            $this->setLayoutFormIcon($icon, $formNumber, $v);
        }
        return $this;
    }


    /*******************
     * LISTS
     ******************/
    public function setLayoutListIcon($value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue('listIcon', $value);
        return $this;
    }

    public function setLayoutListTitle($value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue('listTitle', $value);
        return $this;
    }

    public function setLayoutList($title=null, $icon=null, $v=null)
    {
        if (!is_null($title)) {
            $this->setLayoutListTitle($title, $v);
        }

        if (!is_null($icon)) {
            $this->setLayoutListIcon($icon, $v);
        }

        return $this;
    }


    /*******************
     * TABS
     ******************/
    public function setLayoutTabTitle($tabNumber, $value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->isViewSetUp();
        $this->view->tabTitle[$tabNumber] = $value;
        return $this;
    }

    public function setLayoutTabIcon($tabNumber, $value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->isViewSetUp();
        $this->view->tabIcon[$tabNumber] = $value;
        return $this;
    }

    public function setLayoutTab($tabNumber, $title=null, $icon=null, $active=false, $v=null)
    {
        if (! is_null($title)) {
            $this->setLayoutTabTitle($tabNumber, $title, $v);
        }

        if (! is_null($icon)) {
            $this->setLayoutTabIcon($tabNumber, $icon, $v);
        }

        if ($active || ($tabNumber==1) ) {
            $this->setViewValue('activeTab', $tabNumber);
        }

        return $this;
    }


    /*******************
     * FORMS ACTION AND METHOD
     ******************/
    public function setLayoutFormAction($value, $method='POST', $upload=false, $v=null)
    {
        return $this->setFormAction(action($value), $method, $upload, $v);
    }

    public function setLayoutFormUrl($value, $method='POST', $upload=false, $v=null)
    {
        return $this->setFormAction(url($value), $method, $upload, $v);
    }

    public function setLayoutFormRoute($value, $method='POST', $upload=false, $v=null)
    {
        return $this->setFormAction(route($value), $method, $upload, $v);
    }

    private function setFormAction($action, $method, $upload, $v)
    {
        $this->setViewIfValid($v);
        $this->setViewValue('urlForm', $action);
        $this->setViewValue('methodForm', $method);
        $this->setViewValue('uploadArquivos', $upload);
        return $this;
    }

    /******************************************
     * ACTIVE MENU
     */
    public function setLayoutActiveMenu($value, $v=null)
    {
        $this->setViewIfValid($v);
        $this->setViewValue('activeMenu', $value);
        return $this;
    }

}