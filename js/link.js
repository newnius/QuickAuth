function register_events_link(){
  $('#btn-link-add').click(function(e){
    $("form#form-link :input").each(function(){
      $(this).val("");
    });
    $('#form-link-lang').val('0');
    $('#form-link-order').val('1');
    $('#form-link-delete').addClass('hidden');
    $('#modal-link').modal('show');
    $('#form-link-img').removeAttr("disabled");
  });

  $('#form-link-delete').click(function(e){
    var id = $("#form-link-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){ return; }
    var ajax = $.ajax({
      url: "ajax.php?action=link_remove",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-link').modal('hide');
        $('#table-link').bootstrapTable("refresh");
      }else{
        $("#form-link-msg").html(res["msg"]);
        $("#modal-link").effect("shake");
      }
    });
  });

  $("#form-link-submit").click(function(e){
    $("#form-link-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-link")[0]);
    var action = "link_add";
    if($("#form-link-submit-type").val()=="update"){
      action = "link_update";
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
        $('#modal-link').modal('hide');
        $('#table-link').bootstrapTable("refresh");
      }else{
        $("#form-link-msg").html(res["msg"]);
        $("#modal-link").effect("shake");
      }
      $("#form-link-submit").removeAttr("disabled");
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#form-link-submit").removeAttr("disabled");
    });
  });
}


function load_links(){
  $table = $("#table-link");
  $table.bootstrapTable({
    url: 'ajax.php?action=links_get',
    responseHandler: linkResponseHandler,
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
    sortName: 'npbody',
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
        formatter: linkLangFormatter
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
        field: 'img',
        title: '图片',
        align: 'center',
        valign: 'middle',
        sortable: true
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
        events: linkOperateEvents,
        formatter: linkOperateFormatter
    }]
  });
}

function linkResponseHandler(res){
  if(res['errno'] == 0){
    return res['links'];
  }
  alert(res['msg']);
  return [];
}

function linkOperateFormatter(value, row, index) {
  return [
    '<button class="btn btn-default edit" href="javascript:void(0)">',
    '<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
    '</button>'
  ].join('');
}

function linkLangFormatter(value, row, index) {
  if(value==0){
    return "中文";
  }
  return "English";
}

window.linkOperateEvents = {
  'click .edit': function (e, value, row, index) {
    show_modal_link(row);
  }
};
  
  
function show_modal_link(link){
  $('#modal-link').modal('show');
  $('#modal-link-title').html('编辑链接');
  $('#form-link-submit').html('保存');
  $('#form-link-submit-type').val('update');
  $('#form-link-delete').removeClass('hidden');
  $('#form-link-id').val(link.id);
  $('#form-link-lang').val(link.lang);
  $('#form-link-text').val(link.text);
  $('#form-link-url').val(link.url);
  $('#form-link-order').val(link.order);
  $('#form-link-img').attr("disabled", "disabled");
}
