@if (isset($icon) || isset($title))
    <div class="portlet-title">
        <div class="row">
            <div class="col-md-6">
                @if (isset($icon))
                    <i class="{{ $icon }}"></i>
                @endif
                {{ $title or ''}}
            </div>
            <div class="col-md-6">
                @if (isset($actions))
                    @yield($actions)
                @endif
            </div>
        </div>
    </div>
@endif