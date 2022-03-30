/**
 * Created by pmenezes on 05/06/14.
 * função para destacar as abas que contem erros de submit do formulário
 */
/**/
$.fn.validaFormAbas = function()
{
    try {
        if( $('.alert-danger').length ){
            var i = 0;

            $($('.has-error')).each( function() {
                var id = $(this).parent().attr('id');
                var aba = '#aba'+id;
                //$(aba).css("background-color", "#EBCCD1");
                $(aba).attr("class", "alert-danger aba_erro");

                //coloca o foco na primeira aba que contenha erro
                if(i == 0){
                    $('#abas a[href="#'+id+'"]').tab('show');
                }

                i++;
            });
        }
    }
    catch(e) {
        alert(e);
    }
}

$(document).ready(function()
{
    $().validaFormAbas();
});