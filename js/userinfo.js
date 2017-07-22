function load_profile(){
  var ajax = $.ajax({
    url: "ajax.php?action=userinfo_get",
    type: 'GET',
    data: {}
  });
  ajax.done(function(json){
    var res = JSON.parse(json);
    if(res["errno"] == 0){
      var info = res["userinfo"];
      if(info.picture != null)
        $("#profile-picture").attr("src", "upload/image/" + info.picture);
      $("#profile-name-zh").html(info.name_zh);
      $("#profile-name-en").html(info.name_en);
      $("#profile-group").html(info.group);
      $("#profile-title").html(info.title);
      $("#form-profile-name-zh").val(info.name_zh);
      $("#form-profile-name-en").val(info.name_en);
      $("#form-profile-group").val(info.group);
      $("#form-profile-title").val(info.title);
    }else{
      alert(res['msg']);
    }
  });
  return true;
}

function register_events_profile(){
  $("#form-profile-upload").click(function(){
    $("#form-profile-picture").click();
  });

  $("#form-profile-picture").change(function(){
    show_image(this.files[0], $("#profile-picture"));
  });

  $("#form-profile-edit").click(function(){
    $("#profile .view").addClass("hidden");
    $("#profile .edit").removeClass("hidden");
  });

  $("#form-profile-save").click(function(e){
    e.preventDefault();
    var formData = new FormData($("#form-profile")[0]);
    formData.append("name_zh", $("#form-profile-name-zh").val());
    formData.append("name_en", $("#form-profile-name-en").val());
    formData.append("group", $("#form-profile-group").val());
    formData.append("title", $("#form-profile-title").val());
    var ajax = $.ajax({
      url: "ajax.php?action=userinfo_update",
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $("#profile .edit").addClass("hidden");
        $("#profile .view").removeClass("hidden");
        load_profile();
      }else{
        alert(res['msg']);
      }
    });
  });

  
  $("#btn-update-cv").click(function(){
    var content = UE.getEditor('editor').getContent();
    var lang = $("#form-cv-lang").val();
    var ajax = $.ajax({
      url: "ajax.php?action=cv_update",
      type: 'POST',
      data: {
        content: content,
        lang:lang
      }
    });
    ajax.done(function(msg){
      var res = JSON.parse(msg);
      alert(res['msg']);
    });
  });

  $("#btn-generate-cv").click(function(){
    if(!confirm('此操作会覆盖现有简历，是否继续？')){
      return;
    }
    UE.getEditor('editor').setContent("Loading");
    var lang = $("#form-cv-lang").val();
    var ajax = $.ajax({
      url: "ajax.php?action=achievements_get&scope=self",
      type: 'GET',
      data: {
        language: lang
      }
    });
    ajax.done(function(msg){
      var res = JSON.parse(msg);
      if(res['errno'] != 0){
        alert(res['msg']);
        return false;
      }
      var achievements = res['achievements'];
      var i;
      var content = "<h4>学术成果</h4>";
      content += "<ol>";
      for(i=0; i<achievements.length; i++){
        var detail = achievements[i][lang];
        content += "<li>";
        content += formatAchievement(detail);
        if(detail.attachment){
          var file_url = "upload/file/"+detail.attachment;
          var file_name = "下载";
          if(lang == 'en') file_url = "../" + file_url;
          if(lang == 'en') file_name = "Download";
          content += '<a target="_blank" href="' + file_url + '">(' + file_name + ')</a>';
        }
        content += "</li>";
      }
      UE.getEditor('editor').setContent(content);
    });
  });

}

function show_image(file, input){
  var reader = new FileReader();
  reader.onloadend = function(){
    input.attr("src", reader.result);
  }
  if(file){
    if(!/image\/\w+/.test(file.type)){
      alert("文件必须为图片！");
      return false;
    }
    reader.readAsDataURL(file);
  }
}

function load_cv(lang){
  $("#form-cv-lang").val(lang);
  var ue = UE.getEditor('editor');
  ue.ready(function(){
    var ue = UE.getEditor('editor');
    var ajax = $.ajax({
      url: "ajax.php?action=userinfo_get",
      type: 'get',
      data: { }
    });
    ajax.done(function(msg){
      var res = JSON.parse(msg);
      if(res["errno"] ==0 ){
        var content;
        if(lang=="zh")
          content = res['userinfo']['cv_content_zh'];
        else
          content = res['userinfo']['cv_content_en'];
        if(content == null){
          content = "";
        }
        ue.setContent(content);
      }else{
        alert(res["msg"]);
      }
    });
  });
}
