<div class="table-responsive">
	<table class="table table-hovered table-striped" id="tb-data">
		<thead>
			<tr>
				<th rowspan="2">#</th>
				<th rowspan="2">NIK</th>
				<th rowspan="2">Nama</th>
				<th rowspan="2">Line</th>
				<th rowspan="2">Team</th>
				<th colspan="<?= count($pertanyaan) ?>">Pertanyaan</th>
				<th rowspan="2">Skor</th>
			</tr>
			<tr>
				<?php 
					if ($pertanyaan) {
						foreach ($pertanyaan as $row) {
							echo '<th>'.$row->pertanyaan.'</th>';
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
						$point = 0;

						echo '
						<tr>
							<td>'.$no++.'</td>
							<td>'.$row->nik.'</td>
							<td>'.$row->nama.'</td>
							<td>'.$row->line.'</td>
							<td>'.$row->team.'</td>';

						$jawaban = $this->Report_Model->get_detail_question($tgl, $row->nik);

						if ($jawaban) {
							for ($i = 0; $i < count($jawaban); $i++) {
								echo '<td>'.$jawaban[$i]['jawaban'];

								$point += $jawaban[$i]['point'];

								$child = isset($jawaban[$i]['sub']) ? $jawaban[$i]['sub'] : [];

								if ($child) {
									for ($j = 0; $j < count($child); $j++) {
										echo '<br /> <b>' . $jawaban[$i]['sub'][$j]['pertanyaan'] . '</b>';
										echo '<br />' . $jawaban[$i]['sub'][$j]['jawaban'];

										$point += $jawaban[$i]['sub'][$j]['point'];
									}
								}

								echo '</td>';
							}
						} else {
							for ($i = 0; $i < count($pertanyaan); $i++) {
								echo '<td></td>';
							}
						}

						echo '<td>'.$point.'</td>';
						
						echo '</tr>';
					}
				}
			?>
		</tbody>
	</table>
</div>