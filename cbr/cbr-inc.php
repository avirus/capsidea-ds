<?php 
$currency=array("R01235" => "US Dollar", "R01010" => "Australian Dollar", "R01239" => "Euro",
		"R01820" => "Japanese Yen",	"R01035" => "British Pound Sterling","R01375" => "China Yuan",
		"R01115" => "Brazil Real",	"R01270" => "Indian Rupee",	"R01815" => "South Korean Won",	"R01350" => "Canadian Dollar");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$server_url="http://beta.capsidea.com/api?s=ImportService&delimeter=,&nullstr=&reload=1&withheader=1&name=currency";
$capsidea_appid=182;
$capsidea_client_secret="put-your-data-here"; // put-your-data-here
$capsidea_permanent_access_token="put-your-data-here";
$my_data_dir="/var/www/slavik/data/tmp";
$kurs=array();
function get_capsidea_data2($capsidea_client_secret)
{
	$ret=array();
	if (isset($_GET["token"])) {$token=$_GET["token"];} else {die ("cant find capsidea.com token, please contact application support");}
	if (36!=strlen($token)) {die("capsidea.com token incorrect, please contact application support");}
	$ret["c"]=$str=preg_replace('/[^A-Za-z0-9\-]/', '', $token);
	$ret["t"]=sha1($capsidea_client_secret.$token);
	if (isset($_GET["schemakey"])) $ret["k"]=(int)$_GET["schemakey"];
	return $ret;
}
function get_capsidea_data($capsidea_client_secret)
{
    $ret=array();
    $parsed_url=parse_url($_SERVER['HTTP_REFERER']);
    $var  = explode('&', $parsed_url['query']);
    foreach($var as $val)
    {
	$x          = explode('=', $val);
	$arr[$x[0]] = $x[1];
    }
    unset($val, $x, $var, $qry, $parsed_url, $ref);
    if (isset($arr["token"])) {$token=$arr["token"];} else {die ("cant find capsidea.com token, please contact application support");}
    if (36!=strlen($token)) {die("capsidea.com token incorrect, please contact application support");}
    $ret["c"]=$str=preg_replace('/[^A-Za-z0-9\-]/', '', $token);
    $ret["t"]=sha1($capsidea_client_secret.$token);
    if (isset($arr["schemakey"])) $ret["k"]=(int)$arr["schemakey"];
    return $ret;
}
function generatehash($length = 20) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function generateuniqid(&$used, $length = 20) {
	// $used array contains used elements
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = generatehash($length);
	if (!isset($used[$randomString])) {
		$used[$randomString]=1;
		return $randomString;
	} else {
		$randomString=generateuniqid($used);
		$used[$randomString]=1;
		return $randomString;
	};
}

function save_array_as_csv($selected, $kurs){
	// save array as csv, produce header first
	global $my_data_dir, $currency;
	$fname=tempnam($my_data_dir,"currency_data_").".csv";
	$fw=fopen($fname, "w");
	$column_names=array();
	foreach ($selected as $this_selected) {
		$column_names[]=$currency[$this_selected];
	}
	array_unshift($column_names, "date");
	fputcsv($fw, $column_names);
	foreach ($kurs as $kdate => $line) {
		array_unshift($line, $kdate);
		fputcsv($fw, $line);
	}
	fclose($fw);
	return $fname;
}

function load_array_from_mongo($data_collection, $rangeQuery,$selected)
{
	$cursor2=$data_collection->find($rangeQuery);
	$kurs=array();
	foreach ($cursor2 as $data_item) {
		// generate array, to avoid possible duplicates
		$this_item_date=date("Y-m-d H:00:00", $data_item["ts"]);
		foreach ($selected as $curr_name ) {
			if (isset($data_item[$curr_name])) {
				$kurs[$this_item_date][$curr_name]=$data_item[$curr_name];
			}
			else {
				$kurs[$this_item_date][$curr_name]=null;
			}
		}
	} // foreach kursor
	return $kurs;	
}

function save_array_to_mongo($kurs, $currency, $collection)
{
	foreach ($kurs as $kurs_ts => $this_line) {
		$document=array();
		$c=0;
		$document["ts"]=$kurs_ts;
		foreach ($currency as $c_key => $c_value) {
			if (isset($this_line[$c_key])) {
				$document[$c_key]=$this_line[$c_key];
				$c++;
			}
			else {
				$document[$c_key]=null;
			}
		}
		if (0!=$c) $collection->insert($document);
	} // foreach date
	return true;
}

function download_data_from_cbr($date1, $currency)
{
	$date2=date("d/m/Y");
	$kurs=array();
	echo " download";
	foreach ($currency as $c_key => $c_value) {
		$data=askhost("http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$date1&date_req2=$date2&VAL_NM_RQ=$c_key");
		$xml = simplexml_load_string($data);
		foreach($xml->Record as $item) {
			$this_record_ts=strtotime((string)$item['Date']);
			$this_currency_quantity=floatval((string)$item->Nominal);
			$this_currency_cost=floatval(str_replace(",", ".", (string)$item->Value))/($this_currency_quantity);
			$kurs[$this_record_ts][$c_key]=$this_currency_cost;
		} // foreach record
	} // foreach curr
	return $kurs;
}

function  mylog($line) {
	global $my_data_dir;
	file_put_contents("$my_data_dir/cbr.log", date(DATE_ATOM)." $line\n", FILE_APPEND);
}
function get_timer()
{
	$mtime = microtime ();
	$mtime = explode ( " ", $mtime );
	$mtime = $mtime [1] + $mtime [0];
	return  $mtime;
}

?>