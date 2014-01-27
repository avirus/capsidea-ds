<?php
// cbr fetcher $Author: slavik $
include_once 'cbr-inc.php'; 
$ref=$_SERVER['HTTP_REFERER'];
$token=substr($ref, (strpos($ref, "token=")+6));
if (36!=strlen($token)) {die("token incorrect");}
$secret=sha1($capsidea_client_secret.$token);
include_once 'cbr-inc.php';
include_once 'askhost.php';
$selected=array();
for ($i=1;$i<10;$i++){	if (isset($_POST["element_3_$i"])) $selected[$i]= $_POST["element_3_$i"];}
$date1=sprintf("%02d",$_POST["element_1_1"])."/".sprintf("%02d",$_POST["element_1_2"])."/".$_POST["element_1_3"];
$date2=sprintf("%02d",$_POST["element_2_1"])."/".sprintf("%02d",$_POST["element_2_2"])."/".$_POST["element_2_3"];
//print_r($currency); //debug
foreach ($selected as $key => $value) {
	$data=askhost("http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$date1&date_req2=$date2&VAL_NM_RQ=$value");
$xml = simplexml_load_string($data);
foreach($xml->Record as $item) {
	$kdate=(string)$item['Date'];
	$kval=(string)$item->Value;
	$kq=(string)$item->Nominal;
	$kval=floatval(str_replace(",", ".", $kval))/$kq;
	//echo "$kdate: $kval\n"; // debug
	$kurs[$kdate][$value]=array('price'=>$kval);
} // foreach record
} // foreach curr
//print_r($kurs) // debug
$fname=create_csv_file($selected, $kurs,$currency);
$host_reply=askhost($server_url, array('file_contents'=>'@'.$fname),"","","",80000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
unlink($fname);
$httpcode=$host_reply["httpcode"];
$jsonres=json_decode($host_reply["data"], true);
$key=$jsonres["Key"];
$err=$host_reply["data"]."<br><pre>".$host_reply["d"]."</pre>";
if (500!=$httpcode) {
if (isset($_POST["element_4_1"])) {
	//save autoupdate
	$dbconn = pg_connect($pg_host) //defintd in cbr-inc.php
	or die('Could not connect: ' . pg_last_error());
	$sdata=base64_encode(serialize($selected));
	@pg_query("delete from updates where ikey=$key");
	@pg_query("insert into updates (ikey, ival, idate, iapp ) values ($key, '$sdata', CURRENT_TIMESTAMP,$capsidea_appid);");
	$err=$err."<br>delete from updates where ikey=$key<br>insert into updates (ikey, ival, idate ) values ($key, '$sdata', CURRENT_TIMESTAMP);<br>".pg_last_error()."\n";
}
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://alpha.capsidea.com/api.js\"></script><title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\">
</head><body id=\"main_body\" onload=\"CI.openSource($key)\"><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
<h1><a>Source created</a></h1><br>RES: $err <div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
die;}
else {echo "<br>ERROR $httpcode<br> debug info:";die;} 
?>
