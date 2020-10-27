<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Covid extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
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

}

/* End of file Covid.php */
/* Location: ./application/controllers/Covid.php */ ?>