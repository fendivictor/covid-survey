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
			<div class="col-md-12">
				<div class="form-group">
					<?php 
						if ($pertanyaan) {
							foreach ($pertanyaan as $row) {
								echo '

									<div class="form-check">
			                          	<input class="form-check-input" type="checkbox" name="pertanyaan" value="'.$row->id.'">
			                          	<label class="form-check-label">'.$row->pertanyaan.'</label>
			                        </div>

								';
							}
						}
					?>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<button class="btn btn-primary" id="btn-tampil"><i class="fas fa-search"></i> <?= lang('btn_show'); ?></button>
					<!-- <button class="btn btn-success" id="btn-download"><i class="fas fa-download"></i> <?= lang('btn_download'); ?></button> -->
				</div>
			</div>

			<div class="col-md-12">
				<div id="report-survey"></div>
			</div>
		</div>
	</div>
</div>