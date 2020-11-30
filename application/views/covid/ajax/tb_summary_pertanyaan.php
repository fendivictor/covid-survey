<div class="table-responsive">
	<table class="table table-hovered table-striped" id="tb-data">
		<thead>
			<tr>
				<th rowspan="2">#</th>
				<th rowspan="2">NIK</th>
				<th rowspan="2">Nama</th>
				<th rowspan="2">Line</th>
				<th rowspan="2">Team</th>
				<th colspan="<?= count($tgl) ?>" align="center">Tanggal</th>
			</tr>
			<tr>
				<?php 
					if ($tgl) {
						foreach ($tgl as $row) {
							echo '<th align="center">'.custom_date_format($row, 'Y-m-d', 'm/d').'</th>';
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
							<td>'.$row->line.'</td>
							<td>'.$row->team.'</td>';

						if ($tgl) {
							foreach ($tgl as $date) {
								echo '<td>';
								if ($pertanyaan) {
									foreach ($pertanyaan as $val) {
										$result = $this->Report_Model->get_pertanyaan_perhari($date, $row->nik, $val);

										echo '<ul>';
										if ($result) {
											echo '
											<li>
												<div>'.$result->pertanyaan.'</div>
												<div>'.$result->answer.'</div>
												<div>'.$result->keterangan.'</div>
											</li>';


											$child = $this->Report_Model->get_child_pertanyaan($date, $row->nik, $val);

											if ($child) {
												echo '<ul>';
												foreach ($child as $chl) {
												echo '
													<li>
														<div>'.$chl->pertanyaan.'</div>
														<div>'.$chl->title.'</div>
														<div>'.$chl->keterangan.'</div>
													</li>';
												}
												echo '</ul>';
											}
										}
										echo '</ul>';
									}
								}
								echo '</td>';
							}
						}
												
						echo '</tr>';
					}
				}
			?>
		</tbody>
	</table>
</div>