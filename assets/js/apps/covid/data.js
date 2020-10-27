$(() => {
	let startDate = moment();
	let endDate = moment();
	let date = $("#date");
	let reportHeader = $("#report-header");
	let reportDetail = $("#report-detail");
	let jawabanSurvey = $("#jawaban-survey");
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

	let tbHeader = '';
	const loadTbReport = async () => {
		blockUI();

		$.get(`${baseUrl}ajax/Ajax/summary_report?datestart=${startDate.format('YYYY-MM-DD')}&dateend=${endDate.format('YYYY-MM-DD')}`)
			.done(function(data) {
				unBlockUI();

				reportHeader.html(data);
				tbHeader = $('#dt-table').DataTable({
					searching: false,
					paging: false,
					info: false,
					buttons: [{
				        extend: "excel",
				        className: "btn yellow btn-outline ",
				        title: 'Summary Report',
				        text: '<i class="fa fa-file-excel"></i> Export to Excel'
				    }],
				    dom: "<'row'<'col-md-12 mb-4'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"
				});

				$("#dt-table tbody").on('click', 'tr', function() {
					if (! $(this).hasClass('selected') ) {
						tbHeader.$('tr.selected').removeClass('selected');
			            $(this).addClass('selected');
			        }
				});
			})
			.fail(function(err) {
				toastr.error('Terjadi kesalahan saat memuat data');
				unBlockUI();
			});
	}

	loadTbReport();

	btnTampil.click(function() {
		loadTbReport();
		reportDetail.html('');
		jawabanSurvey.html('');
		$("#map-survey").html('');
	});

	$(document).on('click', '.show-detail', function() {
		let date = $(this).data('date');
		let category = $(this).data('category');

		blockUI();

		$.get(`${baseUrl}ajax/Ajax/category_report?category=${category}&date=${date}`)
			.done(function(data) {
				unBlockUI();

				reportDetail.html(data);
				jawabanSurvey.html('');
				$("#map-survey").html('');

				let tbDetail = $('#dt-detail').DataTable({
					info: false,
					buttons: [{
				        extend: "excel",
				        title: 'Detail Summary Report',
				        className: "btn yellow btn-outline ",
				        text: '<i class="fa fa-file-excel"></i> Export to Excel'
				    }],
				    dom: "<'row'<'col-md-12 mb-4'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>"
				});

				$("#dt-detail tbody").on('click', 'tr', function() {
					blockUI();
					$("#map-survey").html('');
					let data = tbDetail.row(this).data();
					let tgl = data[1];
					let nik = data[2];

					$.get(`${baseUrl}ajax/Ajax/detail_question?date=${tgl}&nik=${nik}`)
						.done(function(data) {
							jawabanSurvey.html(data);

							$.get(`${baseUrl}ajax/Ajax/get_survey_coordinates?date=${tgl}&nik=${nik}`)
								.done(function(data) {
									let lat = data.lat;
									let long = data.long;

									if (lat != '' && long != '') {
										$("#map-survey").html("<div id='map' style='height: 450px;'></div>")

										let map = L.map('map').setView([lat, long], 15);

										L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
										    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
										}).addTo(map);

										L.marker([lat, long]).addTo(map);
									}

									unBlockUI();
								})
								.fail(function(err) {
									toastr.error('Terjadi kesalahan saat memuat data');

									unBlockUI();
								});

							unBlockUI();
						})
						.fail(function(err) {
							toastr.error('Terjadi kesalahan saat memuat data');

							unBlockUI();
						});

					if (! $(this).hasClass('selected') ) {
						tbDetail.$('tr.selected').removeClass('selected');
			            $(this).addClass('selected');
			        }
				});
			})
			.fail(function(err) {
				toastr.error('Terjadi kesalahan saat memuat data');
				unBlockUI();
			});
	});
});