<?php

namespace Celepar\Light\Menu\ViewComposers;

use Tlr\Menu\Laravel\MenuItem;
use Illuminate\Contracts\View\View;

class Menu
{
    public static $menu;
    public static $filter = null;

    /**
     *
     */
    public function __construct()
    {
        self::$menu = new \Tlr\Menu\Laravel\MenuItem();
    }

    /**
     * @param $father
     * @param $link
     * @param $label
     * @param null $icon
     * @param $key
     * @return \Tlr\Menu\Tlr\Menu\MenuItem
     */
    public static function item($father, $link, $label, $icon=null, $key)
    {
        if (is_null($father)) {
            $father = self::$menu;
        }

        return $father->item($key, $label, ['link'=>$link, 'icon'=>$icon]);
    }

    /**
     * Filtro a ser aplicado aos menus.
     * @param $closure
     * @throws \Exception
     */
    public static function filter($closure)
    {
        if (! is_callable($closure)) {
            throw new \Exception('Not a callable filter');
        }

        self::$menu->addFilter($closure);
    }

    /**
     * Método da viewComposer
     * Ao renderizar a view layout::master.sidebar este método é executado
     * O método popular a variável $menu com código html que representa o menu.
     * @param View $view
     */
    public function compose(View $view)
    {
        include(app_path('menu.php'));

        self::$menu->setAttributes(['class'=>'page-sidebar-menu']);

        if (! is_null($view->activeMenu)) {
            //Definido menu ativo no controller
            self::$menu->activate($view->activeMenu, 'key');
        } else {
            //Tentando definir qual menu esta ativo via URL
            self::$menu->activate( \Request::url() );
        }

        $html = $this->render(self::$menu);

        $view->with('menu', $html);
    }

    /**
     * renderiza o menu principal, UL
     * @param MenuItem $menu
     * @return string
     */
    protected function render(MenuItem $menu)
    {
        $items = $menu->getItems();

        $renderItems = '';
        foreach ($items as $item) {
            $renderItems .= $this->renderItem($item);
        }

        if (empty($renderItems)) {
            return;
        }

        $openTag = $this->element('ul', $menu->getAttributes());
        $closeTag = '</ul>';

        return $openTag . $renderItems . $closeTag;
    }

    /**
     * rendereiza os menus-filhos LI
     * @param MenuItem $item
     * @return string
     */
    protected function renderItem(MenuItem $item)
    {
        if ($item->isActive() ) {
            $item->setAttributes(['class'=>'open']);
        }
        $return = $this->element('li', $item->compileAttributes());

        $anchorContent = '';
        if ($icon = $item->option('icon')) {
            $anchorContent = $this->element('i', ['class' => $icon], '&nbsp;');
        }

        $anchorContent .= $this->element('span', ['class'=>'title'], $item->option('title'));
        if ($item->hasItems()) {
            $anchorContent .= $this->element('span', ['class'=>"arrow {$this->getActiveClass($item)}"], '&nbsp;');
        }
        $return .= $this->element('a', ['href'=>$item->option('link') ? $item->option('link') : '#'], $anchorContent);

        if ($item->hasItems()) {
            $item->setAttributes(['class'=>'sub-menu']);
            $subItems = $this->render($item); //renderiza novo UL
            if (empty($subItems)) {
                return;
            }
            $return .= $subItems;
        }

        $return .= '</li>';
        return $return;
    }

    /**
     * @param string $element
     * @param array $attributes
     * @param null $content
     * @return string
     */
    protected function element($element = 'div', $attributes = array(), $content = null )
    {
        foreach ($attributes as $attribute => $values) {
            $attributes[$attribute] = implode(' ', (array)$values);
        }

        $html = "<{$element}" . app('html')->attributes( $attributes ) . ">";

        if (! is_null($content) ) {
            $html .= "{$content}</{$element}>";
        }

        return $html;
    }

    /**
     * @param MenuItem $item
     * @return string
     */
    public function getActiveClass(MenuItem $item)
    {
        if ($item->isActive()) {
            return 'open';
        }
    }
}
