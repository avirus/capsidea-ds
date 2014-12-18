<?php 
//$testrun=true;
$testrun=false;
//td: should use tempnam for user input
include_once 'paypal-inc.php';
$capsidea_appid=2239;
$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";
//$m = new MongoClient();
//$db = $m->paypal2dc;
//$clients_collection = $db->pp2clients; // for dataconnector
//$lr_collection = $db->pp2lastrun;
//$clients_collection->findAndModify(array("schemakey" => $key), array("schemakey" => $key, "selected"=>$sdata), null, array("upsert" => true ));

$stime1=get_timer();
$cdata=get_capsidea_data($capsidea_client_secret);

include_once 'askhost.php';
$dbconn = pg_connect($pg_host) or log_fatal('Could not connect to database, please contact support.');
$paypal_sftp_login=escapeshellarg($_POST["element_1"]);
$paypal_sftp_password=escapeshellarg($_POST["element_2"]);
$sftppwd=pg_escape_string($_POST["element_2"]);
$sftplog=pg_escape_string($_POST["element_1"]);
$eml=pg_escape_string($_POST["element_3"]);
$contact_name=pg_escape_string($_POST["element_4_1"])." ".pg_escape_string($_POST["element_4_2"]);
$company_name=pg_escape_string($_POST["element_5"]);
if (strlen($company_name)<3) die("Please provide company name, longer then 3 symbols.\n Press back and try again");
if (strlen($contact_name)<2) die("Please provide contact name.\n Press back and try again");
if (strlen($eml)<2) die("Please provide contact email.\n Press back and try again");
if (strlen($sftppwd)<2) die("Please provide reporting server password.\n Press back and try again");
if (strlen($sftplog)<2) die("Please provide reporting server login.\n Press back and try again");
//print_r($cdata);
$secret=$cdata["t"];
if (isset($cdata["k"])) { // check, if this is reinitialisation
	$key=(int)$cdata["k"];
	$row=@pg_fetch_assoc(pg_query("select ihash from client where ikey=$key"));
	$sk="&schemakey=".$key;
	$hash=$row["ihash"];
	if (strlen($hash)<10) { // workaround
		$hash=generatehash();
		$sk="";
		unset($key);		
	}
} else  {
	$hash=generatehash();
	$sk="";
}
$parent_key=0;
pg_prepare("query1", 'select * from client where sftplogin=$1 and iparent=0');
$result=pg_execute("query1", array($sftplog));
//$result=@pg_query("select * from client where sftplogin='$sftplog' and iparent=0");
if (@pg_num_rows($result)>0) {
	$row=pg_fetch_assoc($result);
	//$eml=$row["ieml"];
	$hash=$row["ihash"];
	$parent_key=$row["ikey"];
	file_put_contents($my_data_dir."/paypal_ds.log", date(DATE_ATOM)."already registered. client [$company_name] $contact_name l: $sftplog \n", FILE_APPEND);
	//die("this client already registered, please contact user $eml for shared datasource");
};
// send progress bar to client
file_put_contents($my_data_dir."/".$hash.".prc", "10");
$waiter=file_get_contents($wwwrealpath."/paypal-wait.html");
$waiter=str_replace("thekey", $hash, $waiter);
echo $waiter; // send page to user
flush();
ob_flush();
flush();



if (!$testrun) {
	$ret=get_file_list($paypal_sftp_login, $paypal_sftp_password);	
//$result=exec("./getpaypal-list-files.sh $paypal_sftp_login"."@reports.paypal.com $paypal_sftp_password 2>&1", $ret);
//$ftp_response=$result;
$stime2=get_timer();$list_time=($stime2-$stime1);
$some_data_found=0;
foreach ($ret as $fname) {
	if (FALSE===strpos($fname, "TRR-")) continue;
	$some_data_found=1;
}
if (($ret[3]==$ret[2])&&(FALSE!==strpos("Password Authentication", $ret[2]))) {
//    [2] => Password Authentication
//    [3] => Password Authentication	
	$resp="<b>unable to login into paypal transactions report sftp server (reports.paypal.com), <br>please check credentials and try again</b><br>If credentials valid, and you still get this message - please contact support.";
	file_put_contents($my_data_dir."/paypal_ds.log", date(DATE_ATOM)." ERR PWD: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
	file_put_contents($my_data_dir."/paypal_ds.log", print_r($ret, true)." \n", FILE_APPEND);
	file_put_contents($my_data_dir."/".$hash.".prc", "100");
	file_put_contents($my_data_dir."/".$hash.".resp", $resp);
	die();
}
if (0==$some_data_found) { // post fake data?
	$resp= "<b>unable to find any paypal transactions report files (/ppreports/outgoing/TRR-*), <br>Please contact support.";
	file_put_contents($my_data_dir."/paypal_ds.log", date(DATE_ATOM)." ERR NO FILES client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
	file_put_contents($my_data_dir."/paypal_ds.log", print_r($ret, true)." \n", FILE_APPEND);
	file_put_contents($my_data_dir."/".$hash.".prc", "100");
	file_put_contents($my_data_dir."/".$hash.".resp", $resp);
	die();
} // end of no-data
}// debug
//file_put_contents($my_data_dir."/".$hash.".prc", "20");
//$fname="$wwwrealpath/ppfake.csv";  // fake file
//$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=null&reload=1&withheader=1&name=dspaypal&reloaddim=1";
//$host_reply=askhost($server_url.$sk.$schemajson, array('file_contents'=>'@'.$fname),"","","",120000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
//$jsonres=json_decode($host_reply["data"], true);
//$ikey=(int)$jsonres["Key"];
//if (0==$ikey) $ikey=$cdata["k"]; // $ikey = something useful
//$sk="&schemakey=".$ikey;
// get real data - $ftp_response

if (0!=$parent_key) { //  uploading parent content
	file_put_contents($my_data_dir."/".$hash.".prc", "50");
	$source_id=$parent_key;
	$cases=load_cases_from_db($source_id);
	$ware=array();
	$merchant=load_merchant_data_from_db($source_id, $ware);
	$cntryarr=load_countries_from_csv("./country.csv");
	$report_fname=generate_application_report_to_cvs($tmp_dir,$dbconn,$source_id,$cases,$merchant,$ware,$cntryarr);
	unset($cases);
	unset($merchant);
	unset($ware);
}
// else { // bad luck, jfdi - load data, process, link, and generate report
// 	$file_num=0;
// 	foreach ($ret as $fname) {
// 		$merchant_cube_id=$ikey;
// 		file_put_contents($my_data_dir."/".$hash.".prc", 11+$file_num); // report progress
// 		$client_dir=$tmp_dir.$merchant_cube_id."/";
// 		if ((FALSE===strpos($fname, "TRR-"))&&(FALSE===strpos($fname, "DDR-"))) continue; // unknown file, skip it
// 		if (FALSE!==strpos($fname, ".gz")) {$dbg=$dbg."archive $fname\n";continue;} // file is archive (empty? broken?)
// 		$realfname=$fname;
// 		$fname=str_replace("/ppreports/outgoing/", "", $realfname);
// 		//$fname=str_replace(".gz", "", $fname);
// 		if (is_file_already_loaded($fname,$merchant_cube_id)) continue; // file already loaded
// 		echo "get file $realfname\n";
// 		$stime=get_timer();
// 		$txn=get_file_content($paypal_sftp_login, $paypal_sftp_password, $realfname,$client_dir,$fname); // get file from sftp
// 		echo " time ".((get_timer()-$stime)/1)." sec ";
// 		if (FALSE!==strpos($fname, "TRR-")) {
// 			$file_num++;
// 			echo " processing TRR file $fname:";
// 			$fid=mark_file_as_processed($fname,$merchant_cube_id,1);
// 			$records=process_paypal_txns($txn,$dbconn,$merchant_cube_id, $fid);
// 			echo " $records ";
// 		}
// 		if (FALSE!==strpos($fname, "DDR-")) {
// 			$file_num++;
// 			echo " processing DDR file $fname:";
// 			$fid=mark_file_as_processed($fname,$merchant_cube_id,2);
// 			$records=process_paypal_cases($txn, $dbconn, $merchant_cube_id, $fid);
// 			echo " $records ";
// 		}
// 		echo "\n";
// 	} // file list processing	
// 	$linked_count=link_merchant_data($merchant_cube_id,$dbconn); // link data
// 	file_put_contents($my_data_dir."/".$hash.".prc", "60");
// 	$source_id=$merchant_cube_id;
// 	$cases=load_cases_from_db($source_id);
// 	$ware=array();
// 	$merchant=load_merchant_data_from_db($source_id, $ware);
// 	$cntryarr=load_countries_from_csv("./country.csv");
// 	$report_fname=generate_application_report_to_cvs($tmp_dir,$dbconn,$source_id,$cases,$merchant,$ware,$cntryarr);
// 	unset($cases);
// 	unset($merchant);
// 	unset($ware);
	
// }
$fname=$report_fname;
// upload data to cps
file_put_contents($my_data_dir."/".$hash.".prc", "70");
$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=null&reload=1&withheader=1&name=paypal&reloaddim=0";
$host_reply=askhost($server_url.$sk.$schemajson, array('file_contents'=>'@'.$fname),"","","",120000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
$stime3=get_timer();$cps_time=($stime3-$stime2);
$httpcode=$host_reply["httpcode"];
$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200!=$httpcode) {
	file_put_contents($my_data_dir."/paypal_ds.log", date(DATE_ATOM)." ERR CPS ERR: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
	file_put_contents($my_data_dir."/paypal_ds.log", print_r($ret, true)." \n cdata:".print_r($cdata, true)."\n reply:".print_r($host_reply, true)."\n", FILE_APPEND);
	$resp="system error, unable to create datasource in capsidea.com. Please contact support.";
	file_put_contents($my_data_dir."/".$hash.".prc", "100");
	file_put_contents($my_data_dir."/".$hash.".resp", $resp);
	die();
}
$fsize=floor(filesize($fname)/(1024*1024)); // in megs
unlink($fname);
$jsonres=json_decode($host_reply["data"], true);
//$ikey=(int)$jsonres["Key"];
//if (0==$ikey) $ikey=$cdata["k"];
//$ikey=0;
if (isset($key)) { // check, if this is reinitialisation 	-> update client
	if (1!=pg_affected_rows(pg_query("update client set sftplogin='$sftplog', sftppwd='$sftppwd', ieml='$eml', app=$capsidea_appid ,todo=1 where ikey=$key"))) {
		file_put_contents($my_data_dir."/paypal_ds.log", date(DATE_ATOM)." ERR CPS ERR: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
		file_put_contents($my_data_dir."/paypal_ds.log", print_r($ret, true)." \n cdata:".print_r($cdata, true)."\n reply:".print_r($host_reply, true)."\n pgerr:".pg_errormessage()."\n", FILE_APPEND);
		$resp="system error, unable update client in database. Please contact support.";
		file_put_contents($my_data_dir."/".$hash.".prc", "100");
		file_put_contents($my_data_dir."/".$hash.".resp", $resp);
		die();
	}
} else {
if (1!=pg_affected_rows(@pg_query("insert into client (iname, ikey, ihash, ieml, cname, sftplogin, sftppwd,idate, todo, iparent, active, app ) values ('$company_name',$ikey,'$hash','$eml','$contact_name','$sftplog','$sftppwd',CURRENT_TIMESTAMP,1,$parent_key, 1, $capsidea_appid)"))) 
{
	file_put_contents($my_data_dir."/paypal_ds.log", date(DATE_ATOM)." ERR CPS ERR: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
	file_put_contents($my_data_dir."/paypal_ds.log", print_r($ret, true)." \n cdata:".print_r($cdata, true)."\n reply:".print_r($host_reply, true)."\n pgerr:".pg_errormessage()."\n", FILE_APPEND);
	$resp="system error, unable to insert client into database. Please contact support.";
	file_put_contents($my_data_dir."/".$hash.".prc", "100");
	file_put_contents($my_data_dir."/".$hash.".resp", $resp);
	die();
}
}
// all ok, proceed to cps
// create json for DB
if (0==$parent_key) {
	$source_id=$ikey;
} else {
	$source_id=$parent_key;
}
$resp="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script><script>CI.updateSource($ikey);CI.closeApp();</script>
<title>Success</title><link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head><body id=\"main_body\"
<img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\"><h1><b>Source $ikey created</b></h1><br>
<div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
// report time and mem
$mem=floor(memory_get_peak_usage(true)/(1024*1024));
$full_time=(get_timer()-$stime1);
mystat("signup [$ikey] mem: $mem MB list: $list_time upload: $cps_time size: $fsize MB full: $full_time");
file_put_contents($my_data_dir."/".$hash.".resp", $resp);
file_put_contents($my_data_dir."/".$hash.".prc", "100");
die();

?>