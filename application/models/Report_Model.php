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
			SELECT COUNT(nik) AS jumlah, judul
			FROM (
				SELECT a.*, b.`class`, b.judul
				FROM (
					SELECT a.`nik`, SUM(a.point) AS `point`
					FROM tb_survei a
					WHERE a.`tanggal` = ?
					GROUP BY a.`nik`
				) AS a
				INNER JOIN tb_rule b ON a.point BETWEEN b.`min` AND b.`max`
			) AS b 
			GROUP BY class
			ORDER BY class ", [$date])->result();

		if ($data) {
			foreach ($data as $row) {
				$labels[] = $row->judul;
				$dataitem[] = $row->jumlah;
			}
		}

		$dataset[] = [
			'label' => $labels,
			'data' => $dataitem,
			'backgroundColor' => [
				'rgb(255, 99, 132)',
				'rgb(54, 162, 235)',
				'rgb(255, 205, 86)'
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
		$answer = ($parent == '') ? " IF(a.`answer` = 1, 'Ya', 'Tidak') AS jawaban " : " IF(c.id IS NULL, a.answer, IFNULL(c.title, '')) AS jawaban ";

		return $this->covidDb->query("
			SELECT b.id, a.`tanggal`, a.`nik`, a.`user_insert`, b.`pertanyaan`, $answer, a.`point`,
			IFNULL(c.`id`, '') AS id_penyakit, IFNULL(c.`title`, '') AS jenis_penyakit
			FROM tb_survei a
			INNER JOIN ms_pertanyaan b ON a.`id_pertanyaan` = b.`id`
			LEFT JOIN ms_penyakit c ON c.`id` = a.`id_penyakit`
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

	public function personal_data($nik)
	{
		return $this->covidDb->where(['nik' => $nik])->get('ms_personal_data')->row();
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
			ORDER BY score DESC
			LIMIT 10 ", [$date])->result();
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
}

/* End of file Report_Model.php */
/* Location: ./application/models/Report_Model.php */ ?>