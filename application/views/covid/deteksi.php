<div class="card card-primary card-outline">
	<div class="card-header">
		<h3 class="card-title"><?= $title ?></h3>
	</div> <!-- /.card-body -->
	<div class="card-body">
		<div class="row">
			<div class="col-md-3">
				<div class="form-group">
	                <div class="input-group date" data-target-input="nearest">
	                    <input type="text" class="form-control datetimepicker-input" id="date"/>
	                    <div class="input-group-append" data-toggle="datetimepicker">
	                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
	                    </div>
	                </div>
	            </div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<button class="btn btn-primary" id="btn-tampil"><i class="fas fa-search"></i> <?= lang('btn_show'); ?></button>
					<button class="btn btn-success" id="btn-download"><i class="fas fa-download"></i> <?= lang('btn_download'); ?></button>
				</div>
			</div>

			<div class="col-md-12">
				<div id="deteksi-personal"></div>
			</div>
		</div>
	</div>
</div>


<div id="modal-form" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
        	<div class="modal-header">
                <h5 class="modal-title">
                	<span id="nama-detail"></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                	<div class="col-md-8">
						<div id="jawaban-survey"></div>
					</div>
					<div class="col-md-4">
						<div id="map-survey"></div>
					</div>
                </div>	
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>