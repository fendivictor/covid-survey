<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function is_valid_password($username, $password)
	{
		$password = $password.'&fk_project*123#';
		$sql = $this->db->query("
				SELECT * 
				FROM tb_user a 
				WHERE a.username = '$username' 
				AND a.password = MD5('$password')
				AND a.status = 1 ")->row();

		return ($sql) ? true : false;
	}

	public function change_password($username, $password, $profile_name = '', $bahasa = '')
	{
		$update_colom = '';
		if ($password != '') {
			$password = $password.'&fk_project*123#';
			$update_colom .= " password = MD5('$password'), ";
		}

		if ($profile_name != '') {
			$update_colom .= " profile_name = '$profile_name', ";
		}

		if ($bahasa != '') {
			$update_colom .= " language = '$bahasa', ";
		}

		$this->db->query("
			UPDATE tb_user 
			SET user_update = '$username',
			$update_colom
			update_at = NOW()
			WHERE username = '$username'");

		return $this->db->affected_rows();
	}

	public function dt_user()
	{
		return $this->db->query("
			SELECT *
			FROM tb_user ")->result();
	}

	public function add_user($username, $password, $profile_name, $bahasa) 
	{
		$password = $password.'&fk_project*123#';
		$this->db->insert('tb_user', [
			'username' => $username,
			'password' => $password,
			'profile_name' => $profile_name,
			'language' => $bahasa,
			'user_insert' => $this->session->userdata('username')
		]);

		return $this->db->affected_rows();
	}

	public function menu_privilege($username)
	{
		if ($username == '') {
			return [];
		}

		return $this->db->query("
			SELECT b.`id`, b.`id_menu`, a.`label`, b.`tools`, b.`status`,
			IF (c.id = '' OR c.id IS NULL, 0, 1) AS checked
			FROM tb_menu a
			INNER JOIN tb_menu_setting b ON a.`id` = b.`id_menu`
			LEFT JOIN (
				SELECT c.*
				FROM tb_privilege c
				WHERE c.username = '$username'
			) AS c ON c.tools = b.tools
			WHERE a.`status` = 1 ")->result();
	}

	public function add_privilege($data, $username)
	{
		$this->db->trans_begin();

		$this->db->where(['username' => $username])->delete('tb_privilege');

		$this->db->insert_batch('tb_privilege', $data);

		if ($this->db->trans_status()) {
			$this->db->trans_commit();
			return true;
		} else {
			$this->db->trans_rollback();
			return false;
		}
	}
}

/* End of file User_Model.php */
/* Location: ./application/models/User_Model.php */ ?>