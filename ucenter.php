<?php
require_once('predis/autoload.php');

require_once('util4p/util.php');
require_once('util4p/ReSession.class.php');
require_once('util4p/AccessController.class.php');

require_once('user.logic.php');
require_once('global.inc.php');
require_once('config.inc.php');
require_once('secure.inc.php');
require_once('init.inc.php');


if (Session::get('username') == null) {
	header('location:login?a=notlogged');
	exit;
}

$page_type = 'home';
$username = Session::get('username');

if (isset($_GET['profile'])) {
	$page_type = 'profile';

} elseif (isset($_GET['changepwd'])) {
	$page_type = 'changepwd';

} elseif (isset($_GET['users'])) {
	$page_type = 'users';

} elseif (isset($_GET['sites'])) {
	$page_type = 'sites';

} elseif (isset($_GET['sites_all'])) {
	$page_type = 'sites_all';

} elseif (isset($_GET['users_online'])) {
	$page_type = 'users_online';

} elseif (isset($_GET['user_sessions'])) {
	$page_type = 'user_sessions';

} elseif (isset($_GET['blocked_list'])) {
	$page_type = 'blocked_list';

} elseif (isset($_GET['logs'])) {
	$page_type = 'logs';

} elseif (isset($_GET['auth_list'])) {
	$page_type = 'auth_list';

} elseif (isset($_GET['logs_all'])) {
	$page_type = 'logs_all';

} elseif (isset($_GET['visitors'])) {
	$page_type = 'visitors';

} elseif (isset($_GET['admin'])) {
	$page_type = 'admin';
}

$entries = array(
	array('home', 'Home'),
	array('profile', 'Profile'),
	array('changepwd', 'Password'),
	array('logs', 'Activities'),
	array('auth_list', 'Auths'),
	array('sites', 'Sites'),
	array('user_sessions', 'Sessions'),
	array('admin', 'Admin')
);
$visible_entries = array();
foreach ($entries as $entry) {
	if (AccessController::hasAccess(Session::get('role'), 'ucenter.show_' . $entry[0])) {
		$visible_entries[] = array($entry[0], $entry[1]);
	}
}

$admin_entries = array(
	array('users', 'Users'),
	array('sites_all', 'Sites (ALL)'),
	array('users_online', 'Online'),
	array('blocked_list', 'Blocks'),
	array('logs_all', 'Activities'),
	array('visitors', 'Visitors')
);
$visible_admin_entries = array();
foreach ($admin_entries as $entry) {
	if (AccessController::hasAccess(Session::get('role'), 'ucenter.show_' . $entry[0])) {
		$visible_admin_entries[] = array($entry[0], $entry[1]);
	}
}
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<?php require('head.php'); ?>
	<title>User Center | QuickAuth</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.12.1/dist/bootstrap-table.min.css" rel="stylesheet">
	<script type="text/javascript">
		var page_type = "<?=$page_type?>";
	</script>
</head>
<body>
<div class="wrapper">
	<?php require('header.php'); ?>
	<?php require('modals.php'); ?>
	<div class="container">
		<div class="row">

			<div class="hidden-xs hidden-sm col-md-2 col-lg-2">
				<div class="panel panel-default">
					<div class="panel-heading">Options</div>
					<ul class="nav nav-pills nav-stacked panel-body">
						<?php foreach ($visible_entries as $entry) { ?>
							<li role="presentation" <?php if ($page_type == $entry[0]) echo 'class="disabled"'; ?> >
								<a href="?<?= $entry[0] ?>"><?= $entry[1] ?></a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>

			<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
				<div class="visible-xs visible-sm">
					<div class=" panel panel-default">
						<div class="panel-heading">Options</div>
						<ul class="nav nav-pills panel-body">
							<?php foreach ($visible_entries as $entry) { ?>
								<li role="presentation" <?php if ($page_type == $entry[0]) echo 'class="disabled"'; ?> >
									<a href="?<?= $entry[0] ?>"><?= $entry[1] ?></a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>

				<?php if ($page_type == 'home') { ?>
					<div id="home">
						<div class="panel panel-default">
							<div class="panel-heading">Welcome</div>
							<div class="panel-body">
								Welcome back, <?= htmlspecialchars($username) ?>.<br/>
								Now: &nbsp; <?php echo date('H:i:s', time()) ?>
							</div>
						</div>
						<div class="panel panel-default">
							<div class="panel-heading">Notices</div>
							<div class="panel-body">
								No new notices.
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'profile') { ?>
					<div id="profile">
						<div class="panel panel-default">
							<div class="panel-heading">Profile</div>
							<div class="panel-body">
								<table class="table">
									<tr>
										<th>Username</th>
										<td>
											<span id="user-username">Loading...</span>
										</td>
									</tr>
									<tr>
										<th>Email</th>
										<td>
											<span id="user-email">Loading...</span>&nbsp;(
											<a href="javascript:void(0)" id="btn-update-email">Edit</a>&nbsp;&nbsp;
											<a href="javascript:void(0)" id="btn-verify-email">Verify</a>
											)
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
											<a href="?changepwd">Edit</a>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'changepwd') { ?>
					<div id="changepwd">
						<div class="panel panel-default">
							<div class="panel-heading">Password</div>
							<div class="panel-body">
								<div id="resetpwd">
									<h2>Update Password</h2>
									<form>
										<div class="form-group">
											<label class="sr-only" for="inputOldpwd">Old password</label>
											<div class="input-group">
												<div class="input-group-addon">
													<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
												</div>
												<input type="password" class="form-control" id="form-updatepwd-oldpwd"
												       placeholder="current password" required/>
											</div>
										</div>
										<div class="form-group">
											<label class="sr-only" for="inputPassword">New Password</label>
											<div class="input-group">
												<div class="input-group-addon">
													<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
												</div>
												<input type="password" class="form-control" id="form-updatepwd-password"
												       placeholder="new password" required/>
											</div>
										</div>
										<button id="btn-updatepwd" class="btn btn-md btn-primary " type="submit">
											Update
										</button>
									</form>
								</div>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'users') { ?>
					<div id="users">
						<div class="panel panel-default">
							<div class="panel-heading">Users</div>
							<div class="panel-body">
								<div class="table-responsive">
									<div id="toolbar">
										<button id="btn-user-add" class="btn btn-primary">
											<i class="glyphicon glyphicon-plus"></i> Add
										</button>
									</div>
									<table id="table-user" data-toolbar="#toolbar" class="table table-striped">
									</table>
									<span class="text-info">* It is not allowd to update yourself</span>
								</div>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'sites') { ?>
					<div id="sites">
						<div class="panel panel-default">
							<div class="panel-heading">Sites</div>
							<div class="panel-body">
								<div class="table-responsive">
									<div id="toolbar">
										<button id="btn-site-add" class="btn btn-primary">
											<i class="glyphicon glyphicon-plus"></i> Add
										</button>
									</div>
									<table id="table-site" data-toolbar="#toolbar" class="table table-striped">
									</table>
									<span class="text-info">* Clients in OAuth2</span>
								</div>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'sites_all') { ?>
					<div id="sites">
						<div class="panel panel-default">
							<div class="panel-heading">Sites (All)</div>
							<div class="panel-body">
								<div class="table-responsive">
									<div id="toolbar">
										<button id="btn-site-add" class="btn btn-primary">
											<i class="glyphicon glyphicon-plus"></i> Add
										</button>
									</div>
									<table id="table-site" data-toolbar="#toolbar" class="table table-striped">
									</table>
									<span class="text-info">* Clients in OAuth2</span>
								</div>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'users_online') { ?>
					<div id="users_online">
						<div class="panel panel-default">
							<div class="panel-heading">Online Users</div>
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

				<?php } elseif ($page_type == 'user_sessions') { ?>
					<div id="user_sessions">
						<div class="panel panel-default">
							<div class="panel-heading">Sessions</div>
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

				<?php } elseif ($page_type == 'blocked_list') { ?>
					<div id="blocked">
						<div class="panel panel-default">
							<div class="panel-heading">Block States</div>
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

				<?php } elseif ($page_type == 'logs') { ?>
					<div id="logs">
						<div class="panel panel-default">
							<div class="panel-heading">Recent activities</div>
							<div class="panel-body">
								<div class="table-responsive">
									<div id="toolbar"></div>
									<table id="table-log" data-toolbar="#toolbar" class="table table-striped">
									</table>
								</div>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'auth_list') { ?>
					<div>
						<div class="panel panel-default">
							<div class="panel-heading">Auth Site</div>
							<div class="panel-body">
								<div class="table-responsive">
									<div id="toolbar"></div>
									<table id="table-auth" data-toolbar="#toolbar" class="table table-striped">
									</table>
									<span class="text-info">* Authentications</span>
								</div>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'logs_all') { ?>
					<div id="logs">
						<div class="panel panel-default">
							<div class="panel-heading">Recent activities</div>
							<div class="panel-body">
								<div class="table-responsive">
									<div id="toolbar"></div>
									<table id="table-log" data-toolbar="#toolbar" class="table table-striped">
									</table>
								</div>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type === 'visitors') { ?>
					<div id="visitors">
						<div class="panel panel-default">
							<div class="panel-heading">Visitors</div>
							<div class="panel-body">
								<table class="table table-striped table-bordered">
									<tr>
										<th>PV</th>
										<td><span class="cr_count_site_pv">Loading</span></td>
									</tr>
									<tr>
										<th>UV</th>
										<td><span class="cr_count_site_uv">Loading</span></td>
									</tr>
									<tr>
										<th>PV (today)</th>
										<td><span class="cr_count_site_pv_24h">Loading</span></td>
									</tr>
									<tr>
										<th>UV (today)</th>
										<td><span class="cr_count_site_uv_24h">Loading</span></td>
									</tr>
								</table>
							</div>
						</div>
					</div>

				<?php } elseif ($page_type == 'admin') { ?>
					<div class=" panel panel-default">
						<div class="panel-heading">Admin Panel</div>
						<h4 style="text-align:center">Admin Panel</h4>
						<ul class="nav nav-pills panel-body">
							<?php foreach ($visible_admin_entries as $entry) { ?>
								<li role="presentation" <?php if ($page_type == $entry[0]) echo 'class="disabled"'; ?> >
									<a href="?<?= $entry[0] ?>"><?= $entry[1] ?></a>
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

<?php require('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.12.1/dist/bootstrap-table.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.12.1/dist/extensions/mobile/bootstrap-table-mobile.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.12.1/dist/extensions/export/bootstrap-table-export.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.1/tableExport.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/blueimp-md5@2.10.0/js/md5.min.js"></script>
<script src="static/auth.js"></script>
<script src="static/user.js"></script>
<script src="static/session.js"></script>
<script src="static/secure.js"></script>
<script src="static/site.js"></script>
<script src="static/ucenter.js"></script>

</body>
</html>