$(() => {
	let startDate = moment();
	let endDate = moment();
	let date = $("#date");
	let btnTampil = $("#btn-tampil");
	let btnDownload = $("#btn-download");

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

	async function loadReportSuhu() {
		blockUI();

		await $.get(`${baseUrl}ajax/Ajax/tb_report_suhu?startdate=${startDate.format('YYYY-MM-DD')}&enddate=${endDate.format('YYYY-MM-DD')}`)
				.done(function(data) {
					unBlockUI();

					$("#laporan-suhu").html(data);
					let tbDeteksi = $("#tb-suhu").DataTable({
						processing: true,
						serverSide: false,
						lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]]
					});

					let columnCount = tbDeteksi.columns().header().length;
					tbDeteksi.order([columnCount - 1, 'desc']).draw();
				})
				.fail(function(err) {
					unBlockUI();

					toastr.error('Terjadi kesalahan saat memuat data');
				});
	}

	loadReportSuhu();

	btnTampil.click(function() {
		loadReportSuhu();
	});

	btnDownload.click(function() {
		window.location.href = `${baseUrl}Excel/laporan_suhu?startdate=${startDate.format('YYYY-MM-DD')}&enddate=${endDate.format('YYYY-MM-DD')}`;
	});
});