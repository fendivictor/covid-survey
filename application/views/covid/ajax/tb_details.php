<div class="table-responsive">
	<table class="table table-stripped" id="dt-detail">
		<thead>
			<tr>
				<th>Kategori</th>
				<th>Tanggal</th>
				<th>NIK</th>
				<th>Nama</th>
				<th>Point</th>
			</tr>
		</thead>
		<tbody>
			<?php  
				if ($report) {
					foreach ($report as $row) {
						echo '
						<tr>
							<td>'.$row->judul.'</td>
							<td>'.$row->tanggal.'</td>
							<td>'.$row->nik.'</td>
							<td>'.$row->user_insert.'</td>
							<td>'.$row->nilai_resiko.'</td>
						</tr> ';
					}
				}
			?>
		</tbody>
	</table>
</div>