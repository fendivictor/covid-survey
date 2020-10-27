<?php  
function arrbulan()
{
	return [
		1 => 'Jan',
		2 => 'Feb',
		3 => 'Mar',
		4 => 'Apr',
		5 => 'Mei', 
		6 => 'Jun',
		7 => 'Jul',
		8 => 'Aug',
		9 => 'Sep',
		10 => 'Oct',
		11 => 'Nop',
		12 => 'Dec'
	];
}

function arrtahun()
{
	$year = date('Y');

	return [ 
		$year,
		($year - 1)
	];
}

function get_last_day_of_month($date)
{
	$ci =& get_instance();

	$q = $ci->db->query("SELECT LAST_DAY('$date') AS tgl")->row();

	return isset($q->tgl) ? $q->tgl : '';
}

function format_tgl($date)
{
	$date = date_create($date);
	$date = date_format($date, 'd/m/Y');

	return $date;
}

function dt_searching($kolom, $keyword)
{
	$condition = '';
	if ($keyword <> '') {
		$condition .= " AND ( ";
		for ($i = 0; $i < count($kolom); $i++) {
			$condition .= " $kolom[$i] LIKE '%$keyword%' ";

			if (end($kolom) <> $kolom[$i]) {
				$condition .= " OR ";
			}
		}
		$condition .= " ) ";
	}

	return $condition;
}

function dt_order($kolom, $order_column, $order_mode)
{
	$order = " ORDER BY $kolom[$order_column] $order_mode ";

	return $order;
}

function to_sql_date($date, $format)
{
	$date = date_create_from_format($format, $date);
	$date = date_format($date, 'Y-m-d');

	return $date;
}

function custom_date_format($date, $from, $to)
{
	if ($date <> '') {
		$date = date_create_from_format($from, $date);
		$date = date_format($date, $to);
	}

	return $date;
}

function sage_date($date)
{
	$date = explode('.', $date);
	$date = $date[0];
	$date = explode(' ', $date);
	$date = $date[0];

	return custom_date_format($date, 'Y-m-d', 'd/m/Y');
}

function barcode_to_lotnumber($barcode)
{
	$lot_results = '';
	$item_code = '';
	$lotnumber_value = '';

	if (substr($barcode, 0, 5) == ']d291') {
		$lot_results = substr_replace($barcode, '', 0, 5);
	} else if (substr($barcode, 0, 2) == '91') {	
		$lot_results = substr_replace($barcode, '', 0, 2);
	}

	if ($lot_results <> '') {
		$item_code = substr($lot_results, 0, 17);
		$lotnumber_value = substr($lot_results, 19, 35);
	}

	return [
		'item_code' => $item_code,
		'lotnumber' => $lotnumber_value
	];
}

function excel_number_to_column_name($number) {
    $numeric = ($number - 1) % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval(($number - 1) / 26);
    if ($num2 > 0) {
        return excel_number_to_column_name($num2) . $letter;
    } else {
        return $letter;
    }
}

function remove_special_characters($string) {
	if (strpos($string, '/')) {
		$string = str_replace('/', '1', $string);
	}

	if (strpos($string, '?')) {
		$string = str_replace('?', '2', $string);
	}

	if (strpos($string, '(')) {
		$string = str_replace('(', '3', $string);
	}

	if (strpos($string, ')')) {
		$string = str_replace(')', '4', $string);
	}

	return $string;
}

function kode_to_jenis($kode)
{
	$data = [
		'MJ' => 'MEN JAS',
		'LJ' => 'LADIES JAS',
		'MP' => 'MEN PANTS',
		'MV' => 'MEN VEST',
		'LP' => 'LADIES PANTS',
		'LS' => 'LADIES SKIRT',
		'FG' => 'FINISH GOOD'
	];	

	return isset($data[$kode]) ? $data[$kode] : '';
}

function kode_jenis($kode)
{
	$data = [
		'MJ' => 'JAS',
		'LJ' => 'JAS',
		'MP' => 'PANTS',
		'MV' => 'VEST',
		'LP' => 'PANTS',
		'LS' => 'SKIRT',
		'FG' => 'FINISH GOOD'
	];	

	return isset($data[$kode]) ? $data[$kode] : '';
}

// Function to get all the dates in given range 
function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
	
	// Declare an empty array 
	$array = array(); 
	
	// Variable that store the date interval 
	// of period 1 day 
	$interval = new DateInterval('P1D'); 

	$realEnd = new DateTime($end); 
	$realEnd->add($interval); 

	$period = new DatePeriod(new DateTime($start), $interval, $realEnd); 

	// Use loop to store date into array 
	foreach($period as $date) {				 
		$array[] = $date->format($format); 
	} 

	// Return the array elements 
	return $array; 
} 
