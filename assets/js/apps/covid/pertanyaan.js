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
					lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]]
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

	btnDownload.click(function(e) {
		var pertanyaan = [];
        $.each($("input[name='pertanyaan']:checked"), function(){
            pertanyaan.push($(this).val());
        });

		window.location.href = `${baseUrl}Excel/survey_result_by_question?pertanyaan=${pertanyaan}&datestart=${startDate.format('YYYY-MM-DD')}&dateend=${endDate.format('YYYY-MM-DD')}`;
	});
});