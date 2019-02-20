<!doctype html>
<html lang="{{app()->getLocale()}}">
    <head>
        <title>App Name - @yield('title')</title>
        @include('_layout.partials._head')
    </head>

    <body>
        <div class="wrapper">
            @include('_layout.partials._sidebar')
            <div class="main-panel">
                @include('_layout.partials._navbar')
                <div class="content">
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
            @include('_layout.partials._footer')
            </div>
        </div>
        @include('_layout.partials._scripts')
        @include('_layout.partials.message')
    </body>
</html>
