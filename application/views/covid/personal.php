<div class="card card-primary card-outline">
	<div class="card-header">
		<h3 class="card-title"><?= $title ?></h3>
	</div> <!-- /.card-body -->
	<div class="card-body">
		<div class="row">
			<div class="col-md-12 text-right mb-4">
				<button id="add-new" type="button" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Tambah Data</button>
			</div>
			<div class="col-md-12">
				<div class="table-responsive">
					<table class="table table-striped table-hovered" id="tb-data" style="width: 100%;">
						<thead>
							<tr>
								<th>#</th>
								<th>NIK</th>
								<th>Nama</th>
								<th>No HP</th>
								<th>Line</th>
								<th>Team</th>
								<th>Jabatan</th>
								<th>Gender</th>
								<th>Tanggal Lahir</th>
								<th>
									<i class="fa fa-bars"></i>
								</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-form" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        	<div class="modal-header">
                <h5 class="modal-title">
                	Form Karyawan
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" id="form-data">
	            <div class="modal-body">
	        		<div class="form-group">
	        			<input type="hidden" name="id" id="id">
	        			<label for="nik">NIK <span class="text-danger">*</span></label>
	        			<input type="text" class="form-control" name="nik" id="nik" autocomplete="off" required="required">
	        		</div>
	        		<div class="form-group">
	        			<label for="nama">Nama <span class="text-danger">*</span></label>
	        			<input type="text" class="form-control" name="nama" id="nama" autocomplete="off" required="required">
	        		</div>
	        		<div class="form-group">
	        			<label for="tgllahir">Tgl Lahir <span class="text-danger">*</span></label>
	        			<input type="text" class="form-control" name="tgllahir" id="tgllahir" autocomplete="off" required="required">
	        		</div>
	        		<div class="form-group">
	        			<label for="nama">No HP </label>
	        			<input type="text" class="form-control" name="no_hp" id="no_hp" autocomplete="off">
	        		</div>
	        		<div class="form-group">
	        			<label for="line">Line <span class="text-danger">*</span></label>
	        			<input type="text" class="form-control" name="line" id="line" autocomplete="off" required="required">
	        		</div>
	        		<div class="form-group">
	        			<label for="team">Team <span class="text-danger">*</span></label>
	        			<input type="text" class="form-control" name="team" id="team" autocomplete="off" required="required">
	        		</div>
	        		<div class="form-group">
	        			<label for="jabatan">Jabatan <span class="text-danger">*</span></label>
	        			<input type="text" class="form-control" name="jabatan" id="jabatan" autocomplete="off" required="required">
	        		</div>
	        		<div class="form-group">
	        			<label for="gender">Gender <span class="text-danger">*</span></label>
	        			<select name="gender" id="gender" class="form-control" required="required">
	        				<option value="Pria">Pria</option>
	        				<option value="Wanita">Wanita</option>
	        			</select>
	        		</div>
	            </div>
	            <div class="modal-footer justify-content-between">
              		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              		<button type="submit" class="btn btn-primary">Save changes</button>
            	</div>
            </form>
        </div>
    </div>
</div>