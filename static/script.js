$(function () {
	$("#form-signup-submit").click(function (e) {
		var self = $(this);
		var username = $("#form-signup-username").val();
		var email = $("#form-signup-email").val();
		var password = $("#form-signup-password").val();
		if (!validateUsername(username) || !validateEmail(email) || !validatePwd(password)) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text('invalid username / email');
			return true;
		}
		self.html("submitting");
		self.attr("disabled", "disabled");
		var pass = cryptPwd(password);
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=user_register",
			type: 'POST',
			data: {
				username: username,
				email: email,
				password: pass
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text('Welcome ! Redirecting to login page ...');
				setTimeout(function () {
					window.location.pathname = "login";
				}, 1500);
			} else {
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text(res['msg']);
				self.html("Register");
				self.removeAttr("disabled");
			}
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
			self.html("Register");
			self.removeAttr("disabled");
		});
	});

	$("#form-login-submit").click(function (e) {
		var self = $(this);
		var account = $("#form-login-account").val();
		var password = $("#form-login-password").val();
		if (!validatePwd(password)) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text('Wrong password !');
			return true;
		}
		var pass = cryptPwd(password);
		var remember_me = $("#form-login-remember").prop("checked");
		self.html("submitting");
		self.attr("disabled", "disabled");
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=login",
			type: 'POST',
			data: {
				account: account,
				password: pass,
				remember_me: remember_me
			}
		});
		ajax.done(function (res) {
			var next = "ucenter";
			if (res["errno"] === 0) {
				if (window.location.href.indexOf("redirect_uri") !== -1) {
					var callback = getParameterByName('redirect_uri');
					if (isURL(callback))
						next = 'auth';
				}
				window.location.pathname = next;
			} else {
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text(res['msg']);
				$("#password").val("");
				self.html("Sign in");
				self.removeAttr("disabled");
			}
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
			self.html("Sign in");
			self.removeAttr("disabled");
		});
	});

	$("#btn-login-continue").click(function (e) {
		var next = "ucenter";
		if (window.location.href.indexOf("redirect_uri") !== -1) {
			var callback = getParameterByName('redirect_uri');
			if (isURL(callback))
				next = 'auth';
		}
		window.location.pathname = next;
	});

	$("#btn-signout, #btn-signout-header").click(function (e) {
		e.preventDefault();
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=signout",
			type: 'POST',
			data: {}
		});
		ajax.done(function (res) {
			window.location.pathname = "login";
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
		});
	});

	$("#form-lostpass-submit").click(function (e) {
		var username = $("#form-lostpass-username").val();
		var email = $("#form-lostpass-email").val();
		if (!validateUsername(username) || !validateEmail(email)) {
			return true;
		}
		var self = $(this);
		self.html("submitting");
		self.attr("disabled", "disabled");
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=reset_pwd_send_code",
			type: 'POST',
			data: {
				username: username,
				email: email
			}
		});
		ajax.done(function (res) {
			$('#modal-msg').modal('show');
			if (res["errno"] === 0) {
				$('#modal-msg-content').text("Email has been sent to your email box.");
			} else {
				$("#modal-msg-content").html(res["msg"]);
			}
			self.html("Send email");
			self.removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
			self.html("Send email");
			self.removeAttr("disabled");
		});
	});

	$("#form-resetpwd-submit").click(function (e) {
		e.preventDefault();
		var self = $(this);
		self.html("submitting");
		self.attr("disabled", "disabled");
		var username = $("#form-resetpwd-username").val();
		var password = $("#form-resetpwd-password").val();
		password = cryptPwd(password);
		var code = getParameterByName("code");
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=reset_pwd",
			type: 'POST',
			data: {
				username: username,
				password: password,
				code: code
			}
		});
		ajax.done(function (res) {
			$('#modal-msg').modal('show');
			if (res["errno"] === 0) {
				$('#modal-msg-content').text("Your password has been successfully reset.");
			} else {
				$('#modal-msg-content').text(res["msg"]);
			}
			self.html("Reset");
			self.removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
			self.html("Reset");
			self.removeAttr("disabled");
		});
	});

	$('#form-auth-accept').click(function (e) {
		e.preventDefault();
		var self = $(this);
		self.html("submitting");
		self.attr("disabled", "disabled");
		var response_type = getParameterByName('response_type');
		var client_id = getParameterByName('client_id');
		var redirect_uri = getParameterByName('redirect_uri');
		var state = getParameterByName('state');
		var scope = [];
		if ($("#form-auth-email").prop("checked"))
			scope.push('email');
		if ($("#form-auth-verified").prop("checked"))
			scope.push('email_verified');
		if ($("#form-auth-role").prop("checked"))
			scope.push('role');
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=auth_grant",
			type: 'POST',
			data: {
				response_type: response_type,
				client_id: client_id,
				redirect_uri: redirect_uri,
				state: state,
				scope: scope.join(',')
			}
		});
		ajax.done(function (res) {
			$('#modal-msg').modal('show');
			if (res["errno"] === 0) {
				$('#modal-msg-content').text("Redirecting, please wait ... ");
				var redirect = decodeURI(redirect_uri);
				if (redirect.indexOf("?") > -1)
					redirect = redirect + "&code=" + res['code'] + "&state=" + res["state"] + "&scope=" + scope;
				else
					redirect = redirect + "?code=" + res['code'] + "&state=" + res["state"] + "&scope=" + scope;
				if (isURL(redirect)) {
					window.location.href = redirect;
				}
			} else {
				$('#modal-msg-content').text(res["msg"]);
			}
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
			self.html("Accept");
			self.removeAttr("disabled");
		});
	});

	$('#form-auth-decline').click(function (e) {
		e.preventDefault();
		var redirect = decodeURI(redirect_uri);
		if (redirect.indexOf("?") > -1)
			redirect = redirect + "&cancel=1";
		else
			redirect = redirect + "?cancel=1";
		if (isURL(redirect))
			window.location.href = redirect;
		else
			window.location.pathname = 'ucenter';
	});
});

function validateUsername(username) {
	return username.length > 0 && username.length <= 12;
}

function validateEmail(email) {
	var emailRegex = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
	if (!$.trim(email)) {
		return false;
	} else if (!(emailRegex.test(email))) {
		return false;
	}
	return true;
}

function validatePwd(password) {
	return password.length >= 6;
}

function cryptPwd(password) {
	password = window.md5(password + "QuickAuth");
	password = window.md5(password + "newnius");
	password = window.md5(password + "com");
	return password;
}

if (window.location.pathname.indexOf("auth") !== -1) {
	var response_type = getParameterByName('response_type');
	var client_id = getParameterByName('client_id');
	var redirect_uri = getParameterByName('redirect_uri');
	if (redirect_uri == null) redirect_uri = 'javascript:void(0)';
	var state = getParameterByName('state');
	var scope = getParameterByName('scope');
	if (scope == null) scope = '';
	var array = scope.split(',');
	$.each(array, function (i) {
		if (array[i] === 'email')
			$("#form-auth-email").attr("checked", "checked");
		if (array[i] === 'email_verified')
			$("#form-auth-verified").attr("checked", "checked");
		if (array[i] === 'role')
			$("#form-auth-role").attr("checked", "checked");
	});
	/* check auto accept */
	var ajax = $.ajax({
		url: window.config.BASE_URL + "/service?action=auth_get_site",
		type: 'GET',
		data: {
			client_id: client_id
		}
	});
	ajax.done(function (res) {
		if (res["errno"] === 0) {
			$('#auth-grant-host').text(res['host']);
		} else {
			$('#modal-msg').modal('show');
			$('#modal-msg-content').text(res["msg"]);
		}
	});
}