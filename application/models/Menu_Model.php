<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Menu_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->covidDb = $this->load->database('covid', true);
	}

	public function data_menu($parent = 0)
	{
		$username = $this->session->userdata('username');

		return $this->covidDb->query("
			SELECT a.*, IFNULL(menu.jumlah, 0) AS jumlah
			FROM tb_menu a
			LEFT JOIN (
				SELECT b.id, COUNT(b.`id`) AS jumlah, b.`parent`
				FROM tb_menu b 
				GROUP BY b.`parent`
			) AS menu ON menu.parent = a.`id`
			INNER JOIN tb_user_menu c ON a.id = c.id_menu
			WHERE a.parent = '$parent' 
			AND a.status = 1
			AND c.username = '$username'
			ORDER BY a.urutan ASC")->result_array();
	}

	public function create_menu()
	{
		$parent = 0;
		$menu = '';
		$active = '';
		$data_menu = $this->data_menu($parent);
		$fungsi = $this->router->fetch_class();
		$method = $this->router->fetch_method();

		if ($data_menu) {
			foreach ($data_menu as $row) {
				$open = [];
				$menu_open = '';
				$logout_button = '';
				$active = ($fungsi == $row['fungsi'] && $method == $row['method']) ? 'active' : '';

				if ($row['label'] == 'menu_logout') {
					$logout_button = ' id="btn-logout" ';
				}

				$url = ($row['url'] <> '') ? base_url($row['url']) : 'javascript:;';

				$item = '
						<li class="nav-item">
                            <a href="'.$url.'" class="nav-link '.$active.'" '.$logout_button.'>
                                <i class="'.$row['icon'].'"></i>
                                <p>
                                    '.lang($row['label']).'
                                </p>
                            </a>
                        </li>';

                if ($row['icon'] == '') {
                	$item = '
                			<li class="nav-header">
                				'.lang($row['label']).'
                			</li>';
                }

				if ($row['jumlah'] > 0) {
					$child_menu = $this->data_menu($row['id']);        
	                if ($child_menu) {
	                	$child = '';
	                	foreach ($child_menu as $val) {
	                		$active = ($fungsi == $val['fungsi'] && $method == $val['method']) ? 'active' : '';
	                		if ($active <> '') {
	                			$open[] = 1;
	                		}

	                		$url = ($val['url'] <> '') ? base_url($val['url']) : 'javascript:;';

	                		$child .= '
	                		<li class="nav-item">
	                            <a href="'.$url.'" class="nav-link '.$active.'">
	                                <i class="'.$val['icon'].'"></i>
	                                <p>'.lang($val['label']).'</p>
	                            </a>
	                        </li>';
	                	}
	                }

	                if (count($open) > 0) {
	                	$menu_open = 'menu-open';
	                }

	                $url = ($row['url'] <> '') ? base_url($row['url']) : 'javascript:;';
                    $item = '
						<li class="nav-item has-treeview '.$menu_open.'">
                            <a href="'.$url.'" class="nav-link">
                                <i class="'.$row['icon'].'"></i>
                                <p>
                                    '.lang($row['label']).'
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">';
                    $item .= $child;
                   	$item .=    
                            '</ul>
                        </li>';
				}

				$menu .= $item;
			}
		}

		return $menu;
	}

	public function current_menu()
	{
		$fungsi = $this->router->fetch_class();
		$method = $this->router->fetch_method();

		$sql = $this->covidDb->where(['fungsi' => $fungsi, 'method' => $method])->get('tb_menu')->row();

		return isset($sql->label) ? $sql->label : '';
	}

	public function jstree_menu($parent = 0, $username = '')
	{
		return $this->covidDb->query("
			SELECT a.`id`, a.`label`, a.`url`, a.`icon`, 
			a.`parent`, a.`urutan`, IFNULL(c.jumlah, 0) AS jumlah,
			IF (e.id <> '', 'true', 'false') AS selected
			FROM tb_menu a
			LEFT JOIN (
				SELECT b.`id`, b.`parent`, COUNT(b.`id`) AS jumlah
				FROM tb_menu b
				WHERE b.`status` = 1
				GROUP BY b.`parent`
			) AS c ON c.parent = a.`id`
			LEFT JOIN (
				SELECT *
				FROM tb_user_menu d
				WHERE d.`username` = '$username'
			) AS e ON e.id_menu = a.`id`
			WHERE a.`status` = 1
			AND a.`icon` <> ''
			AND a.`parent` = '$parent'
			ORDER BY a.`urutan` ASC ")->result();
	}

	public function create_json_jstree($parent = 0, $username = '')
	{
		$data = $this->jstree_menu($parent, $username);
		$response = [];
		$menu = [];
		$child_menu = [];

		if ($data) {
			foreach ($data as $row => $val) {
				$menu[$row] = [
					'id' => $val->id,
					'text' => lang($val->label),
					'icon' => $val->icon
				];

				if ($val->selected == 'true') {
					$menu[$row]['state'] = [
						'selected' => true,
						'opened' => true
					];
				}

				if ($val->jumlah > 0) {
					$children = $this->jstree_menu($val->id, $username);

					if ($children) {
						$child_menu = [];
						foreach ($children as $child => $child_val) {
							$child_menu[$child] = [
								'id' => $child_val->id,
								'text' => lang($child_val->label),
								'icon' => $child_val->icon
							];

							if ($child_val == 'true') {
								$child_menu[$child]['state'] = [
									'selected' => true,
									'opened' => true
								];
							}
						}

						$menu[$row]['children'] = $child_menu;
					}
				}
			}
		}

		return ['menu' => $menu];
	}

	public function dt_user()
	{
		return $this->db->query(" SELECT * FROM tb_user WHERE status = 1 ")->result_array();
	}

	public function simpan($menu, $username)
	{
		$data = [];

		$this->covidDb->trans_begin();

		$this->covidDb->where(['username' => $username])->delete('tb_user_menu');

		for ($i = 0; $i < count($menu); $i++) {
			$data[] = [
				'id_menu' => $menu[$i],
				'username' => $username,
				'user_insert' => $this->session->userdata('username')
			];
		}

		$this->covidDb->insert_batch('tb_user_menu', $data);

		if ($this->covidDb->trans_status()) {
			$this->covidDb->trans_commit();

			$status = 1;
			$message = 'Data berhasil disimpan';
		} else {
			$this->covidDb->trans_rollback();

			$status = 0;
			$message = 'Gagal menyimpan data';
		}

		$result = [
			'status' => $status,
			'message' => $message
		];

		return $result;
	}
}

/* End of file Menu_Model.php */
/* Location: ./application/models/Menu_Model.php */ ?>