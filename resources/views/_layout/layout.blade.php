<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>
            @if(\View::hasSection('head_title'))
            @yield('head_title')
            -
            @endif
            {{config('app.name')}}
        </title>
        @include('_layout.partials._head')
    </head>

    <body>

        <!-- Begin page -->
        <div id="wrapper">

            @include('_layout.partials._navbar')

            @include('_layout.partials._sidebar')



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                    <!-- Start content -->
                    <div class="content">
                        <div class="container-fluid">
                            @yield('content')
                        </div> <!-- container-fluid -->
    
                    </div> <!-- content -->
    
                    @include('_layout.partials._footer')
    
                </div>
    
    
                <!-- ============================================================== -->
                <!-- End Right content here -->
                <!-- ============================================================== -->
        </div>

        @include('_layout.partials._scripts')
    </body>
</html>