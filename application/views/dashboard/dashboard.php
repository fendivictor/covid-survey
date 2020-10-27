<section class="content">
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-3">
				<div class="form-group">
	                <div class="input-group date" data-target-input="nearest">
	                    <input type="text" class="form-control datetimepicker-input" id="date" autocomplete="off" />
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
			<div class="col-md-6">
				<canvas class="chart" id="risk-chart" style="min-height: 450px; height: 450px; max-height: 450px; max-width: 100%;"></canvas>
			</div>
			<div class="col-md-6">
				<h6 class="text-center">10 Orang Resiko Tinggi</h6>
				<div class="table-responsive">
					<table class="table table-striped" id="tb-high-risk">
						<thead>
							<tr>
								<th>#</th>
								<th>NIK</th>
								<th>Nama</th>
								<th>Line</th>
								<th>Point</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
			<div class="col-md-12">
				<canvas class="chart" id="perline-chart" style="min-height: 450px; height: 600px; max-height: 600px; max-width: 100%; margin-bottom: 40px;"></canvas>
			</div>

			<div class="col-md-12">
				<h6 class="text-center">Peta Persebaran Karyawan dengan Resiko Tinggi</h6>
				<div id="map" style="height: 500px; margin-top: 20px;"></div>
			</div>
    	</div>	
    </div>
</section>


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