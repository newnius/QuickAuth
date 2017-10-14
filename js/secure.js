function register_events_blocked()
{
	$('#btn-block-add').click(function(e){
		e.preventDefault();
		$('#modal-block').modal('show');
		$("#form-block-ip").removeAttr("disabled");
	});

	$('#form-block-submit').click(function(e){
		e.preventDefault();
		$('#modal-block').modal('hide');
		var ip = $("#form-block-ip").val();
		var time = $("#form-block-time").val();
		var type = !time||time>0?"block":"unblock";
		var ajax = $.ajax({
			url: "ajax.php?action=" + type,
			type: 'POST', 
			data: {
				ip: ip,
				time: time
			}
		});
		ajax.done(function(msg){
			var res = JSON.parse(msg);
			if(res["errno"]==0){
				$('#table-block').bootstrapTable("refresh");
				$('#form-block-ip').val('');
				$('#form-block-time').val('');
			}else{
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text(res['msg']);
			}
		});
	});

}

function load_list_blocked()
{
	$table = $("#table-block");
	$table.bootstrapTable({
		url: 'ajax.php?action=list_blocked',
		responseHandler: blockedResponseHandler,
		cache: true,
		striped: true,
		pagination: true,
		pageSize: 25,
		pageList: [25, 50, 100, 200],
		search: true,
		showColumns: true,
		showRefresh: true,
		showToggle: true,
		showPaginationSwitch: true,
		minimumCountColumns: 2,
		clickToSelect: false,
		sortName: 'nobody',
		sortOrder: 'desc',
		smartDisplay: true,
		mobileResponsive: true,
		showExport: false,
		columns: [{
			field: 'nobody',
			title: 'Select',
			checkbox: true
		}, {
			field: 'id',
			title: 'IP',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: 'operate',
			title: 'Operations',
			align: 'center',
			events: blockedOperateEvents,
			formatter: blockedOperateFormatter
		}]
	});
}

function blockedResponseHandler(res)
{
	if(res['errno'] == 0){
		return res["list"];
	}
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res['msg']);
	return [];
}

function blockedOperateFormatter(value, row, index)
{
	return [
		'<button class="btn btn-default view" href="javascript:void(0)">',
		'<i class="glyphicon glyphicon-eye-open"></i>&nbsp;View',
		'</button>'
	].join('');
}

window.blockedOperateEvents =
{
	'click .view': function (e, value, row, index) {
		var ajax = $.ajax({
			url: "ajax.php?action=get_blocked_time",
			type: 'POST',
			data: {
				ip: row.id
			}
		});
		ajax.done(function(json){
			var res = JSON.parse(json);
			if(res["errno"] == 0){
				$('#modal-block').modal('show');
				$('#form-block-ip').val(row.id);
				$('#form-block-time').val(res['time']);
				$('#form-block-ip').attr('disabled', 'disabled');
			}else{
				$('#modal-msg').modal('show');
				$('#modal-msg-content').text(res['msg']);
				$('#table-block').bootstrapTable("refresh");
			}
		});
	}
};
