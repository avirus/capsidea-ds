<?php
// cbr application for capsidea
include_once 'cbr-inc.php'; 
$cdata=get_capsidea_data($capsidea_client_secret);
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
for ($i=1;$i<10;$i++){
	if (isset($_POST["element_3_$i"])) $selected[$i]= $_POST["element_3_$i"];
}
$date1=strtotime(sprintf("%02d",$_POST["element_1_1"])."/".sprintf("%02d",$_POST["element_1_2"])."/".$_POST["element_1_3"]); // d/m/Y
$date2=strtotime(sprintf("%02d",$_POST["element_2_1"])."/".sprintf("%02d",$_POST["element_2_2"])."/".$_POST["element_2_3"]);
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
$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200==$httpcode) {
if (isset($_POST["element_4_1"])) {	// autoupdate?
	$sdata=base64_encode(serialize($selected));
	$clients_collection = $db->cbrclients;
	$clients_collection->findAndModify(array("schemakey" => $key), array("schemakey" => $key, "selected"=>$sdata), null, array("upsert" => true ));
} // autoupdate?
echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script>";
echo "<script>CI.updateSource($key);CI.createDashboard($jsondash);CI.closeApp();</script>";	
echo "<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head><body id=\"main_body\" ";
echo "><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
<h1><b>Source $key created</b></h1><br>RES:  <pre>$error_log</pre><br><div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
mylog("OK: web $key in $capsidea_time sec");
die();
} else { // httpcode!=200
	mylog("ERR: web $key in $capsidea_time sec \n $error_log");
	die();
} 
?>
