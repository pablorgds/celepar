@section('light::layouts.footer')
    <div class="footer">
        <div class="footer-inner">
            @section('footer')
                {{Date('Y')}} &copy; Celepar
                @if(Config::has('layout.appVersion'))
                    - versão: {{Config::get('layout.appVersion')}}
                @endif
            @show
        </div>
        <div class="footer-tools"> <span class="go-top"> <i class="fa fa-angle-up"></i> </span> </div>
    </div>
@show