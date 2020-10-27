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
						},
						plugins: {
							datalabels: {
								color: 'white',
								font: {
									size: 20,
									weight: 'bold'
								},
								formatter: function(value, context) {
									return value + ' %'
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

	// makeLineChart();

	btnTampil.click(function() {
		makeRiskChart();
		// makeLineChart();
		loadPopulationMap();
		dtHighRisk.ajax.url(`${baseUrl}ajax/Datatable/tb_high_risk_person?date=${startDate.format('YYYY-MM-DD')}`).load();
		tbLine.ajax.url(`${baseUrl}ajax/Datatable/dt_deteksi_mandiri_line?date=${startDate.format('YYYY-MM-DD')}`).load();
	});

	let dtHighRisk = tbHighRisk.DataTable({
		processing: true,
		serverSide: false,
		ordering: false,
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

	let tbLine = $("#dt-line").DataTable({
		processing: true,
		serverSide: false,
		ajax: {
			url: `${baseUrl}ajax/Datatable/dt_deteksi_mandiri_line?date=${startDate.format('YYYY-MM-DD')}`
		},
		buttons: [{
	        extend: "excel",
	        title: 'Data Kelompok Resiko per Line',
	        className: "btn yellow btn-outline ",
	        text: '<i class="fa fa-file-excel"></i> Export to Excel'
	    }],
	    dom: "<'row'<'col-md-12 mb-4'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
		columnDefs: [
			{targets: 0, data: 'line'},
			{targets: 1, data: 'total', className: 'text-center'},
			{targets: 2, data: 'sdh_survey', className: 'text-center'},
			{targets: 3, data: 'blm_survey', className: 'text-center'},
			{targets: 4, data: 'rendah', className: 'text-center'},
			{targets: 5, data: 'rendah_prc', className: 'text-center'},
			{targets: 6, data: 'sedang', className: 'text-center'},
			{targets: 7, data: 'sedang_prc', className: 'text-center'},
			{targets: 8, data: 'tinggi', className: 'text-center'},
			{targets: 9, data: 'tinggi_prc', className: 'text-center'}
		],
		footerCallback: function(row, data, start, end, display) {
            var api = this.api(), data;

            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            let totalKaryawan = api.column(1).data().reduce(function(a, b) {
            	return intVal(a) + intVal(b);
            }, 0);

            totalKaryawan = totalKaryawan.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $( api.column( 1 ).footer() ).html(totalKaryawan);


            let totalSdhSurvey = api.column(2).data().reduce(function(a, b) {
            	return intVal(a) + intVal(b);
            }, 0);

            totalSdhSurvey = totalSdhSurvey.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $( api.column( 2 ).footer() ).html(totalSdhSurvey);

            let totalBlmSurvey = api.column(3).data().reduce(function(a, b) {
            	return intVal(a) + intVal(b);
            }, 0);

            totalBlmSurvey = totalBlmSurvey.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $( api.column( 3 ).footer() ).html(totalBlmSurvey);

            let totalRendah = api.column(4).data().reduce(function(a, b) {
            	return intVal(a) + intVal(b);
            }, 0);

            totalRendah = totalRendah.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $( api.column( 4 ).footer() ).html(totalRendah);

            let totalSedang = api.column(6).data().reduce(function(a, b) {
            	return intVal(a) + intVal(b);
            }, 0);

            totalSedang = totalSedang.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $( api.column( 6 ).footer() ).html(totalSedang);

            let totalTinggi = api.column(8).data().reduce(function(a, b) {
            	return intVal(a) + intVal(b);
            }, 0);

            totalTinggi = totalTinggi.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");

            $( api.column( 8 ).footer() ).html(totalTinggi);
        }
	});

});