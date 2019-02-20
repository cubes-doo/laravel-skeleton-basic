<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <link rel="apple-touch-icon" sizes="76x76" href="/theme/assets/img/apple-icon.png">
        <link rel="icon" type="image/png" href="/theme/assets/img/favicon.png">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <title>App Name - @yield('title')</title>
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
        <!--     Fonts and icons     -->
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
        <!-- CSS Files -->
        <link href="/theme/assets/css/material-dashboard.css?v=2.1.0" rel="stylesheet" />
    </head>

    <body class="off-canvas-sidebar">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top text-white">
            <div class="container">
                <div class="navbar-wrapper">
                    <a class="navbar-brand" href="#pablo">@yield('title')</a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end">
                    <ul class="navbar-nav">
                        <li class="nav-item @active('register', 'active')">
                            <a href="@route('register')" class="nav-link">
                                <i class="material-icons">person_add</i> Register
                            </a>
                        </li>
                        <li class="nav-item @active('login', 'active')">
                            <a href="@route('login')" class="nav-link">
                                <i class="material-icons">fingerprint</i> Login
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="wrapper wrapper-full-page">
            <div class="page-header login-page header-filter" filter-color="black" style="background-image: url('/theme/assets/img/login.jpg'); background-size: cover; background-position: top center;">
                <!--   you can change the color of the filter page using: data-color="blue | purple | green | orange | red | rose " -->
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-8 ml-auto mr-auto">
                            @yield('content')
                        </div>
                    </div>
                </div>
                <footer class="footer">
                    <div class="container">
                        <div class="copyright float-right">
                            &copy;
                            <script>
                                document.write(new Date().getFullYear())
                            </script>, made with <i class="material-icons">favorite</i> by
                            <a href="https://cubes.rs/" target="_blank">Cubes</a>, how bad do you want IT?
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <!-- Core JS Files -->
        <script src="/theme/assets/js/core/jquery.min.js"></script>
        <script src="/theme/assets/js/core/popper.min.js"></script>
        <script src="/theme/assets/js/core/bootstrap-material-design.min.js"></script>
        <script src="/theme/assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
        <!-- Notifications Plugin -->
        <script src="/theme/assets/js/plugins/bootstrap-notify.js"></script>
        <!-- Card animation -->
        <script type="text/javascript">
            $(document).ready(function () {
                setTimeout(function () {
                    // after 1000 ms we add the class animated to the login/register card
                    $('.card').removeClass('card-hidden');
                }, 700);
            });
        </script>
        @stack('js')
    </body>

</html>
