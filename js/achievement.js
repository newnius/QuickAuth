function register_events_achievement(){
  $('#panel-achievement-paper .close').click(function(e){
    $('#ucenter-achievements').removeClass('hidden');
    $('#panel-achievement-paper').addClass('hidden');
  });
  $('#panel-achievement-workpaper .close').click(function(e){
    $('#ucenter-achievements').removeClass('hidden');
    $('#panel-achievement-workpaper').addClass('hidden');
  });
  $('#panel-achievement-study .close').click(function(e){
    $('#ucenter-achievements').removeClass('hidden');
    $('#panel-achievement-study').addClass('hidden');
  });
  $('#panel-achievement-project .close').click(function(e){
    $('#ucenter-achievements').removeClass('hidden');
    $('#panel-achievement-project').addClass('hidden');
  });
  $('#panel-achievement-monographs .close').click(function(e){
    $('#ucenter-achievements').removeClass('hidden');
    $('#panel-achievement-monographs').addClass('hidden');
  });
  $('#panel-achievement-report .close').click(function(e){
    $('#ucenter-achievements').removeClass('hidden');
    $('#panel-achievement-report').addClass('hidden');
  });


  $('#btn-achievement-add-paper').click(function(e){
    $("form#form-achievement-paper :input").each(function(){
      $(this).val("");
    });
    $('#form-achievement-paper-year-zh').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-paper-month-zh').val(formatDate(new Date(), '%n'));
    $('#form-achievement-paper-year-en').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-paper-month-en').val(formatDate(new Date(), '%n'));
    //$('#form-achievement-paper-attachment').removeClass('hidden');
    $('#form-achievement-paper-attachment-filename').addClass('hidden');
    $('#ucenter-achievements').addClass('hidden');
    $('#panel-achievement-paper').removeClass('hidden');
  });
  $('#btn-achievement-add-workpaper').click(function(e){
    $("form#form-achievement-workpaper :input").each(function(){
      $(this).val("");
    });
    $('#form-achievement-workpaper-year-zh').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-workpaper-month-zh').val(formatDate(new Date(), '%n'));
    $('#form-achievement-workpaper-year-en').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-workpaper-month-en').val(formatDate(new Date(), '%n'));
    //$('#form-achievement-workpaper-attachment').removeClass('hidden');
    $('#form-achievement-workpaper-attachment-filename').addClass('hidden');
    $('#ucenter-achievements').addClass('hidden');
    $('#panel-achievement-workpaper').removeClass('hidden');
  });
  $('#btn-achievement-add-study').click(function(e){
    $("form#form-achievement-study :input").each(function(){
      $(this).val("");
    });
    $('#form-achievement-study-year-zh').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-study-month-zh').val(formatDate(new Date(), '%n'));
    $('#form-achievement-study-year-en').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-study-month-en').val(formatDate(new Date(), '%n'));
    //$('#form-achievement-study-attachment').removeClass('hidden');
    $('#form-achievement-study-attachment-filename').addClass('hidden');
    $('#ucenter-achievements').addClass('hidden');
    $('#panel-achievement-study').removeClass('hidden');
  });
  $('#btn-achievement-add-project').click(function(e){
    $("form#form-achievement-project :input").each(function(){
      $(this).val("");
    });
    $('#form-achievement-project-year-zh').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-project-month-zh').val(formatDate(new Date(), '%n'));
    $('#form-achievement-project-year-en').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-project-month-en').val(formatDate(new Date(), '%n'));
    //$('#form-achievement-project-attachment').removeClass('hidden');
    $('#form-achievement-project-attachment-filename').addClass('hidden');
    $('#ucenter-achievements').addClass('hidden');
    $('#panel-achievement-project').removeClass('hidden');
  });
  $('#btn-achievement-add-monographs').click(function(e){
    $("form#form-achievement-monographs :input").each(function(){
      $(this).val("");
    });
    $('#form-achievement-monographs-year-zh').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-monographs-month-zh').val(formatDate(new Date(), '%n'));
    $('#form-achievement-monographs-year-en').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-monographs-month-en').val(formatDate(new Date(), '%n'));
    //$('#form-achievement-monographs-attachment').removeClass('hidden');
    $('#form-achievement-monographs-attachment-filename').addClass('hidden');
    $('#ucenter-achievements').addClass('hidden');
    $('#panel-achievement-monographs').removeClass('hidden');
  });
  $('#btn-achievement-add-report').click(function(e){
    $("form#form-achievement-report :input").each(function(){
      $(this).val("");
    });
    $('#form-achievement-report-year-zh').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-report-month-zh').val(formatDate(new Date(), '%n'));
    $('#form-achievement-report-year-en').val(formatDate(new Date(), '%Y'));
    $('#form-achievement-report-month-en').val(formatDate(new Date(), '%n'));
    //$('#form-achievement-report-attachment').removeClass('hidden');
    $('#form-achievement-report-attachment-filename').addClass('hidden');
    $('#ucenter-achievements').addClass('hidden');
    $('#panel-achievement-report').removeClass('hidden');
  });


  $('#form-achievement-paper-delete').click(function(e){
    var id = $("#form-achievement-paper-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){
      return;
    }
    var ajax = $.ajax({
      url: "ajax.php?action=achievement_delete",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-paper').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
    });
  });

  $('#form-achievement-workpaper-delete').click(function(e){
    var id = $("#form-achievement-workpaper-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){
      return;
    }
    var ajax = $.ajax({
      url: "ajax.php?action=achievement_delete",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-workpaper').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
    });
  });

  $('#form-achievement-study-delete').click(function(e){
    var id = $("#form-achievement-study-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){
      return;
    }
    var ajax = $.ajax({
      url: "ajax.php?action=achievement_delete",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-study').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
    });
  });

  $('#form-achievement-project-delete').click(function(e){
    var id = $("#form-achievement-project-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){
      return;
    }
    var ajax = $.ajax({
      url: "ajax.php?action=achievement_delete",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-project').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
    });
  });

  $('#form-achievement-monographs-delete').click(function(e){
    var id = $("#form-achievement-monographs-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){
      return;
    }
    var ajax = $.ajax({
      url: "ajax.php?action=achievement_delete",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-monographs').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
    });
  });

  $('#form-achievement-report-delete').click(function(e){
    var id = $("#form-achievement-report-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){
      return;
    }
    var ajax = $.ajax({
      url: "ajax.php?action=achievement_delete",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-report').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
    });
  });


  $("#form-achievement-paper-submit").click(function(e){
    $("#form-achievement-paper-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-achievement-paper")[0]);
    formData.append("type", 'paper');
    var action = "achievement_add";
    if($("#form-achievement-paper-submit-type").val()=="update"){
      action = "achievement_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-paper').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
      $("#form-achievement-paper-submit").removeAttr("disabled");
    });
  });

  $("#form-achievement-workpaper-submit").click(function(e){
    $("#form-achievement-workpaper-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-achievement-workpaper")[0]);
    formData.append("type", 'workpaper');
    var action = "achievement_add";
    if($("#form-achievement-workpaper-submit-type").val()=="update"){
      action = "achievement_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });

    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-workpaper').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
      $("#form-achievement-workpaper-submit").removeAttr("disabled");
    });
  });

  $("#form-achievement-study-submit").click(function(e){
    $("#form-achievement-study-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-achievement-study")[0]);
    formData.append("type", 'study');
    var action = "achievement_add";
    if($("#form-achievement-study-submit-type").val()=="update"){
      action = "achievement_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-study').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
      $("#form-achievement-study-submit").removeAttr("disabled");
    });
  });

  $("#form-achievement-project-submit").click(function(e){
    $("#form-achievement-project-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-achievement-project")[0]);
    formData.append("type", 'project');
    var action = "achievement_add";
    if($("#form-achievement-project-submit-type").val()=="update"){
      action = "achievement_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-project').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
      $("#form-achievement-project-submit").removeAttr("disabled");
    });
  });

  $("#form-achievement-monographs-submit").click(function(e){
    $("#form-achievement-monographs-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-achievement-monographs")[0]);
    formData.append("type", 'monographs');
    var action = "achievement_add";
    if($("#form-achievement-monographs-submit-type").val()=="update"){
      action = "achievement_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-monographs').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
      $("#form-achievement-monographs-submit").removeAttr("disabled");
    });
  });

  $("#form-achievement-report-submit").click(function(e){
    $("#form-achievement-report-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-achievement-report")[0]);
    formData.append("type", 'report');
    var action = "achievement_add";
    if($("#form-achievement-report-submit-type").val()=="update"){
      action = "achievement_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST',
      data: formData,
      processData:false,
      contentType: false
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#ucenter-achievements').removeClass('hidden');
        $('#panel-achievement-report').addClass('hidden');
        $('#table-achievement').bootstrapTable("refresh");
      }else{
        alert(res["msg"]);
      }
      $("#form-achievement-report-submit").removeAttr("disabled");
    });
  });


  $("#form-achievement-paper-preview").click(function(e){
    var detail = new Array();
    detail["authors"] = $("#form-achievement-paper-authors-zh").val();
    detail["title"] = $("#form-achievement-paper-title-zh").val();
    detail["journal"] = $("#form-achievement-paper-journal-zh").val();
    detail["pages"] = $("#form-achievement-paper-pages-zh").val();
    detail["year"] = $("#form-achievement-paper-year-zh").val();
    detail["month"] = $("#form-achievement-paper-month-zh").val();
    detail["keywords"] = $("#form-achievement-paper-keywords-zh").val();
    detail["abstract"] = $("#form-achievement-paper-abstract-zh").val();
    content = formatAchievement(detail);
    $("#form-achievement-paper-msg").html(content);
  });

  $("#form-achievement-workpaper-preview").click(function(e){
    var detail = new Array();
    detail["authors"] = $("#form-achievement-workpaper-authors-zh").val();
    detail["title"] = $("#form-achievement-workpaper-title-zh").val();
    detail["year"] = $("#form-achievement-workpaper-year-zh").val();
    detail["month"] = $("#form-achievement-workpaper-month-zh").val();
    detail["keywords"] = $("#form-achievement-workpaper-keywords-zh").val();
    detail["abstract"] = $("#form-achievement-workpaper-abstract-zh").val();
    content = formatAchievement(detail);
    $("#form-achievement-workpaper-msg").html(content);
  });

  $("#form-achievement-study-preview").click(function(e){
    var detail = new Array();
    detail["authors"] = $("#form-achievement-study-authors-zh").val();
    detail["title"] = $("#form-achievement-study-title-zh").val();
    detail["team"] = $("#form-achievement-study-team-zh").val();
    detail["year"] = $("#form-achievement-study-year-zh").val();
    detail["month"] = $("#form-achievement-study-month-zh").val();
    detail["keywords"] = $("#form-achievement-study-keywords-zh").val();
    detail["abstract"] = $("#form-achievement-study-abstract-zh").val();
    content = formatAchievement(detail);
    $("#form-achievement-study-msg").html(content);
  });

  $("#form-achievement-project-preview").click(function(e){
    var detail = new Array();
    detail["leader"] = $("#form-achievement-project-leader-zh").val();
    detail["project_info"] = $("#form-achievement-project-info-zh").val();
    detail["money_origin"] = $("#form-achievement-project-origin-zh").val();
    detail["money_sum"] = $("#form-achievement-project-money-zh").val();
    detail["duration"] = $("#form-achievement-project-duration-zh").val();
    detail["year"] = $("#form-achievement-project-year-zh").val();
    detail["month"] = $("#form-achievement-project-month-zh").val();
    detail["members"] = $("#form-achievement-project-members-zh").val();
    detail["introduction"] = $("#form-achievement-project-introduction-zh").val();
    content = formatAchievement(detail);
    $("#form-achievement-project-msg").html(content);
  });

  $("#form-achievement-monographs-preview").click(function(e){
    var detail = new Array();
    detail["name"] = $("#form-achievement-monographs-name-zh").val();
    detail["editor"] = $("#form-achievement-monographs-editor-zh").val();
    detail["authors"] = $("#form-achievement-monographs-authors-zh").val();
    detail["press"] = $("#form-achievement-monographs-press-zh").val();
    detail["year"] = $("#form-achievement-monographs-year-zh").val();
    detail["month"] = $("#form-achievement-monographs-month-zh").val();
    detail["introduction"] = $("#form-achievement-monographs-introduction-zh").val();
    content = formatAchievement(detail);
    $("#form-achievement-monographs-msg").html(content);
  });

  $("#form-achievement-report-preview").click(function(e){
    var detail = new Array();
    detail["authors"] = $("#form-achievement-report-authors-zh").val();
    detail["title"] = $("#form-achievement-report-title-zh").val();
    detail["department"] = $("#form-achievement-report-department-zh").val();
    detail["year"] = $("#form-achievement-report-year-zh").val();
    detail["month"] = $("#form-achievement-report-month-zh").val();
    detail["abstract"] = $("#form-achievement-report-abstract-zh").val();
    content = formatAchievement(detail);
    $("#form-achievement-report-msg").html(content);
  });

}

function load_achievements(scope){
  $table = $("#table-achievement");
  $table.bootstrapTable({
    url: 'ajax.php?action=achievements_get&scope='+scope,
    responseHandler: achievementResponseHandler,
    cache: true,
    striped: true,
    pagination: true,
    pageSize: 25,
    pageList: [10, 25, 50, 100, 200],
    search: true,
    showColumns: false,
    showRefresh: false,
    showToggle: false,
    showPaginationSwitch: false,
    minimumCountColumns: 2,
    clickToSelect: false,
    sortName: 'id',
    sortOrder: 'desc',
    smartDisplay: true,
    mobileResponsive: true,
    showExport: false,
    columns: [{
        field: 'state',
        title: '选择',
        checkbox: true
    }, {
        field: 'username',
        title: '录入者',
        align: 'right',
        valign: 'middle',
        width: '10%',
        sortable: true
    }, {
        field: 'type',
        title: '类型',
        align: 'center',
        valign: 'middle',
        width: '10%',
        sortable: true,
        formatter: achievementTypeFormatter
    }, {
        field: 'zh',
        title: '内容',
        align: 'center',
        valign: 'middle',
        sortable: false,
        formatter: formatAchievement
    }, {
        field: 'is_show',
        title: '首页显示',
        align: 'left',
        valign: 'middle',
        width: '10%',
        sortable: true,
        formatter: booleanFormatter
    }, {
        field: 'operate',
        title: '操作',
        align: 'center',
        width: '10%',
        events: achievementOperateEvents,
        formatter: achievementOperateFormatter
    }]
  });
}

var booleanFormatter = function(value){
  if(value=="1"){
    return "是";
  }
  return "否";
}

var achievementTypeFormatter = function(type){
  switch(type){
    case "paper":
      return "论文";
    case "workpaper":
      return "工作论文";
    case "study":
      return "研究进展";
    case "project":
      return "基金项目";
    case "monographs":
      return "专著";
    case "report":
      return "研究报告";
  }
  return "未知类型";
}

var achievementResponseHandler = function(res){
  if(res['errno'] == 0){
    return res['achievements'];
  }
  alert(res["msg"]);
  return [];
}

function achievementOperateFormatter(value, row, index) {
  return [
    '<button class="btn btn-default edit" href="javascript:void(0)">',
    '<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
    '</button>'
  ].join('');
}

window.achievementOperateEvents = {
  'click .edit': function (e, value, row, index) {
    switch(row.type){
      case "paper":
        $('#ucenter-achievements').addClass('hidden');
        $('#panel-achievement-paper').removeClass('hidden');
        $('#panel-achievement-paper-title').html('编辑学术成果---期刊论文');
        $('#form-achievement-paper-submit-type').val('update');
        $('#form-achievement-paper-delete').removeClass('hidden');
        $("form#form-achievement-paper :input").each(function(){
          $(this).val("");
        });

        if(row.zh){
          $('#form-achievement-paper-authors-zh').val(row.zh.authors);
          $('#form-achievement-paper-title-zh').val(row.zh.title);
          $('#form-achievement-paper-journal-zh').val(row.zh.journal);
          $('#form-achievement-paper-pages-zh').val(row.zh.pages);
          $('#form-achievement-paper-year-zh').val(row.zh.year);
          $('#form-achievement-paper-month-zh').val(row.zh.month);
          $('#form-achievement-paper-keywords-zh').val(row.zh.keywords);
          $('#form-achievement-paper-abstract-zh').val(row.zh.abstract);
        }
        if(row.en){
          $('#form-achievement-paper-authors-en').val(row.en.authors);
          $('#form-achievement-paper-title-en').val(row.en.title);
          $('#form-achievement-paper-journal-en').val(row.en.journal);
          $('#form-achievement-paper-pages-en').val(row.en.pages);
          $('#form-achievement-paper-year-en').val(row.en.year);
          $('#form-achievement-paper-month-en').val(row.en.month);
          $('#form-achievement-paper-keywords-en').val(row.en.keywords);
          $('#form-achievement-paper-abstract-en').val(row.en.abstract);
        }
        $('#form-achievement-paper-id').val(row.id);
        //$('#form-achievement-paper-attachment').addClass('hidden');
        if(row.zh.attachment){
          $('#form-achievement-paper-attachment-filename').attr("href", 'upload/file/'+row.zh.attachment);
          $('#form-achievement-paper-attachment-filename').removeClass('hidden');
        }
        $("#form-achievement-paper-isshow").prop("checked", row.is_show==1);
        break;

      case "workpaper":
        $('#ucenter-achievements').addClass('hidden');
        $('#panel-achievement-workpaper').removeClass('hidden');
        $('#panel-achievement-workpaper-title').html('编辑学术成果---工作论文');
        $('#form-achievement-workpaper-submit-type').val('update');
        $('#form-achievement-workpaper-delete').removeClass('hidden');
        $("form#form-achievement-workpaper :input").each(function(){
          $(this).val("");
        });

        if(row.zh){
          $('#form-achievement-workpaper-authors-zh').val(row.zh.authors);
          $('#form-achievement-workpaper-title-zh').val(row.zh.title);
          $('#form-achievement-workpaper-year-zh').val(row.zh.year);
          $('#form-achievement-workpaper-month-zh').val(row.zh.month);
          $('#form-achievement-workpaper-keywords-zh').val(row.zh.keywords);
          $('#form-achievement-workpaper-abstract-zh').val(row.zh.abstract);
        }
        if(row.en){
          $('#form-achievement-workpaper-authors-en').val(row.en.authors);
          $('#form-achievement-workpaper-title-en').val(row.en.title);
          $('#form-achievement-workpaper-year-en').val(row.en.year);
          $('#form-achievement-workpaper-month-en').val(row.en.month);
          $('#form-achievement-workpaper-keywords-en').val(row.en.keywords);
          $('#form-achievement-workpaper-abstract-en').val(row.en.abstract);
        }
        $('#form-achievement-workpaper-id').val(row.id);
        //$('#form-achievement-workpaper-attachment').addClass('hidden');
        if(row.zh.attachment){
          $('#form-achievement-workpaper-attachment-filename').removeClass('hidden');
          $('#form-achievement-workpaper-attachment-filename').attr("href", 'upload/file/'+row.zh.attachment);
        }
        $("#form-achievement-workpaper-isshow").prop("checked", row.is_show==1);
        break;

      case "study":
        $('#ucenter-achievements').addClass('hidden');
        $('#panel-achievement-study').removeClass('hidden');
        $('#panel-achievement-study-title').html('编辑学术成果---研究进展');
        $('#form-achievement-study-submit-type').val('update');
        $('#form-achievement-study-delete').removeClass('hidden');
        $("form#form-achievement-study :input").each(function(){
          $(this).val("");
        });

        if(row.zh){
          $('#form-achievement-study-authors-zh').val(row.zh.authors);
          $('#form-achievement-study-title-zh').val(row.zh.title);
          $('#form-achievement-study-team-zh').val(row.zh.team);
          $('#form-achievement-study-year-zh').val(row.zh.year);
          $('#form-achievement-study-month-zh').val(row.zh.month);
          $('#form-achievement-study-keywords-zh').val(row.zh.keywords);
          $('#form-achievement-study-abstract-zh').val(row.zh.abstract);
        }
        if(row.en){
          $('#form-achievement-study-authors-en').val(row.en.authors);
          $('#form-achievement-study-title-en').val(row.en.title);
          $('#form-achievement-study-team-en').val(row.en.team);
          $('#form-achievement-study-year-en').val(row.en.year);
          $('#form-achievement-study-month-en').val(row.en.month);
          $('#form-achievement-study-keywords-en').val(row.en.keywords);
          $('#form-achievement-study-abstract-en').val(row.en.abstract);
        }
        $('#form-achievement-study-id').val(row.id);
        //$('#form-achievement-study-attachment').addClass('hidden');
        if(row.zh.attachment){
          $('#form-achievement-study-attachment-filename').removeClass('hidden');
          $('#form-achievement-study-attachment-filename').attr("href", 'upload/file/'+row.zh.attachment);
        }
        $("#form-achievement-study-isshow").prop("checked", row.is_show==1);
        break;

      case "project":
        $('#ucenter-achievements').addClass('hidden');
        $('#panel-achievement-project').removeClass('hidden');
        $('#panel-achievement-project-title').html('编辑学术成果---基金项目');
        $('#form-achievement-project-submit-type').val('update');
        $('#form-achievement-project-delete').removeClass('hidden');
        $("form#form-achievement-project :input").each(function(){
          $(this).val("");
        });

        if(row.zh){
          $('#form-achievement-project-leader-zh').val(row.zh.leader);
          $('#form-achievement-project-info-zh').val(row.zh.project_info);
          $('#form-achievement-project-origin-zh').val(row.zh.money_origin);
          $('#form-achievement-project-money-zh').val(row.zh.money_sum);
          $('#form-achievement-project-duration-zh').val(row.zh.duration);
          $('#form-achievement-project-year-zh').val(row.zh.year);
          $('#form-achievement-project-month-zh').val(row.zh.month);
          $('#form-achievement-project-members-zh').val(row.zh.members);
          $('#form-achievement-project-introduction-zh').val(row.zh.introduction);
        }
        if(row.en){
          $('#form-achievement-project-leader-en').val(row.en.leader);
          $('#form-achievement-project-info-en').val(row.en.project_info);
          $('#form-achievement-project-origin-en').val(row.en.money_origin);
          $('#form-achievement-project-money-en').val(row.en.money_sum);
          $('#form-achievement-project-duration-en').val(row.en.duration);
          $('#form-achievement-project-year-en').val(row.en.year);
          $('#form-achievement-project-month-en').val(row.en.month);
          $('#form-achievement-project-members-en').val(row.en.members);
          $('#form-achievement-project-introduction-en').val(row.en.introduction);
        }
        $('#form-achievement-project-id').val(row.id);
        //$('#form-achievement-project-attachment').addClass('hidden');
        if(row.zh.attachment){
          $('#form-achievement-project-attachment-filename').removeClass('hidden');
          $('#form-achievement-project-attachment-filename').attr("href", 'upload/file/'+row.zh.attachment);
        }
        $("#form-achievement-project-isshow").prop("checked", row.is_show==1);
        break;

      case "monographs":
        $('#ucenter-achievements').addClass('hidden');
        $('#panel-achievement-monographs').removeClass('hidden');
        $('#panel-achievement-monographs-title').text('编辑学术成果---专著');
        $('#form-achievement-monographs-submit-type').val('update');
        $('#form-achievement-monographs-delete').removeClass('hidden');
        $("form#form-achievement-monographs :input").each(function(){
          $(this).val("");
        });
        if(row.zh){
          $('#form-achievement-monographs-name-zh').val(row.zh.name);
          $('#form-achievement-monographs-editor-zh').val(row.zh.editor);
          $('#form-achievement-monographs-authors-zh').val(row.zh.authors);
          $('#form-achievement-monographs-press-zh').val(row.zh.press);
          $('#form-achievement-monographs-year-zh').val(row.zh.year);
          $('#form-achievement-monographs-month-zh').val(row.zh.month);
          $('#form-achievement-monographs-introduction-zh').val(row.zh.introduction);
        }
        if(row.en){
          $('#form-achievement-monographs-name-en').val(row.en.name);
          $('#form-achievement-monographs-editor-en').val(row.en.editor);
          $('#form-achievement-monographs-authors-en').val(row.en.authors);
          $('#form-achievement-monographs-press-en').val(row.en.press);
          $('#form-achievement-monographs-year-en').val(row.en.year);
          $('#form-achievement-monographs-month-en').val(row.en.month);
          $('#form-achievement-monographs-introduction-en').val(row.en.introduction);
        }
        $('#form-achievement-monographs-id').val(row.id);
        //$('#form-achievement-monographs-attachment').addClass('hidden');
        if(row.zh.attachment){
          $('#form-achievement-monographs-attachment-filename').removeClass('hidden');
          $('#form-achievement-monographs-attachment-filename').attr("href", 'upload/file/'+row.zh.attachment);
        }
        $("#form-achievement-monographs-isshow").prop("checked", row.is_show==1);
        break;

      case "report":
        $('#ucenter-achievements').addClass('hidden');
        $('#panel-achievement-report').removeClass('hidden');
        $('#panel-achievement-report-title').text('编辑学术成果---研究报告');
        $('#form-achievement-report-submit-type').val('update');
        $('#form-achievement-report-delete').removeClass('hidden');
        $("form#form-achievement-report :input").each(function(){
          $(this).val("");
        });
        if(row.zh){
          $('#form-achievement-report-authors-zh').val(row.zh.authors);
          $('#form-achievement-report-title-zh').val(row.zh.title);
          $('#form-achievement-report-department-zh').val(row.zh.department);
          $('#form-achievement-report-year-zh').val(row.zh.year);
          $('#form-achievement-report-month-zh').val(row.zh.month);
          $('#form-achievement-report-abstract-zh').val(row.zh.abstract);
        }
        if(row.en){
          $('#form-achievement-report-authors-en').val(row.en.authors);
          $('#form-achievement-report-title-en').val(row.en.title);
          $('#form-achievement-report-department-en').val(row.en.department);
          $('#form-achievement-report-year-en').val(row.en.year);
          $('#form-achievement-report-month-en').val(row.en.month);
          $('#form-achievement-report-abstract-en').val(row.en.abstract);
        }
        $('#form-achievement-report-id').val(row.id);
        //$('#form-achievement-report-attachment').addClass('hidden');
        if(row.zh.attachment){
          $('#form-achievement-report-attachment-filename').removeClass('hidden');
          $('#form-achievement-report-attachment-filename').attr("href", 'upload/file/'+row.zh.attachment);
        }
        $("#form-achievement-report-isshow").prop("checked", row.is_show==1);
        break;
    }
  }
};


function formatAchievement(detail){
  var content = "";
  if(detail == null){
    alert("Achievement is null !");
    return "[ERROR]";
  }
  /* a tricky to decide type */
  if(detail.hasOwnProperty("journal")){// paper
    content += detail.authors;
    content += ".";
    content += detail.title;
    content += ",";
    content += detail.journal;
    content += ",";
    content += detail.year + "(" + detail.month + ")";
    if(detail.pages)
      content += ":"+detail.pages;
    content += ".";
  }else if(detail.hasOwnProperty("team")){// study
    content += detail.authors;
    content += ".";
    content += detail.title;
    content += ",";
    content += detail.team;
    content += ",";
    content += detail.year + "(" + detail.month + ")";
    content += ".";
  }else if(detail.hasOwnProperty("leader")){// project
    content += detail.leader;
    content += ".";
    content += detail.project_info;
    content += ",";
    content += detail.year + "(" + detail.month + ")";
    content += ".";
  }else if(detail.hasOwnProperty("press")){// monographs
    content += detail.name;
    content += ".";
    content += detail.editor;
    content += ",";
    content += detail.press;
    content += ".";
  }else if(detail.hasOwnProperty("department")){// department
    content += detail.authors;
    content += ".";
    content += detail.title;
    content += ",";
    content += detail.year + "(" + detail.month + ")";
    content += ".";
  }else{// workpaper
    content += detail.authors;
    content += ".";
    content += detail.title;
    content += ",";
    content += detail.year + "(" + detail.month + ")";
    content += ".";
  }
  return content;
}
