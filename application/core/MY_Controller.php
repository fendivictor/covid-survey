<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$islogin = $this->Login_model->is_login();

		if ($islogin == FALSE) {
			redirect(base_url());
		}
	}

	public function template($header = [], $body = [], $footer = [])
	{
		$this->load->view('template/header', $header);
		$this->load->view('template/body', $body);
		$this->load->view('template/footer', $footer);
	}
}

/* End of file MY_Controller.php */
/* Location: ./application/controllers/MY_Controller.php */ ?>