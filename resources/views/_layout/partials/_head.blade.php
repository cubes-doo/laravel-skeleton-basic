<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
<link href="{{asset('/theme/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">

@stack('head_links')

<script src="{{asset('/theme/assets/js/modernizr.min.js')}}"></script>

@stack('head_scripts')