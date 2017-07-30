<!-- user modal -->
<div class="modal fade" id="modal-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-user-title" class="modal-title">添加用户</h4>
      </div>
      <div class="modal-body">
        <form id="form-user" action="javascript:void(0)">
          <input type="hidden" id="form-user-submit-type" value=""/>
          <table class="table cv-table">
            <tr>
              <th>用户名</th>
              <td>
                <label for="username" class="sr-only">用户名</label>
                <input type="text" id="form-user-username" class="form-group form-control" placeholder="用户名" maxlength=12 required />
              </td>
            </tr>
            <tr>
              <th>邮箱</th>
              <td>
                <label for="email" class="sr-only">邮箱</label>
                <input type="email" id="form-user-email" class="form-group form-control" placeholder="邮箱" maxlength=45 required />
              </td>
            </tr>
            <tr>
              <th>密码</th>
              <td>
                <label for="password" class="sr-only">密码</label>
                <input type="password" id="form-user-password" class="form-group form-control"  placeholder="6位以上" required />
              </td>
            </tr>
            <tr>
              <th>角色</th>
              <td>
                <select id="form-user-role" class="form-group form-control" required>
                  <option value="teacher">普通教师</option>
                  <option value="reviewer">审稿人</option>
                  <option value="admin">管理员</option>
                  <option value="root">超级管理员</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>分组</th>
              <td>
                <select id="form-user-group" class="form-group form-control" required>
                  <option value="0">其他</option>
                  <option value="1">数量经济研究中心兼职人员</option>
                  <option value="2" selected>数量经济研究中心专职人员</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>是否显示简历</th>
              <td>
                <select id="form-user-show-cv" class="form-group form-control" required>
                  <option value="0">隐藏</option>
                  <option value="1" selected>显示</option>
                </select>
              </td>
            </tr>
          </table>
          <div>
            <button id="form-user-submit" type="submit" class="btn btn-primary">添加用户</button>
            <button id="form-user-delete" type="button" class="btn btn-danger hidden">删除用户</button>
            <span id="form-user-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- link modal -->
<div class="modal fade" id="modal-link" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-link-title" class="modal-title">添加链接</h4>
      </div>
      <div class="modal-body">
        <form id="form-link" enctype="multipart/form-data" action="javascript:void(0)">
          <input type="hidden" id="form-link-submit-type" value=""/>
          <input type="hidden" name="id" id="form-link-id" value=""/>
          <table class="table cv-table">
            <tr>
              <th>语言</th>
              <td>
                <label for="language" class="sr-only">语言</label>
                <select name="lang" id="form-link-lang" class="form-group form-control" required>
                  <option value="0" selected>简体中文</option>
                  <option value="1">English</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>链接文本(*)</th>
              <td>
                <label for="text" class="sr-only">链接文本</label>
                <input type="text" name="text" id="form-link-text" class="form-group form-control" maxlength=256 placeholder="例：吉林大学" required />
              </td>
            </tr>
            <tr>
              <th>链接地址(*)</th>
              <td>
                <label for="url" class="sr-only">链接地址</label>
                <input type="url" name="url" id="form-link-url" class="form-group form-control" maxlength=256 placeholder="例：http://www.jlu.edu.cn" required />
              </td>
            </tr>
            <tr>
              <th>图片</th>
              <td>
                <label for="img" class="sr-only">图片</label>
                <input type="file" name="image" id="form-link-img" class="form-group form-control"/>
              </td>
            </tr>
            <tr>
              <th>次序</th>
              <td>
                <input type="number" name="order" id="form-link-order" class="form-group form-control" required/>
              </td>
            </tr>
          </table>
          <div>
            <button id="form-link-submit" type="submit" class="btn btn-primary">保存</button>
            <button id="form-link-delete" type="button" class="btn btn-danger hidden">删除</button>
            <span id="form-link-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- award modal -->
<div class="modal fade" id="modal-award" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-award-title" class="modal-title">添加获奖成果</h4>
      </div>
      <div class="modal-body">
        <form id="form-award" action="javascript:void(0)">
          <input type="hidden" id="form-award-submit-type" value=""/>
          <input type="hidden" id="form-award-id" value=""/>
          <table class="table cv-table">
            <tr>
              <th>语言</th>
              <td>
                <label for="language" class="sr-only">语言</label>
                <select name="lang" id="form-award-lang" class="form-group form-control" required>
                  <option value="0" selected>简体中文</option>
                  <option value="1">English</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>内容</th>
              <td>
                <label for="text" class="sr-only">内容</label>
                <input type="text" id="form-award-text" class="form-group form-control" placeholder="例：第六届长春市社会科学优秀成果奖" maxlength=256 required />
              </td>
            </tr>
            <tr>
              <th>链接地址</th>
              <td>
                <label for="url" class="sr-only">链接地址</label>
                <input type="url" id="form-award-url" class="form-group form-control" placeholder="例：http://www.jlu.edu.cn" maxlength=256 />
              </td>
            </tr>
            <tr>
              <th>次序</th>
              <td>
                <input type="number" id="form-award-order" value="1" class="form-group form-control"/>
              </td>
            </tr>
          </table>
          <div>
            <button id="form-award-submit" type="submit" class="btn btn-primary">保存</button>
            <button id="form-award-delete" type="button" class="btn btn-danger hidden">删除</button>
            <span id="form-award-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- slide modal -->
<div class="modal fade" id="modal-slide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-slide-title" class="modal-title">添加轮播</h4>
      </div>
      <div class="modal-body">
        <form id="form-slide" enctype="multipart/form-data" action="javascript:void(0)">
          <input type="hidden" id="form-slide-submit-type" value=""/>
          <input type="hidden" name="id" id="form-slide-id" value=""/>
          <table class="table cv-table">
            <tr>
              <th>语言</th>
              <td>
                <label for="language" class="sr-only">语言</label>
                <select name="lang" id="form-slide-lang" class="form-group form-control" required>
                  <option value="0" selected>简体中文</option>
                  <option value="1">English</option>
                </select>
              </td>
            </tr>
            <tr>
              <th>轮播文本(*)</th>
              <td>
                <label for="text" class="sr-only">轮播文本</label>
                <input type="text" name="text" id="form-slide-text" class="form-group form-control" placeholder="例：博士学位论文答辩会" maxlength=256 required />
              </td>
            </tr>
            <tr>
              <th>链接地址</th>
              <td>
                <label for="url" class="sr-only">链接地址</label>
                <input type="url" name="url" id="form-slide-url" class="form-group form-control" placeholder="可以留空" maxlength=256 />
              </td>
            </tr>
            <tr>
              <th>图片(*)</th>
              <td>
                <label for="img" class="sr-only">图片</label>
                <input type="file" name="image" id="form-slide-img" class="form-group form-control" required/>
              </td>
            </tr>
            <tr>
              <th>次序</th>
              <td>
                <input type="number" name="order" id="form-slide-order" class="form-group form-control"/>
              </td>
            </tr>
          </table>
          <div>
            <button id="form-slide-submit" type="submit" class="btn btn-primary">保存</button>
            <button id="form-slide-delete" type="button" class="btn btn-danger hidden">删除</button>
            <span id="form-slide-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- option modal -->
<div class="modal fade" id="modal-option" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-option-title" class="modal-title">添加配置</h4>
      </div>
      <div class="modal-body">
        <form id="form-option" action="javascript:void(0)">
          <input type="hidden" id="form-option-submit-type" value=""/>
          <table class="table cv-table">
            <tr>
              <th>键</th>
              <td>
                <label for="key" class="sr-only">键</label>
                <input type="text" id="form-option-key" class="form-group form-control" placeholder="" maxlength=64 required />
              </td>
            </tr>
            <tr>
              <th>值</th>
              <td>
                <label for="value" class="sr-only">值</label>
                <input type="text" id="form-option-value" class="form-group form-control" placeholder="例：" maxlength=256 required />
              </td>
            </tr>
            <tr>
              <th>备注</th>
              <td>
                <label for="remark" class="sr-only">备注</label>
                <input type="text" id="form-option-remark" class="form-group form-control" maxlength=64 />
              </td>
            </tr>
          </table>
          <div>
            <button id="form-option-submit" type="submit" class="btn btn-primary">保存</button>
            <button id="form-option-delete" type="button" class="btn btn-danger hidden">删除</button>
            <span id="form-option-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- post modal -->
<div class="modal fade" id="modal-post" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-post-title" class="modal-title">查看投稿</h4>
      </div>
      <div class="modal-body">
        <form id="form-post" action="javascript:void(0)">
          <input type="hidden" id="form-post-submit-type" value=""/>
          <input type="hidden" id="form-post-id" value=""/>
          <table class="table ">
            <tr>
              <th>稿件</th>
              <td>
                <label for="attachment" class="sr-only">附件</label>
                <input type="file" id="form-post-attachment" class="form-group form-control hidden" required disabled />
                <a href="javascript:void(0)" id="form-post-attachment-filename"/>下载附件</a>
              </td>
            </tr>
            <tr>
              <th>姓名</th>
              <td>
                <label for="name" class="sr-only">姓名</label>
                <input type="text" id="form-post-author" class="form-group form-control" placeholder="" required disabled />
              </td>
            </tr>
            <tr>
              <th>联系电话</th>
              <td>
                <label for="phone" class="sr-only">联系电话</label>
                <input type="phone" id="form-post-phone" class="form-group form-control" placeholder="" required disabled />
              </td>
            </tr>
            <tr>
              <th>电子邮箱</th>
              <td>
                <label for="email" class="sr-only">邮箱</label>
                <input type="email" id="form-post-email" class="form-group form-control" required disabled />
              </td>
            </tr>
            <tr>
              <th>联系地址</th>
              <td>
                <label for="address" class="sr-only">联系地址</label>
                <input type="text" id="form-post-address" class="form-group form-control" disabled />
              </td>
            </tr>
            <tr>
              <th>邮&nbsp;&nbsp;编</th>
              <td>
                <label for="zipcode" class="sr-only">邮编</label>
                <input type="text" id="form-post-postcode" class="form-group form-control" disabled />
              </td>
            </tr>
            <tr>
              <th>其他信息</th>
              <td>
                <label for="remark" class="sr-only">备注</label>
                <input type="text" id="form-post-remark" class="form-group form-control" disabled />
              </td>
            </tr>
          </table>

          <div>
            <button id="form-post-submit" type="submit" class="btn btn-primary hidden">保存</button>
            <button id="form-post-delete" type="button" class="btn btn-danger hidden">删除</button>
            <span id="form-post-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>


<!-- reset pwd modal -->
<div class="modal fade" id="modal-resetpwd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-resetpwd-title" class="modal-title">重置密码</h4>
      </div>
      <div class="modal-body">
        <form id="form-resetpwd" action="javascript:void(0)">
          <input type="hidden" id="form-resetpwd-username" class="form-group form-control" required />
          <input type="hidden" id="form-resetpwd-email" class="form-group form-control" required />
          <table class="table">
            <tr>
              <th>密码</th>
              <td>
                <label for="password" class="sr-only">密码</label>
                <input type="password" id="form-resetpwd-password" class="form-group form-control"  placeholder="6位以上" required />
              </td>
            </tr>
            <tr>
              <th>重复</th>
              <td>
                <label for="password" class="sr-only">重复密码</label>
                <input type="password" id="form-resetpwd-repeat" class="form-group form-control"  placeholder="再次输入密码" required />
              </td>
            </tr>
          </table>
          <div>
            <button id="form-resetpwd-submit" type="submit" class="btn btn-primary">确认重置</button>
            <span id="form-resetpwd-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

