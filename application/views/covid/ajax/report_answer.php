<?php 	
	$nik = isset($personal->nik) ? $personal->nik : '';
	$nama = isset($personal->nama) ? $personal->nama : '';
	$line = isset($personal->line) ? $personal->line : '';
	$team = isset($personal->team) ? $personal->team : '';
	$jabatan = isset($personal->jabatan) ? $personal->jabatan : '';

	echo '
		<p>
			Nama: <b>'.$nama.'</b> <br/>
			Line: <b>'.$line.'</b> <br/>
			Team: <b>'.$team.'</b> <br/>
			Jabatan: <b>'.$jabatan.'</b> <br/>
		</p>
	';

	if ($report) {
		echo '<ol>';
		foreach ($report as $row) {
			$sub = isset($row['sub']) ? $row['sub'] : [];

			echo '
			<li>
				'.$row['pertanyaan'].' <br />
				Jawab: <b>'.$row['jawaban'].'</b>
			</li>';

			if ($sub) {
				echo '<ol>';
				foreach ($sub as $val) {
					echo '
					<li>
						'.$val['pertanyaan'].' <br />
						Jawab: <b>'.$val['jawaban'].'</b>
					</li>';
				}
				echo '</ol>';
			}
		}
		echo '</ol>';
	}
?>