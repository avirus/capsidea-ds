<?php
// cbr fetcher $Author: slavik $
include_once 'cbr-inc.php'; 
$cdata=get_capsidea_data($capsidea_client_secret);
$secret=$cdata["t"];
include_once 'askhost.php';
$selected=array();
for ($i=1;$i<10;$i++){	if (isset($_POST["element_3_$i"])) $selected[$i]= $_POST["element_3_$i"];}
$date1=sprintf("%02d",$_POST["element_1_1"])."/".sprintf("%02d",$_POST["element_1_2"])."/".$_POST["element_1_3"];
$date2=sprintf("%02d",$_POST["element_2_1"])."/".sprintf("%02d",$_POST["element_2_2"])."/".$_POST["element_2_3"];
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
$fname=create_csv_file($selected, $kurs,$currency);
$host_reply=askhost($server_url, array('file_contents'=>'@'.$fname),"","","",80000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
unlink($fname);
$httpcode=$host_reply["httpcode"];
$jsonres=json_decode($host_reply["data"], true);
$key=(int)$jsonres["Key"];
if (0==$key) $key=$cdata["k"];
//construct dashboard json
$i=0;
$sheets="";
$datasources="";
$usedkeys=array();
$ds_template=file_get_contents("cbr1-dst.json");
$sheet_template=file_get_contents("cbr1-st.json");
$jsondash="{   \"Sources\":{  myfulldatasources },    \"CalcMembers\":null,   \"Sheets\":[ myfulldatasheets ],
   \"ActiveSheetId\":\"myfirstsheetid\",   \"SheetsVisible\":true,   \"FixedSheetWidth\":false,   \"SheetWidth\":842,   \"Font\":{\"FontFamily\":\"DefaultFont\", \"Size\":12,
\"Color\":\"#000000\",\"IsBold\":false,\"IsItalic\":false,\"IsUnderline\":false},
   \"Name\":\"Currency generated\",   \"Description\":\"Currency dashboard\",   \"Version\":\"6\"} ";
$orig=array("currencyname","mydatasourceid", "currencyvname", "mycapsideacomdatasetkey", "mysheetname", "mysheetid", "mydatablockid");
foreach ($selected as  $value) {
	$kname=$currency["$value"];
	$dskey="ds_".generateuniqid($usedkeys);
	$skey="sheet_".generateuniqid($usedkeys);
	$dbkey="datablock_".generateuniqid($usedkeys);
	$capsideaname=strtolower( str_replace(" ", "_", $kname));
	$repl=array($capsideaname, $dskey,$kname, $key, $kname, $skey, $dbkey );	
	$this_ds=str_replace($orig, $repl, $ds_template);
	$this_s=str_replace($orig, $repl, $sheet_template);
	if (0!=$i) {
		$datasources=$datasources.",";
		$sheets=$sheets.",";
	} else {
		$first_sheet_id=$skey;
	}
	$datasources=$datasources.$this_ds;
	$sheets=$sheets.$this_s;
	$i++;
}
$orig=array("myfulldatasheets", "myfulldatasources", "myfirstsheetid");
$repl=array($sheets, $datasources, $first_sheet_id);
$jsondash=str_replace($orig, $repl, $jsondash);
file_put_contents("$my_data_dir/dashcbr.json", $jsondash);
$jsondash=json_encode(json_decode($jsondash)); // strip formatting
//$jsondash=str_replace("\"", "\\\"", $jsondash);
// end of json construction 
$err="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200==$httpcode) {
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
<script type=\"text/javascript\" src=\"http://alpha.capsidea.com/api.js\"></script>";
echo "<script>CI.createDashboard(JSON.parse('$jsondash'));CI.updateSource($key);CI.closeApp();</script>";		
echo "<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head><body id=\"main_body\" ";
//echo "onload=\"CI.openSource($key)\"";
//echo "onload=\"CI.createDashboard(JSON.parse('$jsondash'));CI.closeApp();\"";
echo "><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
<h1><b>Source $key created</b></h1><br>RES:  <pre>$err</pre><br>$jsondash<div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
die();}
else {echo "<br>ERROR $httpcode<br>debug info: $err <br>secret:".$cdata["t"];die();} 
?>
