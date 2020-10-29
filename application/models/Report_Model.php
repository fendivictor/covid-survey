<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->covidDb = $this->load->database('covid', true);
	}	

	public function get_daily_summary($date, $judul)
	{
		return $this->covidDb->query("
			SELECT COUNT(nik) AS jumlah
			FROM (
				SELECT b.*, c.`judul`, c.class
				FROM (
					SELECT a.`nik`, a.`user_insert`, 
					SUM(a.point) AS nilai_resiko
					FROM tb_survei a
					WHERE a.`tanggal` = ?
					GROUP BY a.`nik`
				) AS b 
				INNER JOIN (
					SELECT *
					FROM tb_rule c
					WHERE c.`class` = ?
				) AS c ON b.nilai_resiko BETWEEN c.min AND c.max
			) AS d 
			GROUP BY class ", [$date, $judul])->row();
	}

	public function get_summary($datestart, $dateend, $judul)
	{
		$dateArr = getDatesFromRange($datestart, $dateend);

		$response = [];
		if ($dateArr) {
			foreach ($dateArr as $row) {
				$nilai = $this->get_daily_summary($row, $judul);

				$jumlah = isset($nilai->jumlah) ? $nilai->jumlah : 0;

				$response[] = $jumlah;
			}
		}

		return $response;
	}

	public function get_detail_report($category, $date)
	{
		return $this->covidDb->query("
			SELECT *
			FROM (
				SELECT b.*, c.`judul`, c.class
				FROM (
					SELECT a.tanggal, a.`nik`, a.`user_insert`, 
					SUM(a.point) AS nilai_resiko
					FROM tb_survei a
					WHERE a.`tanggal` = ?
					GROUP BY a.`nik`
				) AS b 
				INNER JOIN (
					SELECT *
					FROM tb_rule c
					WHERE c.`class` = ?
				) AS c ON b.nilai_resiko BETWEEN c.min AND c.max
			) AS d ", [$date, $category])->result();
	}

	public function risk_chart_data($date)
	{
		$labels = [];
		$dataset = [];
		$dataitem = [];

		$data = $this->covidDb->query("
			SELECT judul, jumlah, jumlah_survey, (jumlah / jumlah_survey) * 100 AS prc
			FROM (
			SELECT e.`id`, e.`judul`, IFNULL(f.jumlah, 0) AS jumlah, IFNULL(g.jumlah, 0) AS jumlah_survey
			FROM tb_rule e
			LEFT JOIN (

			SELECT id, judul, COUNT(id) AS jumlah
			FROM (
			SELECT b.`id`, b.`judul`, c.*
			FROM tb_rule b
			INNER JOIN (
			SELECT a.`nik`, SUM(a.`point`) AS score
			FROM tb_survei a
			WHERE a.`tanggal` = ?
			GROUP BY a.`nik`
			) AS c ON c.score BETWEEN b.`min` AND b.`max`
			) AS c
			GROUP BY id

			) AS f ON f.id = e.`id`

			LEFT JOIN (
			SELECT COUNT(nik) AS jumlah
			FROM (
			SELECT c.`nik`
			FROM tb_survei c
			WHERE c.`tanggal` = ?
			GROUP BY c.`nik`
			) AS b 
			) AS g ON 1 = 1
			) AS r
			ORDER BY r.id ASC ", [$date, $date])->result();

		if ($data) {
			foreach ($data as $row) {
				$labels[] = $row->judul.' ('.$row->jumlah.')';
				$dataitem[] = number_format($row->prc, 2, '.', ',');
			}
		}

		$dataset[] = [
			'label' => $labels,
			'data' => $dataitem,
			'backgroundColor' => [
				'rgb(54, 162, 235)',
				'rgb(255, 205, 86)',
				'rgb(255, 99, 132)',
				'rgb(255, 99, 132)'
			],
			'datalabels' => [
				'anchor' => 'center',
				'rotation' => 40
			]
		];

		return [
			'labels' => $labels,
			'datasets' => $dataset
		];
	}

	public function get_risk_byline($date, $category)
	{
		return $this->covidDb->query("
			SELECT a.line, IFNULL(j.jumlah, 0) AS jumlah
			FROM (
			SELECT a.`line`
			FROM ms_personal_data a
			GROUP BY a.`line`
			) AS a
			LEFT JOIN (
				SELECT e.`line`, COUNT(d.nik) AS jumlah
				FROM (
				SELECT b.*, c.`judul`, c.class
				FROM (
				SELECT a.`nik`, a.`user_insert`, 
				SUM(a.point) AS nilai_resiko
				FROM tb_survei a
				WHERE a.`tanggal` = ?
				GROUP BY a.`nik`
				) AS b 
				INNER JOIN (
					SELECT *
					FROM tb_rule c
					WHERE c.`class` = ?
				) AS c ON b.nilai_resiko BETWEEN c.min AND c.max
				) AS d 
				INNER JOIN ms_personal_data e ON e.`nik` = d.nik
				GROUP BY e.`line`
			) AS j ON j.line = a.line
			ORDER BY a.line ASC ", [$date, $category])->result();
	}

	public function line_chart($date) 
	{
		$labels = [];
		$dataset = [];
		$dataitem = [];

		$data_low = $this->get_risk_byline($date, 'info');
		$data_mid = $this->get_risk_byline($date, 'warning');
		$data_high = $this->get_risk_byline($date, 'danger');

		$dataitem[0] = [];
		if ($data_low) {
			foreach ($data_low as $low) {
				$labels[] = $low->line;
				$dataitem[0][] = $low->jumlah;
			}
		}

		$dataitem[1] = [];
		if ($data_mid) {
			foreach ($data_mid as $mid) {
				$dataitem[1][] = $mid->jumlah;
			}
		}

		$dataitem[2] = [];
		if ($data_high) {
			foreach ($data_high as $high) {
				$dataitem[2][] = $high->jumlah;
			}
		}

		$dataset[0] = [
			'label' => 'Resiko Rendah',
			'data' => $dataitem[0],
			'fill' => false,
			'borderColor' => [
				'rgb(54, 162, 235)'
			],
			'pointBackgroundColor' => [
				'rgb(54, 162, 235)'
			]
		];

		$dataset[1] = [
			'label' => 'Resiko Sedang',
			'data' => $dataitem[1],
			'fill' => false,
			'borderColor' => [
				'rgb(255, 205, 86)'
			],
			'pointBackgroundColor' => [
				'rgb(255, 205, 86)'
			]
		];

		$dataset[2] = [
			'label' => 'Resiko Tinggi',
			'data' => $dataitem[2],
			'fill' => false,
			'borderColor' => [
				'rgb(255, 99, 132)'
			],
			'pointBackgroundColor' => [
				'rgb(255, 99, 132)'
			]
		];

		return [
			'labels' => $labels,
			'datasets' => $dataset
		];
	}

	public function detail_question($date, $nik, $parent)
	{
		$condition = ($parent == '') ? " AND b.id_parent IS NULL " : " AND b.id_parent = '$parent' ";
		$answer = ($parent == '') ? " IF(a.`answer` = 1, 'Ya', 'Tidak') AS jawaban " : " IF(a.keterangan <> '', a.keterangan, IF(c.id IS NULL, a.answer, IFNULL(d.title, ''))) AS jawaban ";

		return $this->covidDb->query("
			SELECT b.id, a.`tanggal`, a.`nik`, a.`user_insert`, b.`pertanyaan`, $answer, a.`point`,
			IFNULL(c.`id`, '') AS id_penyakit, IFNULL(d.`title`, '') AS jenis_penyakit
			FROM tb_survei a
			INNER JOIN ms_pertanyaan b ON a.`id_pertanyaan` = b.`id`
			LEFT JOIN tb_jawaban c ON c.`id` = a.`id_penyakit`
  			LEFT JOIN ms_penyakit d ON d.`id` = c.`id_jawaban`
			WHERE a.`tanggal` = ?
			AND a.`nik` = ?
			$condition
			GROUP BY a.tanggal, a.nik, b.id
			ORDER BY b.`id` ASC ", [$date, $nik])->result();
	}

	public function get_detail_question($date, $nik)
	{
		$response = [];

		$survey = $this->detail_question($date, $nik, '');

		if ($survey) {
			foreach ($survey as $row => $val) {
				$response[$row] = [
					'tgl' => $val->tanggal,
					'nik' => $val->nik,
					'user_insert' => $val->user_insert,
					'pertanyaan' => $val->pertanyaan,
					'jawaban' => $val->jawaban,
					'point' => $val->point,
					'id_penyakit' => $val->id_penyakit,
					'jenis_penyakit' => $val->jenis_penyakit
				];

				$child = $this->detail_question($date, $nik, $val->id);

				if ($child) {
					foreach ($child as $chl) {
						$response[$row]['sub'][] = [
							'tgl' => $chl->tanggal,
							'nik' => $chl->nik,
							'user_insert' => $chl->user_insert,
							'pertanyaan' => $chl->pertanyaan,
							'jawaban' => $chl->jawaban,
							'point' => $chl->point,
							'id_penyakit' => $chl->id_penyakit,
							'jenis_penyakit' => $chl->jenis_penyakit
						];
					}
				}
			}
		}

		return $response;
	}

	public function personal_data($nik = '')
	{
		if ($nik != '') {
			return $this->covidDb->where(['nik' => $nik])->get('ms_personal_data')->row();
		}

		return $this->covidDb->get('ms_personal_data')->result();
	}

	public function get_survey_coordinates($nik, $date)
	{
		return $this->covidDb->select('lat, long')->where(['nik' => $nik, 'tanggal' => $date])->group_by('lat, long')->limit(1)->get('tb_survei')->row();
	}

	public function get_high_risk_person($date)
	{
		return $this->covidDb->query("
			SELECT b.*, c.`nama`, c.`line`
			FROM (
				SELECT a.`nik`, SUM(a.`point`) AS score
				FROM tb_survei a
				WHERE a.`tanggal` = ?
				GROUP BY a.`nik`
			) AS b 
			INNER JOIN ms_personal_data c ON b.nik = c.`nik`
			WHERE score >= 5
			ORDER BY score DESC ", [$date])->result();
	}

	public function get_high_risk_population($date)
	{
		return $this->covidDb->query("
			SELECT b.*, c.`nama`, c.`line`, d.`judul`
			FROM (
				SELECT a.`nik`, a.`lat`, a.`long`, SUM(a.`point`) AS score
				FROM tb_survei a
				WHERE a.`tanggal` = ?
				GROUP BY a.`nik`
			) AS b 
			INNER JOIN ms_personal_data c ON b.nik = c.`nik`
			INNER JOIN tb_rule d ON b.score BETWEEN d.`min` AND d.`max`
			WHERE d.`class` = 'danger'
			AND b.lat <> '' 
			AND b.long <> ''
			ORDER BY b.score DESC ", [$date])->result();
	}

	public function dt_deteksi_mandiri_line($date)
	{
		return $this->covidDb->query("
			SELECT a.line, IFNULL(b.jumlah, 0) AS jumlah_karyawan,
			IFNULL(c.jumlah, 0) AS sdh_survey, (IFNULL(b.jumlah, 0) - IFNULL(c.jumlah, 0)) AS blm_survey,
			IFNULL(d.rendah, 0) AS rendah, IFNULL(d.sedang, 0) AS sedang, IFNULL(d.tinggi, 0) AS tinggi,
			IFNULL(d.sangat_tinggi, 0) AS sangat_tinggi
			FROM (
			SELECT a.`line`
			FROM ms_personal_data a
			GROUP BY a.`line`
			) AS a
			LEFT JOIN (
				SELECT b.`line`, COUNT(b.`nik`) AS jumlah
				FROM ms_personal_data b
				GROUP BY b.`line`
			) AS b ON a.line = b.line
			LEFT JOIN (
				SELECT c.`line`, COUNT(c.`line`) AS jumlah
				FROM (
					SELECT a.`nik`
					FROM tb_survei a
					WHERE a.`tanggal` = ?
					GROUP BY a.`nik`
				) AS b
				INNER JOIN ms_personal_data c ON c.`nik` = b.nik
				GROUP BY c.`line`
			) AS c ON a.line = c.line
			LEFT JOIN (
				SELECT line, SUM(rendah) AS rendah, SUM(sedang) AS sedang,
				SUM(tinggi) AS tinggi, SUM(sangat_tinggi) AS sangat_tinggi
				FROM (
					SELECT b.nik, b.score, c.`line`, d.`class`,
					IF (d.judul = 'Resiko Rendah', 1, 0) AS rendah,
					IF (d.judul = 'Resiko Sedang', 1, 0) AS sedang,
					IF (d.judul = 'Resiko Tinggi', 1, 0) AS tinggi,
					IF (d.judul = 'Resiko Sangat Tinggi', 1, 0) AS sangat_tinggi
					FROM (
						SELECT a.`nik`, SUM(a.`point`) AS score
						FROM tb_survei a
						WHERE a.`tanggal` = ?
						GROUP BY a.`nik`
					) AS b
					INNER JOIN ms_personal_data c ON c.`nik` = b.nik
					INNER JOIN tb_rule d ON b.score BETWEEN d.`min` AND d.`max`
				) AS e
				GROUP BY e.line
			) AS d ON d.line = a.line ", [$date, $date])->result();
	}

	public function get_survey_timestamp($nik, $date)
	{
		$sql = $this->covidDb->query("
			SELECT a.`insert_at`
			FROM tb_survei a
			WHERE a.`nik` = ?
			AND a.`tanggal` = ?
			GROUP BY a.`insert_at`
			LIMIT 1 ", [$nik, $date])->row();

		return isset($sql->insert_at) ? $sql->insert_at : '';
	}

	public function get_daily_score($nik, $tanggal)
	{
		$sql = $this->covidDb->query("
			SELECT a.`nik`, a.`tanggal`, SUM(a.`point`) AS score
			FROM tb_survei a
			WHERE a.`nik` = ?
			AND a.`tanggal` = ?
			GROUP BY a.`nik`, a.`tanggal` ", [$nik, $tanggal])->row();

		return isset($sql->score) ? $sql->score : '';
	}

	public function get_score_level($nilai)
	{
		$sql = $this->covidDb->query("
			SELECT * 
			FROM tb_rule a 
			WHERE ? BETWEEN a.min AND a.max ", [$nilai])->row();

		return isset($sql->class) ? $sql->class : '';
	}
}

/* End of file Report_Model.php */
/* Location: ./application/models/Report_Model.php */ ?>