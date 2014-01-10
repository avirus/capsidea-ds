<?php
// cbr fetcher $Author: slavik $ 
function askhost($url,  $tmoutms = 8000) {
	$fp=curl_init();
	curl_setopt($fp, CURLOPT_URL, $url);
	curl_setopt($fp, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($fp, CURLOPT_NOPROGRESS, TRUE);
	curl_setopt($fp, CURLOPT_TIMEOUT, ($tmoutms/1000));
	$data = curl_exec($fp);
	curl_close($fp);
	return $data;
};
$ref=$_SERVER['HTTP_REFERER'];
$token=substr($ref, (strpos($ref, "token=")+6));
$secret=sha1("55c1a309-45d6-475d-be95-b41f86f7bd71".$token);

$currency=array("R01235" => "US Dollar", "R01010" => "Australian Dollar", "R01239" => "Euro",
		"R01820" => "Japanese Yen",	"R01035" => "British Pound Sterling","R01375" => "China Yuan",
		"R01115" => "Brazil Real",	"R01270" => "Indian Rupee",	"R01815" => "South Korean Won",	"R01350" => "Canadian Dollar");
//prepare parameters
$selected=array();
for ($i=1;$i<10;$i++){	if (isset($_POST["element_3_$i"])) $selected[$i]= $_POST["element_3_$i"];}
//print_r($selected); //debug
$date1=sprintf("%02d",$_POST["element_1_1"])."/".sprintf("%02d",$_POST["element_1_2"])."/".$_POST["element_1_3"];
$date2=sprintf("%02d",$_POST["element_2_1"])."/".sprintf("%02d",$_POST["element_2_2"])."/".$_POST["element_2_3"];
error_reporting(E_ALL);
ini_set('display_errors', 1);
$kurs=array();
//print_r($currency); //debug
foreach ($selected as $key => $value) {
	$data=askhost("http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$date1&date_req2=$date2&VAL_NM_RQ=$value");
	//echo $data; // debug
$xml = simplexml_load_string($data);
foreach($xml->Record as $item) {
	$kdate=(string)$item['Date'];
	$kval=(string)$item->Value;
	$kq=(string)$item->Nominal;
	$kval=floatval(str_replace(",", ".", $kval))/$kq;
	//echo "$kdate: $kval\n";
	$kurs[$kdate][$value]=array('price'=>$kval);
} // foreach record
} // foreach curr
//print_r($kurs)
$fname="/var/www/slavik/data/tmp/cbr_".rand(1000000, 9999999).".csv";
$fp=fopen("$fname", "w");
fwrite($fp, "DATE");
foreach ($selected as $key => $value) {
	$kname=$currency["$value"];
	fwrite($fp, ",$kname");
}
fwrite($fp, "\n");
foreach ($kurs as $key => $item)
{
	$ts=strtotime($key);
	$ts_full=date("Y-m-d H:00:00",$ts);
	fwrite($fp, "$ts_full");
	//print_r($item);
	foreach ($selected as $kid => $kname)
	{
		if (!isset($item[$kname])) $val="NULL"; else $val=$item[$kname]["price"];
		
		//print_r($val);
		fwrite($fp, ",$val");
	} // for
	fwrite($fp, "\n");
}// foreach kurs
fclose($fp);
//$zip = new ZipArchive();
//$filename = $fname.".zip";
//if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {	die("cant open <$filename>\n");}
//$zip->addFile($fname);
//$zip->close();

//$file_name_with_full_path = realpath("$fname");
$post = array('extra_info' => '123456','file_contents'=>'@'.$fname);
$ch = curl_init();
$url="http://91.225.218.179:8080/capsidea/api?s=ImportService&delimeter=,&nullstr=NULL&withheader=1&name=currency";
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST,1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("appid: 182","sig: $secret"));
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$result=curl_exec ($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close ($ch);
if (500!=$httpcode) {
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\">
</head><body id=\"main_body\" ><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
<h1><a>Source created</a></h1>
<form id=\"form_764903\" class=\"appnitro\"  method=\"post\" action=\"form.html?token=$token\">

<li class=\"buttons\"><input type=\"hidden\" name=\"form_id\" value=\"764903\" />
<input id=\"saveForm\" class=\"button_text\" type=\"submit\" name=\"Create one more\" value=\"Submit\" /></li></ul></form><div id=\"footer\">                                                                                                                                                             
</div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
if (!isset($_POST["element_4_1"])) {
	//save autoupdate
	$jsonres=json_decode($result, true);
$key=$jsonres['key'];		
	$pg_host="host=0.0.0.0 port=0 dbname=srvstat user=postgres password=capsidea connect_timeout=30";
	$dbconn = pg_connect($pg_host)
	or die('Could not connect: ' . pg_last_error());
	$sdata=base64_encode(serialize($selected));
	pg_query("delete from updates where ikey=$key");
	pg_query("insert into updates (ikey, ival, idate ) values ($key, \"$sdata\", CURRENT_TIMESTAMP);");
		
}



die;}
else {echo "ERR<br> debug info:";echo $result;die;} 

?>
