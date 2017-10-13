function register_events_user()
{
	$("#btn-verify-email").click(function(e){
		e.preventDefault();
		$('#modal-verify').modal('show');
	});


	$("#btn-verify-send").click(function(e){
		e.preventDefault();
		$("#btn-verify-send").attr("disabled","disabled");
		$('#btn-verify-send').text("Sent");
		var ajax = $.ajax({
			url: "/service?action=verify_email_send_code",
			type: 'POST',
			data: {}
		});
		ajax.done(function(res){
			if(res["errno"] != 0){
			}else{
			}
		});
	});


	$("#form-verify-submit").click(function(e){
		e.preventDefault();
		var code = $("#form-verify-code").val();
		if(code.length < 6){
			return false;
		}
		$('#modal-verify').modal("hide");
		var ajax = $.ajax({
			url: "service?action=verify_email",
			type: 'POST',	
			data: {
				code: code
			}
		});
		ajax.done(function(res){
			if(res["errno"] == 0){
				load_profile();
			}else{
				$('#modal-msg').modal("show");
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	});


	$("#btn-update-email").click(function(e){
		e.preventDefault();
		$('#modal-email').modal('show');
	});


	$("#form-email-submit").click(function(e){
		e.preventDefault();
		var email = $("#form-email-email").val();
		if(email.length < 6){
			return false;
		}
		$('#modal-email').modal("hide");
		var ajax = $.ajax({
			url: "service?action=user_update",
			type: 'POST',	
			data: {
				email: email
			}
		});
		ajax.done(function(res){
			if(res["errno"] == 0){
				load_profile();
			}else{
				$('#modal-msg').modal("show");
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	});


	$("#btn-updatepwd").click(function(e){
		e.preventDefault();
		$('#modal-msg').modal('show');
		$('#modal-msg-content').text("Processing...");
		var oldpwd = $("#form-updatepwd-oldpwd").val();
		var password = $("#form-updatepwd-password").val();
		if(oldpwd.length<6 || password.length<6){
		$("#modal-msg-content").text("Password length should >= 6");
			return false;
		}
		var oldpwd = cryptPwd(oldpwd);
		var newpwd = cryptPwd(password);
		var ajax = $.ajax({
			url: "ajax.php?action=update_pwd",
			type: 'POST',	
			data: {
				oldpwd: oldpwd,
				password: newpwd
			}
		});
		ajax.done(function(res){
			if(res["errno"] == 0){
				$('#modal-msg-content').text("Your password has been successfully reset.");
			}else{
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	});


	$('#form-user-submit').click(function(e){
		e.preventDefault();
		var username = $("#form-user-username").val();
		var email = $("#form-user-email").val();
		var password = $("#form-user-password").val();
		var role = $("#form-user-role").val();
		if(!validateEmail(email))
			return false;
		if(password.length!=0)
			password=cryptPwd(password);
		$("#form-user-submit").attr("disabled","disabled");
		var ajax = $.ajax({
			url: "ajax.php?action=user_update",
			type: 'POST', 
			data: {
				username: username,
				email: email,
				password: password,
				role: role
			}
		});
		ajax.done(function(res){
			if(res["errno"]==0){
				$('#modal-user').modal('hide');
				$('#table-user').bootstrapTable("refresh");
			}else{
				$("#form-user-msg").html(res["msg"]);
				$("#modal-user").effect("shake");
			}
			$("#form-user-submit").removeAttr("disabled");
		});
		ajax.fail(function(jqXHR,textStatus){
			alert("Request failed :" + textStatus);
			$("#form-user-submit").removeAttr("disabled");
		});
	});

}

function load_users()
{
	$table = $("#table-user");
	$table.bootstrapTable({
		url: 'ajax.php?action=users_get',
		responseHandler: userResponseHandler,
    sidePagination: 'server',
		cache: true,
		striped: true,
		pagination: true,
		pageSize: 25,
		pageList: [25, 50, 100, 200],
		search: true,
		showColumns: true,
		showRefresh: true,
		showToggle: true,
		showPaginationSwitch: true,
		minimumCountColumns: 2,
		clickToSelect: false,
		sortName: 'nobody',
		sortOrder: 'desc',
		smartDisplay: true,
		mobileResponsive: true,
		showExport: false,
		columns: [{
			field: 'nobody',
			title: '选择',
			checkbox: true
		}, {
			field: 'username',
			title: '用户名',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: 'email',
			title: '邮箱',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: 'email_verified',
			title: '邮箱已验证',
			align: 'center',
			valign: 'middle',
			sortable: false
		}, {
			field: 'role',
			title: '角色',
			align: 'center',
			valign: 'middle',
			sortable: true,
			formatter: roleFormatter
		}, {
			field: 'reg_time',
			title: '注册时间',
			align: 'center',
			valign: 'middle',
			sortable: true,
			visible: false,
			formatter: timeFormatter
		}, {
			field: 'reg_ip',
			title: '注册IP',
			align: 'center',
			valign: 'middle',
			sortable: true,
			visible: false,
			formatter: long2ip
		}, {
			field: 'operate',
			title: '操作',
			align: 'center',
			events: userOperateEvents,
			formatter: userOperateFormatter
		}]
	});
}

function roleFormatter(role)
{
	switch(role){
		case "root":
			return "超级管理员";
		case "admin":
			return "管理员";
		case "developer":
			return "开发者";
		case "normal":
			return "普通用户";
		case "blocked":
			return "已封禁";
		case "removed":
			return "已注销";
	}
	return "未知角色";
}

function userResponseHandler(res)
{
	if(res['errno'] == 0){
		var tmp = new Object();
		tmp["total"] = res["count"];
		tmp["rows"] = res["users"];
		return tmp;
	}
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res['msg']);
	return [];
}

function userOperateFormatter(value, row, index)
{
	return [
		'<button class="btn btn-default edit" href="javascript:void(0)">',
		'<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
		'</button>'
	].join('');
}

window.userOperateEvents =
{
	'click .edit': function (e, value, row, index) {
		show_modal_user(row);
	}
};

function show_modal_user(user)
{
	$('#modal-user').modal('show');
	$('#modal-user-title').html('编辑用户信息');
	$('#form-user-submit').html('更新信息');
	$('#form-user-submit-type').val('update');
	$('#form-user-username').val(user.username);
	$('#form-user-password').val("");
	$('#form-user-email').val(user.email);
	$('#form-user-role').val(user.role);
	$('#form-user-username').attr('disabled', 'disabled');
}

function load_profile()
{
	var ajax = $.ajax({
		url: "ajax.php?action=user_get",
		type: 'GET',
		data: { }
	});
	ajax.done(function(res){
		if(res["errno"] == 0){
			var user = res["user"];
			$('#user-username').text(user.username);
			$('#user-email').text(user.email);
			if(user.email_verified==1){
				$('#btn-verify-email').text("Verified");
				$('#btn-verify-email').addClass("hidden");
			}else{
				$('#btn-verify-email').text("verify");
				$('#btn-verify-email').removeClass("hidden");
			}
			$('#user-role').text(user.role);
		}else{
			$('#modal-msg').modal("show");
			$('#modal-msg-content').text(res["msg"]);
		}
	});
}
