$(() => {
	blockUI();

	const TotalNotif = $.get(baseUrl + 'ajax/Ajax/get_total_notification')
		.done(function(data) {
			let response = JSON.parse(data);

			$(".notif-counter-value").html(response.total);

			unBlockUI();
		})
		.fail(function(error) {
			toastr.error('An error occurred while fetching data');

			unBlockUI();
		});


	const loadNotif = $.get(baseUrl + 'ajax/Ajax/get_unread_notification')
		.done(function(data) {
			let response = JSON.parse(data);
			let element = '';
			if (response.data.length > 0) {
				for (i in response.data) {
					element += `
						<div class="dropdown-divider"></div>
                        <a href="${response.data[i].url}" class="dropdown-item">
                        <label>[${response.data[i].style} ${response.data[i].brand}]</label> <br />
                        ${response.data[i].activity} - ${response.data[i].value}
                        <div class="text-right text-muted text-sm">${response.data[i].timestamp}</div>
                        </a>`;
				}
			} else {
				element += `
					<div class="dropdown-divider"></div>
					<a href="javascript:;" class="dropdown-item">
                    <label>No Notification</label>
                    </a>`;
			}

			$("#notification-result").html(element);

			unBlockUI();
		})
		.fail(function(error) {
			toastr.error('An error occurred while fetching data');

			unBlockUI();
		});

	$(document).on('click', '#notif-bell', function(e) {
		$.post(baseUrl + 'ajax/Ajax/read_all_notif')
			.done(function(data) {
				unBlockUI();
			})
			.fail(function(error) {
				toastr.error('An error occurred while update data');

				unBlockUI();
			});
	});
});