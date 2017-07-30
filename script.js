  $(function(){
    $("#tabs").tabs();
  });


  $("#forget-pwd").click(
    function(){
      window.location.href = "lostpass.php";
    }
  );


  $("#username").blur(
    function(){
      var username = $("#username").val();
        if(chkUsername()){
          isUsernameReged(username,function(msg){
            if(msg=='true'){
              $("#username-msg-icon").addClass("glyphicon glyphicon-remove");
              $("#username-msg").html("occupied");
            }else{
              $("#username-msg-icon").addClass("glyphicon glyphicon-ok");
              $("#username-msg").html("");
            }
          });			
        }
  });


  $("#email").blur(
    function(){
      var email = $(this).val();
      if(chkEmail()){
        isEmailReged(email,function(msg){
          if(msg=='true'){
            $("#email-msg-icon").addClass("glyphicon glyphicon-remove");
            $("#email-msg").html("occupied");
          }else{
            $("#email-msg-icon").addClass("glyphicon glyphicon-ok");
            $("#email-msg").html("");
          }
        });
      }
  });


  $("#password").blur(
    function(){
      chkPwd();
    }
  );


  $("#iAgree").click(
    function(){
      if($(this).prop("checked")==true){
        $("#btn-register").removeAttr("disabled");
      }else{
        $("#btn-register").attr("disabled","disabled");
      }
    }
  );


  $("#btn-register").click(
    function(e){
      e.preventDefault();
      $("#btn-register").attr("disabled","disabled");
      $("#btn-register").html("submiting");
      var username = $("#username").val();
      var email = $("#email").val();
      var password = $("#password").val();
      var pass = cryptPwd(password);
      if(!chkUsername() || !chkEmail() || !chkPwd() || $("#iAgree").prop("checked") == false){
        $("#btn-register").removeAttr("disabled");
        $("#btn-register").html("Register");
        return false;
      }
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
          alert('Welcome, my new friend!');
          window.location.href="login.php";
        }else{
          $("#btn-register").removeAttr("disabled");
          $("#btn-register").html("Register");
          $("#reg-error-msg").html(res["msg"]);
          $("#reg-error").css("display","block");
          $("#register").effect("shake");
        }
      });

      ajax.fail(function(jqXHR,textStatus){
        alert("Request failed :" + textStatus);
        $("#btn-register").html("Register");
        $("#btn-register").removeAttr("disabled");
      });
    }
  );


  $("#btn-login").click(
    function(e){
      e.preventDefault();
      if($("#account").val()==""||$("#password").val()==""){
        return false;
      }
      var account = $("#account").val();
      var password = $("#password").val();
      var pass = cryptPwd(password);
      var rememberme = false;
      if($("#rememberme").prop("checked")==true){
        rememberme = true;
      }
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
    }
  );


  $("#btn-lostpass").click(
    function(e){
      e.preventDefault();
      $("#btn-lostpass").html("submiting");
      $("#btn-lostpass").attr("disabled","disabled");
      if(!chkUsername() || !chkEmail()){
        $("#btn-lostpass").html("Send email");
        $("#btn-lostpass").removeAttr("disabled");
        return false;
      }
      var username = $("#username").val();
      var email = $("#email").val();
      var ajax = $.ajax({
        url: "ajax.php?action=reset_pwd_send_code",
        type: 'POST',	
        data: {
          username: username,
          email: email
        }
      });

      ajax.done(function(json){
        $("#btn-lostpass").html("Send email");
        $("#btn-lostpass").removeAttr("disabled");
      	var res = JSON.parse(json);
      	if(res["errno"] == 0){
    			$('#modal-resetpwd').modal('show');
    			$('#form-resetpwd-username').val(username);
    			$('#form-resetpwd-email').val(email);
    			$('#form-resetpwd-code').val(code);

          //alert("An email has been sent to your email box");
          //window.location.href = "index.php";
        }else{
          $("#lostpass-error-msg").html("Unable to deliver("+ res["msg"] +"),<br/> <a href='help.php#qid-9'>see why</a>");
          $("#lostpass-error").css("display","block");
          $("#lostpass").effect("shake");
        }
      });

      ajax.fail(function(jqXHR,textStatus){
        alert("Request failed :" + textStatus);
        $("#btn-lostpass").html("Send email");
        $("#btn-lostpass").removeAttr("disabled");
      });
    }
  );


  $("#form-resetpwd-submit").click(
    function(e){
      e.preventDefault();
      $("#btn-resetpwd").html("submiting");
      $("#btn-resetpwd").attr("disabled","disabled");
      var username = $("#form-resetpwd-username").val();
      var password = cryptPwd($("#form-resetpwd-password").val());
			var code = '123456';
      if(false){
        $("#btn-resetpwd").html("Reset");
        $("#btn-resetpwd").removeAttr("disabled");
        $("#resetpwd-error-msg").html("<br/>* username can not be empty<br/>* password length should >= 6");
        $("#resetpwd-error").css("display","block");
        $("#resetpwd").effect("shake");
        return false;
      }
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
          alert("Your password has been successfully reset");
          window.location.href = "login.php";
        }else{
          $("#form-resetpwd-msg").html(res["msg"]);
          $("#form-resetpwd-msg").effect("shake");
          $("#btn-resetpwd").html("Reset");
          $("#btn-resetpwd").removeAttr("disabled");
        }
      });

      ajax.fail(function(jqXHR,textStatus){
        alert("Request failed :" + textStatus);
          $("#btn-resetpwd").html("Send email");
          $("#btn-resetpwd").removeAttr("disabled");
      });
    }
  );


  $("#btn-verify-online").click(
    function(e){
      e.preventDefault();
      $("#btn-verify-online").html("submiting");
      $("#btn-verify-online").attr("disabled","disabled");
      var ajax = $.ajax({
        url: "ajax-account.php?action=verifyon",
        type: 'POST',	
        data: {  	}
      });

      ajax.done(function(msg){
        if(msg=="0"){
          $("#verify-online-msg").html("An email has been sent");;
          $("#btn-verify-online").html("Send me an email");
        }else{
          $("#verify-online-msg").html("Unable to deliver("+ msg +"), <a href='help.php#qid-9'>see why</a>");
          $("#btn-verify-online").html("Send me an email");
          //$("#btn-verify-online").removeAttr("disabled");
        }
      });

      ajax.fail(function(jqXHR,textStatus){
        alert("Request failed :" + textStatus);
        $("#btn-verify-online").html("Send me an email");
        $("#btn-verify-online").removeAttr("disabled");
      });
    }
  );


  $("#btn-changepwd").click(
    function(e){
      e.preventDefault();
      $("#btn-changepwd").html("submiting");
      $("#btn-changepwd").attr("disabled","disabled");
      if($("#oldpwd").val().length<6||$("#password").val().length<6){
        $("#changepwd-msg").html("password length should >= 6");
        $("#btn-changepwd").html("Update");
        $("#btn-changepwd").removeAttr("disabled");
        return false;
      }
      var oldpwd = cryptPwd($("#oldpwd").val());
      var newpwd = cryptPwd($("#password").val());
      var ajax = $.ajax({
        url: "ajax-account.php?action=changePwd",
        type: 'POST',	
        data: {
          oldpwd: oldpwd,
          newpwd: newpwd
        }
      });

      ajax.done(function(msg){
        if(msg=="1"){
          $("#changepwd-msg").html("Password updated. You are offline.");
        }else{
          $("#changepwd-msg").html(msg);
          $("#btn-changepwd").html("Update");
          $("#btn-changepwd").removeAttr("disabled");
        }
      });

      ajax.fail(function(jqXHR,textStatus){
        alert("Request failed :" + textStatus);
        $("#btn-changepwd").html("Update");
        $("#btn-changepwd").removeAttr("disabled");
      });
     }
  );


  $("#btn-verify").click(
    function(e){
      e.preventDefault();
      $("#btn-verify").html("submiting");
      $("#btn-verify").attr("disabled","disabled");
      if(!chkUsername() ){
        $("#btn-verify").html("Verify");
        $("#btn-verify").removeAttr("disabled");
        $("#verify-error-msg").html("User not exist");
        $("#verify-error").css("display","block");
        $("#verify").effect("shake");
        return false;
      }
      var username = $("#username").val();
      var auth_key = $("#auth_key").val();
      var ajax = $.ajax({
      url: "ajax-account.php?action=verify",
        type: 'POST',	
        data: {
          username: username,
          auth_key: auth_key
        }
      });

      ajax.done(function(msg){
        if(msg=="1"){
          alert("Congratulations, you have been verified");
          window.location.href = "login.php";
        }else{
          $("#verify-error-msg").html(msg);
          $("#verify-error").css("display","block");
          $("#verify").effect("shake");
          $("#btn-verify").html("Reset");
          $("#btn-verify").removeAttr("disabled");
        }
      });

      ajax.fail(function(jqXHR,textStatus){
        alert("Request failed :" + textStatus);
        $("#btn-verify").html("Verify");
        $("#btn-verify").removeAttr("disabled");
      });
    }
  );


  function chkUsername(){
      var username = $("#username").val();
      $("#username-msg-icon").removeClass();
      if(username.length==0||username.length>12){
        $("#username-msg-icon").addClass("glyphicon glyphicon-remove");
        $("#username-msg").html("length must between 1 and 12");
        return false;
      }else{
        $("#username-msg-icon").addClass("glyphicon glyphicon-ok");
        return true;
      }
  }


  function chkEmail(){
    var email = $("#email").val();
    var emailRegex = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/; 
    $("#email-msg-icon").removeClass();
      if(!$.trim(email)){
        $("#email-msg-icon").addClass("glyphicon glyphicon-remove");
        $("#email-msg").html("email can not be empty");
        return false;
      }else if(!(emailRegex.test(email))){
        $("#email-msg-icon").addClass("glyphicon glyphicon-remove");
        $("#email-msg").html("invalid email address");
        return false;
      }else {
        $("#email-msg-icon").addClass("glyphicon glyphicon-ok");
        return true;
      }
  }


  function chkPwd(){
    $("#password-msg-icon").removeClass();
    if($("#password").val().length<6){
      $("#password-msg-icon").addClass("glyphicon glyphicon-remove");
      $("#password-msg").html("password length can not be less than 6!");
      return false;
    }else{
      $("#password-msg-icon").addClass("glyphicon glyphicon-ok");
      $("#password-msg").html("");
      return true;
    }
  }


  function isUsernameReged(username,callback){
    var ajax = $.ajax({
      url: "ajax-account.php?action=isNameReged",
      type: 'GET',	
      data: {username: username}
    });

    ajax.done(function(msg){
      callback(msg);
    });

    ajax.fail(function(jqXHR,textStatus){
      alert(textStatus);
    });
  }


  function isEmailReged(email,callback){
    var ajax = $.ajax({
      url: "ajax-account.php?action=isEmailReged",
      type: 'GET',	
      data: {email: email}
    });

    ajax.done(function(msg){
      callback(msg);
    });

    ajax.fail(function(jqXHR,textStatus){
      alert(textStatus);
    });
  }


  function cryptPwd(password){
    password = window.md5(password + "account");
    password = window.md5(password + "newnius");
    password = window.md5(password + "com");
    return password;
  }
