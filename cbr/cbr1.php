<?php
// cbr fetcher $Author: slavik $
include_once 'cbr-inc.php'; 
$ref=$_SERVER['HTTP_REFERER'];
$token=substr($ref, (strpos($ref, "token=")+6));
$secret=sha1($capsidea_client_secret.$token);

include_once 'cbr-inc.php'; 
//prepare parameters
$selected=array();
for ($i=1;$i<10;$i++){	if (isset($_POST["element_3_$i"])) $selected[$i]= $_POST["element_3_$i"];}
//print_r($selected); //debug
$date1=sprintf("%02d",$_POST["element_1_1"])."/".sprintf("%02d",$_POST["element_1_2"])."/".$_POST["element_1_3"];
$date2=sprintf("%02d",$_POST["element_2_1"])."/".sprintf("%02d",$_POST["element_2_2"])."/".$_POST["element_2_3"];
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
	//echo "$kdate: $kval\n"; // debug
	$kurs[$kdate][$value]=array('price'=>$kval);
} // foreach record
} // foreach curr
//print_r($kurs) // debug
$fname=create_csv_file($selected, $kurs,$currency);
//$file_name_with_full_path = realpath("$fname");
$host_reply=askhost($server_url, array('extra_info' => '123456','file_contents'=>'@'.$fname),"","","",8000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in cbr-inc.php
$httpcode=$host_reply["httpcode"];
$result=$host_reply["data"];
if (500!=$httpcode) {
if (isset($_POST["element_4_1"])) {
	//save autoupdate
	$jsonres=json_decode($result, true);
	$key=$jsonres["Key"];		
	$dbconn = pg_connect($pg_host) //defintd in cbr-inc.php
	or die('Could not connect: ' . pg_last_error());
	$sdata=base64_encode(serialize($selected));
	@pg_query("delete from updates where ikey=$key");
	@pg_query("insert into updates (ikey, ival, idate ) values ($key, '$sdata', CURRENT_TIMESTAMP);");
	$err=""; //.$result."<br>delete from updates where ikey=$key<br>insert into updates (ikey, ival, idate ) values ($key, '$sdata', CURRENT_TIMESTAMP);<br>".pg_last_error()."\n";
}
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\">
<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\">
</head><body id=\"main_body\" ><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
<h1><a>Source created</a></h1>
<a href=\"form.html?token=$token\">Create one more</a><br>RES: $err
<div id=\"footer\">
</div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
//echo $url.$httpcode.$result;
die;}
else {echo "<br>ERROR<br> debug info:";echo $result;die;} 

?>
