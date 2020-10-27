$(document).ready(function() {
	$("#form-data").submit(function(e) {
		e.preventDefault();
		let formData = new FormData($(this)[0]);
		let config = {
			url: baseUrl + 'ajax/User/change_password',
			dataType: 'json',
			type: 'post',
			contentType: false,
			processData: false,
			data: formData
		}

		blockUI();

		$.ajax(config)
			.done(function(data) {
				(data.status == 1) ? toastr.success(data.message) : Swal.fire('', data.message, 'error');

				if (data.status == 1) {
					document.getElementById('form-data').reset();
				}

				unBlockUI();
			})
			.fail(function(e) {
				toastr.error('Terjadi kesalahan saat menyimpan data');

				unBlockUI();
			});

		return false;
	});
});