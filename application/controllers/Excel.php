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
}

/* End of file Excel.php */
/* Location: ./application/controllers/Excel.php */ ?>