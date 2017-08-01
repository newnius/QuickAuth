<?php 
  //require_once('auth-header.php');
  require_once('config.inc.php');
  require_once('util4p/Session.class.php');
  require_once('init.inc.php');
  require_once('secure.php');
  require_once('cookie.php');
?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="keywords" content="QuickAuth, free, quick, OAuth"/>
    <meta name="description" content="QuickAuth is an implement of authorization. By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Also,it is totally free!" />
    <meta name="author" content="Newnius"/>
    <link rel="icon" href="favicon.ico"/>
    <title>Sign in | QuickAuth</title>
    <!-- Bootstrap core CSS -->
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
    <!-- Custom styles for this template -->
    <link href="style.css" rel="stylesheet"/>
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
  </head>

  <body>
    <?php require_once('header.php'); ?>
    <div class="container">
      <div id="signin-error">
        <strong>Error:</strong>
        <span id="signin-error-msg"></span>
      </div>
      <?php if(!isset($_SESSION['username'])){ ?>
      <div id="login">
        <form class="form-signin" action="#">
          <h2 class="form-signin-heading">Please sign in</h2>
          <label for="Account" class="sr-only">Account</label>
          <input type="text" id="account" class="form-control " placeholder="Username/Email" required autofocus>
          <label for="Password" class="sr-only">Password</label>
          <input type="password" id="password" class="form-control" placeholder="Password" required>
          <div class="checkbox">
            <label>
              <input type="checkbox" id="rememberme" value="remember-me"> Remember me
            </label>
            <label>
              <span><a class="text-right" href="lostpass.php">Forget?</a></span>
 	    </label>        
          </div>
          <button id="btn-login" class="btn btn-lg btn-primary btn-block" type="submit" >Sign in</button>
          <p class="msg">
            <a href="<?=BASE_URL?>/register.php" >Register</a>
          </p>
          <div class="alert alert-danger alert-dismissable my-info"  style="display:none">
      	    <button type="button" class="close" data-dismiss="alert"  aria-hidden="true">&times;</button>
            <span id="msg-info"></a>
          </div>
        </form>
      </div>
      <?php }else{ ?>
      <div id="haveloged" class="panel panel-default">
        <h4>You have loged in</h4>
        <div><input type="radio" checked="checked"/>&nbsp;&nbsp;<?php echo $_SESSION['username'] ?></div><br/>
        <a href="<?=BASE_URL?>/ucenter.php" class="btn btn-primary ">Enter Ucenter</a>
        &nbsp;&nbsp;&nbsp;<a href="ucenter.php?signout">Sign out</a>
      </div>
      <?php } ?>

    </div> <!-- /container -->
    <?php require_once('footer.php') ?>

    <script src="js/script.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
    <script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
  </body>
</html>


