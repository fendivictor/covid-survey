$(() => {
	let startDate = moment();
	let endDate = moment();
	let date = $("#date");
	let btnTampil = $("#btn-tampil");

	date.daterangepicker({
		startDate: startDate, 
		endDate: endDate,
		locale: {
			format: 'DD/MM/YYYY'
		}
	}, function(start, end) {
		startDate = start;
		endDate = end;
	});

	async function loadDeteksiPersonal() {
		blockUI();

		await $.get(`${baseUrl}ajax/Ajax/tb_deteksi_personal?startdate=${startDate.format('YYYY-MM-DD')}&enddate=${endDate.format('YYYY-MM-DD')}`)
				.done(function(data) {
					unBlockUI();

					$("#deteksi-personal").html(data);
					let tbDeteksi = $("#tb-deteksi").DataTable({
						processing: true,
						serverSide: false,
						lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
						buttons: [{
					        extend: "excel",
					        className: "btn yellow btn-outline ",
					        title: 'Data Deteksi Mandiri',
					        text: '<i class="fa fa-file-excel"></i> Export to Excel'
					    }],
					    dom: "<'row'<'col-md-12 mb-4'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"
					});

					let columnCount = tbDeteksi.columns().header().length;
					tbDeteksi.order([columnCount - 1, 'desc']).draw();
				})
				.fail(function(err) {
					unBlockUI();

					toastr.error('Terjadi kesalahan saat memuat data');
				});
	}

	loadDeteksiPersonal();

	btnTampil.click(function() {
		loadDeteksiPersonal();
	});
});