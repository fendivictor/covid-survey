<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datatable extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Report_Model']);
	}

	public function tb_high_risk_person()
	{
		$response = [];
		$date = $this->input->get('date', TRUE);

		$data = $this->Report_Model->get_high_risk_person($date);

		if ($data) {
			$i = 1;
			foreach ($data as $row) {
				$response[] = [
					'no' => $i++,
					'nama' => '<a href="javascript:;" class="show-details" data-nik="'.$row->nik.'" data-nama="'.$row->nama.'">'.$row->nama.'</a>',
					'nik' => $row->nik,
					'line' => $row->line,
					'point' => $row->score
				];
			}
		}

		$this->output->set_content_type('application/json')->set_output(json_encode(['data' => $response]));
	}

	public function dt_deteksi_mandiri_line()
	{
		$response = [];
		$date = $this->input->get('date', TRUE);

		$data = $this->Report_Model->dt_deteksi_mandiri_line($date);

		if ($data) {
			$i = 1;
			foreach ($data as $row) {
				$response[] = [
					'line' => $row->line,
					'total' => $row->jumlah_karyawan,
					'sdh_survey' => $row->sdh_survey,
					'blm_survey' => $row->blm_survey,
					'rendah' => $row->rendah,
					'rendah_prc' => ($row->rendah > 0) ? ($row->rendah / $row->sdh_survey) * 100 . ' %' : 0,
					'sedang' => $row->sedang,
					'sedang_prc' => ($row->sedang > 0) ? ($row->sedang / $row->sdh_survey) * 100 . ' %' : 0,
					'tinggi' => $row->tinggi,
					'tinggi_prc' => ($row->tinggi > 0) ? ($row->tinggi / $row->sdh_survey) * 100 . ' %' : 0
				];
			}
		}

		$this->output->set_content_type('application/json')->set_output(json_encode(['data' => $response]));
	}
}

/* End of file Datatable.php */
/* Location: ./application/controllers/Datatable.php */ ?>