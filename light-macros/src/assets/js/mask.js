/**
 * Created by jakjr on 22/04/14.
 * uso: $().mascara('cpf', 'cpf');
 */

$.fn.mask = function($objName, $mask)
{
    try {
        var $obj = $('input[name="'+$objName+'"]');
        var $size = 0;

        switch ($mask) {
            case 'fone':
                $obj.inputmask({"mask": "(99)9999-9999[9]"});
                $size = 13;
                break;
            case 'celular':
                $obj.inputmask({"mask": "(99)99999-9999"});
                $size = 13;
                break;
            case 'cep':
                $obj.inputmask({"mask": "99.999-999"});
                $size = 10;
                break;
            case 'rg':
                $obj.inputmask({"mask": "9", "repeat": 10, "greedy": false });
                $size = 10;
                break;
            case 'onlyNumbers10':
                $obj.inputmask({"mask": "9", "repeat": 10, "greedy": false });
                $size = 10;
                break;
            case 'cpf':
                $obj.inputmask({"mask": "999.999.999-99"});
                $size = 14;
                break;
            case 'date':
                $obj.inputmask({"mask": "99/99/9999"}).datepicker($.datepicker.regional['pt-BR']);
                $size = 10;
                break;
            case 'hour':
                $obj.inputmask({"mask": "99:99"});
                $size = 5;
                break;
            case 'hourMinuteSec':
                $obj.inputmask({"mask": "99:99:99"});
                $size = 8;
                break;
            case 'spinner':
            	$obj.inputmask({"mask": "9", "repeat": 10, "greedy": false });
            	$obj.spinner();
                $size = 8;
                $obj.parent().removeClass('ui-widget-content');
                break;
            default:
            	$obj.inputmask({"mask": $mask});
            	$size = $mask.length;
            	break;
        }
        $obj.css('width', 'auto'); //Sobreescre o width fornecido pelo bootstrap
        $obj.attr('size', $size);
    }
    catch(e) {}
}

$(document).ready(function()
{
    $('input[data-mask]').each(function() {
        $().mask($(this).attr('name'), $(this).data('mask'));
    });
});