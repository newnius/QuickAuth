$(function(){
console.log(page_type);
  switch(page_type){
    case "users":
      load_users();
      register_events_user();
      break;
    case "links":
      load_links();
      register_events_link();
      break;
    case "awards":
      load_awards();
      register_events_award();
      break;
    case "slides":
      load_slides();
      register_events_slide();
      break;
    case "options":
      load_options();
      register_events_option();
      break;
    case "posts":
      load_posts();
      register_events_post();
      break;
    case "newss":
      load_newss();
      register_events_news();
      break;
    case "news_edit":
      //init_news(id);
      register_events_news();
      break;
    case "pages":
      load_pages();
      register_events_page();
      break;
    case "page_edit":
      register_events_page();
      break;
    case "logs":
      load_logs('self');
      break;
    case "logs_all":
      load_logs('all');
      break;
    case "cv":
      load_cv('zh');
      register_events_profile();
      break;
    case "cv_en":
      load_cv('en');
      register_events_profile();
      break;
    case "profile":
      load_profile();
      register_events_profile();
      break;
    case "achievements":
      load_achievements('self');
      register_events_achievement();
      break;
    case "achievements_all":
      load_achievements('all');
      register_events_achievement();
      break;
    case "changepwd":
      register_events_user();
      break;
    default:
      ;
  }

});

function load_logs(scope){
  $table = $("#table-log");
  $table.bootstrapTable({
    url: 'ajax.php?action=get_signin_log&scope='+scope,
    responseHandler: signinLogResponseHandler,
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
        field: 'tag',
        title: '标签',
        align: 'center',
        valign: 'middle',
        sortable: false
    }, {
        field: 'time',
        title: '时间',
        align: 'center',
        valign: 'middle',
        sortable: false,
        formatter: logTimeFormatter
    }, {
        field: 'ip',
        title: 'IP',
        align: 'center',
        valign: 'middle',
        sortable: true,
        formatter: long2ip
    }, {
        field: 'content',
        title: '内容',
        align: 'center',
        valign: 'middle',
        sortable: false
    }]
  });
}

var signinLogResponseHandler = function(res){
  if(res['errno'] == 0){
    return res['logs'];
  }
  alert(res['msg']);
  return [];
}

function logTimeFormatter(unixTimestamp){
  var d = new Date(unixTimestamp*1000);
  d.setTime( d.getTime() - d.getTimezoneOffset()*60*1000 );
  return formatDate(d, '%Y-%M-%d %H:%m');
}
