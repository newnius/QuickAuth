<!-- user modal -->
<div class="modal fade" id="modal-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 id="modal-user-title" class="modal-title">Add User</h4>
			</div>
			<div class="modal-body">
				<form id="form-user" action="javascript:void(0)">
					<input type="hidden" id="form-user-submit-type" value=""/>
					<table class="table cv-table">
						<tr>
							<th>Username</th>
							<td>
								<label for="username" class="sr-only">username</label>
								<input type="text" id="form-user-username" class="form-group form-control" placeholder="username" maxlength=12 required />
							</td>
						</tr>
						<tr>
							<th>Email</th>
							<td>
								<label for="email" class="sr-only">email</label>
								<input type="email" id="form-user-email" class="form-group form-control" placeholder="email" maxlength=45 required />
							</td>
						</tr>
						<tr>
							<th>Password</th>
							<td>
								<label for="password" class="sr-only">password</label>
								<input type="password" id="form-user-password" class="form-group form-control"  placeholder="leave it blank to remain old password"/>
							</td>
						</tr>
						<tr>
							<th>Role</th>
							<td>
								<select id="form-user-role" class="form-group form-control" required>
									<option value="removed">Removed</option>
									<option value="blocked">Blocked</option>
									<option value="normal">Normal</option>
									<option value="developer">Developer</option>
									<option value="admin">Admin</option>
									<option value="root">Root</option>
								</select>
							</td>
						</tr>
					</table>
					<div>
						<button id="form-user-submit" type="button" class="btn btn-primary">Save</button>
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
				<h4 id="modal-msg-title" class="modal-title">Notify</h4>
			</div>
			<div class="modal-body">
				<h4 id="modal-msg-content" class="text-msg text-center">Hello World!</h4>
			</div>
		</div>
	</div>
</div>

<!-- msg site -->
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
							<th>Domain</th>
							<td>
								<label for="domain" class="sr-only">Domain</label>
								<input type="text" id="form-site-domain" class="form-group form-control" placeholder="example.com" required />
							</td>
						</tr>
						<tr>
							<th>Revoke URL(可选)</th>
							<td>
								<label for="revoke url" class="sr-only">Revoke URL</label>
								<input type="text" id="form-site-revokeurl" class="form-group form-control" placeholder="http://example.com/revoke.php" required />
							</td>
						</tr>
						<tr>
							<th>Type</th>
							<td>
								<select id="form-site-level" class="form-group form-control" required>
									<option value="1" selected>Normal</option>
									<option value="99">Partner</option>
								</select>
							</td>
						</tr>
						<tr id="form-site-id-tr">
							<th>APPID</th>
							<td>
								<label for="id" class="sr-only">App ID</label>
								<input type="text" id="form-site-id" class="form-group form-control" required disabled/>
							</td>
						</tr>
						<tr id="form-site-key-tr">
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

<!-- modal block -->
<div class="modal fade" id="modal-block" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 id="modal-block-title" class="modal-title">Block Info</h4>
			</div>
			<div class="modal-body">
				<form id="form-block" action="javascript:void(0)">
					<table class="table">
						<tr>
							<th>IP</th>
							<td>
								<label for="ip" class="sr-only">IP</label>
								<input type="text" id="form-block-ip" class="form-group form-control" placeholder="8.8.8.8" required />
							</td>
						</tr>
						<tr>
							<th>Time</th>
							<td>
								<label for="time" class="sr-only">Time</label>
								<input type="text" id="form-block-time" class="form-group form-control" placeholder="10 (set -1 to unblock)" required />
							</td>
						</tr>
					</table>
					<div>
						<button id="form-block-submit" type="submit" class="btn btn-primary">&nbsp;Save&nbsp;</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- modal update email -->
<div class="modal fade" id="modal-email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 id="modal-email-title" class="modal-title">Update Email</h4>
			</div>
			<div class="modal-body">
				<form id="form-email" action="javascript:void(0)">
					<table class="table">
						<tr>
							<th>Email</th>
							<td>
								<label for="email" class="sr-only">Email</label>
								<input type="email" id="form-email-email" class="form-group form-control" placeholder="new email" required />
							</td>
						</tr>
					</table>
					<div>
						<button id="form-email-submit" type="submit" class="btn btn-primary">&nbsp;Save&nbsp;</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- modal code -->
<div class="modal fade" id="modal-verify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 id="modal-verify-title" class="modal-title">Verify Email</h4>
			</div>
			<div class="modal-body">
				<form id="form-verify" action="javascript:void(0)">
					<table class="table">
						<tr>
							<th>Code</th>
							<td>
								<div class="form-group">
									<label for="code" class="sr-only">Code</label>
									<div class="input-group">
										<input type="number" class="form-control" id="form-verify-code" placeholder="Code Received" required />
										<div class="input-group-addon">
											<a href="javascript:void(0)" id="btn-verify-send">Send</a>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
					<div>
						<button id="form-verify-submit" type="submit" class="btn btn-primary">&nbsp;Save&nbsp;</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
