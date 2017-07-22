<?php
  session_start();
  if(!(isset($_SESSION['username']) )){
    header('location:login.php?a=notloged');
    exit;
  }
  require_once('config.inc.php');
  require_once('secure.php');
  require_once('cookie.php');
  require_once('auth-header.php');
  require_once('account-functions.php');

  $page_type = 'home';
  $username = $_SESSION['username'];

  if(isset($_GET['profile'])){
    $page_type='profile';
  
  }elseif(isset($_GET['changepwd'])){
    $page_type='changepwd';
  
  }elseif(isset($_GET['verify'])){
    $page_type='verify';

  }elseif(isset($_GET['logs'])){
    $page_type='logs';

  }elseif(isset($_GET['signout'])){
    $page_type='signout';
     signout();
     header('location:login.php?a=signout');
     exit;
  }

  switch($page_type)
  {
    case 'profile':
      $profile = get_user_information($username); 
      if($profile == null ){
        header('location:index.php?a=notloged');
        exit;
      }
      break;

    case 'changepwd':
      break;

    case 'verify':
      $profile = get_user_information($username);
      break;

    case 'logs':
      $logs = get_log_by_username($username);
      break;
	  
    default:
      break;
  }
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
    <title>Ucenter | QuickAuth</title>
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
          <div class="panel panel-default">
            <div class="panel-heading">Settings</div>
            <ul class="nav nav-pills nav-stacked panel-body">
              <li role="presentation" <?php if($page_type=='home')echo 'class="disabled"'; ?> >
                <a href="?home">Home</a>
              </li>
              <li role="presentation" <?php if($page_type=='profile')echo 'class="disabled"'; ?> >
                <a href="?profile">Profile</a>
              </li>
              <li role="presentation" <?php if($page_type=='changepwd')echo 'class="disabled"'; ?> >
                <a href="?changepwd">Password</a>
              </li>
              <li role="presentation" <?php  if($page_type=='verify')echo 'class="disabled"'; ?> >
                <a href="?verify">Verify</a>
              </li>
              <li role="presentation" <?php  if($page_type=='logs')echo 'class="disabled"'; ?> >
                <a href="?logs">Logs</a>
              </li>
              <li role="presentation">
                <a href="help.php">Help</a>
              </li>
              <li role="presentation">
                <a href="?signout">Sign out</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-md-offset-1 ">
          <div class=" visible-xs">
            <div class=" panel panel-default">
              <div class="panel-heading">Menubar</div>
              <ul class="nav nav-pills panel-body">
                <li role="presentation" <?php if($page_type == 'home')echo 'class="disabled"'; ?> >
                  <a href="?home">Home</a>
                </li>
                <li role="presentation" <?php if($page_type == 'profile')echo 'class="disabled"'; ?> >
                  <a href="?profile">Profile</a>
                </li>
                <li role="presentation" <?php if($page_type == 'changepwd')echo 'class="disabled"'; ?>>
                  <a href="?changepwd">Password</a>
                </li>
                <li role="presentation" <?php if($page_type == 'verify')echo 'class="disabled"'; ?> >
                  <a href="?verify">Verify</a>
                </li>
                <li role="presentation" <?php if($page_type == 'logs')echo 'class="disabled"'; ?> >
                  <a href="?logs">Logs</a>
                </li>
             </ul>
          </div>
        </div>

        <?php if($page_type == 'home'){ ?>
        <div id="home">
          <div class="panel panel-default">
            <div class="panel-heading">Welcome</div> 
            <div class="panel-body">
              Welcome back, <?php echo htmlspecialchars($username) ?>.<br/>
              Your ip: &nbsp; <?php echo get_ip() ?>.<br/>
              Now: &nbsp; <?php echo date('H:i:s',time()) ?>
            </div>
          </div>
          <div class="panel panel-default">
            <div class="panel-heading">Notice</div> 
            <div class="panel-body">
              You don't have new notice.
            </div>
          </div>
        </div>
      
        <?php }elseif($page_type == 'profile'){ ?>
        <div id="profile">
          <div class="panel panel-default">
            <div class="panel-heading">Profile</div> 
            <div class="panel-body">
              Username:&nbsp;<?php echo htmlspecialchars($profile['username']) ?><br/>
              Email:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($profile['email']) ?>
              <?php if($profile['verified'] == 'f'){
                      echo '(<a href="?verify">Verify</a>)';
                    }else{
                      echo '(Verified)';
                    }
              ?><br/>
              Join time:&nbsp;<?php echo date('M,d H:i',$profile['reg_time'])?>
            </div>
          </div>
        </div>
     
        <?php }elseif($page_type == 'changepwd'){ ?>
        <div id="changepwd">
          <div class="panel panel-default">
            <div class="panel-heading">Change password</div> 
            <div class="panel-body">
              <div id="resetpwd">
                <h2>Update password</h2>
                <form class="form-changepwd">
                  <div class="form-group">
                    <label class="sr-only" for="inputOldpwd">Old password</label>
                    <div class="input-group">
                      <div class="input-group-addon">
                        <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                      </div>
                      <input type="password" class="form-control" id="oldpwd" placeholder="Old password" required />
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="sr-only" for="inputPassword">New Password</label>
                    <div class="input-group">
                      <div class="input-group-addon">
                        <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                      </div>
                      <input type="password" class="form-control" id="password" placeholder="New password" required />
                    </div>
                  </div>
                  <button id="btn-changepwd" class="btn btn-md btn-primary " type="submit" >Update</button>
                </form>
                <span id="changepwd-msg" class="text-danger"></span>
              </div>
            </div>
          </div>
        </div>
   
        <?php }elseif($page_type == 'verify'){ ?>
        <div id="ucenter-verify">
          <div class="panel panel-default">
            <div class="panel-heading">Verify</div> 
            <div class="panel-body">
            <?php //a link of change email(if not verified) should be given here ?>
              Email:<?php echo htmlspecialchars($profile['email']) ?>
              <?php if($profile['verified']=='t'){
                        echo '(Verified)';
                        echo '<br/><br/>If you no longer own this email, you can choose to <a href="#">Unverify</a> it.';
                     }else{
                        echo '<br/><button id="btn-verify-online" class="btn btn-md btn-primary btn-lock">Send me an email</button>';
                        echo '<br/><span id="verify-online-msg" class="text-info"></span>';
                     } 
               ?>
            </div>
          </div>
        </div>
  
        <?php }elseif($page_type == 'logs'){ ?>
        <div id="logs">
          <div class="panel panel-default">
            <div class="panel-heading">Recent activities</div> 
            <div class="panel-body table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Account</th>
                    <th>Time</th>
                    <th>Accepted</th>
                    <th>Ip</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                  $cnt = count($logs); 
                  for($i=0; $i<$cnt; $i++){ ?>
                  <tr>
                    <td><?php echo $i+1 ?></td>
                    <td><?php echo htmlspecialchars($logs[$i]['account']) ?></td>
                    <td><?php echo date('M,d H:i',$logs[$i]['time'])?></td>
                    <td><?php echo $logs[$i]['accepted']=='t'?'Success':'Failed' ?></td>
                    <td><?php echo long2ip($logs[$i]['ip']) ?></td>
                  </tr>
                <?php } ?>
                </tbody>
              </table> 
              <span class="text-info">* Only the last 20 records are listed</span><br/>
              <span class="text-info">* If you find any strange record, be careful and should better <a href="?changepwd">update your password</a></span>
            </div>
          </div>
        </div>
        <?php } ?>
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


