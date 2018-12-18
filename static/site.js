function register_events_site() {
	$('#btn-site-add').click(function (e) {
		e.preventDefault();
		$('#form-site-submit-type').val('site_add');
		$('#form-site-domain').val('');
		$('#modal-site').modal('show');
		$("#form-site-domain").removeAttr("disabled");
		$("#form-site-id-tr").addClass("hidden");
		$("#form-site-key-tr").addClass("hidden");
		$("#form-site-remove").addClass("hidden");
		$("#form-site-msg").html("");
	});

	$('#form-site-submit').click(function (e) {
		e.preventDefault();
		var domain = $("#form-site-domain").val();
		var method = $("#form-site-submit-type").val();
		if (method === "site_update") {
			$('#modal-site').modal('hide');
			return;
		}
		$("#form-site-submit").attr("disabled", "disabled");
		var ajax = $.ajax({
			url: "/service?action=" + method,
			type: 'POST',
			data: {
				domain: domain
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#modal-site').modal('hide');
				$('#table-site').bootstrapTable("refresh");
			} else {
				$("#form-site-msg").html(res["msg"]);
				$("#modal-site").effect("shake");
			}
			$("#form-site-submit").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			alert("Request failed :" + textStatus);
			$("#form-site-submit").removeAttr("disabled");
		});
	});

	$('#form-site-remove').click(function (e) {
		e.preventDefault();
		var id = $("#form-site-id").val();
		$("#form-site-remove").attr("disabled", "disabled");
		var ajax = $.ajax({
			url: "/service?action=site_remove",
			type: 'POST',
			data: {
				client_id: id
			}
		});
		ajax.done(function (res) {
			if (res["errno"] === 0) {
				$('#modal-site').modal('hide');
				$('#table-site').bootstrapTable("refresh");
			} else {
				$("#form-site-msg").html(res["msg"]);
				$("#modal-site").effect("shake");
			}
			$("#form-site-remove").removeAttr("disabled");
		});
		ajax.fail(function (jqXHR, textStatus) {
			alert("Request failed :" + textStatus);
			$("#form-site-remove").removeAttr("disabled");
		});
	});
}

function load_sites(who) {
	var $table = $("#table-site");
	$table.bootstrapTable({
		url: '/service?action=sites_get&who=' + who,
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
			sortable: true,
			visible: false
		}, {
			field: 'domain',
			title: 'Domain',
			align: 'center',
			valign: 'middle',
			sortable: true
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
	alert(res["msg"]);
	return [];
}

function siteOperateFormatter(value, row, index) {
	return [
		'<button class="btn btn-default edit" href="javascript:void(0)">',
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

	$('#form-site-domain').attr("disabled", "disabled");
	$("#form-site-id-tr").removeClass("hidden");
	$("#form-site-key-tr").removeClass("hidden");
	$("#form-site-remove").removeClass("hidden");
	$("#form-site-msg").html("");
}
