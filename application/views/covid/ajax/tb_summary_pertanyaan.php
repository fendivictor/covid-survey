<?php 
	$allPertanyaan = [];
	if ($pertanyaan) {
		for ($i = 0; $i < count($pertanyaan); $i++) {
			$parentPertanyaan = $this->Main_Model->view_data_covid('ms_pertanyaan', ['id' => $pertanyaan[$i]], false);

			$allPertanyaan[] = [
				'id' => $pertanyaan[$i],
				'pertanyaan' => $parentPertanyaan->pertanyaan
			];

			$hasChild = $this->Main_Model->view_data_covid('ms_pertanyaan', ['id_parent' => $pertanyaan[$i]], true);
			if ($hasChild) {
				foreach ($hasChild as $row) {
					$allPertanyaan[] = [
						'id' => $row->id,
						'pertanyaan' => $row->pertanyaan
					];
 				}
			}
		}
	}
?>

<div class="table-responsive">
	<table class="table table-hovered table-striped" id="tb-data">
		<thead>
			<tr>
				<th rowspan="3">#</th>
				<th rowspan="3">NIK</th>
				<th rowspan="3">Nama</th>
				<th rowspan="3">No HP</th>
				<th rowspan="3">Line</th>
				<th rowspan="3">Team</th>
				<th colspan="<?= count($tgl) * count($allPertanyaan) ?>" align="center">Tanggal</th>
			</tr>
			<tr>
				<?php 
					if ($tgl) {
						foreach ($tgl as $row) {
							echo '<th align="center" colspan="'.count($allPertanyaan).'">'.custom_date_format($row, 'Y-m-d', 'm/d').'</th>';
						}
					}
				 ?>
			</tr>
			<tr>
				<?php  
					if ($tgl) {
						foreach ($tgl as $row) {
							if ($allPertanyaan) {
								foreach ($allPertanyaan as $pertanyaan) {
									echo '<th>'.$pertanyaan['pertanyaan'].'</th>';
								}
							}
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
							<td>'.$no++.'</td>
							<td>'.$row->nik.'</td>
							<td>'.$row->nama.'</td>
							<td>'.$row->no_hp.'</td>
							<td>'.$row->line.'</td>
							<td>'.$row->team.'</td>';

						if ($tgl) {
							foreach ($tgl as $date) {
								if ($allPertanyaan) {
									foreach ($allPertanyaan as $val) {
										$result = $this->Report_Model->get_pertanyaan_perhari($date, $row->nik, $val['id']);
										$jawaban = isset($result->answer) ? $result->answer : '';

										if ($result) {
											echo '<td>'.$jawaban.'</td>';
										} else {
											echo '<td></td>';
										}
									}
								}
							}
						}
												
						echo '</tr>';
					}
				}
			?>
		</tbody>
	</table>
</div>