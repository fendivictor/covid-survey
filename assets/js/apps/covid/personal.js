$(() => {
	let tbData = $("#tb-data");

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
			{targets: 3, data: 'line'},
			{targets: 4, data: 'team'},
			{targets: 5, data: 'jabatan'},
			{targets: 6, data: 'gender'},
			{targets: 7, data: 'tanggal_lahir'}
		]
	})
});