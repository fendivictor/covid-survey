$(() => {
	let startDate = moment();
	let date = $("#date");
	let btnTampil = $("#btn-tampil");
	let btnDownload = $("#btn-download");

	date.daterangepicker({
		singleDatePicker: true,
		showDropdowns: true,
		locale: {
			format: 'DD/MM/YYYY'
		}
	}, function(start, end) {
		startDate = start;
	});

	function loadData() {
		blockUI();

		$.get(`${baseUrl}ajax/Ajax/get_data_survey_by_tgl?tgl=${startDate.format('YYYY-MM-DD')}`)
			.done(function(data) {
				unBlockUI();

				$("#report-survey").html(data);
				let tbData = $("#tb-data").DataTable({
					processing: true,
					serverSide: false,
					lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
					buttons: [{
				        extend: "excel",
				        className: "btn yellow btn-outline ",
				        title: 'Laporan Survey',
				        text: '<i class="fa fa-file-excel"></i> Export to Excel'
				    }],
				    dom: "<'row'<'col-md-12 mb-4'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"
				});

				let columnCount = tbData.columns().header().length;
				tbData.order([columnCount - 1, 'desc']).draw();
			})
			.fail(function(err) {
				unBlockUI();

				toastr.error('Terjadi kesalahan saat memuat data');
			});
	}

	btnTampil.click(function() {
		loadData();
	});
});