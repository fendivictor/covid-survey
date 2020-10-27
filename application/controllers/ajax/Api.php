<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Main_Model', 'Report_Model']);
	}

	public function index()
	{
		show_404();
	}

	public function view_data()
	{	
		$criteria = '';

		$condition = $this->input->post('condition', TRUE);
		$collection = $this->input->post('collection', TRUE);
		$multiple = $this->input->post('multiple', TRUE);

		if ($condition) {
			for ($i = 0; $i < count($condition); $i++) {
				$criteria .= $condition[$i];
			}
		}

		$result = $this->Main_Model->view_data($collection, $criteria, $multiple);

		echo json_encode($result);
	}

	public function risk_chart()
	{
		$date = $this->input->get('date', TRUE);

		$data = $this->Report_Model->risk_chart_data($date);

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function line_chart()
	{
		$date = $this->input->get('date', TRUE);

		$data = $this->Report_Model->line_chart($date);

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}
}

/* End of file Api.php */
/* Location: ./application/controllers/Api.php */ ?>