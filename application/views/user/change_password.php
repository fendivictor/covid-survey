<div class="card card-primary card-outline">
	<div class="card-header">
		<h3 class="card-title"><?= lang('form_change_password') ?></h3>
	</div> <!-- /.card-body -->
	<div class="card-body">
		<form action="#" class="row" id="form-data">
			<div class="col-md-3">
				<label for=""><?= lang('old_password'); ?></label>
				<input type="password" name="password_lama" id="password_lama" class="form-control">
			</div>
			<div class="col-md-12"></div>
			<div class="col-md-3">
				<label for=""><?= lang('new_password'); ?></label>
				<input type="password" name="password_baru" id="password_baru" class="form-control">
			</div>
			<div class="col-md-12"></div>
			<div class="col-md-3">
				<label for=""><?= lang('retype_password'); ?></label>
				<input type="password" name="password_confirm" id="password_confirm" class="form-control">
			</div>
			<div class="col-md-12"></div>
			<div class="col-md-3" style="margin-top: 20px;">
				<button class="btn btn-success"><?= lang('btn_save'); ?></button>
			</div>
		</form>
	</div>
</div>