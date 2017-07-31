function register_events_user()
{
  $("#btn-verify-email").click(
    function(e){
      e.preventDefault();
    	$('#modal-msg').modal('show');
    	$('#modal-msg-content').text("Processing...");
      var ajax = $.ajax({
        url: "ajax.php?action=verify_email_send_code",
        type: 'POST',
        data: {}
      });

      ajax.done(function(json){
      	var res = JSON.parse(json);
      	if(res["errno"] == 0){
    			$('#modal-msg-content').text("Email has been sent to your email box.");
        }else{
    			$('#modal-msg-content').text(res["msg"]);
        }
      });
    }
  );


	$("#btn-updatepwd").click(
		function(e){
			e.preventDefault();
			$('#modal-msg').modal('show');
			$('#modal-msg-content').text("Processing...");
			var oldpwd = $("#form-updatepwd-oldpwd").val();
			var password = $("#form-updatepwd-password").val();
			if(oldpwd.length<6 || password.length<6){
				$("#modal-msg-content").html("password length should >= 6");
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

			ajax.done(function(json){
				var res = JSON.parse(json);
				if(res["errno"] == 0){
					$('#modal-msg-content').text("Your password has been successfully reset.");
				}else{
					$('#modal-msg-content').text(res["msg"]);
				}
			});
		}
	);


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
    pagination: true,
    pageSize: 25,
    pageList: [1, 25, 50, 100, 200],
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

function load_userinfo_in_user_modal(username=''){
  var ajax = $.ajax({
    url: "ajax.php?action=user_get",
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

function load_profile(){
  var ajax = $.ajax({
    url: "ajax.php?action=user_get",
    type: 'GET',
    data: { }
  });
  ajax.done(function(json){
    var res = JSON.parse(json);
    if(res["errno"] == 0){
      var user = res["user"];
      $('#user-username').text(user.username);
      $('#user-email').text(user.email);
			if(user.email_verified==1){
      	$('#btn-verify-email').text("Verified");
      	$('#btn-verify-email').addClass("disabled");
			}
      $('#user-role').text(user.role);
    }else{
      alert(res['msg']);
    }
  });
}
