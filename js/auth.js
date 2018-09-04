function load_auth_list() {
	$table = $("#table-auth");
	$table.bootstrapTable({
		url: '/service?action=auth_list',
		responseHandler: authListResponseHandler,
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
			field: 'domain',
			title: 'Domain',
			align: 'center',
			valign: 'middle',
			sortable: false
		}, {
			field: 'expires',
			title: 'Valid',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: timeFormatter
		}, {
			field: 'scope',
			title: 'Scope',
			align: 'center',
			valign: 'middle',
			sortable: false
		}, {
			field: 'operate',
			title: 'Operations',
			align: 'center',
			events: authOperateEvents,
			formatter: authOperateFormatter
		}]
	});
}

function authListResponseHandler(res) {
	if (res['errno'] === 0) {
		return res['list'];
	}
	alert(res['msg']);
	return [];
}

function authOperateFormatter(value, row, index) {
	return [
		'<button class="btn btn-default revoke">',
		'<i class="glyphicon glyphicon-log-out"></i>&nbsp;Revoke',
		'</button>'
	].join('');
}

window.authOperateEvents =
	{
		'click .revoke': function (e, value, row, index) {
			var ajax = $.ajax({
				url: "/service?action=auth_revoke",
				type: 'POST',
				data: {
					client_id: row.client_id
				}
			});
			ajax.done(function (res) {
				if (res["errno"] === 0) {
					$('#table-auth').bootstrapTable("refresh");
				} else {
					$('#modal-msg').modal('show');
					$('#modal-msg-content').text(res["msg"]);
				}
			});
		}
	};