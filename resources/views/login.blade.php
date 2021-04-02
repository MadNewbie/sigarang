<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?= env('APP_NAME') ?> | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href={{asset("vendor/fontawesome-5.15.1/css/all.min.css")}}>
  <!-- Theme style -->
  <link rel="stylesheet" href={{asset("vendor/adminlte-2.4/css/adminlte.min.css")}}>
</head>

<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <?= env('APP_NAME') ?>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="<?php route('login')?>" method="post">
          @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row flex-row-reverse">
<!--          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>-->
          <!-- /.col -->
          <div class="col-md-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

<!--      <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p>
      <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p>-->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src={{asset("vendor/jquery/jquery.min.js")}}></script>
<!-- Bootstrap 4 -->
<script src={{asset("vendor/bootstrap-4/js/bootstrap.bundle.min.js")}}></script>
<!-- AdminLTE App -->
<script src={{asset("vendor/adminlte-2.4/js/adminlte.min.js")}}></script>

<script type="text/javascript">
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

