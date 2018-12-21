function register_events_user() {
	$("#btn-verify-email").click(function (e) {
		e.preventDefault();
		$('#modal-verify').modal('show');
	});

	$("#btn-verify-send").click(function (e) {
		e.preventDefault();
		$("#btn-verify-send").attr("disabled", "disabled");
		$('#btn-verify-send').text("Sending...");
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=verify_email_send_code",
			type: 'POST',
			data: {}
		});
		ajax.done(function (res) {
			if (res["errno"] !== 0) {
				$('#modal-verify').modal('hide');
				$('#modal-msg').modal("show");
				$('#modal-msg-content').text(res["msg"]);
			} else {
				$('#btn-verify-send').text("Sent");
			}
		});
	});

	$("#form-verify-submit").click(function (e) {
		e.preventDefault();
		var code = $("#form-verify-code").val();
		if (code.length < 6) {
			return false;
		}
		$('#modal-verify').modal("hide");
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=verify_email",
			type: 'POST',
			data: {
				code: code
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				load_profile();
			} else {
				$('#modal-msg').modal("show");
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	});

	$("#btn-update-email").click(function (e) {
		e.preventDefault();
		$('#modal-email').modal('show');
	});

	$("#form-email-submit").click(function (e) {
		var email = $("#form-email-email").val();
		if (email.length < 6) {
			return true;
		}
		$('#modal-email').modal("hide");
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=user_update",
			type: 'POST',
			data: {
				email: email
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				load_profile();
			} else {
				$('#modal-msg').modal("show");
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	});

	$("#btn-updatepwd").click(function (e) {
		e.preventDefault();
		$('#modal-msg').modal('show');
		$('#modal-msg-content').text("Processing...");
		var oldpass = $("#form-updatepwd-oldpwd").val();
		var password = $("#form-updatepwd-password").val();
		if (oldpass.length < 6 || password.length < 6) {
			$("#modal-msg-content").text("Password length should >= 6");
			return false;
		}
		var oldpwd = cryptPwd(oldpass);
		var newpwd = cryptPwd(password);
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=update_pwd",
			type: 'POST',
			data: {
				oldpwd: oldpwd,
				password: newpwd
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#modal-msg-content').text("Your password has been successfully reset.");
			} else {
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	});

	$('#form-user-submit').click(function (e) {
		var username = $("#form-user-username").val();
		var email = $("#form-user-email").val();
		var password = $("#form-user-password").val();
		var role = $("#form-user-role").val();
		if (!validateEmail(email))
			return true;
		$('#modal-user').modal('hide');
		if (password.length !== 0)
			password = cryptPwd(password);
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=user_update",
			type: 'POST',
			data: {
				username: username,
				email: email,
				password: password,
				role: role
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-user').bootstrapTable("refresh");
			} else {
				$('#modal-msg').modal("show");
				$('#modal-msg-content').text(res["msg"]);
			}
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal("show");
			$('#modal-msg-content').text("Request failed :" + textStatus);
		});
	});
}

function load_users() {
	$("#table-user").bootstrapTable({
		url: window.config.BASE_URL + '/service?action=users_get',
		responseHandler: userResponseHandler,
		sidePagination: 'server',
		cache: true,
		striped: true,
		pagination: true,
		pageSize: 10,
		pageList: [10, 25, 50, 100, 200],
		search: true,
		showColumns: true,
		showRefresh: true,
		showToggle: true,
		showPaginationSwitch: true,
		minimumCountColumns: 2,
		clickToSelect: false,
		sortName: 'username',
		sortOrder: 'desc',
		smartDisplay: true,
		mobileResponsive: true,
		showExport: false,
		columns: [{
			field: 'nobody',
			title: 'Select',
			checkbox: true
		}, {
			field: 'username',
			title: 'Username',
			align: 'center',
			valign: 'middle',
			escape: true
		}, {
			field: 'email',
			title: 'Email',
			align: 'center',
			valign: 'middle',
			escape: true
		}, {
			field: 'email_verified',
			title: 'Verified',
			align: 'center',
			valign: 'middle'
		}, {
			field: 'role',
			title: 'Role',
			align: 'center',
			valign: 'middle',
			formatter: roleFormatter
		}, {
			field: 'reg_time',
			title: 'create time',
			align: 'center',
			valign: 'middle',
			sortable: true,
			visible: false,
			formatter: timeFormatter
		}, {
			field: 'reg_ip',
			title: 'create IP',
			align: 'center',
			valign: 'middle',
			visible: false,
			formatter: long2ip
		}, {
			field: 'operate',
			title: 'Operation',
			align: 'center',
			events: userOperateEvents,
			formatter: userOperateFormatter
		}]
	});
}

function roleFormatter(role) {
	switch (role) {
		case "root":
			return "Root";
		case "admin":
			return "Admin";
		case "developer":
			return "Developer";
		case "normal":
			return "Normal";
		case "blocked":
			return "Blocked";
		case "removed":
			return "Removed";
	}
	return "Unknown";
}

function userResponseHandler(res) {
	if (res['errno'] === 0) {
		var tmp = {};
		tmp["total"] = res["count"];
		tmp["rows"] = res["users"];
		return tmp;
	}
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res['msg']);
	return [];
}

function userOperateFormatter(value, row, index) {
	return [
		'<button class="btn btn-default edit" href="javascript:void(0)">',
		'<i class="glyphicon glyphicon-edit"></i>&nbsp;Edit',
		'</button>'
	].join('');
}

window.userOperateEvents =
	{
		'click .edit': function (e, value, row, index) {
			show_modal_user(row);
		}
	};

function show_modal_user(user) {
	$('#modal-user').modal('show');
	$('#modal-user-title').html('Edit');
	$('#form-user-submit').html('Save');
	$('#form-user-submit-type').val('update');
	$('#form-user-username').val(user.username);
	$('#form-user-password').val("");
	$('#form-user-email').val(user.email);
	$('#form-user-role').val(user.role);
	$('#form-user-username').attr('readonly', 'readonly');
}

function load_profile() {
	var ajax = $.ajax({
		url: window.config.BASE_URL + "/service?action=user_get",
		type: 'GET',
		data: {}
	});
	ajax.done(function (res) {
		if (res["errno"] === 0) {
			var user = res["user"];
			$('#user-username').text(user.username);
			$('#user-email').text(user.email);
			if (user.email_verified === "1") {
				$('#btn-verify-email').text("Verified");
				$('#btn-verify-email').addClass("hidden");
			} else {
				$('#btn-verify-email').text("verify");
				$('#btn-verify-email').removeClass("hidden");
			}
			$('#user-role').text(user.role);
		} else {
			$('#modal-msg').modal("show");
			$('#modal-msg-content').text(res["msg"]);
		}
	});
}
