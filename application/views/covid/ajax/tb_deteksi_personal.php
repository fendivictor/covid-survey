<div class="table-responsive">
	<table class="table table-striped table-hovered" id="tb-deteksi">
		<thead>
			<tr>
				<th rowspan="2">NIK</th>
				<th rowspan="2">Nama</th>
				<th rowspan="2">Line</th>
				<th colspan="<?= count($date); ?>" class="text-center">Tanggal</th>
				<th rowspan="2" class="text-center">Total</th>
			</tr>
			<tr>
				<?php
					if ($date) {
						foreach ($date as $row) {
							echo '<th class="text-center">'.custom_date_format($row, 'Y-m-d', 'd/m').'</th>';
						}
					}
				?>
			</tr>
		</thead>
		<tbody>
			<?php 
				if ($personal_data) {
					$no = 1;
					foreach ($personal_data as $row) {
						echo '
							<tr>
								<td>'.$row->nik.'</td>
								<td>'.$row->nama.'</td>
								<td>'.$row->line.'</td>
						';

						$total = 0;
						if ($date) {
							foreach ($date as $val) {
								$nilai = $this->Report_Model->get_daily_score($row->nik, $val);
								$level = $this->Report_Model->get_score_level($nilai);

								echo '<td class="text-center"><span class="text-'.$level.'">'.$nilai.'</span></td>';

								$total += $nilai;
							}
						}

						echo '<td class="text-center">'.$total.'</td>';
						echo '</tr>';
					}
				}
			?>
		</tbody>
	</table>
</div>	