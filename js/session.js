function register_events_session()
{

}

function load_users_online()
{
	$table = $("#table-user");
	$table.bootstrapTable({
		url: 'ajax.php?action=users_online',
		responseHandler: usersOnlineResponseHandler,
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
			title: '选择',
			checkbox: true
		}, {
			field: 'group',
			title: '用户名',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: 'operate',
			title: '操作',
			align: 'center',
			events: usersOnlineOperateEvents,
			formatter: usersOnlineOperateFormatter
		}]
	});
}

function usersOnlineResponseHandler(res)
{
	if(res['errno'] == 0){
		return res["users"];
	}
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res['msg']);
	return [];
}

function usersOnlineOperateFormatter(value, row, index)
{
	return [
		'<button class="btn btn-default tickout" href="javascript:void(0)">',
		'<i class="glyphicon glyphicon-log-out"></i>&nbsp;登出',
		'</button>'
	].join('');
}

window.usersOnlineOperateEvents =
{
	'click .tickout': function (e, value, row, index) {
		var ajax = $.ajax({
			url: "ajax.php?action=tick_out",
			type: 'POST',
			data: {
				username: row.username
			}
		});
		ajax.done(function(json){
			var res = JSON.parse(json);
			if(res["errno"] == 0){
				$('#table-user').bootstrapTable("refresh");
			}else{
				$('#modal-msg').modal('show');
				$('#modal-msg-content').text(res["msg"]);
			}
		});
	}
};
