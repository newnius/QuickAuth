function register_events_session() {

}

function load_users_online() {
	$("#table-user").bootstrapTable({
		url: window.config.BASE_URL + '/service?action=users_online',
		responseHandler: usersOnlineResponseHandler,
		cache: true,
		striped: true,
		pagination: true,
		pageSize: 10,
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
			field: 'group',
			title: 'Username',
			align: 'center',
			valign: 'middle',
			escape: true
		}, {
			field: 'operate',
			title: 'Operation',
			align: 'center',
			events: usersOnlineOperateEvents,
			formatter: usersOnlineOperateFormatter
		}]
	});
}

function usersOnlineResponseHandler(res) {
	if (res['errno'] === 0) {
		return res["users"];
	}
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res['msg']);
	return [];
}

function usersOnlineOperateFormatter(value, row, index) {
	return [
		'<button class="btn btn-default view" href="javascript:void(0)">',
		'<i class="glyphicon glyphicon-eye-open"></i>&nbsp;View',
		'</button>'
	].join('');
}

window.usersOnlineOperateEvents =
	{
		'click .view': function (e, value, row, index) {
			window.open("?user_sessions&username=" + row.group);
		}
	};


function load_user_sessions() {
	var username = getParameterByName('username');
	if (username === null)
		username = "";
	$("#table-session").bootstrapTable({
		url: window.config.BASE_URL + '/service?action=user_sessions&username=' + username,
		responseHandler: sessionsResponseHandler,
		cache: true,
		striped: true,
		pagination: true,
		pageSize: 10,
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
			field: '_index',
			title: 'NO',
			align: 'center',
			valign: 'middle',
			sortable: false
		}, {
			field: '_ip',
			title: 'IP',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: '_current',
			title: 'Current',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: 'operate',
			title: 'Operation',
			align: 'center',
			events: sessionsOperateEvents,
			formatter: sessionsOperateFormatter
		}]
	});
}

function sessionsResponseHandler(res) {
	if (res['errno'] === 0) {
		return res["sessions"];
	}
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res['msg']);
	return [];
}

function sessionsOperateFormatter(value, row, index) {
	return [
		'<button class="btn btn-default tickout">',
		'<i class="glyphicon glyphicon-log-out"></i>&nbsp;Log out',
		'</button>'
	].join('');
}

window.sessionsOperateEvents =
	{
		'click .tickout': function (e, value, row, index) {
			var ajax = $.ajax({
				url: window.config.BASE_URL + "/service?action=tick_out",
				type: 'POST',
				data: {
					username: row.username,
					_index: row._index
				}
			});
			ajax.done(function (res) {
				if (res["errno"] !== 0) {
					$('#modal-msg').modal('show');
					$('#modal-msg-content').text(res["msg"]);
				}
				$('#table-session').bootstrapTable("refresh");
			});
			ajax.fail(function (jqXHR, textStatus) {
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text("Request failed :" + textStatus);
			});
		}
	};
