<?php
// crunchbase data connector for capsidea
include 'cb-inc.php';
error_reporting(E_ERROR);
$cdata=get_capsidea_data2($capsidea_client_secret);
$secret=$cdata["t"];
$apikey=get_api_key();$apikey=get_api_key();$apikey=get_api_key();$apikey=get_api_key();
$schemakey="";

$m = new MongoClient();
$db = $m->crunchbase;


//die("\n$fname done");

$report_fname=prepare_report($db);
//$report_fname=prepare_report();

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script>";
echo "<script>function doit(key) {CI.updateSource(key);CI.openSource(key);CI.closeApp();}</script>";
//echo "<script>CI.closeApp();</script>";
echo "<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head>
<body id=\"main_body\"> ";
echo "please standby while Capsidea.com processing report<br>NB: This can take a while, so be patient<br>";
flush();
ob_flush();
flush();
ob_flush();
flush();
ob_flush();
flush();
ob_flush();

$name=date("YMdHi");
$host_reply=askhost($server_url."&name=$name".$schemakey.$schemajson, array('file_contents'=>'@'.$report_fname),"","","",1800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
//echo " time ".$time3=((get_timer()-$stime)/1)." sec "; // $stime=get_timer();
$result=$host_reply["data"];
$error_log="cube: $schemakey"."secret: ".$secret."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (500==$host_reply["httpcode"]) {
	echo "ERR: $error_log\n".$host_reply["httpcode"];
	log_fatal("error 500 from cps: \n $error_log");
} // if 500
if (401==$host_reply["httpcode"]) {
	echo "ERR: unauthorized $error_log\n".$host_reply["httpcode"];
	log_fatal("error 401 from cps \n $error_log");
} // if 500
if (200==$host_reply["httpcode"]) {
	//echo "unlinking ".$report_fname."\n";
		
	//unlink($report_fname);
	//echo  "OK (http == 200)\n";
} else {echo  "UNK: $error_log\n".$host_reply["httpcode"];}


//die();
$js=json_decode($result);
$key=$js->Key;
echo "done, press button to continue<input id=\"save\" class=\"button_text\" type=\"submit\" name=\"submit\" value=\"Done\" onclick=\"doit($key)\"/></body></html><script>doit($key);</script>";
die;




?>