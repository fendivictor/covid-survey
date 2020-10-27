$(document).ready(function(){
	
	var tbUser = $("#tb_user").DataTable({
		processing: true,
		ajax: {
			url: baseUrl + 'ajax/Menu/dt_user'
		}
	});

	var tbData = $("#tb-data").DataTable({
		processing: true,
		ajax: {
			url: baseUrl + 'ajax/Ajax/get_privilege?username='
		},
		columnDefs: [
			{targets: 0, data: 'check'},
			{targets: 1, data: 'menu'},
			{targets: 2, data: 'tools'}
		],
		lengthMenu: [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
		pageLength: -1
	})

	$("#btn-cari").click(function() {
		$("#modal-sm").modal('show');
	});

	$(document).on('click', '.select-user', function() {
		username = $(this).data('username');
		$("#username").val(username);
		$("#modal-sm").modal('hide');

		tbData.ajax.url(baseUrl + 'ajax/Ajax/get_privilege?username=' + username).load();
	});

	$("#save").click(function() {
		let username = $("#username").val();
		let arrPrv = [];

		$("input[name='tools-checkbox']:checked").each(function() {
			let tools = $(this).data('tools');

			arrPrv.push(tools);
		});


		$.post(baseUrl + 'ajax/Ajax/add_privilege', {
			'username': username,
			'privilege': arrPrv
		}, function(data) {
			let response = JSON.parse(data);

			if (response.status) {
				toastr.success(response.message);
				tbData.ajax.url(baseUrl + 'ajax/Ajax/get_privilege?username=' + username).load();
			} else {
				toastr.error(response.message);
			}
		});
	});
});