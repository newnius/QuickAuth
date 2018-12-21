function register_events_site() {
	$('#btn-site-add').click(function (e) {
		e.preventDefault();
		$('#form-site-submit-type').val('site_add');
		$('#form-site-domain').val('');
		$('#modal-site').modal('show');
		$("#form-site-domain").removeAttr("readonly");
		$("#form-site-id-tr").addClass("hidden");
		$("#form-site-key-tr").addClass("hidden");
		$("#form-site-remove").addClass("hidden");
		$("#form-site-msg").html("");
	});

	$('#form-site-submit').click(function (e) {
		e.preventDefault();
		$('#modal-site').modal('hide');
		var domain = $("#form-site-domain").val();
		var method = $("#form-site-submit-type").val();
		if (method === "site_update") {
			return;
		}
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=site_add",
			type: 'POST',
			data: {
				domain: domain
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-site').bootstrapTable("refresh");
			} else {
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text(res['msg']);
			}
			$("#form-site-submit").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
		});
	});

	$('#form-site-remove').click(function (e) {
		e.preventDefault();
		$('#modal-site').modal('hide');
		var id = $("#form-site-id").val();
		var ajax = $.ajax({
			url: window.config.BASE_URL + "/service?action=site_remove",
			type: 'POST',
			data: {
				client_id: id
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#table-site').bootstrapTable("refresh");
			} else {
				$('#modal-msg').modal('show');
				$("#modal-msg-content").text(res["msg"]);
			}
		});
		ajax.fail(function (jqXHR, textStatus) {
			$('#modal-msg').modal('show');
			$("#modal-msg-content").text("Request failed :" + textStatus);
		});
	});
}

function load_sites(who) {
	$("#table-site").bootstrapTable({
		url: window.config.BASE_URL + '/service?action=sites_get&who=' + who,
		responseHandler: siteResponseHandler,
		sidePagination: 'server',
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
			field: 'owner',
			title: 'Username',
			align: 'center',
			valign: 'middle',
			visible: false,
			escape: true
		}, {
			field: 'domain',
			title: 'Domain',
			align: 'center',
			valign: 'middle',
			escape: true
		}, {
			field: 'operate',
			title: 'Operation',
			align: 'center',
			events: siteOperateEvents,
			formatter: siteOperateFormatter
		}]
	});
}

function siteResponseHandler(res) {
	if (res['errno'] === 0) {
		var tmp = {};
		tmp["total"] = res["count"];
		tmp["rows"] = res["sites"];
		return tmp;
	}
	$('#modal-msg').modal('show');
	$("#modal-msg-content").text(res["msg"]);
	return [];
}

function siteOperateFormatter(value, row, index) {
	return [
		'<button class="btn btn-default edit">',
		'<i class="glyphicon glyphicon-edit"></i>&nbsp;View',
		'</button>'
	].join('');
}

window.siteOperateEvents =
	{
		'click .edit': function (e, value, row, index) {
			show_modal_site(row);
		}
	};

function show_modal_site(site) {
	$('#modal-site').modal('show');
	$('#modal-site-title').html('Edit');
	$('#form-site-submit').html('Save');
	$('#form-site-submit-type').val('site_update');
	$('#form-site-id').val(site.client_id);
	$('#form-site-domain').val(site.domain);
	$('#form-site-key').val(site.client_secret);

	$('#form-site-domain').attr("readonly", "readonly");
	$("#form-site-id-tr").removeClass("hidden");
	$("#form-site-key-tr").removeClass("hidden");
	$("#form-site-remove").removeClass("hidden");
	$("#form-site-msg").html("");
}
