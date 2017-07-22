$(function(){
  $("#form-post-attachment").change(function(e){
    var filename = e.target.files[0].name;
    $("#form-post-title").val(filename);
  });

  $(".ie #btn-search").click(function(e){
    var search = $("#input-search").val();
    window.open( 'list.php?search=' + search, '_blank' );
  });

  $("#btn-search-news").click(function(e){
    var search = $("#input-search").val();
    window.open( 'list.php?search=' + search, '_blank' );
  });

  $("#btn-search-achievement").click(function(e){
    var search = $("#input-search").val();
    window.open( 'productionList.php?search=' + search, '_blank' );
  });

  $("#form-post-submit").click(function(e){
    if (!window.FormData){
      alert("您的浏览器版本太低，请换用其他浏览器。");
      return false;
    }
    $("#form-post-submit").attr("disabled", "disabled");
    var title = $("#form-post-title").val();
    var author = $("#form-post-author").val();
    var phone = $("#form-post-phone").val();
    var email = $("#form-post-email").val();
    var address = $("#form-post-address").val();
    var postcode = $("#form-post-postcode").val();
    var remark = $("#form-post-remark").val();
    var formData = new FormData($("#form-post")[0]);
    formData.append("title", title);
    formData.append("author", author);
    formData.append("phone", phone);
    formData.append("email", email);
    formData.append("address", address);
    formData.append("postcode", postcode);
    formData.append("remark", remark);
    var ajax = $.ajax({
      url: "ajax.php?action=post_add",
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });
    ajax.done(function(json){
      /*
       * parseJSON is not recommended since JQuery3.0 
       * ref: http://www.css88.com/jqapi-1.9/jQuery.parseJSON/
       * but to support fucking IE, use this
       */
      //var res = JSON.parse(json);
      var res = jQuery.parseJSON(json);
      alert(res["msg"]);
      if(res["errno"]==0){
        window.location.href = "index.php";
      }
      $("#form-post-submit").removeAttr("disabled");
    });
  });

  $("#btn-login").click(function(e){
    e.preventDefault();
    if (!window.FormData){
      alert("您的浏览器版本太低，请换用其他浏览器。");
      return false;
    }
    if($("#account").val()==""||$("#password").val()==""){
      return false;
    }
    var account = $("#account").val();
    var password = $("#password").val();
    var pass = cryptPwd(password);
    var rememberme = false;
    if($("#remember-me").prop("checked")==true){
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
      /*
       * parseJSON is not recommended since JQuery3.0 
       * ref: http://www.css88.com/jqapi-1.9/jQuery.parseJSON/
       * but to support fucking IE, use this
       */
      //var res = JSON.parse(msg);
      var res = jQuery.parseJSON(json);
      if(res["errno"]==0){
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

  $("#alert-forget-pwd").click(function(){
    alert("请联系管理员重置密码！");
  });


  $('.has-submenu').mouseleave(function(e){
    var menu = $(this);
    clearTimeout(t_delay);
    setTimeout(function(){
      menu.children('.submenu').fadeOut('slow');
    }, 333);
  });

  $('.has-submenu').mouseenter(function(e){
    $(".submenu").removeClass('hidden');//why IE doesn't care display:none
    var menu = $(this);
    t_delay = setTimeout(function(){
      menu.children('.submenu').fadeIn('slow');
    }, 20);
  });
});
