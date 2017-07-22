function register_events_user()
{
  $("#username").blur(function(){
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

  $("#email").blur(function(){
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

  $("#password").blur(function(){
    chkPwd();
  });

  $("#iAgree").click(function(){
    if($(this).prop("checked")==true){
      $("#btn-register").removeAttr("disabled");
    }else{
      $("#btn-register").attr("disabled","disabled");
    }
  });

  $("#btn-register").click(function(e){
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
      url: "ajax-account.php?action=reg",
      type: 'POST',
      data: {
        username: username,
        email: email,
        password:pass
      }
    });
    ajax.done(function(msg){
      if(msg=="1"){
        alert('Welcome, my new friend!');
        window.location.href="login.php";
      }else{
        $("#btn-register").removeAttr("disabled");
        $("#btn-register").html("Register");
        $("#reg-error-msg").html(msg);
        $("#reg-error").css("display","block");
        $("#register").effect("shake");
      }
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#btn-register").html("Register");
      $("#btn-register").removeAttr("disabled");
    });
  });

  $("#btn-lostpass").click(function(e){
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
      url: "ajax-account.php?action=lostpass",
      type: 'POST', 
      data: {
        username: username,
        email: email
      }
    });
    ajax.done(function(msg){
      if(msg=="0"){
        alert("An email has been sent to your email box");
        window.location.href = "index.php";
      }else{
        $("#lostpass-error-msg").html("Unable to deliver("+ msg +"),<br/> <a href='help.php#qid-9'>see why</a>");
        $("#lostpass-error").css("display","block");
        $("#lostpass").effect("shake");
        $("#btn-lostpass").html("Send email");
        $("#btn-lostpass").removeAttr("disabled");
      }
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#btn-lostpass").html("Send email");
      $("#btn-lostpass").removeAttr("disabled");
    });
  });

  $("#btn-resetpwd").click(function(e){
    e.preventDefault();
    $("#btn-resetpwd").html("submiting");
    $("#btn-resetpwd").attr("disabled","disabled");
    if(!chkUsername() || !chkPwd()){
      $("#btn-resetpwd").html("Reset");
      $("#btn-resetpwd").removeAttr("disabled");
      $("#resetpwd-error-msg").html("<br/>* username can not be empty<br/>* password length should >= 6");
      $("#resetpwd-error").css("display","block");
      $("#resetpwd").effect("shake");
      return false;
    }
    var username = $("#username").val();
    var password = cryptPwd($("#password").val());
    var auth_key = $("#auth_key").val();
    var ajax = $.ajax({
      url: "ajax-account.php?action=resetpwd",
      type: 'POST', 
      data: {
        username: username,
        password: password,
        auth_key: auth_key
      }
    });
    ajax.done(function(msg){
      if(msg=="1"){
        alert("Your password has been successfully reset");
        window.location.href = "login.php";
      }else{
        $("#resetpwd-error-msg").html(msg);
        $("#resetpwd-error").css("display","block");
        $("#resetpwd").effect("shake");
        $("#btn-resetpwd").html("Reset");
        $("#btn-resetpwd").removeAttr("disabled");
      }
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#btn-resetpwd").html("Send email");
      $("#btn-resetpwd").removeAttr("disabled");
    });
  });

  $("#btn-verify-online").click(function(e){
    e.preventDefault();
    $("#btn-verify-online").html("submiting");
    $("#btn-verify-online").attr("disabled","disabled");
    var ajax = $.ajax({
      url: "ajax-account.php?action=verifyon",
      type: 'POST', 
      data: {   }
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
  });

  $("#btn-changepwd").click(function(e){
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
      url: "ajax.php?action=user_update",
      type: 'POST',
      data: {
        oldpwd: oldpwd,
        password: newpwd
      }
    });
    ajax.done(function(msg){
      var res = JSON.parse(msg);
      if(res["errno"] == 0){
        alert("修改密码成功，请重新登录。");
        window.location.href="?signout";
      }else{
        $("#changepwd-msg").html(res["msg"]);
        $("#btn-changepwd").html("Update");
        $("#btn-changepwd").removeAttr("disabled");
      }
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#btn-changepwd").html("Update");
      $("#btn-changepwd").removeAttr("disabled");
    });
  });

  $("#btn-verify").click(function(e){
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
  });


  $('#btn-user-add').click(function(e){
    $('#modal-user').modal('show');
    $('#modal-user-title').html('添加用户');
    $('#form-user-submit').html('添加用户');
    $('#form-user-submit-type').val('add');
    $('#form-user-delete').addClass('hidden');
    $("#form-user-username").removeAttr("disabled");
    $("#form-user-submit").removeAttr("disabled");
    $("#form-user-msg").html("");
    
    $('#form-user-username').val("");
    $('#form-user-password').val("");
    $('#form-user-email').val("");
    $('#form-user-role').val("teacher");
    $('#form-user-show-cv').val("1");
  });


  $('#form-user-submit').click(function(e){
    if($("#form-user-username").val()==""){
      return true;//change this to true to make required work
    }
    if($("#form-user-email").val()==""){
      return true;//change this to true to make required work
    }
    if($("#form-user-password").val()==""){
      return true;//change this to true to make required work
    }
    if($("#form-user-role").val()==""){
      return true;//change this to true to make required work
    }
    var username = $("#form-user-username").val();
    var email = $("#form-user-email").val();
    var password = $("#form-user-password").val();
    var role = $("#form-user-role").val();
    var crypted_pwd = cryptPwd(password);
    
    var group = $("#form-user-group").val();
    var show_cv = $("#form-user-show-cv").val();

    $("#form-user-submit").attr("disabled","disabled");

    var action = "user_add";
    if($("#form-user-submit-type").val()=="update"){
      action = "user_update";
    }

    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST', 
      data: {
        username: username,
        email: email,
        password: crypted_pwd,
        role: role,
        group: group,
        show_cv: show_cv
      }
    });

    ajax.done(function(msg){
      var res = JSON.parse(msg);
      console.log(res);
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

  $('#form-user-delete').click(function(e){
    var username = $("#form-user-username").val();
    if(!confirm('确认删除用户:'+username+' 吗（操作不可逆）')){
      return;
    }
    var ajax = $.ajax({
      url: "ajax.php?action=user_remove",
      type: 'POST',
      data: {
        username: username
      }
    });

    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-user').modal('hide');
        $('#table-user').bootstrapTable("refresh");
      }else{
        $("#form-user-msg").html(res["msg"]);
        $("#modal-user").effect("shake");
      }
    });
      
  });
 
}

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
  
function load_users(){
  $table = $("#table-user");
  $table.bootstrapTable({
    url: 'ajax.php?action=users_get',
    responseHandler: userResponseHandler,
    cache: true,
    striped: true,
    pagination: false,
    pageSize: 25,
    pageList: [10, 25, 50, 100, 200],
    search: false,
    showColumns: false,
    showRefresh: false,
    showToggle: false,
    showPaginationSwitch: false,
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
        field: 'role',
        title: '角色',
        align: 'center',
        valign: 'middle',
        sortable: true,
        formatter: roleFormatter
    }, {
        field: 'operate',
        title: '操作',
        align: 'center',
        events: userOperateEvents,
        formatter: userOperateFormatter
    }]
  });
}

function roleFormatter(role){
  switch(role){
    case "root":
      return "超级管理员";
    case "admin":
      return "管理员";
    case "reviewer":
      return "审稿人";
    case "teacher":
      return "普通教师";
  }
  return "未知角色";
}

function userResponseHandler(res){
  if(res['errno'] == 0){
    return res['users'];
  }
  alert(res["msg"]);
  return [];
}

function userOperateFormatter(value, row, index) {
  return [
    '<button class="btn btn-default edit" href="javascript:void(0)">',
    '<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
    '</button>'
  ].join('');
}

window.userOperateEvents = {
  'click .edit': function (e, value, row, index) {
    show_modal_user(row);
    load_userinfo_in_user_modal(row.username);
  }
};
  
  
function show_modal_user(user){
  $('#modal-user').modal('show');
  $('#modal-user-title').html('编辑用户信息');
  $('#form-user-submit').html('更新信息');
  $('#form-user-submit-type').val('update');
  $('#form-user-delete').removeClass('hidden');
  $('#form-user-username').val(user.username);
  $('#form-user-username').attr('disabled', 'disabled');
  $('#form-user-password').val("");
  $('#form-user-email').val(user.email);
  $('#form-user-role').val(user.role);
}

function load_userinfo_in_user_modal(username){
  var ajax = $.ajax({
    url: "ajax.php?action=userinfo_get",
    type: 'GET',
    data: { username:username }
  });
  ajax.done(function(json){
    var res = JSON.parse(json);
    if(res["errno"] == 0){
      var info = res["userinfo"];
      $('#form-user-group').val(info.group);
      $('#form-user-show-cv').val(info.show_cv);
    }else{
      alert(res['msg']);
    }
  });
}
