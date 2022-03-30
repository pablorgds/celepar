@extends('layout::master')

@section('content-title')
    @include('layout::content.title')
@endsection

@section('message')
    @include('messenger::message')
@endsection

@section('js')
    <script src="{{asset('vendor/forms/js/validaFormAbas.js')}}"></script>
@append

@section('pre-content')
    <div class="row">
        <div class="col-md-12 light">
            <div class="portlet box green">

                @include('forms::inc.title', [
                    'icon'=>@$formIcon,
                    'title'=>@$formTitle,
                    'actions'=>'menu-actions'
                ])

                <div class="portlet-body">
                    @if($errors->has())
                        <div class="alert alert-danger center-block text-center" style="margin: 14px 50px 10px 50px;">
                            Por favor corrija o(s) erro(s) em vermelho no formulário abaixo.
                        </div>
                    @endif
@endsection

@section('content')

                    @yield('nav')

                    <div class="form-body">
                        <div class="tabbable tabbable-custom nav-justified" >

                            @yield('pre-nav')

                            <ul class="nav nav-tabs" id="abas">

                                @foreach ($tabTitle as $k => $v)

                                    @if ($activeTab == $k)
                                        <li class="active" id="aba{{ $k }}">
                                    @else
                                        <li class="" id="aba{{ $k }}">
                                    @endif

                                            <a data-toggle="tab" href="#{{ $k }}">
                                                @if (isset($tabIcon[$k]))
                                                    <i class="{{ $tabIcon[$k] }}"></i>
                                                @endif
                                                {{ $v }}
                                            </a>
                                        </li>

                                @endforeach
                            </ul>

                            <div class="tab-content" id="conteudo">

                                @foreach ($tabTitle as $k => $v)

                                    @if ($activeTab == $k)
                                        <div id="{{ $k }}" class="tab-pane fade in active">
                                            @else
                                                <div id="{{ $k }}" class="tab-pane fade">
                                                    @endif
                                                    @yield("a$k")
                                                </div>

                                                @endforeach

                                        </div>
                            </div>

                            @yield('pos-nav')

                        </div>

                        <div class="form-actions fluid text-center">
                            @yield('actions')
                        </div>
                    </div>

@endsection

@section('pos-content')
                </div>
            </div>
        </div>
    </div>
@endsection