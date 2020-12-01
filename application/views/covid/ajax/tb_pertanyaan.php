<?php 
$allPertanyaan = $this->Main_Model->view_data_covid('ms_pertanyaan', [], true);
$jumlahAll = count($allPertanyaan);
?>
<div class="table-responsive">
	<table class="table table-hovered table-striped" id="tb-data">
		<thead>
			<tr>
				<th rowspan="2">NIK</th>
				<th rowspan="2">Nama</th>
				<th rowspan="2">Line</th>
				<th rowspan="2">Team</th>
				<th colspan="<?= $jumlahAll ?>">Pertanyaan</th>
				<th rowspan="2">Skor</th>
			</tr>
			<tr>
				<?php 
					$childArr = [];
					if ($pertanyaan) {
						foreach ($pertanyaan as $row) {
							$child = $this->Main_Model->view_data_covid('ms_pertanyaan', ['id_parent' => $row->id], true);
							$jumlahChild = count($child) + 1;

							echo '<th>'.$row->pertanyaan.'</th>';

							if ($child) {
								foreach ($child as $val) {
									echo '<th>'.$val->pertanyaan.'</th>';
								}
							}

							$childArr[$row->id] = $jumlahChild;
						}
					}
				 ?>
			</tr>
		</thead>
		<tbody>
			<?php  
				if ($personal_data) {
					foreach ($personal_data as $row) {
						$point = 0;

						echo '
						<tr>
							<td>'.$row->nik.'</td>
							<td>'.$row->nama.'</td>
							<td>'.$row->line.'</td>
							<td>'.$row->team.'</td>';

						$jawaban = $this->Report_Model->get_detail_question($tgl, $row->nik);

						if ($jawaban) {
							for ($i = 0; $i < count($jawaban); $i++) {
								echo '<td>'.$jawaban[$i]['jawaban'].'</td>';

								$point += $jawaban[$i]['point'];

								$child = isset($jawaban[$i]['sub']) ? $jawaban[$i]['sub'] : [];

								if ($child) {
									for ($j = 0; $j < count($child); $j++) {
										echo '<td>' . $jawaban[$i]['sub'][$j]['jawaban'] . '</td>';

										$point += $jawaban[$i]['sub'][$j]['point'];
									}
								} else {
									$jumlahChild = isset($childArr[$jawaban[$i]['id_pertanyaan']]) ? $childArr[$jawaban[$i]['id_pertanyaan']] : 0;

									for ($j = 0; $j < ($jumlahChild - 1); $j++) {
										echo '<td></td>';
									}
								}
							}
						} else {
							for ($k = 0; $k < $jumlahAll; $k++) {
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