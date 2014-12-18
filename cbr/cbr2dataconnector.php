<?php
// cbr data connector for capsidea
include_once 'cbr-inc.php'; 
$capsidea_appid=2235;
$capsidea_client_secret="put-your-data-here"; // put-your-data-here
$server_url="http://beta.capsidea.com/api?s=ImportService&delimeter=,&nullstr=&reload=1&withheader=1&name=rurrates&frequency=daily";

$cdata=get_capsidea_data2($capsidea_client_secret);
$secret=$cdata["t"];
$schemakey="";
if (isset($cdata["k"])) { // check, if this is reinitialisation
	$cps_key=$cdata["k"];
	$schemakey="&schemakey=$cps_key";
} 
$m = new MongoClient();
$db = $m->currency;
$data_collection = $db->cbr;
include_once 'askhost.php';
$selected=array();
foreach ($currency as $key => $value) {
	$selected[]=$key;
}
$date1="01/01/1990";
$date2=date("d/m/Y");
$rangeQuery = array('ts' => array( '$gt' => 0, '$lt' => time() )); // select all new records
$kurs=load_array_from_mongo($data_collection, $rangeQuery,$selected);
//file_put_contents("$my_data_dir/c.log",print_r($kurs,true));
$fname=save_array_as_csv($selected, $kurs);
$stime=get_timer();
$host_reply=askhost($server_url.$schemakey, array('file_contents'=>'@'.$fname),"","","",80000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
$capsidea_time=get_timer()-$stime;
unlink($fname);
$httpcode=$host_reply["httpcode"];
$jsonres=json_decode($host_reply["data"], true);
$key=(int)$jsonres["Key"];
if (0==$key) if (isset($cdata["k"])) { $key=$cdata["k"];} else {$key=0;}
$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200==$httpcode) {
	// autoupdate
	$sdata=base64_encode(serialize($selected));
	$clients_collection = $db->cbr2clients; // for dataconnector
	$clients_collection->findAndModify(array("schemakey" => $key), array("schemakey" => $key, "selected"=>$sdata), null, array("upsert" => true ));

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script>";
echo "<script>CI.updateSource($key);CI.openSource($key);CI.closeApp();</script>";	
echo "<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head><body id=\"main_body\" ";
echo "><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
<h1><b>Source $key created</b></h1><br>RES:  <pre>$error_log</pre><br><div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
mylog("OK: web $key in $capsidea_time sec");
die();
} else { // httpcode!=200
	mylog("ERR: web $key in $capsidea_time sec \n $error_log");
	die("unable to create dataset in capsidea.com, please contact support");
} 
?>
