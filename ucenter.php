<?php
	require_once('predis/autoload.php');
	require_once('util4p/util.php');
	require_once('util4p/ReSession.class.php');
	require_once('util4p/AccessController.class.php');

	require_once('user.logic.php');
	require_once('UserManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');

	if(Session::get('username')==null){
		header('location:login.php?a=notloged');
		exit;
	}

	$page_type = 'home';
	$username = Session::get('username');

	if(isset($_GET['profile'])){
		$page_type='profile';

	}elseif(isset($_GET['changepwd'])){
		$page_type='changepwd';

	}elseif(isset($_GET['users'])){
		$page_type='users';

	}elseif(isset($_GET['sites_all'])){
		$page_type='sites_all';

	}elseif(isset($_GET['users_online'])){
		$page_type='users_online';

	}elseif(isset($_GET['user_sessions'])){
		$page_type='user_sessions';

	}elseif(isset($_GET['blocked_list'])){
		$page_type='blocked_list';

	}elseif(isset($_GET['logs'])){
		$page_type='logs';

	}elseif(isset($_GET['auth_list'])){
		$page_type='auth_list';

	}elseif(isset($_GET['logs_all'])){
		$page_type='logs_all';

	}elseif(isset($_GET['admin'])){
		$page_type='admin';

	}elseif(isset($_GET['signout'])){
		$page_type='signout';
		signout();
		header('location:login.php?a=signout');
		exit;
	}

	$entries = array(
		array('home', '个人首页'),
		array('profile', '用户信息'),
		array('changepwd', '修改密码'),
		array('logs', '登录日志'),
		array('auth_list', '授权记录'),
		array('user_sessions', '登录状态'),
		array('admin', '管理入口'),
		array('signout', '退出登录')
	);
	$visible_entries = array();
	foreach($entries as $entry){
		if(AccessController::hasAccess( Session::get('role'), 'show_ucenter_'.$entry[0])){
			$visible_entries[] = array($entry[0], $entry[1]);
		}
	}

	$admin_entries = array(
		array('users', '用户管理'),
		array('sites_all', '站点管理'),
		array('users_online', '在线用户'),
		array('blocked_list', '封禁列表'),
		array('logs_all', '操作日志'),
	);
	$visible_admin_entries = array();
	foreach($admin_entries as $entry){
		if(AccessController::hasAccess( Session::get('role'), 'show_ucenter_'.$entry[0])){
			$visible_admin_entries[] = array($entry[0], $entry[1]);
		}
	}
?>
<!DOCTYPE html>
<html lang="zh-CN">
	<head>
		<meta charset="utf-8"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<meta name="keywords" content="QuickAuth, free, quick, OAuth"/>
		<meta name="description" content="QuickAuth is an implement of authorization. By using QuickAuth, you can log in to some websites without sign up for another account, which most likely will be used only once. Also ,it is totally free!" />
		<meta name="author" content="Newnius"/>
		<link rel="icon" href="favicon.ico"/>
		<title>User Center | QuickAuth</title>
		<link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
		<link href="style.css" rel="stylesheet"/>
		<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>

		<link href="//cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table.min.css" rel="stylesheet">

		<script type="text/javascript">
			var page_type = "<?=$page_type?>";
		</script>
	</head>

	<body>
		<div class="wrapper">
			<?php require_once('header.php'); ?>
			<?php require_once('modals.php'); ?>
			<div class="container">
				<div class="row">

					<div class="hidden-xs hidden-sm col-md-2 col-lg-2">
						<div class="panel panel-default">
							<div class="panel-heading">功能列表</div>
							<ul class="nav nav-pills nav-stacked panel-body">
								<?php foreach($visible_entries as $entry){ ?>
								<li role="presentation" <?php if($page_type==$entry[0])echo 'class="disabled"'; ?> >
									<a href="?<?=$entry[0]?>"><?=$entry[1]?></a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
						<div class="visible-xs visible-sm">
							<div class=" panel panel-default">
								<div class="panel-heading">功能列表</div>
								<ul class="nav nav-pills panel-body">
									<?php foreach($visible_entries as $entry){ ?>
									<li role="presentation" <?php if($page_type==$entry[0])echo 'class="disabled"'; ?> >
										<a href="?<?=$entry[0]?>"><?=$entry[1]?></a>
									</li>
									<?php } ?>
								</ul>
							</div>
						</div>

						<?php if($page_type == 'home'){ ?>
						<div id="home">
							<div class="panel panel-default">
								<div class="panel-heading">Welcome</div> 
								<div class="panel-body">
									欢迎回来, <?php echo htmlspecialchars($username) ?>.<br/>
									当前IP: &nbsp; <?=cr_get_client_ip() ?>.<br/>
									现在时间: &nbsp; <?php echo date('H:i:s',time()) ?>
								</div>
							</div>
							<div class="panel panel-default">
								<div class="panel-heading">通知</div> 
								<div class="panel-body">
									<h4 class="text-info">提示</h4>
									<ul>
										<li>建议及时修改初始密码，不要将密码设置为简单密码</li>
									</ul>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'profile'){ ?>
						<div id="profile">
							<div class="panel panel-default">
								<div class="panel-heading">基本信息</div> 
								<div class="panel-body">
									<table class="table">
										<tr>
											<th>用户名</th>
											<td>
												<span id="user-username">Loading...</span>
											</td>
										</tr>
										<tr>
											<th>Email</th>
											<td>
												<span id="user-email">Loading...</span><a href="javascript:void(0)" id="btn-verify-email" class="btn">Verify</a>
											</td>
										</tr>
										<tr>
											<th>Role</th>
											<td>
												<span id="user-role">Loading...</span>
											</td>
										</tr>
										<tr>
											<th>Password</th>
											<td>
												<span>******</span><a href="?changepwd" class="btn">Update</a>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'changepwd'){ ?>
						<div id="changepwd">
							<div class="panel panel-default">
								<div class="panel-heading">修改密码</div> 
								<div class="panel-body">
									<div id="resetpwd">
										<h2>修改密码</h2>
										<form>
											<div class="form-group">
												<label class="sr-only" for="inputOldpwd">Old password</label>
												<div class="input-group">
													<div class="input-group-addon">
														<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
													</div>
													<input type="password" class="form-control" id="form-updatepwd-oldpwd" placeholder="原来的密码" required />
												</div>
											</div>
											<div class="form-group">
												<label class="sr-only" for="inputPassword">New Password</label>
												<div class="input-group">
													<div class="input-group-addon">
														<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
													</div>
													<input type="password" class="form-control" id="form-updatepwd-password" placeholder="新的密码" required />
												</div>
											</div>
											<button id="btn-updatepwd" class="btn btn-md btn-primary " type="submit" >确认修改</button>
										</form>
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'users'){ ?>
						<div id="users">
							<div class="panel panel-default">
								<div class="panel-heading">用户管理</div> 
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar">
											<button id="btn-user-add" class="btn btn-primary">
												<i class="glyphicon glyphicon-plus"></i> 添加用户
											</button>
										</div>
										<table id="table-user" data-toolbar="#toolbar" class="table table-striped">
										</table> 
										<span class="text-info">* 不支持修改自己</span>
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'sites_all'){ ?>
						<div id="sites_all">
							<div class="panel panel-default">
								<div class="panel-heading">站点管理</div> 
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar">
											<button id="btn-site-add" class="btn btn-primary">
												<i class="glyphicon glyphicon-plus"></i> 添加站点
											</button>
										</div>
										<table id="table-site" data-toolbar="#toolbar" class="table table-striped">
										</table> 
										<span class="text-info">* 不支持</span>
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'users_online'){ ?>
						<div id="users_online">
							<div class="panel panel-default">
								<div class="panel-heading">在线用户</div> 
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar">
										</div>
										<table id="table-user" data-toolbar="#toolbar" class="table table-striped">
										</table> 
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'user_sessions'){ ?>
						<div id="user_sessions">
							<div class="panel panel-default">
								<div class="panel-heading">会话</div> 
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar">
										</div>
										<table id="table-session" data-toolbar="#toolbar" class="table table-striped">
										</table> 
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'blocked_list'){ ?>
						<div id="blocked">
							<div class="panel panel-default">
								<div class="panel-heading">封禁列表</div>
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar">
											<button id="btn-block-add" class="btn btn-primary">
												<i class="glyphicon glyphicon-plus"></i> Add
											</button>
										</div>
										<table id="table-block" data-toolbar="#toolbar" class="table table-striped">
										</table>
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'logs'){ ?>
						<div id="logs">
							<div class="panel panel-default">
								<div class="panel-heading">Recent activities</div> 
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar"></div>
										<table id="table-log" data-toolbar="#toolbar" class="table table-striped">
										</table> 
										<span class="text-info">* 最多显示20条最近的记录</span>
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'auth_list'){ ?>
						<div>
							<div class="panel panel-default">
								<div class="panel-heading">Auth Site</div> 
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar"></div>
										<table id="table-auth" data-toolbar="#toolbar" class="table table-striped">
										</table> 
										<span class="text-info">* 最多显示20条最近的记录</span>
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'logs_all'){ ?>
						<div id="logs">
							<div class="panel panel-default">
								<div class="panel-heading">Recent activities</div> 
								<div class="panel-body">
									<div class="table-responsive">
										<div id="toolbar"></div>
										<table id="table-log" data-toolbar="#toolbar" class="table table-striped">
										</table> 
										<span class="text-info">* 只显示7天内的登录日志</span><br />
										<span class="text-info">* 标签最后一个单词表示操作人</span>
									</div>
								</div>
							</div>
						</div>

						<?php }elseif($page_type == 'admin'){ ?>
						<div class=" panel panel-default">
							<div class="panel-heading">管理入口</div>
							<h4 style="text-align:center">中英文统一管理后台</h4>
							<ul class="nav nav-pills panel-body">
								<?php foreach($visible_admin_entries as $entry){ ?>
								<li role="presentation" <?php if($page_type==$entry[0])echo 'class="disabled"'; ?> >
									<a href="?<?=$entry[0]?>"><?=$entry[1]?></a>
								</li>
								<?php } ?>
							</ul>
						</div>
						<?php } ?>

					</div>
				</div>
			</div> <!-- /container -->

			<!--This div exists to avoid footer from covering main body-->
			<div class="push"></div>
		</div>
		<?php require_once('footer.php'); ?>

		<script src="js/util.js"></script>
		<script src="js/script.js"></script>
		<script src="js/user.js"></script>
		<script src="js/session.js"></script>
		<script src="js/secure.js"></script>
		<script src="js/site.js"></script>
		<script src="js/ucenter.js"></script>

		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table-locale-all.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/extensions/mobile/bootstrap-table-mobile.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/extensions/export/bootstrap-table-export.min.js"></script>
		<script src="//cdn.bootcss.com/TableExport/5.0.0-rc.11/js/tableexport.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
		<script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
	</body>
</html>
