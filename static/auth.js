function load_auth_list() {
	$("#table-auth").bootstrapTable({
		url: window.config.BASE_URL + '/service?action=auth_list',
		responseHandler: authListResponseHandler,
		cache: true,
		striped: true,
		pagination: false,
		pageSize: 10,
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
			escape: true
		}, {
			field: 'expires',
			title: 'Valid',
			align: 'center',
			valign: 'middle',
			formatter: timeFormatter
		}, {
			field: 'scope',
			title: 'Scope',
			align: 'center',
			valign: 'middle',
			escape: true
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
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res['msg']);
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
				url: window.config.BASE_URL + "/service?action=auth_revoke",
				type: 'POST',
				data: {
					client_id: row.client_id
				}
			});
			ajax.done(function (res) {
				if (res["errno"] !== 0) {
					$('#modal-msg').modal('show');
					$('#modal-msg-content').text(res["msg"]);
				}
				$('#table-auth').bootstrapTable("refresh");
			});
			ajax.fail(function (jqXHR, textStatus) {
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text("Request failed :" + textStatus);
			});
		}
	};