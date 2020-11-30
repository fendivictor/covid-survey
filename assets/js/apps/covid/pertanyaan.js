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

	function loadData() {
		blockUI();

		var pertanyaan = [];
        $.each($("input[name='pertanyaan']:checked"), function(){
            pertanyaan.push($(this).val());
        });

        let data = {
        	datestart: startDate.format('YYYY-MM-DD'),
        	dateend: endDate.format('YYYY-MM-DD'),
        	pertanyaan: pertanyaan
        }

        $.get(`${baseUrl}ajax/Ajax/get_data_survey_by_pertanyaan`, data) 
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