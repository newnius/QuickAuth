function register_events_page()
{
  $("#btn-page-add").click(function(){
    window.location.href = '?page_edit';
  });

  $("#form-page-submit").click(function(e){
    $("#form-page-submit").attr("disabled", "disabled");
    var key = $("#form-page-key").val();
    var title = $("#form-page-title").val();
    var content = UE.getEditor('editor').getContent();
    var action = "page_add";
    if($("#form-page-submit-type").val()=="update"){
      action = "page_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action=" + action,
      type: 'POST',
      data: {
        key: key,
        title: title,
        content: content
      }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      alert(res["msg"]);
      if(res["errno"] == 0){
        window.location.href = "?pages";
      }
      $("#form-page-submit").removeAttr("disabled");
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#form-page-submit").removeAttr("disabled");
    });
  });
}

function init_page(key){
  $("#form-page-submit-type").val('update');
  $("#form-page-key").val(key);
  $("#form-page-key").attr("disabled", "disabled");
  var ue = UE.getEditor('editor');
  var ajax = $.ajax({
    url: "ajax.php?action=page_get_by_key",
    type: 'get',
    data: { key: key }
  });
  ajax.done(function(msg){
    var res = JSON.parse(msg);
    if(res["errno"] == 0 ){
      $("#form-page-key").val(res["page"]["key"]);
      $("#form-page-title").val(res["page"]["title"]);
      ue.setContent(res["page"]['content']);
    }else{
      alert(res["msg"]);
    }
  });
}

function delete_page(key){
  var ajax = $.ajax({
    url: "ajax.php?action=page_remove",
    type: 'post',
    data: { key: key }
  });
  ajax.done(function(msg){
    var res = JSON.parse(msg);
    if(res["errno"] != 0){
      alert(res["msg"]);
    }
    $('#table-page').bootstrapTable("refresh");
  });
}

function load_pages(){
  $table = $("#table-page");
  $table.bootstrapTable({
    url: 'ajax.php?action=pages_get',
    responseHandler: pagesResponseHandler,
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
    sortName: 'default',
    sortOrder: 'desc',
    smartDisplay: true,
    mobileResponsive: true,
    showExport: false,
    columns: [{
        field: 'state',
        title: '选择',
        checkbox: true
    }, {
        field: 'key',
        title: 'KEY',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'title',
        title: '标题',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'operate',
        title: '操&nbsp;作',
        align: 'center',
        events: pagesOperateEvents,
        formatter: pagesOperateFormatter
    }]
  });
}

var pagesResponseHandler = function(res){
  if(res['errno'] == 0){
    return res['pages'];
  }
  alert(res['msg']);
  return [];
}

function pagesOperateFormatter(value, row, index) {
  return [
    '<button class="btn btn-default edit" title="编辑">',
    '<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
    '</button>  '
    /*,
    '<a class="del" href="javascript:void(0)" title="删除">',
    '<i class="glyphicon glyphicon-remove"></i>',
    '</a>'*/
  ].join('');
}

window.pagesOperateEvents = {
  'click .edit': function (e, value, row, index) {
    window.location.href = '?page_edit&key=' + row.key;
  },
  'click .del': function (e, value, row, index) {
    if(confirm('确认删除这条记录吗（操作不可逆）')){
      delete_page(row.key);
    }
  }
};
