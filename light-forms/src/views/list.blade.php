@extends('layout::master')

@section('content-title')
    @include('layout::content.title')
@endsection

@section('message')
    @include('messenger::message')
@endsection

@section('pre-content')
    <div class="row">
        <div class="col-md-12 light">
            <div class="portlet box green">

                @include('forms::inc.title', [
                    'icon'=>@$listIcon,
                    'title'=>@$listTitle,
                    'actions'=>'menu-actions'
                ])

                <div class="portlet-body">
                    @if($errors->has())
                        <div class="alert alert-danger center-block text-center" style="margin: 14px 50px 10px 50px;">
                            Por favor corrija o(s) erro(s) em vermelho no formulário abaixo.
                        </div>
                    @endif
                    @endsection

                    @section('pos-content')
                </div>
                <div class="form-actions fluid text-center">
                    @yield('actions')
                </div>
            </div>
        </div>
    </div>
@endsection