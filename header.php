<nav class="navbar navbar-default">
  <div class="container">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo DOMAIN ?>/">QuickAuth</a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li><a href="<?php echo DOMAIN ?>/">Main Page</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
      <?php if(!(isset($_SESSION['username']))){ ?>
        <li><a href="<?php echo DOMAIN ?>/register.php">Sign up</a></li>
        <li><a href="<?php echo DOMAIN ?>/login.php">Sign in</a></li>
      <?php }else{ ?>
        <li><a href="<?php echo DOMAIN ?>/ucenter.php?profile"><?php echo htmlspecialchars($_SESSION['username'])?></a></li>
        <li><a href="<?php echo DOMAIN ?>/ucenter.php">Explore</a></li>
      <?php } ?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">More<span class="caret"></span></a>
          <ul class="dropdown-menu">
          <?php if(isset($_SESSION['username']) ){ ?>
            <li><a href="<?php echo DOMAIN ?>/ucenter.php?#notice">Notice&nbsp;<span class="badge">0</span></a></li>
            <li><a href="<?php echo DOMAIN ?>/ucenter.php?home">Settings</a></li>
            <li><a href="<?php echo DOMAIN ?>/ucenter.php?signout">Sign out</a></li>
            <li role="separator" class="divider"></li>
          <?php } ?>
            <li><a href="<?php echo DOMAIN ?>/help.php">Help</a></li>
            <li><a href="<?php echo DOMAIN ?>/help.php?about#qid-1">About</a></li>
          </ul>
        </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container -->
</nav>
