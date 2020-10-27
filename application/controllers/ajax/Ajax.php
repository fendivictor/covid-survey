<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if (! $this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model(['Login_model', 'Main_Model', 'User_Model', 'Report_Model']);
		$islogin = $this->Login_model->is_login();
		if ($islogin == FALSE) {
			redirect(base_url());
		}

		$this->load->library('form_validation');
	}

	public function summary_report()
	{
		$datestart = $this->input->get('datestart', TRUE);
		$dateend = $this->input->get('dateend', TRUE);

		$dateArr = getDatesFromRange($datestart, $dateend);

		$data = [
			'date' => $dateArr,
			'rendah' => $this->Report_Model->get_summary($datestart, $dateend, 'info'),
			'sedang' => $this->Report_Model->get_summary($datestart, $dateend, 'warning'),
			'tinggi' => $this->Report_Model->get_summary($datestart, $dateend, 'danger')
		];

		$view = $this->load->view('covid/ajax/tb_summary', $data, TRUE);

		echo $view;
	}

	public function category_report()
	{
		$category = $this->input->get('category', TRUE);
		$date = $this->input->get('date', TRUE);

		$report = $this->Report_Model->get_detail_report($category, $date);

		$data = [
			'report' => $report
		];

		$view = $this->load->view('covid/ajax/tb_details', $data, TRUE);

		echo $view;
	}

	public function detail_question()
	{
		$date = $this->input->get('date', TRUE);
		$nik = $this->input->get('nik', TRUE);

		$report = $this->Report_Model->get_detail_question($date, $nik);
		$personal = $this->Report_Model->personal_data($nik);

		$data = [
			'report' => $report,
			'personal' => $personal
		];

		$view = $this->load->view('covid/ajax/report_answer', $data, TRUE);

		echo $view;
	}

	public function get_survey_coordinates()
	{
		$date = $this->input->get('date', TRUE);
		$nik = $this->input->get('nik', TRUE);

		$data = $this->Report_Model->get_survey_coordinates($nik, $date);

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function get_high_risk_population()
	{
		$date = $this->input->get('date', TRUE);

		$data = $this->Report_Model->get_high_risk_population($date);

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
}

/* End of file Ajax.php */
/* Location: ./application/controllers/Ajax.php */ ?>