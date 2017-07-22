function register_events_post()
{
  $('#form-post-delete').click(function(e){
    var id = $("#form-post-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){ return; }
    var ajax = $.ajax({
      url: "ajax.php?action=post_remove",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-post').modal('hide');
        $('#table-post').bootstrapTable("refresh");
      }else{
        $("#form-post-msg").html(res["msg"]);
        $("#modal-post").effect("shake");
      }
    });
  });
}

function load_posts(){
  $table = $("#table-post");
  $table.bootstrapTable({
    url: 'ajax.php?action=posts_get',
    responseHandler: postResponseHandler,
    cache: true,
    striped: true,
    pagination: true,
    pageSize: 25,
    pageList: [10, 25, 50, 100, 200],
    search: false,
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
        field: 'author',
        title: '通讯作者',
        align: 'center',
        valign: 'middle',
        sortable: true
    }, {
        field: 'title',
        title: '论文标题',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'email',
        title: '邮箱',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'time',
        title: '投稿时间',
        align: 'center',
        valign: 'middle',
        sortable: false,
        formatter: postTimeFormatter
    }, {
        field: 'operate',
        title: '操作',
        align: 'center',
        events: postOperateEvents,
        formatter: postOperateFormatter
    }]
  });
}

function postResponseHandler(res){
  if(res['errno'] == 0){
    return res['posts'];
  }
  alert(res['msg']);
  return [];
}

function postOperateFormatter(value, row, index) {
  return [
    '<button class="btn btn-default edit" href="javascript:void(0)">',
    '查看',
    '</button>'
  ].join('');
}

window.postOperateEvents = {
  'click .edit': function (e, value, row, index) {
    show_modal_post(row);
  }
};

function show_modal_post(post){
  $('#modal-post').modal('show');
  $('#modal-post-title').text('查看用户投稿');
  $('#form-post-submit').text('保存');
  $('#form-post-submit-type').val('update');
  $('#form-post-delete').removeClass('hidden');
  $('#form-post-id').val(post.id);
  $('#form-post-title').val(post.title);
  $('#form-post-author').val(post.author);
  $('#form-post-phone').val(post.phone);
  $('#form-post-email').val(post.email);
  $('#form-post-address').val(post.address);
  $('#form-post-postcode').val(post.postcode);
  $('#form-post-remark').val(post.remark);

  if(post.attachment)
    $('#form-post-attachment-filename').text(post.title);
    $('#form-post-attachment-filename').attr("href", "upload/file/"+post.attachment);
}

function postTimeFormatter(unixTimestamp){
  var d = new Date(unixTimestamp*1000);
  d.setTime( d.getTime() - d.getTimezoneOffset()*60*1000 );
  return formatDate(d, '%Y-%M-%d %H:%m');
}
