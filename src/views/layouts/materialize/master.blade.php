<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title>Materialize Template</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,400i,500,700" rel="stylesheet">
    <link href="http://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">


    <!-- 
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css">
     -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/css/materialize.min.css"/>


    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Put these in a seperate file -->
    <!-- These are here to make Materialize behave -->
    <style type="text/css">

        /* end color definitions*/
        html {
            box-sizing: border-box;
            font-size: 16px;
        }

        body {
            /*    background-color: #dcdcdc;*/
            background-color: #e4e7f6;
        }

        .side-nav {
            width: 240px;
            background-color: #0a0d1e;
            transition: .5s ease-in-out;
        }

        .side-nav li,
        .side-nav a {
            color: #fff;
            transition: background-color .3s;
        }

        .side-nav > .topheader {
            background-color: #263238;
            height: 64px;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .side-nav > .topheader h3 {
            font-weight: 300;
            font-size: 28px;
            margin: 8px 16px;
        }

        .side-nav li > a {
            display: inline;
        }

        .side-nav li.active {
            border-left: 8px solid #7280ce;
            /* Teal Related*/
            background-color: #0a0d1e;
        }

        .side-nav li.active > a {
            margin-left: -8px;
        }

        .side-nav li.active:hover > a {
            background-color: transparent;
            color: #fff;
        }

        .side-nav li:hover {
            background-color: #7280ce;
            /* Teal-lighter-2 */
        }

        .side-nav li:hover > a {
            background-color: transparent;
            color: #fff;
        }

        .side-nav li:hover a.lgi-btn,
        .side-nav li:hover a.btn.btn-small.lgi-btn .material-icons {
            display: inline;
        }

        .side-nav.fixed {
            transform: translateX(0%);
        }

        .side-nav li > a.lgi-btn > i,
        .side-nav li > a.lgi-btn > [class^="mdi-"],
        .side-nav li > a.lgi-btn > [class*="mdi-"],
        .side-nav li > a.lgi-btn > i.material-icons {
            width: 24px;
            height: 24px;
            line-height: 24px;
            float: right;
        }

        .side-nav li > a.btn.btn-small {
            width: 24px;
            height: 24px;
            line-height: 24px;
            float: right;
        }

        .side-nav li > a.lgi-btn > i,
        .side-nav li > a.lgi-btn > [class^="mdi-"],
        .side-nav li > a.lgi-btn > [class*="mdi-"],
        .side-nav li > a.lgi-btn > i.material-icons {
            float: inherit;
            margin-right: -8px;
        }

        .black-54 {
            color: #757575;
        }

        .black-87 {
            color: #212121;
        }

        .black-38 {
            color: #9E9E9E;
        }

        .container-custom-1 {
            margin: 0 auto;
            max-width: 1280px;
            width: 100%;
        }

        .form-panel.container, .form-panel.container-custom-1 {
            padding: 1rem .5rem;
            margin-bottom: 1rem;
        }

        .row .col {
            padding: 0;
        }

        @media only screen and (min-width: 601px) {
            .container-custom-1 {
                width: 98%;
            }

            .form-panel.container, .form-panel.container-custom-1 {
                padding: 1rem 1rem;
            }

            .row .col {
                padding: 0 .5rem;
            }
        }

        @media only screen and (min-width: 993px) {
            .container-custom-1 {
                width: 96%;
            }

            .form-panel.container, .form-panel.container-custom-1 {
                padding: 1rem 2rem;
            }

            .row .col {
                padding: 0 1rem;
            }
        }

        td {
            color: #212121;
            font-size: 16px;
            font-weight: 400;
        }

        thead {
            color: #7a7a7a;
            font-size: 15px;
            font-weight: 500;
        }

        thead a {
            color: #7a7a7a;
            font-size: 15px;
            font-weight: 500;
        }

        table.highlight > tbody > tr:hover {
            background-color: #eeeeee;
            /*background-color: #f2f2f2;*/
        }

        .container-custom-1 .row {
            margin-left: -0.75rem;
            margin-right: -0.75rem;
        }

        .icon-block {
            padding: 0 15px;
        }

        .icon-block .material-icons {
            font-size: inherit;
        }

        nav .button-collapse {
            margin: 0;
        }

        a.btn.btn-small.lgi-btn {
            font-size: 1rem;
            display: none;
        }

        a.btn.btn-small.lgi-btn .material-icons {
            font-size: 1rem;
            display: none;
        }

        .btn-small {
            height: 24px;
            line-height: 24px;
            padding: 0 8px;
        }

        .btn-small i {
            font-size: 1rem;
            line-height: 24px;
            padding: 0;
        }

        .btn-small.btn-floating,
        .btn-floating.btn-small {
            width: 24px;
        }

        .btn-small.btn-floating i,
        .btn-floating.btn-small i {
            margin-left: -8px;
        }

        a.btn-small {
            padding: 0 8px;
        }

        .input-field.col label {
            left: 1rem;
        }

        .form-panel {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 1rem 1rem;
            margin-top: 1rem;
            margin-bottom: 1rem;
            /*margin: 16px;*/
            display: block;
            background-color: #ffffff;
            box-shadow: 0 3px 3px 0 rgba(0, 0, 0, 0.14), 0 1px 7px 0 rgba(0, 0, 0, 0.12), 0 3px 1px -1px rgba(0, 0, 0, 0.2);
            overflow: visible;
            /* Allows for date picker*/
        }

        .form-panel h3 {
            padding: 0;
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
            font-weight: 300;
            font-size: 2rem;
        }

        .row {
            margin-bottom: .5rem;
        }

        footer.page-footer {
            /*position: absolute;
            bottom: 0;
            right: 0;
            left: 0;*/
            margin-top: 1rem;
            padding-top: 8px;
        }

        footer.page-footer .footer-copyright {
            height: 48px;
            line-height: 48px;
        }

        ul.pagination {
            float: right;
        }

        .pagination li {
            height: 28px;
            font-size: 15px;
            /* same as theader*/
        }

        .pagination li a {
            padding: 0 8px;
            line-height: 28px;
            height: 28px;
            font-size: 15px;
            /* same as theader*/
        }

        input:not([type]), input[type=text], input[type=password], input[type=email], input[type=url], input[type=time], input[type=date], input[type=datetime], input[type=datetime-local], input[type=tel], input[type=number], input[type=search] {
            margin: 0 0 .5rem 0;
        }

        textarea.materialize-textar {
            margin: 0 0 .5rem 0;
        }

        .fixed-container {
            margin-left: 1rem;
            margin-right: 1rem;
        }

        header,
        main,
        footer {
            padding-left: 240px;
            transition: .5s ease-in-out;
        }

        @media only screen and (max-width: 992px) {
            header,
            main,
            footer {
                padding-left: 0;
            }
        }

        @media only screen and (min-width: 993px) {
            nav .nav-wrapper {
                margin-right: 240px;
            }
        }

        .snackbar {
            display: none;
        }
    </style>
    @yield('css')
</head>

<body>
<div id="content">
    <header>

        <!-- Side Nav Here -->
        <div id="nav-sidebar" class="side-nav fixed z-depth-0">
            <div class="topheader">
                <h3 class="white-text">
                    <b>Product</b> Name
                </h3>
            </div>
            <ul>
                @yield('sidenav-items')

            </ul>

        </div>
        <div class="navbar-fixed ">
            <nav class="indigo" role="navigation">
                <div class="nav-wrapper fixed-container">
                    <div class="col s12 m12 l12">
                        <a href="#" data-activates="nav-sidebar"
                           class="button-collapse left top-nav full hide-on-large-only">
                            <i class="material-icons">menu</i>
                        </a>
                        <ul class="right">
                            @yield('topnav-items')
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>


    <main>

        <div class="row">
            <div class="col s12 m12 l12">

                @yield('content')
            </div>
        </div>
    </main>
    <footer class="page-footer fixed indigo">
        <div class="footer-copyright ">
            <div class="container">
                <p><i class="material-icons">copyright</i> 2017; Made by SomeOne</p>
            </div>
        </div>
    </footer>
</div>


<!-- Core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $('.button-collapse').sideNav();
</script>

@yield('scripts')

</body>
</html>
