@extends('layout::master')

@section('content-title')
    @include('layout::content.title')
@endsection

@section('message')
    @include('messenger::message')
@endsection

@section('content')
    <div class="row">

        @if(! $errors->has())
            <div class="alert alert-danger center-block text-center" style="margin: 0px 8px 0px 8px;">
                Por favor corrija o(s) erro(s) em vermelho no formulário abaixo.
            </div>
        @endif

        <div class="col-md-6 light">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        @if (isset($formIcon1))
                            <i class="{{ $formIcon1 }}"></i>
                        @endif
                        {{ $formTitle1 or ''}}
                    </div>
                </div>
                <div class="portlet-body form">
                    @yield('content1')
                </div>
            </div>
        </div>

        <div class="col-md-6 light">
            <div class="portlet box green">
                <div class="portlet-title">
                    <div class="caption">
                        @if (isset($formIcon2))
                            <i class="{{ $formIcon2 }}"></i>
                        @endif
                        {{ $formTitle2 or ''}}
                    </div>
                </div>
                <div class="portlet-body form">
                    @yield('content2')
                </div>
            </div>
        </div>

    </div>

    @yield('actions')

@endsection