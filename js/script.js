	$(function(){
		$("#tabs").tabs();
	});

	$("#btn-register").click(function(e){
		e.preventDefault();
		$('#modal-msg').modal('show');
		$('#modal-msg-content').text("Processing...");
		var username = $("#form-signup-username").val();
		var email = $("#form-signup-email").val();
		var password = $("#form-signup-password").val();
		var pass = cryptPwd(password);
		var ajax = $.ajax({
			url: "ajax.php?action=user_register",
			type: 'POST',
			data: {
				username: username,
				email: email,
				password:pass
			}
		});
		
		ajax.done(function(json){
			var res = JSON.parse(json);
			if(res["errno"] == 0){
				$('#modal-msg-content').text("Verify email");
			}else{
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	}
);

	$("#btn-login").click(function(e){
		e.preventDefault();
		var account = $("#account").val();
		var password = $("#password").val();
		var pass = cryptPwd(password);
		var rememberme = $("#rememberme").prop("checked");
		$("#btn-login").html("submiting");
		$("#btn-login").attr("disabled","disabled");
		var ajax = $.ajax({
			url: "ajax.php?action=login",
			type: 'POST',	
			data: {
				account: account,
				password: pass,
				rememberme: rememberme
			}
		});
		ajax.done(function(json){
			var res = JSON.parse(json);
			if(res["errno"] == 0){
				window.location.href = "ucenter.php";
			}else{
				$("#signin-error-msg").html(res["msg"]);
				$("#signin-error").css("display","block");
				$("#password").val("");
				$("#login").effect("shake");
				$("#btn-login").html("Signin");
				$("#btn-login").removeAttr("disabled");
			}
		});
		ajax.fail(function(jqXHR,textStatus){
			alert("Request failed :" + textStatus);
			$("#btn-login").html("Signin");
			$("#btn-login").removeAttr("disabled");
		});
	});

	$("#form-lostpass-submit").click(function(e){
		e.preventDefault();
		$('#modal-msg').modal('show');
		$('#modal-msg-content').text("Processing...");
		var username = $("#form-lostpass-username").val();
		var email = $("#form-lostpass-email").val();
		var ajax = $.ajax({
			url: "ajax.php?action=reset_pwd_send_code",
			type: 'POST',	
			data: {
				username: username,
				email: email
			}
		});
		ajax.done(function(json){
			var res = JSON.parse(json);
			if(res["errno"] == 0){
				$('#modal-msg-content').text("Email has been sent to your email box.");
			}else{
				$("#modal-msg-content").html("Unable to deliver("+ res["msg"] +"),<br/> <a href='help.php#qid-9'>see why</a>");
			}
		});
	});

	$("#form-resetpwd-submit").click(function(e){
		e.preventDefault();
		$('#modal-msg').modal('show');
		$('#modal-msg-content').text("Processing...");
		var username = $("#form-resetpwd-username").val();
		var password = $("#form-resetpwd-password").val();
		password = cryptPwd(password);
		var code = getParameterByName("code");
		var ajax = $.ajax({
			url: "ajax.php?action=reset_pwd",
			type: 'POST',	
			data: {
				username: username,
				password: password,
				code: code
			}
		});
		ajax.done(function(json){
			var res = JSON.parse(json);
			if(res["errno"] == 0){
				$('#modal-msg-content').text("Your password has been successfully reset.");
			}else{
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	});


	function validateUsername(username)
	{
		return username.length>0 && username.length<=12;
	}

	function validateEmail(email)
	{
		var emailRegex = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
		if(!$.trim(email)){
			return false;
		}else if(!(emailRegex.test(email))){
			return false;
		}
		return true;
	}

	function validatePwd(password)
	{
		return password.length>=6;
	}

	function cryptPwd(password)
	{
		password = window.md5(password + "account");
		password = window.md5(password + "newnius");
		password = window.md5(password + "com");
		return password;
	}
