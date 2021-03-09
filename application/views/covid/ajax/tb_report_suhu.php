<div class="table-responsive">
	<table class="table table-striped table-hovered" id="tb-suhu">
		<thead>
			<tr>
				<th rowspan="2">NIK</th>
				<th rowspan="2">Nama</th>
				<th rowspan="2">No HP</th>
				<th rowspan="2">Line</th>
				<th colspan="<?= count($date); ?>" class="text-center">Tanggal</th>
				<th rowspan="2" class="text-center">Rata-rata</th>
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
								<td>'.$row->no_hp.'</td>
								<td>'.$row->line.'</td>
						';

						$total = 0;
						$jumlah = 0;
						if ($date) {
							foreach ($date as $val) {
								$suhu = $this->Report_Model->get_suhu($row->nik, $val);

								echo '<td class="text-center">'.$suhu.'</td>';

								$total += $suhu;
								$jumlah += ($suhu > 0) ? 1 : 0;
							}
						}

						$avg = ($total > 0) ? ($total / $jumlah) : 0;

						echo '<td class="text-center">'.number_format($avg, 2, '.', ',').'</td>';
						echo '</tr>';
					}
				}
			?>
		</tbody>
	</table>
</div>	