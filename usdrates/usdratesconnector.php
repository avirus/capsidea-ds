<?php 
// federalreserve data connector for capsidea
include_once 'usdrates-inc.php';
file_put_contents("/tmp/debug.log", date(DATE_ATOM)." usdrates-dc ".print_r($_REQUEST,true)." \n", FILE_APPEND);
$capsidea_appid=2233;
$capsidea_client_secret="put-your-data-here";

$cdata=get_capsidea_data($capsidea_client_secret);
$secret=$cdata["t"];
$schemakey="";
if (isset($cdata["k"])) { // check, if this is reinitialisation
	$cps_key=$cdata["k"];
	$schemakey="&schemakey=$cps_key";
}
//$my_data_dir="/tmp";
include_once 'askhost.php';

$host_reply=askhost($server_url.$schemakey."&frequency=daily", array('file_contents'=>'@'."$my_data_dir/usrates.csv"),"","","",80000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
$httpcode=$host_reply["httpcode"];
$jsonres=json_decode($host_reply["data"], true);
$key=(int)$jsonres["Key"];
if (0==$key) $key=$cdata["k"];
//construct dashboard json
$i=0;
$sheets="";
$datasources="";
//$usedkeys=array();
//$ds_template=file_get_contents("cbr1-dst.json");
//$sheet_template=file_get_contents("cbr1-st.json");
// $jsondash="{   \"Sources\":{  myfulldatasources },    \"CalcMembers\":null,   \"Sheets\":[ myfulldatasheets ],
//    \"ActiveSheetId\":\"myfirstsheetid\",   \"SheetsVisible\":true,   \"FixedSheetWidth\":false,   \"SheetWidth\":842,   \"Font\":{\"FontFamily\":\"DefaultFont\", \"Size\":12,
// \"Color\":\"#000000\",\"IsBold\":false,\"IsItalic\":false,\"IsUnderline\":false},
//    \"Name\":\"Currency generated\",   \"Description\":\"Currency dashboard\",   \"Version\":\"6\"} ";
// $orig=array("currencyname","mydatasourceid", "currencyvname", "mycapsideacomdatasetkey", "mysheetname", "mysheetid", "mydatablockid", "\$currency");
// foreach ($cname as  $value) {
// 	$kname=$value;
// 	$dskey="ds_".generateuniqid($usedkeys);
// 	$skey="sheet_".generateuniqid($usedkeys);
// 	$dbkey="datablock_".generateuniqid($usedkeys);
// 	$capsideaname=strtolower( str_replace(" ", "_", $kname));
// 	$repl=array($capsideaname, $dskey,$kname, $key, $kname, $skey, $dbkey, "\$usdcurrency" );
// 	$this_ds=str_replace($orig, $repl, $ds_template);
// 	$this_s=str_replace($orig, $repl, $sheet_template);
// 	if (0!=$i) {
// 		$datasources=$datasources.",";
// 		$sheets=$sheets.",";
// 	} else {
// 		$first_sheet_id=$skey;
// 	}
// 	$datasources=$datasources.$this_ds;
// 	$sheets=$sheets.$this_s;
// 	$i++;
// }
//$orig=array("myfulldatasheets", "myfulldatasources", "myfirstsheetid");
//$repl=array($sheets, $datasources, $first_sheet_id);
//$jsondash=str_replace($orig, $repl, $jsondash);
//file_put_contents("$my_data_dir/dashusd.json", $jsondash);
//$jsondash=json_encode(json_decode($jsondash)); // strip formatting
//$jsondash=str_replace("\"", "\\\"", $jsondash);
// end of json construction
$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200==$httpcode) {
	if (isset($_POST["element_4_1"])) {
		//save autoupdate
		$dbconn = pg_connect($pg_host) //defintd in cbr-inc.php
		or die('Could not connect: ' . pg_last_error());
		$sdata="1";
		@pg_query("delete from updates where ikey=$key");
		@pg_query("insert into updates (ikey, ival, idate, iapp ) values ($key, '$sdata', CURRENT_TIMESTAMP,$capsidea_appid);");
		$error_log=$error_log."<br>delete from updates where ikey=$key<br>insert into updates (ikey, ival, idate ) values ($key, '$sdata', CURRENT_TIMESTAMP);<br>".pg_last_error()."\n";
	}
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script>";
	echo "<script>CI.updateSource($key);CI.openSource($key);CI.closeApp();</script>";
	echo "<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head><body id=\"main_body\" ";
	//echo "onload=\"CI.openSource($key)\"";
	//echo "onload=\"CI.createDashboard(JSON.parse('$jsondash'));CI.closeApp();\"";
	echo "><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
	<h1><b>Source $key created</b></h1><br>RES:  <pre>$error_log</pre><br><div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
	die();}
else {echo "<br>ERROR $httpcode<br>debug info: $error_log <br>secret:".$cdata["t"];die();}

?>