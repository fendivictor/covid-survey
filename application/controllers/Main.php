<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends MY_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Main_Model']);
	}

	public function index()
	{
		$page = $this->Login_model->isvalid_page();
		if ($page == false) {
			show_404();
		}

		$header = [];

		$body = [
			'content' => 'dashboard/dashboard',
			'title' => lang('menu_dashboard')
		];

		$footer = [
			'js' => ['assets/js/apps/dashboard.js']
		];

		$this->template($header, $body, $footer);
	}

	public function switch_language($language = '') 
	{
       $language = ($language != '') ? $language : 'english';
       $this->session->set_userdata('site_lang', $language);
       redirect($_SERVER['HTTP_REFERER']);
   }
}

/* End of file Main.php */
/* Location: ./application/controllers/Main.php */ ?>