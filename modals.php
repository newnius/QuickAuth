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
                <input type="password" id="form-user-password" class="form-group form-control"  placeholder="重置密码"/>
              </td>
            </tr>
            <tr>
              <th>角色</th>
              <td>
                <select id="form-user-role" class="form-group form-control" required>
                  <option value="removed">已注销</option>
                  <option value="blocked">已封禁</option>
                  <option value="normal">普通用户</option>
                  <option value="developer">开发者</option>
                  <option value="admin">管理员</option>
                  <option value="root">超级管理员</option>
                </select>
              </td>
            </tr>
          </table>
          <div>
            <button id="form-user-submit" type="button" class="btn btn-primary">更新用户</button>
            <span id="form-user-msg" class="text-danger"></span>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- msg modal -->
<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 id="modal-msg-title" class="modal-title">提示</h4>
      </div>
      <div class="modal-body">
				<h4 id="modal-msg-content" class="text-msg text-center">lalalalal</h4>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-site" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Site</h4>
			</div>
			<div class="modal-body">
				<form id="form-site" action="javascript:void(0)">
					<input type="hidden" id="form-site-submit-type" value=""/>
					<table class="table">
						<tr>
							<th>域名</th>
							<td>
								<label for="domain" class="sr-only">Domain</label>
								<input type="text" id="form-site-domain" class="form-group form-control" placeholder="example.com" required />
							</td>
						</tr>
						<tr>
							<th>撤销授权URL(可选)</th>
							<td>
								<label for="revoke url" class="sr-only">Revoke URL</label>
								<input type="text" id="form-site-revokeurl" class="form-group form-control" placeholder="http://example.com/revoke.php" required />
							</td>
						</tr>
						<tr>
							<th>站点类型</th>
							<td>
								<select id="form-site-level" class="form-group form-control" required>
									<option value="1" selected>普通站点</option>
									<option value="99">合作伙伴</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>APPID</th>
							<td>
								<label for="id" class="sr-only">App ID</label>
								<input type="text" id="form-site-id" class="form-group form-control" required disabled/>
							</td>
						</tr>
						<tr>
							<th>KEY</th>
							<td>
								<label for="key" class="sr-only">Key</label>
								<input type="text" id="form-site-key" class="form-group form-control" required disabled/>
							</td>
						</tr>
					</table>
					<div>
						<button id="form-site-submit" type="submit" class="btn btn-primary">Save</button>
						<span id="form-site-msg" class="text-danger"></span>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

