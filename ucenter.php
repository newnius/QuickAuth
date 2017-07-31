<?php
	require_once('util4p/util.php');
	require_once('util4p/Session.class.php');
	require_once('util4p/AccessController.class.php');
	
	require_once('user.logic.php');
	require_once('UserManager.class.php');

	require_once('config.inc.php');
	require_once('init.inc.php');
	require_once('secure.php');
	require_once('cookie.php');
  if(Session::get('username')==null){
    header('location:login.php?a=notloged');
    exit;
  }

  $page_type = 'home';
  $username = Session::get('username');

  if(isset($_GET['profile'])){
    $page_type='profile';
  
  }elseif(isset($_GET['changepwd'])){
    $page_type='changepwd';
  
  }elseif(isset($_GET['users'])){
    $page_type='users';
  
  }elseif(isset($_GET['cv'])){
    $page_type='cv';

  }elseif(isset($_GET['cv_en'])){
    $page_type='cv_en';

  }elseif(isset($_GET['logs'])){
    $page_type='logs';

  }elseif(isset($_GET['logs_all'])){
    $page_type='logs_all';

  }elseif(isset($_GET['achievements'])){
    $page_type='achievements';

  }elseif(isset($_GET['achievements_all'])){
    $page_type='achievements_all';

  }elseif(isset($_GET['links'])){
    $page_type='links';

  }elseif(isset($_GET['pages'])){
    $page_type='pages';

  }elseif(isset($_GET['page_edit'])){
    $page_type='page_edit';

  }elseif(isset($_GET['awards'])){
    $page_type='awards';

  }elseif(isset($_GET['slides'])){
    $page_type='slides';

  }elseif(isset($_GET['options'])){
    $page_type='options';

  }elseif(isset($_GET['posts'])){
    $page_type='posts';

  }elseif(isset($_GET['newss'])){
    $page_type='newss';

  }elseif(isset($_GET['news_edit'])){
    $page_type='news_edit';

  }elseif(isset($_GET['admin'])){
    $page_type='admin';

  }elseif(isset($_GET['signout'])){
    $page_type='signout';
     signout();
     header('location:login.php?a=signout');
     exit;
  }


  $entries = array(
    array('home', '个人首页'),
    array('profile', '用户信息'),
    array('changepwd', '修改密码'),
    array('cv', '个人简历'),
    array('cv_en', '个人简历(英)'),
    array('achievements', '学术成果'),
    array('logs', '登录日志'),
    array('admin', '管理入口'),
    array('signout', '退出登录')
  );
  $visible_entries = array();
  foreach($entries as $entry){
    if(AccessController::hasAccess( Session::get('role'), 'show_ucenter_'.$entry[0])){
      $visible_entries[] = array($entry[0], $entry[1]);
    }
  }

  $admin_entries = array(
    array('users', '用户管理'),
    array('newss', '新闻管理'),
    array('slides', '轮播管理'),
    array('links', '链接管理'),
    array('achievements_all', '学术成果'),
    array('pages', '页面管理'),
    array('awards', '获奖管理'),
    array('options', '网站设置'),
    array('posts', '投稿管理'),
    array('logs_all', '操作日志'),
  );
  $visible_admin_entries = array();
  foreach($admin_entries as $entry){
    if(AccessController::hasAccess( Session::get('role'), 'show_ucenter_'.$entry[0])){
      $visible_admin_entries[] = array($entry[0], $entry[1]);
    }
  }
?>
<!DOCTYPE html>
<!--[if IE 7]> <html lang="zh-CN" class="ie ie7"> <![endif]-->
<!--[if IE 8]> <html lang="zh-CN" class="ie ie8"> <![endif]-->
<!--[if IE 9]> <html lang="zh-CN" class="ie ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="zh-CN">
<!--<![endif]-->
  <head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="keywords" content="吉林大学,数量经济研究中心,吉林大学数量经济研究中心,吉林大学商学院,教育部人文社会科学重点研究基地"/>
    <meta name="description" content="吉林大学数量经济研究中心成立于1999年10月，2000年9月25日被教育部批准为普通高等学校人文社会科学重点研究基地。研究内容包括：经济增长、经济波动与经济政策、金融与投资、区域经济和产业经济、微观经济、经济系统模拟实验和经济权力范式、经济博弈论、数量经济分析方法等。" />
    <meta name="author" content="Newnius"/>
    <link rel="icon" href="favicon.ico"/>
    <title>个人中心 | 数量经济研究中心</title>
    <link href="//cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="style.css" rel="stylesheet"/>
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
  
		<link href="//cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table.min.css" rel="stylesheet">

    <script type="text/javascript">
      var page_type = "<?=$page_type?>";
    </script>
<!--[if IE 7]>
    <link rel="stylesheet" href="//stanford.edu/assets/css/ie/ie7.css"/>
<![endif]-->
<!--[if IE 8]>
    <link rel="stylesheet" href="//stanford.edu/assets/css/ie/ie8.css"/>
<![endif]-->
<!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <link href="fuckie.css" rel="stylesheet"/>
<![endif]-->
  </head>

  <body>
    <div class="wrapper">
      
      <?php require_once('header.php'); ?>
      <?php require_once('modals.php'); ?>
      <div class="container">
        <div class="row">
          
          <div class="hidden-xs hidden-sm col-md-2 col-lg-2">
            <div class="panel panel-default">
              <div class="panel-heading">功能列表</div>
              <ul class="nav nav-pills nav-stacked panel-body">
                <?php foreach($visible_entries as $entry){ ?>
                <li role="presentation" <?php if($page_type==$entry[0])echo 'class="disabled"'; ?> >
                  <a href="?<?=$entry[0]?>"><?=$entry[1]?></a>
                </li>
                <?php } ?>
              </ul>
            </div>
          </div>
          
          <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
            <div class="visible-xs visible-sm">
              <div class=" panel panel-default">
                <div class="panel-heading">功能列表</div>
                <ul class="nav nav-pills panel-body">
                  <?php foreach($visible_entries as $entry){ ?>
                  <li role="presentation" <?php if($page_type==$entry[0])echo 'class="disabled"'; ?> >
                    <a href="?<?=$entry[0]?>"><?=$entry[1]?></a>
                  </li>
                  <?php } ?>
                </ul>
              </div>
            </div>

            <?php if($page_type == 'home'){ ?>
            <div id="home">
              <div class="panel panel-default">
                <div class="panel-heading">Welcome</div> 
                <div class="panel-body">
                  欢迎回来, <?php echo htmlspecialchars($username) ?>.<br/>
                  当前IP: &nbsp; <?=cr_get_client_ip() ?>.<br/>
                  现在时间: &nbsp; <?php echo date('H:i:s',time()) ?>
                </div>
              </div>
              <div class="panel panel-default">
                <div class="panel-heading">通知</div> 
                <div class="panel-body">
                  <h4 class="text-info">提示</h4>
                  <ul>
                    <li>如果您在30分钟内没有刷新页面，会话会超时</li>
                    <li>图片最大为2M，附件最大为16M，超出会显示“文件上传失败”或“内容不完整”的错误提示</li>
                    <li>允许的附件格式：.rar .zip .7z .doc(x) .xls(x) .ppt(x) .pdf</li>
                    <li>建议及时修改初始密码，不要将密码设置为简单密码</li>
                  </ul>
                </div>
              </div>
            </div>
      
            <?php }elseif($page_type == 'profile'){ ?>
            <div id="profile">
              <div class="panel panel-default">
                <div class="panel-heading">基本信息</div> 
                <div class="panel-body">
                    <table class="table">
                      <tr>
                        <th>用户名</th>
                        <td>
                          <span id="user-username">Loading...</span>
                        </td>
                      </tr>
                      <tr>
                        <th>Email</th>
                        <td>
                          <span id="user-email">Loading...</span><a href="javascript:void(0)" id="btn-verify-email" class="btn">Verify</a>
                        </td>
                      </tr>
                      <tr>
                        <th>Role</th>
                        <td>
                          <span id="user-role">Loading...</span>
                        </td>
                      </tr>
                      <tr>
                        <th>Password</th>
                        <td>
                          <span>******</span><a href="?changepwd" class="btn">Update</a>
                        </td>
                      </tr>
                    </table>
                  </form>
                </div>
              </div>
            </div>
     
            <?php }elseif($page_type == 'changepwd'){ ?>
            <div id="changepwd">
              <div class="panel panel-default">
                <div class="panel-heading">修改密码</div> 
                <div class="panel-body">
                  <div id="resetpwd">
                    <h2>修改密码</h2>
                    <form class="form-changepwd">
                      <div class="form-group">
                        <label class="sr-only" for="inputOldpwd">Old password</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                          </div>
                          <input type="password" class="form-control" id="oldpwd" placeholder="原来的密码" required />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="sr-only" for="inputPassword">New Password</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
                          </div>
                          <input type="password" class="form-control" id="password" placeholder="新的密码" required />
                        </div>
                      </div>
                      <button id="btn-changepwd" class="btn btn-md btn-primary " type="submit" >确认修改</button>
                      <span id="changepwd-msg" class="text-danger"></span>
                    </form>
                  </div>
                </div>
              </div>
            </div>
   
            <?php }elseif($page_type == 'cv' || $page_type == 'cv_en'){ ?>
            <div id="cv">
              <div class="panel panel-default">
                <div class="panel-heading">我的简历</div> 
                <div class="panel-body">
                  <input id="form-cv-lang" type="hidden" value="zh" />
                  <script id="editor" type="text/plain" style="width:100%;height:500px;"></script>
                  <div style="margin-top: 15px;">
                    <button id="btn-update-cv" type="button" class="btn btn-primary">更新</button>
                    <button id="btn-generate-cv" type="button" class="btn btn-default">自动生成</button>
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'users'){ ?>
            <div id="users">
              <div class="panel panel-default">
                <div class="panel-heading">用户管理</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar">
                      <button id="btn-user-add" class="btn btn-primary">
                        <i class="glyphicon glyphicon-plus"></i> 添加用户
                      </button>
                    </div>
                    <table id="table-user" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                    <span class="text-info">* 学术团队根据用户名增序排序</span><br/>
                    <span class="text-info">* 不支持现有用户的用户名修改操作</span><br/>
                    <span class="text-info">* 不支持修改自己的角色</span>
                  </div>
                </div>
              </div>
            </div>
  
            <?php }elseif($page_type == 'links'){ ?>
            <div id="links">
              <div class="panel panel-default">
                <div class="panel-heading">链接管理</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar">
                      <button id="btn-link-add" class="btn btn-primary">
                        <i class="glyphicon glyphicon-plus"></i> 添加链接
                      </button>
                    </div>
                    <table id="table-link" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                    <span class="text-info">* 图片留空表示文字链接</span><br/>
                    <span class="text-info">* 次序值越大，显示越靠前</span><br/>
                    <span class="text-info">* 若要更换图片，先删除原有的再添加</span>
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'pages'){ ?>
            <div id="pages">
              <div class="panel panel-default">
                <div class="panel-heading">页面管理</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar">
                      <button id="btn-page-add" class="btn btn-primary">
                        <i class="glyphicon glyphicon-plus"></i> 添加页面
                      </button>
                    </div>
                    <table id="table-page" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'page_edit'){ ?>
            <div id="page_edit">
              <div class="panel panel-default">
                <div class="panel-heading">页面编辑</div>
                <div class="panel-body">

                  <form id="form-page" action="javascript:void(0)">
                    <input type="hidden" id="form-page-submit-type" />
                      <table class="table">
                      <tr>
                        <td>
                          <label for="key" class="sr-only">Key</label>
                          <input type="text" id="form-page-key" class="form-group form-control"  placeholder="Key" maxlength=64 required />
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <label for="title" class="sr-only">标题</label>
                          <input type="text" id="form-page-title" class="form-group form-control"  placeholder="标题" maxlength=512 required />
                        </td>
                      </tr>
                      <tr>
                        <td>
                            <script id="editor" type="text/plain" style="width:100%;height:500px;"></script>
                            <script type="text/javascript">
                              var ue = UE.getEditor('editor');
                              ue.ready(function(){
                                <?php
                                  if(isset($_GET['key'])){
                                    echo "init_page(\"{$_GET['key']}\")";
                                  }
                                ?>
                              });
                            </script>
                        </td>
                      </tr>
                    </table>
                    <div>
                      <button id="form-page-submit" type="submit" class="btn btn-primary">保存</button>
                      <span id="form-page-msg" class="text-danger"></span>
                    </div>
                  </form>

                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'newss'){ ?>
            <div id="newss">
              <div class="panel panel-default">
                <div class="panel-heading">新闻管理</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar">
                      <button id="btn-news-add" class="btn btn-primary">
                        <i class="glyphicon glyphicon-plus"></i> 添加新闻
                      </button>
                    </div>
                    <table id="table-news" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                    <span class="text-info">* 点击列名可以按照该列进行排序</span><br/>
                    <span class="text-info">* 新闻内容过多时可以选择分页大小</span>
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'news_edit'){ ?>
            <div id="links">
              <div class="panel panel-default">
                <div class="panel-heading">新闻编辑</div> 
                <div class="panel-body">

                  <form id="form-news" action="javascript:void(0)">
                    <input type="hidden" id="form-news-submit-type" />
                    <input type="hidden" id="form-news-id" />
                      <table class="table">
                      <tr>
                        <th>语言</th>
                        <td>
                          <label for="language" class="sr-only">语言</label>
                          <select id="form-news-lang" class="form-group form-control" required>
                            <option value="0" selected>简体中文</option>
                            <option value="1">English</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <th>分类</th>
                        <td>
                          <label for="type" class="sr-only">分类</label>
                          <select id="form-news-type" class="form-group form-control" required>
                            <option value="1">中心通知</option>
                            <option value="5">中心动态</option>
                            <option value="6">学术会议</option>
                            <option value="7">学界动态</option>
                            <option value="11">中心大事记</option>
                            <option value="12">人才培养</option>
                            <option value="14">获奖成果</option>
                            <option value="15">科研管理</option>
                            <option value="49">中改院动态</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <th>标题</th>
                        <td>
                          <label for="title" class="sr-only">标题</label>
                          <input type="text" id="form-news-title" class="form-group form-control"  placeholder="新闻标题" maxlength=512 required />
                        </td>
                      </tr>
                      <tr>
                        <th>优先级</th>
                        <td>
                          <select id="form-news-order" class="form-group form-control" required>
                            <option value="0">普通</option>
                            <option value="50">置顶</option>
                            <option value="99">置顶,加粗</option>
                          </select>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">
                            <script id="editor" type="text/plain" style="width:100%;height:500px;"></script>
                            <script type="text/javascript">
                              var ue = UE.getEditor('editor');
                              ue.ready(function(){
                                <?php
                                  if(isset($_GET['id'])){
                                    echo "init_news({$_GET['id']})";
                                  }
                                ?>
                              });
                            </script>
                        </td>
                      </tr>
                    </table>
                    <div>
                      <button id="form-news-submit" type="submit" class="btn btn-primary">保存</button>
                      <span id="form-news-msg" class="text-danger"></span>
                    </div>
                  </form>

                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'awards'){ ?>
            <div id="awards">
              <div class="panel panel-default">
                <div class="panel-heading">获奖成果管理</div>
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar">
                      <button id="btn-award-add" class="btn btn-primary">
                        <i class="glyphicon glyphicon-plus"></i> 添加获奖
                      </button>
                    </div>
                    <table id="table-award" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                    <span class="text-info">* 链接留空表示不设置超链接</span><br/>
                    <span class="text-info">* 次序值越大，显示越靠前</span><br/>
                    <span class="text-info">* 首页最多显示三条</span>
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'slides'){ ?>
            <div id="slides">
              <div class="panel panel-default">
                <div class="panel-heading">首页轮播管理</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar">
                      <button id="btn-slide-add" class="btn btn-primary">
                        <i class="glyphicon glyphicon-plus"></i> 添加轮播
                      </button>
                    </div>
                    <table id="table-slide" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                    <span class="text-info">* 链接留空表示不设置超链接</span><br/>
                    <span class="text-info">* 次序值越大，越靠前</span><br/>
                    <span class="text-info">* 若要更换图片，先删除原有的再添加</span>
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'options'){ ?>
            <div id="options">
              <div class="panel panel-default">
                <div class="panel-heading">网站参数设置</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar">
                      <button id="btn-option-add" class="btn btn-primary hidden">
                        <i class="glyphicon glyphicon-plus"></i> 添加设置
                      </button>
                    </div>
                    <table id="table-option" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'posts'){ ?>
            <div id="posts">
              <div class="panel panel-default">
                <div class="panel-heading">用户投稿管理</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar"></div>
                    <table id="table-post" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'logs'){ ?>
            <div id="logs">
              <div class="panel panel-default">
                <div class="panel-heading">Recent activities</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar"></div>
                    <table id="table-log" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                    <span class="text-info">* 最多显示20条最近的记录</span>
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'logs_all'){ ?>
            <div id="logs">
              <div class="panel panel-default">
                <div class="panel-heading">Recent activities</div> 
                <div class="panel-body">
                  <div class="table-responsive">
                    <div id="toolbar"></div>
                    <table id="table-log" data-toolbar="#toolbar" class="table table-striped">
                    </table> 
                    <span class="text-info">* 只显示7天内的登录日志</span><br />
                    <span class="text-info">* 标签最后一个单词表示操作人</span>
                  </div>
                </div>
              </div>
            </div>

            <?php }elseif($page_type == 'admin'){ ?>
            <div class=" panel panel-default">
              <div class="panel-heading">管理入口</div>
              <h4 style="text-align:center">中英文统一管理后台</h4>
              <ul class="nav nav-pills panel-body">
                <?php foreach($visible_admin_entries as $entry){ ?>
                <li role="presentation" <?php if($page_type==$entry[0])echo 'class="disabled"'; ?> >
                  <a href="?<?=$entry[0]?>"><?=$entry[1]?></a>
                </li>
                <?php } ?>
              </ul>
            </div>
            <?php } ?>

          </div>
        </div>
      </div> <!-- /container -->
      
      <!--This div exists to avoid footer from covering main body-->
      <div class="push"></div>
    </div>
    <?php require_once('footer.php'); ?>

    <script src="js/util.js"></script>
    <script src="js/script.js"></script>
    <script src="js/user.js"></script>
    <script src="js/ucenter.js"></script>
		
		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table-locale-all.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/bootstrap-table.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/extensions/mobile/bootstrap-table-mobile.min.js"></script>
		<script src="//cdn.bootcss.com/bootstrap-table/1.11.1/extensions/export/bootstrap-table-export.min.js"></script>

		<script src="//cdn.bootcss.com/TableExport/5.0.0-rc.11/js/tableexport.min.js"></script>

    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="//cdn.bootcss.com/blueimp-md5/1.1.1/js/md5.min.js"></script>
    <script src="//cdn.bootcss.com/jqueryui/1.11.4/jquery-ui.js"></script> 
  </body>
</html>
