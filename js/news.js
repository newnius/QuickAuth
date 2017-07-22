function register_events_news()
{
  $("#btn-news-add").click(function(){
    window.location.href = '?news_edit';
  });

  $("#form-news-submit").click(function(e){
    $("#form-news-submit").attr("disabled", "disabled");    
    var id = $("#form-news-id").val();
    var type = $("#form-news-type").val();
    var lang = $("#form-news-lang").val();
    var title = $("#form-news-title").val();
    var order = $("#form-news-order").val();
    var content = UE.getEditor('editor').getContent();
    var action = "news_add";
    if($("#form-news-submit-type").val()=="update"){
      action = "news_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action=" + action,
      type: 'POST',
      data: {
        id: id,
        type: type,
        lang: lang,
        title: title,
        order: order,
        content: content
      }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      alert(res["msg"]);
      if(res["errno"] == 0){
        window.location.href = "?newss";
      }
      $("#form-news-submit").removeAttr("disabled");
    });
  });
}

function init_news(id){
  $("#form-news-submit-type").val('update');
  $("#form-news-id").val(id);
  var ue = UE.getEditor('editor');
  var ajax = $.ajax({
    url: "ajax.php?action=news_get_by_id",
    type: 'get',
    data: { id: id }
  });
  ajax.done(function(msg){
    var res = JSON.parse(msg);
    if(res["errno"] == 0 ){
      $("#form-news-lang").val(res["news"]["lang"]);
      $("#form-news-type").val(res["news"]["type"]);
      $("#form-news-title").val(res["news"]["title"]);
      $("#form-news-order").val(res["news"]["order"]);
      ue.setContent(res["news"]['content']);
    }else{
      alert(res["msg"]);
    }
  });
}

function delete_news(id){
  var ajax = $.ajax({
    url: "ajax.php?action=news_remove",
    type: 'post',
    data: { id: id }
  });

  ajax.done(function(msg){
    var res = JSON.parse(msg);
    if(res["errno"] != 0){
      alert(res["msg"]);
    }
    $('#table-news').bootstrapTable("refresh");
  });
}

function load_newss(){
  $table = $("#table-news");
  $table.bootstrapTable({
    url: 'ajax.php?action=news_get',
    responseHandler: newsResponseHandler,
    //sidePagination: 'server',
    cache: true,
    striped: true,
    pagination: true,
    pageSize: 25,
    pageList: [10, 25, 50, 100, 200],
    search: true,
    showColumns: true,
    showRefresh: true,
    showToggle: true,
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
        formatter: newsLangFormatter
    }, {
        field: 'type',
        title: '类别',
        align: 'center',
        valign: 'middle',
        width: '10%',
        sortable: true,
        formatter: newsTypeFormatter
    }, {
        field: 'title',
        title: '标题',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'author',
        title: '作者',
        align: 'center',
        valign: 'middle',
        sortable: true
    }, {
        field: 'time',
        title: '发表时间',
        align: 'center',
        valign: 'middle',
        sortable: true,
        formatter: newsTimeFormatter
    }, {
        field: 'order',
        title: '优先级',
        align: 'center',
        valign: 'middle',
        sortable: false,
        formatter: newsOrderFormatter
    }, {
        field: 'operate',
        title: '操&nbsp;作',
        align: 'center',
        events: newsOperateEvents,
        formatter: newsOperateFormatter
    }]
  });
}

function newsResponseHandler(res){
  if(res['errno'] == 0){
    return res['newss'];
  }
  alert(res['msg']);
  return [];
}

function newsOperateFormatter(value, row, index) {
  return [
    '<a class="edit" href="javascript:void(0)" title="编辑">',
    '<i class="glyphicon glyphicon-edit"></i>',
    '</a>  ',
    '<a class="del" href="javascript:void(0)" title="删除">',
    '<i class="glyphicon glyphicon-remove"></i>',
    '</a>'
  ].join('');
}

window.newsOperateEvents = {
  'click .edit': function (e, value, row, index) {
    window.location.href = '?news_edit&id=' + row.id; 
  },
  'click .del': function (e, value, row, index) {
    if(confirm('确认删除这条记录吗（操作不可逆）')){
      delete_news(row.id);
    }
  }
};

var newsTypeFormatter = function(type){
  switch(type){
    case "1":
      return "中心通知";
    case "5":
      return "中心动态";
    case "6":
      return "学术会议";
    case "7":
      return "学界动态";
    case "11":
      return "中心大事记";
    case "12":
      return "人才培养";
    case "14":
      return "获奖管理";
    case "15":
      return "科研管理";
    case "49":
      return "中改院动态";
  }
  return "未知类型";
}

function newsLangFormatter(value, row, index) {
  if(value==0){
    return "简体中文";
  }
  return "English";
}

function newsTimeFormatter(unixTimestamp){
  var d = new Date(unixTimestamp*1000);
  d.setTime( d.getTime() - d.getTimezoneOffset()*60*1000 );
  return formatDate(d, '%Y-%M-%d %H:%m');
}

function newsOrderFormatter(order){
  if(order == 0){
    return "普通";
  }
  if(order <= 50){
    return "置顶";
  }
  return "置顶,加粗";
}

