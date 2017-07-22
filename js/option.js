function register_events_option(){
  $('#btn-option-add').click(function(e){
    $("form#form-option :input").each(function(){
      $(this).val("");
    });
    $('#form-option-delete').addClass('hidden');
    $('#form-option-key').removeAttr('disabled');
    $('#modal-option').modal('show');
  });

  $('#form-option-delete').click(function(e){
    var key = $("#form-option-key").val();
    if(!confirm('确认删除这条记录吗（操作不可逆）')){ return; }
    var ajax = $.ajax({
      url: "ajax.php?action=option_remove",
      type: 'POST',
      data: { key: key }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-option').modal('hide');
        $('#table-option').bootstrapTable("refresh");
      }else{
        $("#form-option-msg").html(res["msg"]);
        $("#modal-option").effect("shake");
      }
    });
  });

  $("#form-option-submit").click(function(e){
    $("#form-option-submit").attr("disabled", "disabled");
    var key = $("#form-option-key").val();
    var value = $("#form-option-value").val();
    var remark = $("#form-option-remark").val();
    var action = "option_add";
    if($("#form-option-submit-type").val()=="update"){
      action = "option_update";
    }
    var ajax = $.ajax({
      url: "ajax.php?action="+action,
      type: 'POST',
      data: {
        key: key,
        value: value,
        remark: remark
      }
    });
    ajax.done(function(json){
      var res = JSON.parse(json);
      if(res["errno"] == 0){
        $('#modal-option').modal('hide');
        $('#table-option').bootstrapTable("refresh");
      }else{
        $("#form-option-msg").html(res["msg"]);
        $("#modal-option").effect("shake");
      }
      $("#form-option-submit").removeAttr("disabled");
    });
    ajax.fail(function(jqXHR,textStatus){
      alert("Request failed :" + textStatus);
      $("#form-option-submit").removeAttr("disabled");
    });
  });
}

function load_options(){
  $table = $("#table-option");
  $table.bootstrapTable({
    url: 'ajax.php?action=options_get',
    responseHandler: optionResponseHandler,
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
        field: 'key',
        title: '键',
        align: 'center',
        valign: 'middle',
        sortable: true
    }, {
        field: 'value',
        title: '值',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'remark',
        title: '备注',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'operate',
        title: '操作',
        align: 'center',
        events: optionOperateEvents,
        formatter: optionOperateFormatter
    }]
  });
}

function optionResponseHandler(res){
  if(res['errno'] == 0){
    return res['options'];
  }
  alert(res['msg']);
  return [];
}

function optionOperateFormatter(value, row, index) {
  return [
    '<button class="btn btn-default edit" href="javascript:void(0)">',
    '<i class="glyphicon glyphicon-edit"></i>&nbsp;编辑',
    '</button>'
  ].join('');
}

window.optionOperateEvents = {
  'click .edit': function (e, value, row, index) {
    show_modal_option(row);
  }
};
  
function show_modal_option(option){
  $('#modal-option').modal('show');
  $('#modal-option-title').html('编辑网站配置');
  $('#form-option-submit').html('保存');
  $('#form-option-submit-type').val('update');
  //$('#form-option-delete').removeClass('hidden');
  $('#form-option-key').attr('disabled', 'disabled');    
  $('#form-option-key').val(option.key);
  $('#form-option-value').val(option.value);
  $('#form-option-remark').val(option.remark);
}
