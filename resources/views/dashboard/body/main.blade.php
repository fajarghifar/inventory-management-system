<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard Inventory</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}" />

        <!-- Styles CSS -->
        <link href="{{ asset('assets/css/styles.css') }}" rel="stylesheet" />

        <!-- Icons -->
        <script data-search-pseudo-elements="" defer="" src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js" crossorigin="anonymous"></script>

        <!-- Custom CSS for specific page.  -->
        @yield('specificpagestyles')
    </head>

    <body class="nav-fixed">
        <!-- BEGIN: Navbar Brand -->
        @include('dashboard.body.header')
        <!-- END: Navbar Brand -->

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <!-- BEGIN: Sidenav -->
                @include('dashboard.body.sidebar')
                <!-- END: Sidenav -->
            </div>


            <div id="layoutSidenav_content">
                <main>
                <!-- BEGIN: Content -->
                    @yield('content')
                <!-- END: Content -->
                </main>

                <!-- BEGIN: Footer  -->
                @include('dashboard.body.footer')
                <!-- END: Footer  -->
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/scripts.js') }}"></script>

        <!-- Custom JS for specific page.  -->
        @yield('specificpagescripts')
    </body>
</html>
