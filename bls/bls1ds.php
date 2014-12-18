<?php
// bls data connector for capsidea
//http://www.bls.gov/help/hlpforma.htm
//BLS part 1, National Employment, Hours, and Earnings
// http://www.bls.gov/ces/cesprog.htm

echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script>";
echo "<script>function doit(key) {CI.updateSource(key);CI.openSource(key);CI.closeApp();}</script>";
//echo "<script>CI.closeApp();</script>";
echo "<title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head>
<body id=\"main_body\"> ";
//echo "onload=\"CI.openSource($key)\"";
//echo "onload=\"CI.createDashboard(JSON.parse('$jsondash'));CI.closeApp();\"";
echo "please wait until Capsidea.com processing data<br>";
flush();
ob_flush();
flush();
ob_flush();
flush();
ob_flush();
flush();
ob_flush();

include_once 'bls1inc.php';

$cdata=get_capsidea_data($capsidea_client_secret);
$secret=$cdata["t"];
//$my_data_dir="/tmp";
include_once 'askhost.php';

$dt=get_datatype1();
$industry=get_datatype2();
$fname=tempnam("/tmp", "bls1");
$csvname=get_data_from_mongo($dt, $industry, $fname);
zip_compress_csv($fname.".zip",$csvname);
@unlink($fname);

$array1=create_list_of_measures($dt);
$measures="";
foreach ($array1 as $indkey => $val) {
	$measures=$measures."{Name: \"$val\", TypeName:\"double\", Caption: \"".strtolower($dt[$indkey])."\" Description:\"".strtolower($dt[$indkey])."\" },";
}

$schemajson="&fields=".urlencode('[{Name: "date", TypeName:"timestamp" },'.$measures.'{Name: "industry", TypeName:"string", Description:"Industry sector name" }]');




$host_reply=askhost($server_url.$schemajson, array('file_contents'=>'@'.$fname.".zip"),"","","",800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
//echo "done\n";
$httpcode=$host_reply["httpcode"];
$jsonres=json_decode($host_reply["data"], true);
$key=(int)$jsonres["Key"];
if (0==$key) $key=$cdata["k"];
$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200==$httpcode) {
	unlink($fname.".zip");
	//echo "$schemajson<br>";
	echo "done, press button to continue<input id=\"save\" class=\"button_text\" type=\"submit\" name=\"submit\" value=\"Done\" onclick=\"doit($key)\"/></body></html>";
	die();
}	else {echo $fname.".zip"."<br>ERROR $httpcode<br>debug info: $error_log <br>secret:".$cdata["t"];die();}




?>
