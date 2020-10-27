<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_Model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		
	}

	public function create_counter($kolom, $tabel, $periode, $prefix)
	{
		$last = $this->db->where(['periode' => $periode])
					->get($tabel)
					->row();

		$number = 1;
		if ($last) {
			$number = $last->$kolom + 1;
			$this->db->query(" UPDATE $tabel SET $kolom = '$number' WHERE periode = '$periode' ");
		} else {
			$this->db->query(" INSERT INTO $tabel ($kolom, periode) VALUES (1, '$periode') ");
		}

		$autonumber = '';
		if (strlen($number) <= 4) {
			for ($i = 0; $i < (4 - strlen($number)); $i++) {
				$autonumber .= '0';
			}
		}

		$number = $autonumber.$number;

		return $number.$prefix;
	}

	public function view_data($table, $condition, $multiple = 1)
	{
		$result = $this->db->where($condition)->get($table);

		return ($multiple == 1) ? $result->result() : $result->row();
	}

	public function get_time($format)
	{
		$sql = $this->db->query("SELECT DATE_FORMAT(NOW(), '$format') AS hasil ")->row();

		return isset($sql->hasil) ? $sql->hasil : '';
	}

	public function delete_data($table, $condition)
	{
		$this->db->where($condition)
			->delete($table);

		return $this->db->affected_rows();
	}

	public function store_data($table, $data, $condition = [], $bulk = false)
    {   
        $this->db->trans_begin();
        if ($condition) {
            $this->db->update($table, $data, $condition);
        } else {
            if ($bulk) {
                $this->db->insert_batch($table, $data);
            } else {
                $this->db->insert($table, $data);
            }
        }

        if ($this->db->trans_status()) {
            $this->db->trans_commit();

            return true;
        } else {
            $this->db->trans_rollback();

            return false;
        }
    }
}

/* End of file Main_Model.php */
/* Location: ./application/models/Main_Model.php */ ?>