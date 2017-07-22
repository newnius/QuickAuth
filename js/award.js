function register_events_award(){
  $('#btn-award-add').click(function(e){
    $("form#form-award :input").each(function(){
      $(this).val("");
    });
    $('#form-award-delete').addClass('hidden');
    $('#form-award-order').val('0');
    $('#form-award-lang').val('0');
    $('#modal-award').modal('show');
  });

  $('#form-award-delete').click(function(e){
    if(!confirm('确认删除这条记录吗（操作不可逆）')){ return; }
    var id = $("#form-award-id").val();
    var ajax = $.ajax({
      url: "ajax.php?action=award_remove",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-award').modal('hide');
        $('#table-award').bootstrapTable("refresh");
      }else{
        $("#form-award-msg").html(res["msg"]);
        $("#modal-award").effect("shake");
      }
    });
  });
  
  $("#form-award-submit").click(function(e){
    $("#form-award-submit").attr("disabled", "disabled");
    var id = $("#form-award-id").val();
    var url = $("#form-award-url").val();
    var text = $("#form-award-text").val();
    var order = $("#form-award-order").val();    
    var lang = $("#form-award-lang").val();    
    var action = "award_add";
    if($("#form-award-submit-type").val()=="update"){
      action = "award_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action=" + action,
      type: 'POST',
      data: {
        id: id,
        url: url,
        lang: lang,
        text: text,
        order: order
      }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-award').modal('hide');
        $('#table-award').bootstrapTable("refresh");
      }else{
        $("#form-award-msg").html(res["msg"]);
        $("#modal-award").effect("shake");
      }
      $("#form-award-submit").removeAttr("disabled");
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#form-award-submit").removeAttr("disabled");
    });
  });
}

function load_awards(){
  $table = $("#table-award");
  $table.bootstrapTable({
    url: 'ajax.php?action=awards_get',
    responseHandler: awardResponseHandler,
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
        field: 'state',
        title: '选择',
        checkbox: true
    }, {
        field: 'lang',
        title: '语言',
        align: 'center',
        valign: 'middle',
        sortable: true,
        formatter: awardLangFormatter
    }, {
        field: 'text',
        title: '文本',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'url',
        title: '链接地址',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'order',
        title: '次序',
        align: 'center',
        valign: 'middle',
        sortable: true
    }, {
        field: 'operate',
        title: '操作',
        align: 'center',
        events: awardOperateEvents,
        formatter: awardOperateFormatter
    }]
  });
}

function awardResponseHandler(res){
  if(res['errno'] == 0){
    return res['awards'];
  }
  alert(res['msg']);
  return [];
}

function awardOperateFormatter(value, row, index){
  return [
    '<button class="btn btn-default edit" href="javascript:void(0)">',
    '<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
    '</button>'
  ].join('');
}

function awardLangFormatter(value, row, index) {
  if(value==0){
    return "中文";
  }
  return "English";
}

window.awardOperateEvents = {
  'click .edit': function (e, value, row, index) {
    show_modal_award(row);
  }
};


function show_modal_award(award){
  $('#modal-award').modal('show');
  $('#modal-award-title').html('编辑获奖成果');
  $('#form-award-submit').html('保存');
  $('#form-award-submit-type').val('update');
  $('#form-award-delete').removeClass('hidden');
  $('#form-award-id').val(award.id);
  $('#form-award-lang').val(award.lang);
  $('#form-award-text').val(award.text);
  $('#form-award-url').val(award.url);
  $('#form-award-order').val(award.order);
}
