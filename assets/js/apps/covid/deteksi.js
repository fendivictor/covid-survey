$(() => {
	let startDate = moment();
	let endDate = moment();
	let date = $("#date");
	let btnTampil = $("#btn-tampil");
	let btnDownload = $("#btn-download");
	let modalForm = $("#modal-form");
	let jawabanSurvey = $("#jawaban-survey");

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

	loadDeteksiPersonal();

	btnTampil.click(function() {
		loadDeteksiPersonal();
	});

	btnDownload.click(function() {
		window.location.href = `${baseUrl}Excel/deteksi_mandiri?startdate=${startDate.format('YYYY-MM-DD')}&enddate=${endDate.format('YYYY-MM-DD')}`;
	});


	$(document).on('click', '.showDetailSurvey', function() {
		let nik = $(this).data('nik');
		let nama = $(this).data('nama');
		let tgl = $(this).data('date');

		modalForm.modal('show');
		$("#nama-detail").html(nama);
		$("#map-survey").html('');

		$.get(`${baseUrl}ajax/Ajax/detail_question?date=${tgl}&nik=${nik}`)
			.done(function(data) {
				jawabanSurvey.html(data);

				$.get(`${baseUrl}ajax/Ajax/get_survey_coordinates?date=${tgl}&nik=${nik}`)
					.done(function(data) {
						let lat = data.lat;
						let long = data.long;

						if (lat != '' && long != '') {
							$("#map-survey").html("<div id='map-detail' style='height: 450px;'></div>")

							let map = L.map('map-detail').setView([lat, long], 15);

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
	});
});