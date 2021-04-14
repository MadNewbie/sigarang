<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>
      <?= env('APP_NAME') ?>
  </title>

  <link rel="icon" href="https://2.bp.blogspot.com/-Ne5sknY1pJw/WhUK2mTUbUI/AAAAAAAAFPY/PnobQKmeO3Ev71-6TSlFunw08Pnk3LpogCLcBGAs/s1600/Sampang.png" sizes="16x16">

  @yield('css-include-before')

  <!-- Bootstrap Core CSS -->
  <link rel="stylesheet" href={{asset("vendor/bootstrap-4/css/bootstrap.min.css")}}>

  <!-- Custom Fonts -->
  <link rel="stylesheet" href={{asset("vendor/fontawesome-5.15.1/css/all.min.css")}}>
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href={{asset("vendor/stylish-portofolio/css/stylish-portfolio.min.css")}}>

  <!--jquery-ui-->
  <link rel="stylesheet" href={{asset("vendor/jquery-ui/jquery-ui.min.css")}}>
  <link rel="stylesheet" href={{asset("vendor/jquery-ui/jquery-ui.theme.min.css")}}>

  <!--Chartjs-->
  <link rel="stylesheet" href={{asset("vendor/chartjs/css/chart.min.css")}}>

  <style>
    @yield('css-inline')
  </style>

</head>

<body id="page-top">

    <!-- Navigation -->
    <a class="menu-toggle rounded" href="#">
        <i class="fas fa-bars"></i>
    </a>
    <nav id="sidebar-wrapper">
        <ul class="sidebar-nav text-white">
            <li class="sidebar-brand sidebar-nav-item">
                <a class="js-scroll-trigger" href="#page-top"><?= env('APP_NAME') ?></a>
            </li>
            <li class="sidebar-nav-item">
                <a class="js-scroll-trigger" href="#perubahan">Info Perubahan Harga</a>
            </li>
            <li class="sidebar-nav-item">
                @if(Auth::user())
                <a href={{ route('backyard.home') }}>Dashboard</a>
                @else
                <a href={{ route('login') }}>Login</a>
                @endif
            </li>
        </ul>
    </nav>
    @yield('content')
    <!-- Footer -->
    <footer class="footer text-center text-white">
        <p class="small mb-0"><strong>Copyright &copy; 2021 Bintang Alfa.</strong> All rights reserved.</p>
    </footer>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded js-scroll-trigger" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- jQuery -->
    <script src={{asset("vendor/jquery/jquery.min.js")}}></script>
    <!-- Bootstrap 4 -->
    <script src={{asset("vendor/bootstrap-4/js/bootstrap.bundle.min.js")}}></script>

    <!-- Plugin JavaScript -->
    <script src={{asset("vendor/jquery-easing/js/jquery.easing.min.js")}}></script>

    <!-- Custom scripts for this template -->
    <script src={{asset("vendor/stylish-portofolio/js/stylish-portfolio.min.js")}}></script>

    <!-- jQuery UI 1.11.4 -->
    <script src={{asset("vendor/jquery-ui/jquery-ui.min.js")}}></script>

    <!-- daterangepicker -->
    <script src={{asset("vendor/moment/moment-with-locales.min.js")}}></script>
    <script src={{asset("vendor/daterangepicker/js/daterangepicker.js")}}></script>

    <!--Alertify-->
    <script src={{asset("vendor/alertifyjs-1.13.1/js/alertify.min.js")}}></script>

    <!-- ChartJS -->
    <script src={{asset("vendor/chartjs/js/chart.min.js")}}></script>

    <script type="text/javascript">
        @yield('js-inline-data')
    </script>
    @yield('js-include')
    <script type="text/javascript">
    @yield('js-inline')
    $(function () {
        @if(isset($notifMessage['success']))
            alertify.success('<?= $notifMessage['success'] ?>');
        @endif
        @if(isset($notifMessage['error']))
            alertify.error('<?= $notifMessage['error'] ?>');
        @endif
        @if(isset($notifMessage['warning']))
            alertify.notify('<?= $notifMessage['warning'] ?>', 'yellow');
        @endif
        @if(isset($notifMessage['info']))
            alertify.notify('<?= $notifMessage['info'] ?>', 'blue');
        @endif
        @if($message = Session::get('success'))
            alertify.success('<?= $message ?>');
        @endif
        @if($message = Session::get('error'))
            alertify.error('<?= $message ?>');
        @endif
        @if($message = Session::get('warning'))
            alertify.notify('<?= $message ?>', 'yellow');
        @endif
        @if($message = Session::get('info'))
            alertify.notify('<?= $message ?>', 'blue');
        @endif
        @if(isset($errors) && count($errors) > 0)
            <?php
                $message = implode('</br>', $errors->all());
            ?>
            alertify.error('<?= $message ?>');
        @endif
    })
    </script>

</body>

</html>
