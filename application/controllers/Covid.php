<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Covid extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Report_Model']);
	}

	public function data()
	{
		$page = $this->Login_model->isvalid_page();
		if ($page == false) {
			show_404();
		}

		$header = [];

		$body = [
			'content' => 'covid/data',
			'title' => lang('menu_deteksi_mandiri')
		];

		$footer = [
			'js' => ['assets/js/apps/covid/data.js']
		];

		$this->template($header, $body, $footer);
	}

	public function deteksi()
	{
		$page = $this->Login_model->isvalid_page();
		if ($page == false) {
			show_404();
		}

		$header = [];

		$body = [
			'content' => 'covid/deteksi',
			'title' => lang('menu_deteksi_mandiri')
		];

		$footer = [
			'js' => ['assets/js/apps/covid/deteksi.js']
		];

		$this->template($header, $body, $footer);
	}

	public function personal()
	{
		$page = $this->Login_model->isvalid_page();
		if ($page == false) {
			show_404();
		}

		$header = [];

		$body = [
			'content' => 'covid/personal',
			'title' => lang('menu_personal_data')
		];

		$footer = [
			'js' => ['assets/js/apps/covid/personal.js']
		];

		$this->template($header, $body, $footer);
	}

	public function suhu()
	{
		$page = $this->Login_model->isvalid_page();
		if ($page == false) {
			show_404();
		}

		$header = [];

		$body = [
			'content' => 'covid/suhu',
			'title' => lang('menu_laporan_suhu')
		];

		$footer = [
			'js' => ['assets/js/apps/covid/suhu.js']
		];

		$this->template($header, $body, $footer);
	}

	public function survey()
	{
		$page = $this->Login_model->isvalid_page();
		if ($page == false) {
			show_404();
		}

		$header = [];

		$body = [
			'content' => 'covid/survey',
			'title' => lang('menu_laporan_survey')
		];

		$footer = [
			'js' => ['assets/js/apps/covid/survey.js']
		];

		$this->template($header, $body, $footer);
	}

	public function pertanyaan()
	{
		$page = $this->Login_model->isvalid_page();
		if ($page == false) {
			show_404();
		}

		$header = [];

		$body = [
			'content' => 'covid/pertanyaan',
			'title' => lang('menu_deteksi_pertanyaan'),
			'pertanyaan' => $this->Report_Model->get_pertanyaan()
		];

		$footer = [
			'js' => ['assets/js/apps/covid/pertanyaan.js']
		];

		$this->template($header, $body, $footer);
	}
}

/* End of file Covid.php */
/* Location: ./application/controllers/Covid.php */ ?>