{{--Injecao de js macro--}}
@if(isset($scriptHeadCollection))
    @foreach($scriptHeadCollection->all() as $script)
        <script src="{{asset($script)}}"></script>
    @endforeach
@endif

@section('js-head')
@show