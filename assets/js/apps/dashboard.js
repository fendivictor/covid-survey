$(() => {
	let startDate = moment();
	let date = $("#date");
	let btnTampil = $("#btn-tampil");
	let tbHighRisk = $("#tb-high-risk");
	let modalForm = $("#modal-form");
	let jawabanSurvey = $("#jawaban-survey");

	date.daterangepicker({
		singleDatePicker: true,
		showDropdowns: true,
		locale: {
			format: 'DD/MM/YYYY'
		}
	}, function(start, end) {
		startDate = start;
	});

	let riskChart;
	let makeRiskChart = () => {
		blockUI();

		let chartElement = $("#risk-chart").get(0).getContext('2d');

		$.get(`${baseUrl}ajax/Api/risk_chart?date=${startDate.format('YYYY-MM-DD')}`)
			.done(function(chartData) {
				if (riskChart) {
					riskChart.destroy();
				}

				riskChart = new Chart(chartElement, {
					type: 'doughnut',
					data: chartData,
					options: {
						maintainAspectRatio: false,
				      	responsive: true,
						legend: {
							position: 'top',
						},
						title: {
							display: true,
							text: 'Hasil Deteksi Mandiri Berdasarkan Resiko'
						},
				        animation: {
							animateScale: true,
							animateRotate: true
						}
					}
				});

				unBlockUI();
			})
			.fail(function(err) {
				toastr.error('Terjadi kesalahan saat memuat data');

                unBlockUI();
			});
	}

	makeRiskChart();

	let lineChart;
	let makeLineChart = () => {
		blockUI();

		let chartElement = $("#perline-chart").get(0).getContext('2d');

		$.get(`${baseUrl}ajax/Api/line_chart?date=${startDate.format('YYYY-MM-DD')}`)
			.done(function(chartData) {
				if (lineChart) {
					lineChart.destroy();
				}

				lineChart = new Chart(chartElement, {
					type: 'line',
                    data: chartData,
                    options: {
				        maintainAspectRatio : false,
      					responsive : true,
      					datasetFill: false,
      					title: {
							display: true,
							text: 'Hasil Deteksi Mandiri Line'
						},
      					plugins: {
							datalabels: {
								borderRadius: 4,
								color: 'black',
								font: {
									weight: 'bold'
								},
								formatter: function(value, context) {
				                    return numberWithCommas(value);
				                }
							}
						},
						scales: {
				            xAxes: [{
				                ticks: {
				                    fontColor: '#000',
				                },
				                gridLines: {
				                    display: false,
				                    color: '#d0d0d0',
				                    drawBorder: false,
				                }
				            }],
				            yAxes: [{
				                ticks: {
				                    stepSize: 100,
				                    fontColor: '#000',
				                    beginAtZero: true,
				                    callback: function(value, index, values) {
							          	value = value.toString();
										value = value.split(/(?=(?:...)*$)/);
										value = value.join('.');
										return value;
							        }
				                },
				                gridLines: {
				                    display: true,
				                    color: '#d0d0d0',
				                    drawBorder: false,
				                }
				            }]
				        },
				        tooltips: {
				        	callbacks: {
				        		label: function(tooltip) {
				        			return numberWithCommas(tooltip.value);
				        		}
				        	}
				        }
				    }
				});

				unBlockUI();
			})
			.fail(function(err) {
				toastr.error('Terjadi kesalahan saat memuat data');

                unBlockUI();
			});
	}

	makeLineChart();

	btnTampil.click(function() {
		makeRiskChart();
		makeLineChart();
		loadPopulationMap();
		dtHighRisk.ajax.url(`${baseUrl}ajax/Datatable/tb_high_risk_person?date=${startDate.format('YYYY-MM-DD')}`).load();
	});

	let dtHighRisk = tbHighRisk.DataTable({
		processing: true,
		serverSide: false,
		searching: false,
		ordering: false,
		paging: false,
		info: false,
		ajax: {
			url: `${baseUrl}ajax/Datatable/tb_high_risk_person?date=${startDate.format('YYYY-MM-DD')}`
		},
		columnDefs: [
			{targets: 0, data: 'no'},
			{targets: 1, data: 'nik'},
			{targets: 2, data: 'nama'},
			{targets: 3, data: 'line'},
			{targets: 4, data: 'point'}
		]
	});


	let map = L.map('map').setView([-7.0131817, 110.4132001], 12);

	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);

	var markersLayer = new L.LayerGroup();
	markersLayer.addTo(map);

	const loadPopulationMap = () => {
		var loadPopulationData = $.ajax({
			url: `${baseUrl}ajax/Ajax/get_high_risk_population?date=${startDate.format('YYYY-MM-DD')}`,
			success: console.log("Data Loaded"),
			error: function(xhr) {
				toastr.error('Terjadi kesalahan saat memuat data');
			}
		});

		markersLayer.clearLayers();

		$.when(loadPopulationData)
			.done(function() {

				var marker;
				var locationCoor = []; 
				for (row in loadPopulationData.responseJSON) {
					var data = loadPopulationData.responseJSON[row];
					var lat = data.lat;
					var long = data.long;
					var nama = data.nama;
					var line = data.line;

					locationCoor[row] = [lat, long];
					var popup = L.popup()
					            .setLatLng([lat, long])
					            .setContent(`${nama} <br /> ${line}`);

					marker = L.marker([lat, long], {
			            clickable: true
			        }).bindPopup(popup, {showOnMouseOver:true});

			        markersLayer.addLayer(marker); 
				}

				var bounds = new L.latLngBounds(locationCoor);
			    map.fitBounds(bounds, {padding: [50,50]});
			})
			.fail(function(err) {
				toastr.error('Terjadi kesalahan saat memuat data');
			});
	}

	loadPopulationMap();

	$(document).on('click', '.show-details', function() {
		let nik = $(this).data('nik');
		let nama = $(this).data('nama');
		let tgl = startDate.format('YYYY-MM-DD');

		modalForm.modal('show');
		$("#nama-detail").html(nama);

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