<div class="card card-primary card-outline">
	<div class="card-header">
		<h3 class="card-title"><?= $title ?></h3>
	</div> <!-- /.card-body -->
	<div class="card-body">
		<div class="row" style="margin-top: 20px;">
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
				</div>
			</div>

			<div class="col-md-12">
				<div id="report-header"></div>
			</div>

			<div class="col-md-12" style="margin-top: 50px;">
				<div id="report-detail"></div>
			</div>

			<div class="col-md-8" style="margin-top: 50px;">
				<div id="jawaban-survey"></div>
			</div>
			<div class="col-md-4" style="margin-top: 50px;">
				<div id="map-survey"></div>
			</div>
		</div>
	</div>
</div>