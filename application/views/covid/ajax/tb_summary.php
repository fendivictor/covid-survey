<div class="table-responsive">
	<table class="table table-stripped" id="dt-table">
		<thead>
			<tr>
				<th>Kategori</th>
				<?php  
					if ($date) {
						foreach ($date as $row) {
							echo "<th>".custom_date_format($row, 'Y-m-d', 'd/m')."</th>";
						}
					}
				?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					Resiko Rendah
				</td>
				<?php  
					if ($rendah) {
						foreach ($rendah as $row => $val) {
							echo '<th><a href="javascript:;" class="show-detail" data-date="'.$date[$row].'" data-category="info">'.$val.'</a></th>';
						}
					}
				?>
			</tr>
			<tr>
				<td>
					Resiko Sedang
				</td>
				<?php  
					if ($sedang) {
						foreach ($sedang as $row => $val) {
							echo '<th><a href="javascript:;" class="show-detail" data-date="'.$date[$row].'" data-category="warning">'.$val.'</a></th>';
						}
					}
				?>
			</tr>
			<tr>
				<td>
					Resiko Tinggi
				</td>
				<?php  
					if ($tinggi) {
						foreach ($tinggi as $row => $val) {
							echo '<th><a href="javascript:;" class="show-detail" data-date="'.$date[$row].'" data-category="danger">'.$val.'</a></th>';
						}
					}
				?>
			</tr>
		</tbody>
	</table>
</div>