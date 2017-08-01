<?php
  require_once('util4p/Session.class.php');
  require_once('config.inc.php');
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
    <meta name="description" content="QuickAuth is an implement of authorization. By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Also ,it is totally free!" />
    <meta name="author" content="Newnius"/>
    <link rel="icon" href="favicon.ico"/>
    <title>QuickAuth | free and quick auth</title>
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
    <div class="jumbotron">
      <h2>What is QuickAuth</h2>
      <p><a href="<?=BASE_URL?>/">QuickAuth</a> is an implement of authorization(not a standard). By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Frankly speaking, only my websites would use this platform.(hahah)</p>
      <p><a class="btn btn-primary btn-lg" href="<?=BASE_URL?>/register.php?getstarted">Get started</a></p>
    </div>
    <div class="jumbotron">
      <h2>How does QuickAuth work</h2>
      <div id="tabs">
        <ul class="nav nav-tabs">
          <li role="presentation active"><a href="#how-user">User</a></li>
          <li role="presentation"><a href="#how-developer">Developer</a></li>
        </ul>
        <div id="how-user">
          <p>As a user: All you need to do is when the websites you visit redirect you here, fill in your username and password. Done!</p>
        </div>
        <div id="how-developer">
          <p>Here is a <a href="<?=BASE_URL?>/help.php#qid-3">demo</a>, help yourself.</p>
              <a class="btn btn-primary btn-lg" href="<?=BASE_URL?>/register.php?tryitfree">Try it <strong>FREE</strong></a>
        </div>
      </div>
    </div>
    <div class="jumbotron">
      <h2>Why will I use QuickAuth</h2>
	<p>&nbsp;&nbsp;You don't have to own a Google account, but when you access some Google products, you need one, right?</p>
        <p><a class="btn btn-primary btn-lg" href="<?=BASE_URL?>/register.php?tryitnow">Try It Now</a></p>
      </div>
      <div class="jumbotron">
        <h2>FAQs</h2>
      <div>
      <h4><span class="glyphicon glyphicon-question-sign"></span>What kind of information can be accessed?</h4>
      <p>&nbsp;&nbsp;In short,information such as username, email, verified or not, and some personal information(granted by the user) can be accessed.</p>
    </div>
    <div>
      <h4><span class="glyphicon glyphicon-question-sign"></span>I am a developer, how to garanteen the auth?</h4>
        <p>&nbsp;&nbsp;It is hard to answer in short, you can have a look at <a href="<?=BASE_URL?>/help.php#qid-6">this</a>.</p>
    </div>
    <div>
      <h4><span class="glyphicon glyphicon-question-sign"></span>What the relationship with OAuth?</h4>
      <p>&nbsp;&nbsp;If you leave state blank in OAuth, this is kind of an implement of OAuth (code), but do not following that standrd especially the format of data exchanged.</p>
      <a href="<?=BASE_URL?>/help.php" class="btn btn-primary">Learn more</a>
    </div>
  </div>
</div> <!-- /container -->
<?php require_once('footer.php'); ?>

  <script src="js/script.js"></script>
  <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
  <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
  <script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
  <script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script>
</body>
</html>


