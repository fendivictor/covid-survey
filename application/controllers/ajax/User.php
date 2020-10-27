<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['User_Model', 'Main_Model']);
		$this->load->library('form_validation');
	}

	public function change_password()
	{
		$password_lama = $this->input->post('password_lama', TRUE);
		$password_baru = $this->input->post('password_baru', TRUE);
		$confirm_password = $this->input->post('password_confirm', TRUE);
		$username = $this->session->userdata('username');

		$this->form_validation->set_rules('password_lama', 'Password Lama', 'required', ['required' => 'Masukkan %s']);
		$this->form_validation->set_rules('password_baru', 'Password baru', 'required', ['required' => 'Masukkan %s']);
		$this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required', ['required' => 'Masukkan %s']);

		$status = 0;
		$message = 'Ganti password gagal';

		if ($this->form_validation->run()) {
			$isvalid_confirm_password = false;
			if ($password_baru == $confirm_password) {
				$isvalid_confirm_password = true;
			} else {
				$message = 'Password tidak cocok';
			}

			$isvalid_current_password = $this->User_Model->is_valid_password($username, $password_lama);
			if ($isvalid_current_password == false) {
				$message = 'Password Lama salah';
			}

			if ($isvalid_current_password && $isvalid_confirm_password) {
				$change = $this->User_Model->change_password($username, $password_baru);

				if ($change > 0) {
					$status = 1;
					$message = 'Ganti password berhasil';
				}
			}
		} else {
			$message = validation_errors();
		}

		$result = [
			'status' => $status,
			'message' => $message
		];

		echo json_encode($result);
	}

	public function dt_user()
	{
		$response = [];
		$dt_table = [];

		$data = $this->User_Model->dt_user();

		if ($data) {
			$no = 1;
			foreach ($data as $row) {
				$change_password = '<a href="javascript:;" title="change password" class="change-password btn btn-warning btn-xs" data-username="'.$row->username.'"> <i class="fa fa-edit"></i> </a>';

				$dt_table[] = [
					$no++,
					$row->username,
					$row->profile_name,
					$row->language,
					($row->status == 1) ? 'ACTIVE' : 'DISABLE',
					custom_date_format($row->last_login, 'Y-m-d H:i:s', 'd/m/Y H:i'),
					$change_password
				];
			}
		}

		$response['data'] = $dt_table;

		echo json_encode($response);
	}

	public function ganti_manual()
	{
		$username = $this->input->post('username-change', TRUE);
		$password = $this->input->post('password-change', TRUE);
		$profile_name = $this->input->post('profile-name-change', TRUE);
		$bahasa = $this->input->post('bahasa-change', TRUE);

		$status = 0;

		$ganti = $this->User_Model->change_password($username, $password, $profile_name, $bahasa);

		$message = 'Ganti password gagal';

		if ($ganti > 0) {
			$status = 1;
			$message = 'Password berhasil diganti';
		}

		$result = [
			'status' => $status,
			'message' => $message
		];

		echo json_encode($result);
	}

	public function add_user()
	{
		$username = $this->input->post('username-add', TRUE);
		$password = $this->input->post('password-add', TRUE);
		$profile_name = $this->input->post('profile-name', TRUE);
		$bahasa = $this->input->post('bahasa', TRUE);

		$exists = $this->Main_Model->view_data('tb_user', ['username' => $username], false);
		if ($exists) {
			$status = 0;
			$message = 'username sudah terdaftar';
		} else {
			$simpan = $this->User_Model->add_user($username, $password, $profile_name, $bahasa);

			$status = 0;
			$message = 'Gagal menyimpan data';

			if ($simpan > 0) {
				$status = 1;
				$message = 'Data berhasil disimpan';
			}
		}

		$result = [
			'status' => $status,
			'message' => $message
		];

		echo json_encode($result);
	}

	public function user_detail()
	{
		$username = $this->input->get('username', TRUE);

		$data = $this->Main_Model->view_data('tb_user', ['username' => $username], false);

		echo json_encode($data);
	}
}

/* End of file User.php */
/* Location: ./application/controllers/User.php */ ?>