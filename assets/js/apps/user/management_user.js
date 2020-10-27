$(document).ready(function() {
	var dtTable = $("#tb-user").DataTable({
		processing: true,
		ajax: {
			url: baseUrl + 'ajax/User/dt_user'
		}
	});

	$(document).on('click', '.change-password', function() {
		let username = $(this).data('username');

		$.get(baseUrl + 'ajax/User/user_detail?username=' + username, function(data) {
			let response = JSON.parse(data);

			$("#profile-name-change").val(response.profile_name);
			$("#bahasa-change").val(response.language);
		});

		$("#username-change").val(username);
		$("#modal-change").modal('show');
	});

	$("#form-change").submit(function(e) {
		e.preventDefault();
		let formData = new FormData($(this)[0]);
		let config = {
			url: baseUrl + 'ajax/User/ganti_manual',
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
					document.getElementById('form-change').reset();
					$("#modal-change").modal('hide');
				}

				unBlockUI();
			})
			.fail(function(e) {
				toastr.error('Terjadi kesalahan saat menyimpan data');

				unBlockUI();
			});

		return false;
	});

	$("#add_new").click(function() {
		$("#modal-add").modal('show');
	});

	$("#form-data").submit(function(e) {
		e.preventDefault();
		let formData = new FormData($(this)[0]);
		let config = {
			url: baseUrl + 'ajax/User/add_user',
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
					$("#modal-add").modal('hide');
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