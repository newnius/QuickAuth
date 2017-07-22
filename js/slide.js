function register_events_slide(){
  $('#btn-slide-add').click(function(e){
    $("form#form-slide :input").each(function(){
      $(this).val("");
    });
    $('#form-slide-delete').addClass('hidden');
    $('#form-slide-order').val('1');
    $('#form-slide-lang').val('0');
    $('#modal-slide').modal('show');
    $('#form-slide-img').removeAttr("disabled");
  });

  $('#form-slide-delete').click(function(e){
    var id = $("#form-slide-id").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){ return; }
    var ajax = $.ajax({
      url: "ajax.php?action=slide_remove",
      type: 'POST',
      data: { id: id }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-slide').modal('hide');
        $('#table-slide').bootstrapTable("refresh");
      }else{
        $("#form-slide-msg").html(res["msg"]);
        $("#modal-slide").effect("shake");
      }
    });
  });
  
  $("#form-slide-submit").click(function(e){
    $("#form-slide-submit").attr("disabled", "disabled");
    var formData = new FormData($("#form-slide")[0]);
    var action = "slide_add";
    if($("#form-slide-submit-type").val()=="update"){
      action = "slide_update";
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
        $('#modal-slide').modal('hide');
        $('#table-slide').bootstrapTable("refresh");
      }else{
        $("#form-slide-msg").html(res["msg"]);
        $("#modal-slide").effect("shake");
      }
      $("#form-slide-submit").removeAttr("disabled");
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#form-slide-submit").removeAttr("disabled");
    });
  });

}

function load_slides(){
  $table = $("#table-slide");
  $table.bootstrapTable({
    url: 'ajax.php?action=slides_get',
    responseHandler: slideResponseHandler,
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
        formatter: slideLangFormatter
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
        events: slideOperateEvents,
        formatter: slideOperateFormatter
    }]
  });
}

function slideResponseHandler(res){
  if(res['errno'] == 0){
    return res['slides'];
  }
  alert(res['msg']);
  return [];
}

function slideOperateFormatter(value, row, index) {
  return [
    '<button class="btn btn-default edit" href="javascript:void(0)">',
    '<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
    '</button>'
  ].join('');
}

function slideLangFormatter(value, row, index) {
  if(value==0){
    return "简体中文";
  }
  return "English";
}

window.slideOperateEvents = {
  'click .edit': function (e, value, row, index) {
    show_modal_slide(row);
  }
};

function show_modal_slide(slide){
  $('#modal-slide').modal('show');
  $('#modal-slide-title').html('编辑轮播');
  $('#form-slide-submit').html('保存');
  $('#form-slide-submit-type').val('update');
  $('#form-slide-delete').removeClass('hidden');
  $('#form-slide-id').val(slide.id);
  $('#form-slide-text').val(slide.text);
  $('#form-slide-url').val(slide.url);
  $('#form-slide-lang').val(slide.lang);
  $('#form-slide-order').val(slide.order);
  $('#form-slide-img').attr("disabled", "disabled");
}
