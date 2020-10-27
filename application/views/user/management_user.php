<div class="card card-primary card-outline">
	<div class="card-header">
		<h3 class="card-title">Management User</h3>
	</div> <!-- /.card-body -->
	<div class="card-body">
		<div class="row">
			<div class="col-md-3">
				<button type="button" class="btn btn-success" id="add_new"><i class="fa fa-plus"></i> Tambah Data</button>
			</div>
			<div class="col-md-12" style="margin-top: 20px;">
				<table class="table table-bordered" id="tb-user">
					<thead>
						<tr>
							<th>#</th>
							<th>Username</th>
              <th>Profile Name</th>
              <th>Bahasa</th>
							<th>Status</th>
              <th>Login Terakhir</th>
							<th>Tools</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-change">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
              	<h4 class="modal-title">Update User</h4>
              	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
              	</button>
            </div>
            <div class="modal-body">
              	<form id="form-change" class="row">
              		<div class="col-md-12">
              			<div class="form-group">
              				<label for="">Username </label>
              				<input type="text" name="username-change" id="username-change" class="form-control" readonly="readonly">
              			</div>
                    
                    <div class="form-group">
                      <label for="">Profile Name </label>
                      <input type="text" name="profile-name-change" id="profile-name-change" class="form-control" required="required">
                    </div>
                    
                    <div class="form-group">
                      <label for="">Bahasa </label>
                      <select name="bahasa-change" id="bahasa-change" class="form-control">
                        <option value="english">English</option>
                        <option value="japan">Japan</option>
                      </select>
                    </div>

              			<div class="form-group">
              				<label for="">Password </label>
              				<input type="text" id="password-change" name="password-change" class="form-control">
              			</div>

              			<div class="form-group text-right">
              				<button class="btn btn-primary">Simpan</button>
              			</div>
              		</div>
              	</form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal --> 

<div class="modal fade" id="modal-add">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
              	<h4 class="modal-title">Tambah User</h4>
              	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
              	</button>
            </div>
            <div class="modal-body">
              	<form id="form-data" class="row">
              		<div class="col-md-12">
              			<div class="form-group">
              				<label for="">Username </label>
              				<input type="text" name="username-add" id="username-add" class="form-control" required="required">
              			</div>

                    <div class="form-group">
                      <label for="">Profile Name </label>
                      <input type="text" name="profile-name" id="profile-name" class="form-control" required="required">
                    </div>
                    
                    <div class="form-group">
                      <label for="">Bahasa </label>
                      <select name="bahasa" id="bahasa" class="form-control">
                        <option value="english">English</option>
                        <option value="japan">Japan</option>
                      </select>
                    </div>

              			<div class="form-group">
              				<label for="">Password </label>
              				<input type="text" id="password-add" name="password-add" class="form-control" required="required">
              			</div>

              			<div class="form-group text-right">
              				<button class="btn btn-primary">Simpan</button>
              			</div>
              		</div>
              	</form>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal --> 