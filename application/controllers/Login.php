<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Login_model');
	}

	public function index()
	{
		$islogin = $this->Login_model->is_login();

		if ($islogin) {
			redirect('Main');
		}

		$this->load->view('login');
	}

	public function proses_login()
	{
		$islogin = $this->Login_model->is_login();

		if ($islogin) {
			redirect('Main');
		}

		$username = $this->input->post('username', TRUE);
		$password = $this->input->post('password', TRUE);
		$remember = $this->input->post('remember', TRUE);

		$login = $this->Login_model->login($username, $password);

		$islogin = isset($login['islogin']) ? $login['islogin'] : FALSE;
		$session = isset($login['session']) ? $login['session'] : '';
		$language = isset($login['language']) ? $login['language'] : '';

		if ($islogin == true) {
			if ($remember == 1) {
				set_cookie('auth-login', $session, 36000);
			}

			$session_data = array(
				'site_lang' => $language,
				'session' => $session,
				'username' => $username
			);
			
			$this->session->set_userdata( $session_data );
			redirect('Main');
		}

		$this->session->set_flashdata('message', 'Username / password salah');
		$this->load->view('login');
	}

	public function clear_session()
	{
		$this->session->sess_destroy();
		delete_cookie('auth-login');
	}
}

/* End of file Login.php */
/* Location: ./application/controllers/Login.php */ ?>