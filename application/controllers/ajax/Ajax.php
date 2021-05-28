<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require('./phpspreadsheet/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Ajax extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		if (! $this->input->is_ajax_request()) {
			show_404();
		}

		$this->load->model(['Login_model', 'Main_Model', 'User_Model', 'Report_Model']);
		$islogin = $this->Login_model->is_login();
		if ($islogin == FALSE) {
			redirect(base_url());
		}

		$this->load->library('form_validation');
	}

	public function summary_report()
	{
		$datestart = $this->input->get('datestart', TRUE);
		$dateend = $this->input->get('dateend', TRUE);

		$dateArr = getDatesFromRange($datestart, $dateend);

		$data = [
			'date' => $dateArr,
			'rendah' => $this->Report_Model->get_summary($datestart, $dateend, 'info'),
			'sedang' => $this->Report_Model->get_summary($datestart, $dateend, 'warning'),
			'tinggi' => $this->Report_Model->get_summary($datestart, $dateend, 'danger')
		];

		$view = $this->load->view('covid/ajax/tb_summary', $data, TRUE);

		echo $view;
	}

	public function category_report()
	{
		$category = $this->input->get('category', TRUE);
		$date = $this->input->get('date', TRUE);

		$report = $this->Report_Model->get_detail_report($category, $date);

		$data = [
			'report' => $report
		];

		$view = $this->load->view('covid/ajax/tb_details', $data, TRUE);

		echo $view;
	}

	public function detail_question()
	{
		$date = $this->input->get('date', TRUE);
		$nik = $this->input->get('nik', TRUE);

		$report = $this->Report_Model->get_detail_question($date, $nik);
		$personal = $this->Report_Model->personal_data($nik);
		$timestamp = $this->Report_Model->get_survey_timestamp($nik, $date);

		$data = [
			'report' => $report,
			'personal' => $personal,
			'timestamp' => $timestamp
		];

		$view = $this->load->view('covid/ajax/report_answer', $data, TRUE);

		echo $view;
	}

	public function get_survey_coordinates()
	{
		$date = $this->input->get('date', TRUE);
		$nik = $this->input->get('nik', TRUE);

		$data = $this->Report_Model->get_survey_coordinates($nik, $date);

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function get_high_risk_population()
	{
		$date = $this->input->get('date', TRUE);

		$data = $this->Report_Model->get_high_risk_population($date);

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function tb_deteksi_personal()
	{
		$startdate = $this->input->get('startdate', TRUE);
		$enddate = $this->input->get('enddate', TRUE);

		$dateRange = getDatesFromRange($startdate, $enddate);
		$personal_data = $this->Report_Model->personal_data();

		$data = [
			'date' => $dateRange,
			'personal_data' => $personal_data
		];

		$view = $this->load->view('covid/ajax/tb_deteksi_personal', $data, TRUE);

		echo $view;
	}

	public function tb_report_suhu()
	{
		$startdate = $this->input->get('startdate', TRUE);
		$enddate = $this->input->get('enddate', TRUE);

		$dateRange = getDatesFromRange($startdate, $enddate);
		$personal_data = $this->Report_Model->personal_data();

		$data = [
			'date' => $dateRange,
			'personal_data' => $personal_data
		];

		$view = $this->load->view('covid/ajax/tb_report_suhu', $data, TRUE);

		echo $view;
	}

	public function personal_id()
	{
		$nik = $this->input->get('nik', TRUE);

		$data = $this->Report_Model->personal_data($nik);

		$this->output->set_content_type('application/json')->set_output(json_encode($data));
	}

	public function add_personal()
	{
		$id = $this->input->post('id', TRUE);
		$nik = $this->input->post('nik', TRUE);
		$nama = $this->input->post('nama', TRUE);
		$tgllahir = $this->input->post('tgllahir', TRUE);
		$line = $this->input->post('line', TRUE);
		$team = $this->input->post('team', TRUE);
		$jabatan = $this->input->post('jabatan', TRUE);
		$gender = $this->input->post('gender', TRUE);
		$no_hp = $this->input->post('no_hp', TRUE);

		$tgllahir = custom_date_format($tgllahir, 'd/m/Y', 'Y-m-d');

		$data = [
			'nik' => $nik,
			'nama' => $nama,
			'line' => $line,
			'team' => $team,
			'jabatan' => $jabatan,
			'gender' => $gender,
			'tanggal_lahir' => $tgllahir,
			'country' => 'indo',
			'no_hp' => $no_hp
		];

		$condition = ($id != '') ? ['id' => $id] : [];

		$simpan = $this->Report_Model->add_personal($data, $condition);

		$status = 0;
		$message = 'Gagal menyimpan data';

		if ($simpan) {
			$status = 1;
			$message = 'Data berhasil disimpan';
		}

		$result = [
			'status' => $status,
			'message' => $message
		];

		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}

	public function delete_personal()
	{
		$nik = $this->input->post('nik', TRUE);

		$delete = $this->Report_Model->delete_personal($nik);

		$status = 0;
		$message = 'Gagal menghapus data';

		if ($delete) {
			$status = 1;
			$message = 'Data berhasil dihapus';
		}

		$result = [
			'status' => $status,
			'message' => $message
		];

		$this->output->set_content_type('application/json')->set_output(json_encode($result));
	}

	public function get_data_survey_by_tgl()
	{
		$tgl = $this->input->get('tgl', TRUE);

		$personal_data = $this->Report_Model->personal_data();

		$data = [
			'personal_data' => $personal_data,
			'pertanyaan' => $this->Report_Model->get_pertanyaan(),
			'tgl' => $tgl
		];

		$view = $this->load->view('covid/ajax/tb_pertanyaan', $data, TRUE);

		echo $view;
	}

	public function get_data_survey_by_pertanyaan()
	{
		$datestart = $this->input->get('datestart', TRUE);
		$dateend = $this->input->get('dateend', TRUE);
		$pertanyaan = $this->input->get('pertanyaan', TRUE);

		$personal_data = $this->Report_Model->personal_data();

		$data = [
			'personal_data' => $personal_data,
			'tgl' => getDatesFromRange($datestart, $dateend),
			'pertanyaan' => $pertanyaan
		];

		$view = $this->load->view('covid/ajax/tb_summary_pertanyaan', $data, TRUE);

		echo $view;
	}

	public function upload_personal()
	{
		$username = $this->input->post('username', TRUE);
		$now = $this->Main_Model->get_time('%Y-%m-%d %H:%i:%s');

		$config = [
			'upload_path' => './assets/uploads/personal/',
			'allowed_types' => 'xlsx|xls',
			'file_name' => date('YmdHis').rand()
		];

		$this->load->library('upload', $config);

		if (! $this->upload->do_upload('file')) {
			$status = 0;
			$message = $this->upload->display_errors();
		} else {
			$filename = $this->upload->data('file_name');

			$extension = pathinfo($filename, PATHINFO_EXTENSION);

			if ($extension == 'xls') {
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
			} else {
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			}

			$spreadsheet = $reader->load('./assets/uploads/personal/'.$filename);
			$personalData = $spreadsheet->getSheetByName('List Karyawan')->toArray(null, true, true, true);

			$jumlahData = count($personalData);

			$dataToInsert = [];
			if ($jumlahData > 0) {
				foreach ($personalData as $row => $val) {
					if ($val['A'] != 'NIK' && $val['A'] != '') {
						$dataToInsert[] = [
							'nik' => $val['A'], 
							'nama' => $val['B'],
							'line' => $val['D'],
							'team' => $val['E'],
							'jabatan' => $val['F'],
							'gender' => $val['G'],
							'tanggal_lahir' => custom_date_format($val['H'], 'd/m/Y', 'Y-m-d'),
							'no_hp' => $val['C'],
							'country' => 'indo'
						];

						$existsPersonalData = $this->Report_Model->personal_data($val['A']);
						if ($existsPersonalData) {
							$this->Report_Model->delete_personal($val['A']);
						}
					}
				}
			}

			$status = 0;
			$message = 'Gagal menyimpan data';
			if ($dataToInsert) {
				$simpan = $this->Report_Model->store_data('ms_personal_data', $dataToInsert, [], true);

				if ($simpan) {
					$status = 1;
					$message = 'Data berhasil disimpan';
				}
			}
		}

		$result = [
			'status' => $status,
			'message' => $message
		];

		echo json_encode($result);
	}
}

/* End of file Ajax.php */
/* Location: ./application/controllers/Ajax.php */ ?>