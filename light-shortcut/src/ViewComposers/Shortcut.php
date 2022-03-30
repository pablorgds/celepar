<?php namespace Celepar\Light\Shortcut\ViewComposers;

use App;

class Shortcut
{
    public function compose($view)
    {
        $view->with('shortcut', self::render());
    }

    private static function render()
    {
        if (!file_exists($file = app_path() . '/shortcut.php')) {
            return false;
        }

        $shortcuts = require $file;

        if ((!is_array($shortcuts)) || (!count($shortcuts))) {
            return false;
        }

        $return = '';
        foreach ($shortcuts as $shortcut) {
            $url = key($shortcut);
            $values = current($shortcut);

            if (is_array($values)) {
                $icon = array_shift($values);
                if (count($values)) {
                    $anchorParameters = [];
                    foreach ($values[0] as $parameter => $parameterValue) {
                        $anchorParameters[] = "$parameter='$parameterValue'";
                    }
                    $anchorParameters = implode(' ', $anchorParameters);
                }
            } else {
                $icon = $values;
            }

            $return .= "<li class='dropdown'><a href='{$url}'" . @$anchorParameters . "><i class='{$icon} x-bigger text-nav-icon'></i></a></li>";
        }

        return $return;
    }
} 