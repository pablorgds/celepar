@include('vendor.central-seguranca.shared.header')

<div class="alert alert-danger" role="alert">
    <span style="font-size: 30px;" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><br>
    OPS... Houve um erro.<br>

    @if( isset($msgErroCS) )
        <b>{{ $msgErroCS }}</b><br>
    @endif

    Tente novamente, caso o erro persista entre em contato com o administrador do sistema.
</div>

@include('vendor.central-seguranca.shared.footer')