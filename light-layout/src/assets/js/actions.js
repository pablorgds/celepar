/**
 * Autor: jakjr João Alfredo Knopik Junior
 * uso:
 * <button type="submit" name="action" data-url="professor/{{$professor->id}}" data-method='DELETE' class="btn btn-danger">Apagar</button>
 * ou
 * <button type="button" name="action" data-url="professor/list" data-params="qq-paramentro=1" class="btn btn-defaultr">Voltar</button>
 */
$(document).ready(function()
{
    $(document).on('click',
        'input[type=button][name=action],' +
            'input[type=submit][name=action],' +
            'button[type=button][name=action],' +
            'button[type=submit][name=action], ' +
            'a[type=submit][name=action]',
        function(e)
        {
            try {
                e.preventDefault();
                var $url = $(this).data('url');

                if ( $(this).attr("type") == 'button' ) {
                    var $params = $(this).data('params') ? '?' + $(this).data('params') : '';
                    var action = $().app_url() + $url + $params;
                    window.location = action;
                    return;
                }
                else {

                    if ($url) {
                        $('form').attr('action', $().app_url() + $url);
                    }

                    if ( $('input[name="_method"]') && $(this).data('method')) {
                        $('input[name="_method"]').val($(this).data('method'));
                    }

                    $('form').submit();
                    return;
                }

            }
            catch(e){}
        });
});

$.fn.app_url = function() {
    var $url = '';
    $url += window.location.protocol + '//' + window.location.host

    if (window.location.port) {
        $url += ':' + window.location.port;
    }
    $url += '/';
    //<protocol>//<hostname>:<port>/<pathname><search><hash>

    return $url;
}