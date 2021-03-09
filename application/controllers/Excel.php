<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Load library phpspreadsheet
require('./phpspreadsheet/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
// End load library phpspreadsheet

class Excel extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['Report_Model', 'Main_Model']);
	}

	public function replace_invalid_character($string)
	{
		$invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');

		for ($i = 0; $i < count($invalidCharacters); $i++) {
			if (strpos($string, $invalidCharacters[$i])) {
				$string = str_replace($invalidCharacters[$i], ' ', $string);
			}
		}

		return $string;
	}

	public function deteksi_mandiri()
	{
		$startdate = $this->input->get('startdate', TRUE);
		$enddate = $this->input->get('enddate', TRUE);
		$filename = 'data-deteksi-'.custom_date_format($startdate, 'Y-m-d', 'Ymd').'-'.custom_date_format($enddate, 'Y-m-d', 'Ymd');

		$dateRange = getDatesFromRange($startdate, $enddate);

		$spreadsheet = new Spreadsheet();
		// Set document properties
		$spreadsheet->getProperties()->setCreator('Fukuryo covid survey system')
			->setLastModifiedBy('Fukuryo covid survey system')
			->setTitle('Fukuryo covid survey system')
			->setSubject('Fukuryo covid survey system')
			->setDescription('Fukuryo covid survey system')
			->setKeywords('covid survey system')
			->setCategory('covid survey system');

		$merge = [];
		$styling = [];

		$text_middle = [
			'alignment' => [
			    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
			]
		];

		$font_green = [
			'font' => [
				'color' => [
					'argb' => '009900'
				],
				'bold' => true
			]
		];

		$font_red = [
			'font' => [
				'color' => [
					'argb' => 'FF0000'
				],
				'bold' => true
			]
		];

		$font_yellow = [
			'font' => [
				'color' => [
					'argb' => 'CCCC00'
				],
				'bold' => true
			]
		];

		$font_blue = [
			'font' => [
				'color' => [
					'argb' => '0080FF'
				],
				'bold' => true
			]
		];

		// Add some data
		$sheet = $spreadsheet->setActiveSheetIndex(0);

		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle('Data Deteksi Mandiri');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		$sheet->setCellValue('A1', 'NIK');
		$sheet->setCellValue('B1', 'NAMA');
		$sheet->setCellValue('C1', 'NO HP');
		$sheet->setCellValue('D1', 'LINE');
		$sheet->setCellValue('E1', 'TANGGAL');

		$merge[] = 'A1:A2';
		$merge[] = 'B1:B2';
		$merge[] = 'C1:C2';
		$merge[] = 'D1:D2';

		$baris = 5;
		if ($dateRange) {
			foreach ($dateRange as $tgl) {
				$kolom = excel_number_to_column_name($baris);

				$sheet->setCellValue($kolom."2", custom_date_format($tgl, 'Y-m-d', 'm/d'));
				$baris++;
			}
		}

		$merge[] = "E1:".$kolom."1";
		$kolom = excel_number_to_column_name($baris);
		$sheet->setCellValue($kolom."1", "TOTAL");

		$merge[] = $kolom."1:".$kolom."2";
		$personal = $this->Report_Model->personal_data();

		$row = 3;
		if ($personal) {
			foreach ($personal as $val) {
				$sheet->setCellValue("A$row", $val->nik);
				$sheet->setCellValue("B$row", $val->nama);
				$sheet->setCellValue("C$row", $val->no_hp);
				$sheet->setCellValue("D$row", $val->line);

				$baris = 5;
				$kolomAwal = excel_number_to_column_name($baris);
				if ($dateRange) {
					foreach ($dateRange as $dt) {
						$kolom = excel_number_to_column_name($baris);
						$nilai = $this->Report_Model->get_daily_score($val->nik, $dt);
						$level = $this->Report_Model->get_score_level($nilai);

						$sheet->setCellValue($kolom.$row, $nilai);

						if ($level == 'info') {
							$style = $font_blue;
						} else if ($level == 'warning') {
							$style = $font_yellow;
						} else {
							$style = $font_red;
						}

						$styling[] = ['col' => "$kolom$row", 'style' => $style];

						$baris++;
					}
				}
				$kolomAkhir = excel_number_to_column_name($baris);
				$kolom = excel_number_to_column_name($baris);
				$sheet->setCellValue($kolom.$row, "=SUM($kolomAwal$row:$kolomAkhir$row)");

				$row++;
			}
		}


		if ($merge) {
			foreach ($merge as $row) {
				$spreadsheet->getActiveSheet()->mergeCells($row);
			}
		}

		if ($styling) {
			foreach ($styling as $row) {
				$sheet->getStyle($row['col'])->applyFromArray($row['style']);
			}
		}


		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}

	public function laporan_suhu()
	{
		$startdate = $this->input->get('startdate', TRUE);
		$enddate = $this->input->get('enddate', TRUE);
		$filename = 'laporan-suhu'.custom_date_format($startdate, 'Y-m-d', 'Ymd').'-'.custom_date_format($enddate, 'Y-m-d', 'Ymd');

		$dateRange = getDatesFromRange($startdate, $enddate);

		$spreadsheet = new Spreadsheet();
		// Set document properties
		$spreadsheet->getProperties()->setCreator('Fukuryo covid survey system')
			->setLastModifiedBy('Fukuryo covid survey system')
			->setTitle('Fukuryo covid survey system')
			->setSubject('Fukuryo covid survey system')
			->setDescription('Fukuryo covid survey system')
			->setKeywords('covid survey system')
			->setCategory('covid survey system');

		$merge = [];
		$styling = [];

		$text_middle = [
			'alignment' => [
			    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
			]
		];

		$font_green = [
			'font' => [
				'color' => [
					'argb' => '009900'
				],
				'bold' => true
			]
		];

		$font_red = [
			'font' => [
				'color' => [
					'argb' => 'FF0000'
				],
				'bold' => true
			]
		];

		$font_yellow = [
			'font' => [
				'color' => [
					'argb' => 'CCCC00'
				],
				'bold' => true
			]
		];

		$font_blue = [
			'font' => [
				'color' => [
					'argb' => '0080FF'
				],
				'bold' => true
			]
		];

		// Add some data
		$sheet = $spreadsheet->setActiveSheetIndex(0);

		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle('Data Laporan Suhu');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		$sheet->setCellValue('A1', 'NIK');
		$sheet->setCellValue('B1', 'NAMA');
		$sheet->setCellValue('C1', 'NO HP');
		$sheet->setCellValue('D1', 'LINE');
		$sheet->setCellValue('E1', 'TANGGAL');

		$merge[] = 'A1:A2';
		$merge[] = 'B1:B2';
		$merge[] = 'C1:C2';
		$merge[] = 'D1:D2';

		$baris = 5;
		if ($dateRange) {
			foreach ($dateRange as $tgl) {
				$kolom = excel_number_to_column_name($baris);

				$sheet->setCellValue($kolom."2", custom_date_format($tgl, 'Y-m-d', 'm/d'));
				$baris++;
			}
		}

		$merge[] = "E1:".$kolom."1";
		$kolom = excel_number_to_column_name($baris);
		$sheet->setCellValue($kolom."1", "RATA-RATA");

		$merge[] = $kolom."1:".$kolom."2";
		$personal = $this->Report_Model->personal_data();

		$row = 3;
		if ($personal) {
			foreach ($personal as $val) {
				$sheet->setCellValue("A$row", $val->nik);
				$sheet->setCellValue("B$row", $val->nama);
				$sheet->setCellValue("C$row", $val->no_hp);
				$sheet->setCellValue("D$row", $val->line);

				$baris = 5;
				$kolomAwal = excel_number_to_column_name($baris);
				if ($dateRange) {
					foreach ($dateRange as $dt) {
						$kolom = excel_number_to_column_name($baris);
						$nilai = $this->Report_Model->get_suhu($val->nik, $dt);

						$sheet->setCellValue($kolom.$row, $nilai);

						$baris++;
					}
				}
				$kolomAkhir = excel_number_to_column_name($baris - 1);
				$kolom = excel_number_to_column_name($baris);
				$sheet->setCellValue($kolom.$row, "=AVERAGE($kolomAwal$row:$kolomAkhir$row)");

				$row++;
			}
		}

		if ($merge) {
			foreach ($merge as $row) {
				$spreadsheet->getActiveSheet()->mergeCells($row);
			}
		}

		if ($styling) {
			foreach ($styling as $row) {
				$sheet->getStyle($row['col'])->applyFromArray($row['style']);
			}
		}

		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}

	public function survey_result_by_question()
	{
		$pertanyaan = $this->input->get('pertanyaan', TRUE);
		$datestart = $this->input->get('datestart', TRUE);
		$dateend = $this->input->get('dateend', TRUE);

		$dateRange = getDatesFromRange($datestart, $dateend);
		$pertanyaan = explode(',', $pertanyaan);

		$allPertanyaan = [];
		if ($pertanyaan) {
			for ($i = 0; $i < count($pertanyaan); $i++) {
				$parentPertanyaan = $this->Main_Model->view_data_covid('ms_pertanyaan', ['id' => $pertanyaan[$i]], false);

				$allPertanyaan[] = [
					'id' => $pertanyaan[$i],
					'pertanyaan' => $parentPertanyaan->pertanyaan
				];

				$hasChild = $this->Main_Model->view_data_covid('ms_pertanyaan', ['id_parent' => $pertanyaan[$i]], true);
				if ($hasChild) {
					foreach ($hasChild as $row) {
						$allPertanyaan[] = [
							'id' => $row->id,
							'pertanyaan' => $row->pertanyaan
						];
	 				}
				}
			}
		}

		$jumlahPertanyaan = count($allPertanyaan);

		$spreadsheet = new Spreadsheet();
		// Set document properties
		$spreadsheet->getProperties()->setCreator('Fukuryo covid survey system')
			->setLastModifiedBy('Fukuryo covid survey system')
			->setTitle('Fukuryo covid survey system')
			->setSubject('Fukuryo covid survey system')
			->setDescription('Fukuryo covid survey system')
			->setKeywords('covid survey system')
			->setCategory('covid survey system');

		// Add some data
		$sheet = $spreadsheet->setActiveSheetIndex(0);

		// Rename worksheet
		$spreadsheet->getActiveSheet()->setTitle('Report Pertanyaan');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$spreadsheet->setActiveSheetIndex(0);

		$sheet->setCellValue('A1', 'NIK');
		$sheet->setCellValue('B1', 'NAMA');
		$sheet->setCellValue('C1', 'NO HP');
		$sheet->setCellValue('D1', 'LINE');
		$sheet->setCellValue('E1', 'TEAM');
		$sheet->setCellValue('F1', 'TANGGAL');

		if ($dateRange) {
			$kolomAwal = 6;
			foreach ($dateRange as $date) {
				$sheet->setCellValue(excel_number_to_column_name($kolomAwal) . '2', custom_date_format($date, 'Y-m-d', 'm/d'));

				$kolomAwal +=  $jumlahPertanyaan;
			} 
		}

		if ($allPertanyaan) {
			$kolomAwal = 6;
			foreach ($dateRange as $date) {
				foreach ($allPertanyaan as $row) {
					$sheet->setCellValue(excel_number_to_column_name($kolomAwal) . '3', $row['pertanyaan']);

					$kolomAwal += 1;
				}
			}
		}

		$personal_data = $this->Report_Model->personal_data();

		if ($personal_data) {
			$baris = 4;
			foreach ($personal_data as $row) {
				$kolomAwal = 6;

				$sheet->setCellValue('A' . $baris, $row->nik);
				$sheet->setCellValue('B' . $baris, $row->nama);
				$sheet->setCellValue('C' . $baris, $row->no_hp);
				$sheet->setCellValue('D' . $baris, $row->line);
				$sheet->setCellValue('E' . $baris, $row->team);

				if ($dateRange) {
					foreach ($dateRange as $date) {
						if ($allPertanyaan) {
							foreach ($allPertanyaan as $val) {
								$result = $this->Report_Model->get_pertanyaan_perhari($date, $row->nik, $val['id']);
								$jawaban = isset($result->answer) ? $result->answer : '';

								if ($result) {
									$sheet->setCellValue(excel_number_to_column_name($kolomAwal) . $baris, $jawaban);
								} else {
									$sheet->setCellValue(excel_number_to_column_name($kolomAwal) . $baris, '');
								}

								$kolomAwal += 1;
							}
						}
					}
				}

				$baris += 1;
			}
		}


		$filename = 'Report-pertanyaan-' . date('d-m-Y');
		// Redirect output to a client’s web browser (Xlsx)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('php://output');
		exit;
	}
}

/* End of file Excel.php */
/* Location: ./application/controllers/Excel.php */ ?>