$(() => {
	let tbData = $("#tb-data");
	let addNew = $("#add-new");
	let modalForm = $("#modal-form");
	let startDate = moment();
	let date = $("#tgllahir");
	let formData = $("#form-data");

	let dtTable = tbData.DataTable({
		processing: true,
		serverSide: false,
		ajax: {
			url: `${baseUrl}ajax/Datatable/dt_personal`
		},
		columnDefs: [
			{targets: 0, data: 'no'},
			{targets: 1, data: 'nik'},
			{targets: 2, data: 'nama'},
			{targets: 3, data: 'no_hp'},
			{targets: 4, data: 'line'},
			{targets: 5, data: 'team'},
			{targets: 6, data: 'jabatan'},
			{targets: 7, data: 'gender'},
			{targets: 8, data: 'tanggal_lahir'},
			{targets: 9, data: 'tools', className: 'text-center', orderable: false}
		]
	});

	addNew.click(function() {
		modalForm.modal('show');
		$("#form-data").trigger('reset');
		$("#nik").attr('readonly', false);

		$("#id").val('');
	});

	date.daterangepicker({
		singleDatePicker: true,
		showDropdowns: true,
		locale: {
			format: 'DD/MM/YYYY'
		}
	}, function(start, end) {
		startDate = start;
	});

	$(document).on('click', '.btn-edit', function() {
		let nik = $(this).data('nik');
		modalForm.modal('show');
		blockModal();

		$.get(`${baseUrl}ajax/Ajax/personal_id?nik=${nik}`)
			.done(function(data) {
				unBlockModal();
				if (data) {
					$("#nik").attr('readonly', true);
					$("#id").val(data.id);
					$("#nik").val(data.nik);
					$("#nama").val(data.nama);
					$("#no_hp").val(data.no_hp);
					$("#tgllahir").data('daterangepicker').setStartDate(moment(data.tanggal_lahir).format('DD/MM/YYYY'));
					$("#line").val(data.line);
					$("#team").val(data.team);
					$("#jabatan").val(data.jabatan);
					$("#gender").val(data.gender);
				}
			})
			.fail(function(err) {
				unBlockModal();
				toastr.error('Terjadi kesalahan saat memuat data');
			});
	});

	$(document).on('click', '.btn-danger', function() {
		let nik = $(this).data('nik');

		swalWithBootstrapButtons.fire({
		  	title: 'Konfirmasi',
		  	text: "Apakah Yakin akan menghapus data ?",
		  	showCancelButton: true,
		  	confirmButtonText: 'Ya, saya yakin',
		  	cancelButtonText: 'Tidak',
		  	reverseButtons: true
		}).then((result) => {
		  	if (result.value) {
		  		blockModal();

				$.post(baseUrl + 'ajax/Ajax/delete_personal', {nik: nik})
				.done(function(data) {
					(data.status == 1) ? toastr.success(data.message) : Swal.fire('', data.message, 'error');

					if (data.status == 1) {
						dtTable.ajax.reload(null, false);
					}

					unBlockModal();
				})
				.fail(function(err) {
					toastr.error('Terjadi kesalahan saat menghapus data');
					unBlockModal();
				});
		  	} 
		});
	});

	formData.submit(function(e) {
		e.preventDefault();
		blockModal();

		let config = {
			url: `${baseUrl}ajax/Ajax/add_personal`,
			data: new FormData($(this)[0]),
			dataType: 'json',
			type: 'post',
			contentType: false,
			processData: false
		}

		$.ajax(config)
			.done(function(data) {
				unBlockModal();

				(data.status == 1) ? toastr.success(data.message) : Swal.fire('', data.message, 'error');

				if (data.status == 1) {
					modalForm.modal('hide');
					dtTable.ajax.reload(null, false);
				}
			})
			.fail(function(err){
				unBlockModal();
				toastr.error('Terjadi kesalahan saat memuat data');
			});

		return false;
	});
});