<div class="card card-primary card-outline">
	<div class="card-header">
		<h3 class="card-title">Privilege User</h3>
	</div> <!-- /.card-body -->
	<div class="card-body">
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
					<label for="">Username: </label>
					<div class="input-group input-group-md">
	                  	<input type="text" class="form-control" name="username" id="username" readonly="readonly" placeholder="Username">
	                  	<span class="input-group-append">
	                    	<button type="button" class="btn btn-info btn-flat" id="btn-cari">
	                    		<i class="fa fa-search"></i>
	                    	</button>
	                  	</span>
	                </div>
				</div>
			</div>
			<div class="col-md-12">
				<div class="text-right" style="margin-bottom: 20px;">
					<button type="button" id="save" class="btn btn-primary"><i class="fa fa-check"></i> <?= lang('btn_save') ?></button>
				</div>
				<table class="table table-striped" id="tb-data" style="width: 100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Menu</th>
							<th>Tools</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal-sm">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
              	<h4 class="modal-title">Data User</h4>
              	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                	<span aria-hidden="true">&times;</span>
              	</button>
            </div>
            <div class="modal-body">
              	<table class="table table-striped table-hovered" id="tb_user" style="width: 100%">
              		<thead>
              			<tr>
              				<th>#</th>
              				<th>Profile Name</th>
              				<th>Username</th>
              			</tr>
              		</thead>
              	</table>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->