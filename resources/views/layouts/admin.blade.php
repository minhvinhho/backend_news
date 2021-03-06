@include('layouts._header')
@include('layouts._navbar')
@include('layouts._alert')
<div class="main-container container-fluid no-padding">

    @auth
        <aside class="col-sm-2 no-padding sidebar-container">
            @include('layouts._sidebar')
        </aside>
    @endauth
    <div class="{{ auth()->check() ? '' : 'col-sm-offset-2' }} col-sm-10 no-padding">
        @yield('content')
    </div>
</div>
@include('layouts._footer')
