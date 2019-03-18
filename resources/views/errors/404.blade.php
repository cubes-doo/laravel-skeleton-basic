<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />
        <title>
            404
            -
            {{config('app.name')}}
        </title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta name="description" content="{{config('app.description')}}" />
        <meta content="Cubes d.o.o." name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('/assets/images/favicon.ico')}}">

        <!-- App css -->
        <link href="{{asset('/theme/assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/theme/assets/css/metismenu.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/theme/assets/css/icons.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/theme/assets/css/style.css')}}" rel="stylesheet" type="text/css" />

        <link href="{{asset('/theme/plugins/bootstrap-select/css/bootstrap-select.min.css')}}" rel="stylesheet" />

        @stack('head_links')

        <script src="{{asset('/theme/assets/js/modernizr.min.js')}}"></script>

        @stack('head_scripts')

    </head>


    <body class="bg-transparent">

        <!-- HOME -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-center">

                        <div class="wrapper-page">
                            <img src="{{asset('/theme/assets/images/logo_dark.png')}}" alt="" height="30">
                            <br/>
                            <img src="{{asset('/theme/assets/images/icons/high_priority.svg')}}" alt="high_priority.svg" height="60" class="m-t-50">
                            <h2 class="text-uppercase text-primary m-t-50">@lang('Page Not Found')</h2>
                            <br>
                            <a class="btn btn-info waves-effect waves-light m-t-20" href="{{url('/')}}"> Return Home</a>
                        </div>

                    </div>
                </div>
            </div>
          </section>
          <!-- END HOME -->

        <script>
            var resizefunc = [];
        </script>

       <!-- jQuery  -->
       <script src="{{asset('/theme/assets/js/jquery.min.js')}}"></script>
        <script src="{{asset('/theme/assets/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('/theme/assets/js/metisMenu.min.js')}}"></script>
        <script src="{{asset('/theme/assets/js/waves.js')}}"></script>
        <script src="{{asset('/theme/assets/js/jquery.slimscroll.js')}}"></script>
        <script src="{{asset('/theme/plugins/bootstrap-select/js/bootstrap-select.min.js')}}"></script>

        <!-- App js -->
        <script src="{{asset('/theme/assets/js/jquery.core.js')}}"></script>
        <script src="{{asset('/theme/assets/js/jquery.app.js')}}"></script>
        
    </body>
</html>