<?php 
error_reporting(E_ERROR);
//$testrun=true;
$testrun=false;
$cpsdebug=false;
$wehave_data_files=false;
//td: should use tempnam for user input
include_once 'paypal-inc.php';
$stime1=get_timer();
$cdata=get_capsidea_data($capsidea_client_secret);

include_once 'askhost.php';
$dbconn = pg_connect($pg_host) or log_fatal('Could not connect to application database, please contact support.');
$paypal_sftp_login=escapeshellarg($_POST["element_1"]);
$paypal_sftp_password=escapeshellarg($_POST["element_2"]);
$sftppwd=pg_escape_string($_POST["element_2"]);
$sftplog=pg_escape_string($_POST["element_1"]);


if (strlen($sftppwd)<2) die("Please provide reporting server password.\n Press back and try again");
if (strlen($sftplog)<2) die("Please provide reporting server login.\n Press back and try again");
//print_r($cdata);
$secret=$cdata["t"];
$res=askhost($base_url."?s=CommonService&m=getUserInfo",array("[]"),"","","",180000,array("appid: $capsidea_appid","sig: $secret","Content-Type: application/json;charset=UTF-8"),true);
$js=json_decode($res['data']);
//echo $res['data'];
$username=pg_escape_string($js->Login);
$contact_name=pg_escape_string($js->FirstName)." ".pg_escape_string($js->LastName);
$company_name=$contact_name;
$eml=$username;

if (isset($cdata["k"])) { // check, if this is reinitialisation
	$key=$cdata["k"];
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
$result=@pg_query("select * from client where sftplogin='$sftplog' and iparent=0");
if (@pg_num_rows($result)>0) {
	$row=pg_fetch_assoc($result);
	//$eml=$row["ieml"];
	$hash=$row["ihash"];
	$parent_key=$row["ikey"];
	file_put_contents($my_data_dir."/paypal.log", date(DATE_ATOM)."already registered. client [$company_name] $contact_name l: $sftplog \n", FILE_APPEND);
	//die("this client already registered, please contact user $eml for shared datasource");
};

if (!$testrun) {
	$ret=get_file_list($paypal_sftp_login, $paypal_sftp_password);	
//$result=exec("./getpaypal-list-files.sh $paypal_sftp_login"."@reports.paypal.com $paypal_sftp_password 2>&1", $ret);
//$ftp_response=$result;
$stime2=get_timer();$list_time=($stime2-$stime1);
//$some_data_found=0;
foreach ($ret as $fname) {
	if (FALSE===strpos($fname, "TRR-")) continue;
		$wehave_data_files=true;
}
if (($ret[3]==$ret[2])&&(FALSE!==strpos("Password Authentication", $ret[2]))) {
//    [2] => Password Authentication
//    [3] => Password Authentication	
	$resp="unable to login into paypal transactions report sftp server (reports.paypal.com), <br>please press <b>back</b>, check <b>reporting credentials</b> and try again<br>If credentials valid, and you still get this message - please contact support.";
	echo "$resp";
	file_put_contents($my_data_dir."/paypal-password.log", date(DATE_ATOM)." ERR PWD: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
	file_put_contents($my_data_dir."/paypal-password.log", print_r($ret, true)." \n", FILE_APPEND);
//	file_put_contents($my_data_dir."/".$hash.".prc", "100");
//	file_put_contents($my_data_dir."/".$hash.".resp", $resp);
	die();
}

// send progress bar to client
file_put_contents($my_data_dir."/".$hash.".prc", "10");
$waiter=file_get_contents($wwwrealpath."/paypal-wait.html");
$waiter=str_replace("thekey", $hash, $waiter);
if (0!=$parent_key) $waiter=str_replace("<!-- message  -->", "please standby while we uploading report to capsidea.com <br>NB: This can take a while, so be patient", $waiter);
if ($wehave_data_files) $waiter=str_replace("<!-- message  -->", "please standby while we downloading reports from paypal <br>NB: You can upload your data via application api", $waiter); 
 	else $waiter=str_replace("<!-- message  -->", "You have no reports on paypal reporting sftp<br>So, we create some empty reports for you and update it since data become available. <br>NB: You can upload your data via application api", $waiter);
echo $waiter; // send page to user
flush();
ob_flush();
flush();

//if (0==$some_data_found) { // post fake data?
//	$wehave_data_files=false;
	//$resp= "<b>unable to find any paypal transactions report files (/ppreports/outgoing/TRR-*), <br>Please contact support.";
//	file_put_contents($my_data_dir."/paypal.log", date(DATE_ATOM)." ERR NO FILES client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
//	file_put_contents($my_data_dir."/paypal.log", print_r($ret, true)." \n", FILE_APPEND);
	//file_put_contents($my_data_dir."/".$hash.".prc", "90");
	//file_put_contents($my_data_dir."/".$hash.".resp", $resp);
	//die();
//} // end of no-data
}// debug
file_put_contents($my_data_dir."/".$hash.".prc", "20");
$fname="$wwwrealpath/ppfake.csv";  // fake file
$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=null&reload=1&withheader=1&name=paypal&reloaddim=1&frequency=daily".$schemajson;
$host_reply=askhost($server_url.$sk.$schemajson, array('file_contents'=>'@'.$fname),"","","",120000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
$jsonres=json_decode($host_reply["data"], true);
$ikey=(int)$jsonres["Key"];
if (0==$ikey) $ikey=$cdata["k"]; // $ikey = something useful
$sk="&schemakey=".$ikey;
// get real data - $ftp_response

if ($wehave_data_files) {
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
} else { // bad luck, jfdi - load data, process, link, and generate report
	$file_num=0;
	foreach ($ret as $fname) {
		$merchant_cube_id=$ikey;
		file_put_contents($my_data_dir."/".$hash.".prc", 30+$file_num); // report progress
		$client_dir=$tmp_dir.$merchant_cube_id."/";
		if ((FALSE===strpos($fname, "TRR-"))&&(FALSE===strpos($fname, "DDR-"))) continue; // unknown file, skip it
		if (FALSE!==strpos($fname, ".gz")) {$dbg=$dbg."archive $fname\n";continue;} // file is archive (empty? broken?)
		$realfname=$fname;
		$fname=str_replace("/ppreports/outgoing/", "", $realfname);
		//$fname=str_replace(".gz", "", $fname);
		if (is_file_already_loaded($fname,$merchant_cube_id)) continue; // file already loaded
		echo "get file $realfname\n";
		$stime=get_timer();
		$txn=get_file_content($paypal_sftp_login, $paypal_sftp_password, $realfname,$client_dir,$fname); // get file from sftp
		echo " time ".((get_timer()-$stime)/1)." sec ";
		if (FALSE!==strpos($fname, "TRR-")) {
			$file_num++;
			echo " processing TRR file $fname:";
			$fid=mark_file_as_processed($fname,$merchant_cube_id,1);
			$records=process_paypal_txns($txn,$dbconn,$merchant_cube_id, $fid);
			echo " $records ";
		}
		if (FALSE!==strpos($fname, "DDR-")) {
			$file_num++;
			echo " processing DDR file $fname:";
			$fid=mark_file_as_processed($fname,$merchant_cube_id,2);
			$records=process_paypal_cases($txn, $dbconn, $merchant_cube_id, $fid);
			echo " $records ";
		}
		echo "\n";
	} // file list processing	
	$linked_count=link_merchant_data($merchant_cube_id,$dbconn); // link data
	file_put_contents($my_data_dir."/".$hash.".prc", "60");
	$source_id=$merchant_cube_id;
	$cases=load_cases_from_db($source_id);
	$ware=array();
	$merchant=load_merchant_data_from_db($source_id, $ware);
	$cntryarr=load_countries_from_csv("./country.csv");
	$report_fname=generate_application_report_to_cvs($tmp_dir,$dbconn,$source_id,$cases,$merchant,$ware,$cntryarr);
	unset($cases);
	unset($merchant);
	unset($ware);
	
} 
$fname=$report_fname;
// upload data to cps
file_put_contents($my_data_dir."/".$hash.".prc", "70");
$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=null&reload=1&withheader=1&name=paypal&reloaddim=0&frequency=daily".$schemajson;
$host_reply=askhost($server_url.$sk.$schemajson, array('file_contents'=>'@'.$fname),"","","",120000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
$stime3=get_timer();$cps_time=($stime3-$stime2);
$httpcode=$host_reply["httpcode"];
$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200!=$httpcode) {
	file_put_contents($my_data_dir."/paypal.log", date(DATE_ATOM)." ERR CPS ERR: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
	file_put_contents($my_data_dir."/paypal.log", print_r($ret, true)." \n cdata:".print_r($cdata, true)."\n reply:".print_r($host_reply, true)."\n", FILE_APPEND);
	if ($cpsdebug) {echo $resp=$error_log;} else $resp="system error, unable to create datasource in capsidea.com. Please contact support.";
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
} // upload real data if $wehave_data_files
if (isset($key)) { // check, if this is reinitialisation 	-> update client
	if (1!=pg_affected_rows(pg_query("update client set sftplogin='$sftplog', sftppwd='$sftppwd', ieml='$eml', todo=1 where ikey=$key"))) {
		file_put_contents($my_data_dir."/paypal.log", date(DATE_ATOM)." ERR CPS ERR: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
		file_put_contents($my_data_dir."/paypal.log", print_r($ret, true)." \n cdata:".print_r($cdata, true)."\n reply:".print_r($host_reply, true)."\n pgerr:".pg_errormessage()."\n", FILE_APPEND);
		$resp="system error, unable update client in database. Please contact support.";
		file_put_contents($my_data_dir."/".$hash.".prc", "100");
		file_put_contents($my_data_dir."/".$hash.".resp", $resp);
		die();
	}
} else {
if (1!=pg_affected_rows(@pg_query("insert into client (iname, ikey, ihash, ieml, cname, sftplogin, sftppwd,idate, todo, iparent, active ) values ('$company_name',$ikey,'$hash','$eml','$contact_name','$sftplog','$sftppwd',CURRENT_TIMESTAMP,1,$parent_key, 1)"))) 
{
	file_put_contents($my_data_dir."/paypal.log", date(DATE_ATOM)." ERR CPS ERR: client $eml [$company_name] $contact_name l: $sftplog p: $sftppwd \n", FILE_APPEND);
	file_put_contents($my_data_dir."/paypal.log", print_r($ret, true)." \n cdata:".print_r($cdata, true)."\n reply:".print_r($host_reply, true)."\n pgerr:".pg_errormessage()."\n", FILE_APPEND);
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
// get element codes
$url=$base_url."?s=DataService&m=OpenDataSource";
$post_data="[\"$ikey\$paypal\"]";
$res=askhost($url,$post_data,"","","",180000,array("appid: $capsidea_appid","sig: $secret","Content-Type: application/json;charset=UTF-8"),true);
mylog($res['d']."| ".$post_data." json: ".$res['data']);
//$secret1=sha1($capsidea_client_secret.$capsidea_permanent_access_token);
$url=$base_url."?s=DataService&m=GetChildEls";
$post_data="[\"$ikey\$case_reason\",\"\"]";
$res=askhost($url,$post_data,"","","",180000,array("appid: $capsidea_appid","sig: $secret","Content-Type: application/json;charset=UTF-8"),true);
mylog($res['d']."| ".$post_data." json: ".$res['data']);
$js=json_decode($res['data']);
//print_r($js);
$selected="";
$i=0;
foreach ($js as $this_item) {
	if ($this_item->n=="") continue;
	if ($i>0) $selected=$selected.",";
	$selected=$selected."\"".$this_item->k."\"";
	$i++;
}
$selected0=$selected;
// refund
$selected="";
$i=0;
foreach ($js as $this_item) {
	if ($this_item->n=="") continue;
	if (FALSE!==stripos($this_item->n, "Inquiry")) continue;
	//if (FALSE!==stripos($this_item->n, "Merchandise")) continue;
	if (FALSE!==stripos($this_item->n, "Unauthorized")) continue;
	if ($i>0) $selected=$selected.",";
	$selected=$selected."\"".$this_item->k."\"";
	$i++;
}
$selected1=$selected;
// unauthorized
$selected="";
$i=0;
foreach ($js as $this_item) {
	if (FALSE===stripos($this_item->n, "Inquiry")) continue;
	if ($i>0) $selected=$selected.",";
	$selected=$selected."\"".$this_item->k."\"";
	$i++;
}
$selected2=$selected;
// inqury
$selected="";
$i=0;
foreach ($js as $this_item) {
	if (FALSE===stripos($this_item->n, "Unauthorized")) continue;
	if ($i>0) $selected=$selected.",";
	$selected=$selected."\"".$this_item->k."\"";
	$i++;
}
$selected3=$selected;
// not as described
$selected="";
$i=0;
foreach ($js as $this_item) {
	if (FALSE===stripos($this_item->n, "not as described")) continue;
	if ($i>0) $selected=$selected.",";
	$selected=$selected."\"".$this_item->k."\"";
	$i++;
}
$selected4=$selected;

//   ".[Count percent];sum({[case_status].[1],[case_status].[2],[case_status].[3], [case_status].[4]},[Measures].[Fact Count])/[Measures].[Fact Count]*100"
//   ".[Count percent];sum({[case_status].[1],[case_status].[2],[case_status].[3], [case_status].[4]},[Measures].[Fact Count])/[Measures].[Fact Count]*100"
//   ".[Amount percent];sum({[case_status].[1],[case_status].[2],[case_status].[3],[case_status].[4]},[Measures].[amount])/[Measures].[amount]*100"
// "f":"sum({[case_status].[1],[case_status].[2],[case_status].[3],[case_status].[4]},[Measures].[amount])/[Measures].[amount]*100",
// "f":"sum({[case_status].[1],[case_status].[2],[case_status].[3], [case_status].[4]},[Measures].[Fact Count])/[Measures].[Fact Count]*100",
// un=6+9
// inq = 3+5
$orig=array("mycapsideacomdatasetkey", "mydatasetname", "Generated_hash", "Scheme_ID","SelectedKeys_exclude0","SelectedKeys_refund","SelectedKeys_inq","SelectedKeys_un", "SelectedKeys_nad");
$repl=array($ikey, "paypal", $hash, $source_id, $selected0, $selected1,$selected3 ,$selected2, $selected4);
$dash_template=file_get_contents("d1.json");
$jsondash=str_replace($orig, $repl, $dash_template);
file_put_contents("$my_data_dir/dashpp.json", $jsondash);
$jsondash=json_encode(json_decode($jsondash)); // strip formatting
$resp="<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script><script>CI.updateSource($ikey);CI.createDashboard($jsondash);CI.closeApp();</script>
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