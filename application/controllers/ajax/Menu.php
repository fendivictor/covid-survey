<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (! $this->input->is_ajax_request()) {
			show_404();
		}
	}

	public function index()
	{
		show_404();
	}

	public function jstree()
	{
		$parent = 0;
		$username = $this->input->get('username', TRUE);

		$data = $this->Menu_Model->create_json_jstree($parent, $username);

		echo json_encode($data);
	}

	public function dt_user()
	{
		$response = [];
		$dt_table = [];

		$data = $this->Menu_Model->dt_user();

		if ($data) {
			$no = 1;
			foreach($data as $row) {
				$dt_table[] = [
					$no++,
					$row['profile_name'],
					'<a href="javascript:;" data-username="'.$row['username'].'" class="select-user">'.$row['username'].'</a>'
				];
			}
		}

		$response['data'] = $dt_table;

		echo json_encode($response);
	}

	public function simpan()
	{
		$menu = $this->input->get('menu', TRUE);
		$username = $this->input->get('username', TRUE);

		$simpan = $this->Menu_Model->simpan($menu, $username);

		echo json_encode($simpan);
	}
}

/* End of file Menu.php */
/* Location: ./application/controllers/Menu.php */ ?>