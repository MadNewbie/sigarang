<?php
use App\Libraries\Mad\Helper;
?>

<!DOCTYPE html>
<html>
    <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <title>
          <?= env('APP_NAME') ?> @yield('pagetitle')
      </title>
      <!--<link rel="icon" type="image/png" href="<!?= asset('/') !?>">-->
      <meta name="csrf-token" content="{{ csrf_token() }}">
      @yield('css-include-before')
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!--Alertify-->
      <link rel="stylesheet" href={{asset("vendor/alertifyjs-1.13.1/css/alertify.min.css")}}>
      <link rel="stylesheet" href={{asset("vendor/alertifyjs-1.13.1/css/themes/bootstrap.min.css")}}>
      <link rel="stylesheet" href={{asset("vendor/alertifyjs-1.13.1/css/themes/custom.css")}}>
      <!-- Font Awesome -->
      <link rel="stylesheet" href={{asset("vendor/fontawesome-5.15.1/css/all.min.css")}}>
      <!-- Theme style -->
      <link rel="stylesheet" href={{asset("vendor/adminlte-2.4/css/adminlte.min.css")}}>
      <!-- overlayScrollbars -->
      <link rel="stylesheet" href={{asset("vendor/overlayscrollbars-1.13/css/OverlayScrollbars.min.css")}}>
      <!-- Daterange picker -->
      <link rel="stylesheet" href={{asset("vendor/daterangepicker/css/daterangepicker.css")}}>
      <!-- summernote -->
      <link rel="stylesheet" href={{asset("vendor/summernote-0.8.18/css/summernote-bs4.min.css")}}>
      <!--Datatables-->
      <link rel="stylesheet" href={{asset("vendor/datatables/css/dataTables.bootstrap4.min.css")}}>
      <!--Chartjs-->
      <link rel="stylesheet" href={{asset("vendor/chartjs/css/chart.min.css")}}>
      <!--jquery-ui-->
      <link rel="stylesheet" href={{asset("vendor/jquery-ui/jquery-ui.min.css")}}>
      <link rel="stylesheet" href={{asset("vendor/jquery-ui/jquery-ui.theme.min.css")}}>
      <!-- Google Font: Source Sans Pro -->
      <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    </head>
    <body class="hold-transition sidebar-mini layout-fixed">
        <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark navbar-dark-grey">
          <!--Left navbar links--> 
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
          </ul>

          <!-- Right navbar links -->
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a href="#" class="nav-link user-panel d-flex" data-toggle="dropdown" aria-expanded="true">
                    <div class="image">
                        <img 
                            src="<?= route('media.photo_profile') ?>" 
                            class="img-size-50 img-circle elevation-2" 
                            alt="User Image"
                        >
                    </div>
                    <div class="info">
                        <?= Auth::user()->name ?>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-md dropdown-menu-right">
                    <a class="dropdown-item" href="<?= route('backyard.user.user.show',Auth::user()->id) ?>">
                        <i class="fas fa-id-card-alt"></i> View Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?= route('logout') ?>">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                </div>
            </li>
          </ul>
        </nav>
        <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href=<?= Route('home') ?> class="brand-link">
          <img src={{asset('icon.png')}} alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
               style="opacity: .8">
          <span class="brand-text font-weight-light"><?= env('APP_NAME') ?></span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">

          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <ul class="nav nav-pills nav-child-indent nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php
                    $menus = require base_path("resources/views") . "/menu.php";
                    echo Helper::renderMenus($menus);
                ?>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                  <h1 class="m-0 text-dark">
                      @yield('submodule-header')
                  </h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  @yield('breadcrumb')
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
              @yield('content')
          </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <footer class="main-footer">
        <strong>Copyright &copy; 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
          MadNewbieTech
        </div>
      </footer>

      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->
    </div>
        <!-- ./wrapper -->

        <!-- jQuery -->
        <script src={{asset("vendor/jquery/jquery.min.js")}}></script>
        <!-- jQuery UI 1.11.4 -->
        <script src={{asset("vendor/jquery-ui/jquery-ui.min.js")}}></script>
        <!-- Bootstrap 4 -->
        <script src={{asset("vendor/bootstrap-4/js/bootstrap.bundle.min.js")}}></script>
        <!-- daterangepicker -->
        <script src={{asset("vendor/moment/moment-with-locales.min.js")}}></script>
        <script src={{asset("vendor/daterangepicker/js/daterangepicker.js")}}></script>
        <!-- ChartJS -->
        <script src={{asset("vendor/chartjs/js/chart.min.js")}}></script>
        <!-- Summernote -->
        <script src={{asset("vendor/summernote-0.8.18/js/summernote-bs4.min.js")}}></script>
        <!-- overlayScrollbars -->
        <script src={{asset("vendor/overlayscrollbars-1.13/js/jquery.overlayScrollbars.min.js")}}></script>
        <!-- AdminLTE App -->
        <script src={{asset("vendor/adminlte-2.4/js/adminlte.min.js")}}></script>
        <!--Alertify-->
        <script src={{asset("vendor/alertifyjs-1.13.1/js/alertify.min.js")}}></script>
        <!--Datatables-->
        <script src={{asset("vendor/datatables/js/jquery.dataTables.min.js")}}></script>
        <script src={{asset("vendor/datatables/js/dataTables.bootstrap4.min.js")}}></script>
        
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
                    $message = implode('</br>', $errors->all())
                    ?>
                    alertify.error('<?= $message ?>');
                @endif
            })
        </script>
    </body>
</html>