@include('vendor.central-seguranca.shared.header')

<div class="alert alert-danger" role="alert">
    <span style="font-size: 30px;" class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><br>
    Você foi deslogado desta aplicaçao.<br>
    Caso queira acessá-la novamente<br>clique <a href="{{ url('auth/login') }}">aqui</a>
</div>

@include('vendor.central-seguranca.shared.header')