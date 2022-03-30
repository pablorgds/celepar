$(document).ready(function(){$().make_filter_fields();});

$.fn.make_filter_fields = function()
{
    //Focus
    var $focus = false;

    $('input[name^=filter]:text').each(function(){
        if($(this).val() != '') {
            $focus = true;
            $(this).focus().val($(this).val())
        }
    });
    if (!$focus) {
        $('input[name^=filter]:text').each(function()
        {
            $(this).focus().val();
            return false;
        });
    }

    //Search
    $('input[name^=filter]').keypress(function(e)
    {
        if (e.keyCode == '13') {
            e.preventDefault();
            submit();
        }
    });
    $('input[name^=filter]').click(function(){
        if ( $(this).attr('type') == 'checkbox' ) {
            submit();
        }
    });

    $('select:not([multiple])[name^=filter]').change(function(e)
    {
        submit();
    });

    //ao clicar no X do input limpa todos os filters e submete o formulario
    $('.fa-times').click(function(){
    	$("input[name^='filter']").val("");
    	submit();
    });
    
    //ao clicar na lupa do input faz o submit do formulario
    $('#filter-search.fa-search').click(function(){
    	submit();
    });
    
    function submit()
    {
        $('form').submit();
        return;
    }
}
