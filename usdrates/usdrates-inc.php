<?php 
$server_url="http://beta.capsidea.com/api?s=ImportService&delimeter=,&nullstr=null&reload=1&withheader=1&name=usdcurrency";
$cname=array("BRD" => "Broad currency index",
		"MJC" => "Major currency index",
		"OIT" => "OITP currency index",
		"AUD"=> "Australian Dollar",
		"EUR"=> "Euro",
		"NZD"=> "New Zealand Dollar",
		"JPY" => "Japanese Yen",
		"GBP" => "British Pound Sterling",
		"ZAL"=>"South Africa Rand",
		"BRL"=>"Brazil Real",
		"DKK"=>"Danish Krone",
		"HKD"=>"Hong Kong Dollar",
		"MYR"=>"Malaysian Ringgit",
		"MXN"=>"Mexican Peso",
		"NOK"=>"Norwegian Krone",
		"SGD"=>"Singapore Dollar",
		"LKR"=>"Sri Lankan Rupee",
		"SEK"=>"Swedish Krona",
		"CHF"=>"Swiss Franc",
		"TWD"=>"New Taiwan Dollar",
		"THB"=>"Thai Baht",
		"VEB"=>"Venezuela Bolivar",
		"CNY" => "China Yuan",
		"INR" => "Indian Rupee",
		"KRW" => "South Korean Won",
		"CAD" => "Canadian Dollar");
$my_data_dir="/tmp";
$capsidea_appid=1518;
$pg_host="host=1.2.3.208 port=31337 dbname=apps user=capsidea password=31337 connect_timeout=30";
$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";

function get_capsidea_data($capsidea_client_secret)
{
	$ret=array();
	if (isset($_GET["token"])) {$token=$_GET["token"];} else {die ("cant find capsidea.com token, please contact application support");}
	if (36!=strlen($token)) {die("capsidea.com token incorrect, please contact application support");}
	$ret["c"]=$str=preg_replace('/[^A-Za-z0-9\-]/', '', $token);
	$ret["t"]=sha1($capsidea_client_secret.$token);
	if (isset($_GET["schemakey"])) $ret["k"]=(int)$_GET["schemakey"];
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


?>