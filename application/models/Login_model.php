<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->covidDb = $this->load->database('covid', true);
	}

	public function is_login()
	{
		$username = $this->session->userdata('username');
		$session = $this->session->userdata('session');

		$is_login = FALSE;

		if ($username <> '' && $session <> '') {
			$is_logged_in = $this->db->where([
				'username' => $username, 
				'session' => $session
			])
			->get('tb_user')
			->row();

			if (!empty($is_logged_in)) {
				$is_login = TRUE;
			}
		} else {
			$cookie = get_cookie('auth-login');
			if ($cookie <> '') {
				$cari = $this->db->where([
					'session' => $cookie
				])
				->get('admin')
				->row();

				if ($cari) {
					$username = isset($cari->username) ? $cari->username : '';
					if ($username != '') {
						$random = rand();
						$session = md5($random);
						$this->db->query("
							UPDATE tb_user 
							SET session = '$session' 
							WHERE username = '$username'");

						set_cookie('auth-login', $session, 36000);

						$session_data = array(
							'session' => $session,
							'username' => $username
						);
						
						$this->session->set_userdata( $session_data );
						$is_login = TRUE;
					}
				}
			}
		}

		return ($is_login) ? TRUE : FALSE;
	}

	public function login($username = '', $password = '')
	{
		$password = md5($password.'&fk_project*123#');
		$is_login = $this->db->query("
				SELECT * 
				FROM tb_user a 
				WHERE a.username = '$username' 
				AND a.password = '$password'
				AND a.status = 1 ")->row();

		if ($is_login) {
			$random = rand();
			$session = md5($random);

			$this->db->query("
				UPDATE tb_user 
				SET session = '$session', last_login = NOW()
				WHERE username = '$username'");

			return [
				'language' => $is_login->language,
				'session' => $session,
				'islogin' => TRUE
			];
		}

		return [
			'language' => '',
			'session' => '',
			'islogin' => FALSE
		];
	}

	public function isvalid_page()
	{
		$username = $this->session->userdata('username');

		$fungsi = $this->router->fetch_class();
		$method = $this->router->fetch_method();

		$data = $this->covidDb->query("
			SELECT a.*
			FROM tb_user_menu a
			INNER JOIN tb_menu b ON a.`id_menu` = b.id
			WHERE a.`username` = '$username'
			AND b.`method` = '$method'
			AND b.`fungsi` = '$fungsi' ")->row();

		return ($data) ? $data : false;
	}

	public function get_user_profile($username)
	{
		return $this->db->where([
				'username' => $username
			])
			->get('tb_user')
			->row();
	}
}

/* End of file Login_model.php */
/* Location: ./application/models/Login_model.php */ ?>