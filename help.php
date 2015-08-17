<?php
  session_start();
  require_once('qa-config.php');
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
    <title>Manual | QuickAuth</title>
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
      <div class="row">
        <div class="col-sm-4 col-md-3 hidden-xs">
          <div id="help-nav" class="panel panel-default">
            <div class="panel-heading">Documents</div>
            <ul class="nav nav-pills nav-stacked panel-body">
              <li role="presentation">
                <a href="#qid-1">What is QuickAuth</a>
              </li>
              <li role="presentation">
                <a href="#qid-2">How to use (user)</a>
              </li>
              <li role="presentation">
                <a href="#qid-3">How to use (developer)</a>
              </li>
              <li role="presentation">
                <a href="#qid-4">Why choose QuickAuth</a>
              </li>
              <li role="presentation">
                <a href="#qid-5">Information accessed</a>
              </li>
              <li role="presentation">
                <a href="#qid-6">Process of auth</a>
              </li>
              <li role="presentation">
                <a href="#qid-7">How long will auth be valid</a>
              </li>
              <li role="presentation">
                <a href="#qid-8">Relation with OAuth</a>
              </li>
              <li role="presentation">
                <a href="#qid-9">Unable to deliver email</a>
              </li>
              <li role="presentation">
                 <a href="#qid-999">Feedback</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-md-offset-1 ">
          <div id="qid-1" class="panel panel-default">
            <div class="panel-heading">What is QuickAuth</div> 
            <div class="panel-body">
              <p><a href="<?php echo DOMAIN ?>">QuickAuth</a> is an implement of authorization(not a standard). By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Frankly speaking, only my websites would use this platform.(hahah)</p>
            </div>
          </div>
          <div id="qid-2" class="panel panel-default">
            <div class="panel-heading">How to use QuickAuth (As a user)</div>
            <div class="panel-body">
              <p>As a user: All you need to do is when the websites you visit redirect you here, fill in your username and password. Done!</p>
            </div>
          </div>
          <div id="qid-3" class="panel panel-default">
            <div class="panel-heading">How to use QuickAuth (As a developer)</div> 
            <div class="panel-body">
              <p>Here is a <a href="http://demo.newnius.com/quickauthdemo/demo.php" target="_blank">demo</a>, help yourself.</p>
            </div>
          </div>
          <div id="qid-4" class="panel panel-default">
            <div class="panel-heading">Why will I use QuickAuth</div> 
            <div class="panel-body">
              <p>&nbsp;&nbsp;You don't have to own a Google account, but when you access some Google products, you need one, right?</p>
            </div>
          </div>
          <div id="qid-5" class="panel panel-default">
            <div class="panel-heading">What kind of information can be accessed</div> 
            <div class="panel-body">
              <p>&nbsp;&nbsp;In short,information such as username, email, verified or not, and some personal information(granted by the user) can be accessed.</p>
            </div>
          </div>
          <div id="qid-6" class="panel panel-default">
            <div class="panel-heading">I am a developer, how to guaranteen the auth</div> 
            <div class="panel-body">
              <p>The process of auth is:<br/>1. Client redirect user to QuickAuth, attaching your redirect uri<br/>2. User sign on to finish grant<br/>3. Redirect to uri requested, attaching auth code and username<br/>4. Client sending auth code and username QuickAuth in the backend<br/>5. QuickAuth response information in json for success, or error info in json for invalid auth key<br/>Note: If you know <abbr title="An open protocol to allow secure authorization">OAuth</abbr>, it would be easy to understand</p>
            </div>
          </div>
          <div id="qid-7" class="panel panel-default">
            <div class="panel-heading">How long will the auth be valid</div> 
            <div class="panel-body">
              <p>&nbsp;&nbsp;THe access token can be used only once, so you don't have to worry about consistent access.</p>
            </div>
          </div>
          <div id="qid-8" class="panel panel-default">
            <div class="panel-heading">What is the relationship between this and OAuth</div> 
            <div class="panel-body">
              <p>&nbsp;&nbsp;If you leave state blank in OAuth, this is kind of an implement of OAuth (code), but do not following that standrd especially the format of data exchanged.</p>
            </div>
          </div>
          <div id="qid-9" class="panel panel-default">
            <div class="panel-heading">Why it says unable to deliver email to my email address</div> 
            <div class="panel-body">
              <p>Possible reasons<br/>1. Email address not exist.<br/>2. Your email address provider refused that email.<br/>3. Our email service provider is busy now, please try again later.<br/>4. In order to avoid abuse, maximum request exceeded from your request, please try another day.<br/>5. If neither of them can solve your problem, please contact us (At the buttom)</p>
            </div>
          </div>
          <div id="qid-999" class="panel panel-default">
            <div class="panel-heading">More</div> 
            <div class="panel-body">
              <p>This document has not been completed. If you have any problem, please contact me at <a href="mailto:support@newnius.com?subject=From QuicjAuth">support@newnius.com</a></p>
            </div>
          </div>
        
        </div>
      </div>
    </div> <!-- /container -->
    <?php require_once('footer.php'); ?>
    <script src="script.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
    <script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
  </body>
</html>


