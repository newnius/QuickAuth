$(function () {
	console.log(page_type);
	switch (page_type) {
		case "users":
			load_users();
			register_events_user();
			break;
		case "sites":
			load_sites('self');
			register_events_site();
			break;
		case "sites_all":
			load_sites('all');
			register_events_site();
			break;
		case "users_online":
			load_users_online();
			register_events_session();
			break;
		case "user_sessions":
			load_user_sessions();
			register_events_session();
			break;
		case "blocked_list":
			load_list_blocked();
			register_events_blocked();
			break;
		case "logs":
			load_logs('self');
			break;
		case "auth_list":
			load_auth_list();
			break;
		case "logs_all":
			load_logs('all');
			break;
		case "profile":
			load_profile();
			register_events_user();
			break;
		case "changepwd":
			register_events_user();
			break;
		default:
			break;
	}
});

function load_logs(scope) {
	$("#table-log").bootstrapTable({
		url: window.config.BASE_URL + '/service?action=get_logs&who=' + scope,
		responseHandler: logResponseHandler,
		sidePagination: 'server',
		cache: true,
		striped: true,
		pagination: true,
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
			field: 'scope',
			title: 'UID',
			align: 'center',
			valign: 'middle',
			sortable: false,
			visible: scope === 'all'
		}, {
			field: 'tag',
			title: 'Tag',
			align: 'center',
			valign: 'middle',
			sortable: false,
			visible: scope === 'all'
		}, {
			field: 'time',
			title: 'Time',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: timeFormatter
		}, {
			field: 'ip',
			title: 'IP',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: long2ip
		}, {
			field: 'content',
			title: 'Result',
			align: 'center',
			valign: 'middle',
			sortable: false,
			formatter: resultFormatter
		}, {
			field: 'content',
			title: 'Content',
			align: 'center',
			valign: 'middle',
			sortable: false,
			visible: scope === 'all',
			escape: true
		}]
	});
}

var logResponseHandler = function (res) {
	if (res['errno'] === 0) {
		var tmp = {};
		tmp["total"] = res["count"];
		tmp["rows"] = res["logs"];
		return tmp;
	}
	$("#modal-msg-content").html(res["msg"]);
	$("#modal-msg").modal('show');
	return [];
};

var resultFormatter = function (json) {
	var res = JSON.parse(json);
	if (res['response'] === 0) {
		return '<span class="text-success">成功</span>';
	}
	return '<span class="text-dander">失败</span>';
};
