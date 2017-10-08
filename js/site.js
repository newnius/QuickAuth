function register_events_site()
{
	$('#btn-site-add').click(function(e){
		e.preventDefault();
		$('#form-site-submit-type').val('site_add');
		$('#form-site-domain').val('');
		$('#form-site-revokeurl').val('');
		$('#form-site-level').val('1');
		$('#modal-site').modal('show');
		$("#form-site-domain").removeAttr("disabled");
		$("#form-site-id-tr").addClass("hidden");
		$("#form-site-key-tr").addClass("hidden");
	});

	$('#form-site-submit').click(function(e){
		e.preventDefault();
		var id = $("#form-site-id").val();
		var domain = $("#form-site-domain").val();
		var revoke_url = $("#form-site-revokeurl").val();
		var level = $("#form-site-level").val();
		var method = $("#form-site-submit-type").val();
		$("#form-site-submit").attr("disabled","disabled");
		var ajax = $.ajax({
			url: "ajax.php?action=" + method,
			type: 'POST', 
			data: {
				id: id,
				domain: domain,
				revoke_url: revoke_url,
				level: level
			}
		});
		ajax.done(function(msg){
			var res = JSON.parse(msg);
			if(res["errno"]==0){
				$('#modal-site').modal('hide');
				$('#table-site').bootstrapTable("refresh");
			}else{
				$("#form-site-msg").html(res["msg"]);
				$("#modal-site").effect("shake");
			}
			$("#form-site-submit").removeAttr("disabled");
		});
		ajax.fail(function(jqXHR,textStatus){
			alert("Request failed :" + textStatus);
			$("#form-site-submit").removeAttr("disabled");
		});
	});

}

function load_sites()
{
	$table = $("#table-site");
	$table.bootstrapTable({
		url: 'ajax.php?action=sites_get',
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
			title: '选择',
			checkbox: true
		}, {
			field: 'owner',
			title: '用户名',
			align: 'center',
			valign: 'middle',
			sortable: true,
			visible: false
		}, {
			field: 'domain',
			title: '域名',
			align: 'center',
			valign: 'middle',
			sortable: true
		}, {
			field: 'level',
			title: '类型',
			align: 'center',
			valign: 'middle',
			sortable: true,
			formatter: siteLevelFormatter
		}, {
			field: 'operate',
			title: '操作',
			align: 'center',
			events: siteOperateEvents,
			formatter: siteOperateFormatter
		}]
	});
}

function siteLevelFormatter(level)
{
	switch(level){
		case "99":
			return "合作站点";
		case "1":
			return "普通站点";
		case "0":
			return "已删除";
	}
	return "未知";
}

function siteResponseHandler(res)
{
	if(res['errno'] == 0){
		var tmp = new Object();
		tmp["total"] = res["count"];
		tmp["rows"] = res["sites"];
		return tmp;
	}
	alert(res["msg"]);
	return [];
}

function siteOperateFormatter(value, row, index)
{
	return [
		'<button class="btn btn-default edit" href="javascript:void(0)">',
		'<i class="glyphicon glyphicon-edit"></i>&nbsp;查看',
		'</button>'
	].join('');
}

window.siteOperateEvents =
{
	'click .edit': function (e, value, row, index) {
		show_modal_site(row);
	}
};

function show_modal_site(site)
{
	$('#modal-site').modal('show');
	$('#modal-site-title').html('编辑站点');
	$('#form-site-submit').html('更新信息');
	$('#form-site-submit-type').val('site_update');
	$('#form-site-id').val(site.id);
	$('#form-site-domain').val(site.domain);
	$('#form-site-revokeurl').val(site.revoke_url);
	$('#form-site-key').val(site.key);
	$('#form-site-level').val(site.level);

	$('#form-site-domain').attr("disabled", "disabled");
	$("#form-site-id-tr").removeClass("hidden");
	$("#form-site-key-tr").removeClass("hidden");
}
