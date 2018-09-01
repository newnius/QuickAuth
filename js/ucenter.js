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
			;
	}
});

function load_logs(who) {
    var $table = $("#table-log");
	$table.bootstrapTable({
		url: '/service?action=get_logs&who=' + who,
		responseHandler: logResponseHandler,
        sidePagination: 'server',
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
		sortName: 'time',
		sortOrder: 'desc',
		smartDisplay: true,
		mobileResponsive: true,
		showExport: false,
		columns: [{
			field: 'scope',
			title: 'Operator',
			align: 'center',
			valign: 'middle',
			sortable: false
		}, {
			field: 'tag',
			title: 'Tag',
			align: 'center',
			valign: 'middle',
			sortable: false
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
			title: 'Content',
			align: 'center',
			valign: 'middle',
			sortable: false
		}]
	});
}

function logResponseHandler(res) {
    if (res['errno'] === 0) {
        var tmp = {};
        tmp["total"] = res["count"];
        tmp["rows"] = res["logs"];
        return tmp;
    }
	alert(res['msg']);
	return [];
}

function timeFormatter(unixTimestamp) {
	var d = new Date(unixTimestamp * 1000);
	d.setTime(d.getTime() - d.getTimezoneOffset() * 60 * 1000);
	return formatDate(d, '%Y-%M-%d %H:%m');
}


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
			field: 'app_id',
			title: 'APP_ID',
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
		'<button class="btn btn-default revoke" href="javascript:void(0)">',
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
					app_id: row.app_id
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

