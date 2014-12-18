<?php 
// paypal loader for capsidea
include_once 'csv2arr.php';
include_once 'askhost.php';
include_once 'paypal-inc.php';
if (isset($argv[1])) {$force_run=1;}
$avoid_xsolla_datasource_upload=true;
$secret=sha1($capsidea_client_secret.$capsidea_permanent_access_token);

error_reporting(E_ALL);
ini_set('display_errors', 1);

$dbconn = pg_connect($pg_host) or die('Could not connect: ' . pg_last_error());
$dbconn0 = pg_connect($pg_host) or die('Could not connect: ' . pg_last_error());

while (true) {
$dbg="";
$data_modified=false;	
	if (isset($force_run))	{ 
		$topres=pg_query($dbconn0,"select * from client order by ikey asc;"); 
	}else  {
		$topres=pg_query($dbconn0,"select * from client where todo>0 or pdate<date_trunc('day',CURRENT_TIMESTAMP) order by ikey asc;");
	} // forced run
while($toprow = pg_fetch_assoc($topres)) {
	$time1=$time4=get_timer();
	$merchant_cube_id=$toprow["ikey"];
	$parent_key=$toprow["iparent"];
	$client_name=$toprow["iname"];
	$client_dir=$tmp_dir.$merchant_cube_id."/";
	echo "\n".date(DATE_ATOM)." $merchant_cube_id: [$client_name] #";	
	
if (0==$parent_key) {	// should we download new data?
	if (!file_exists($client_dir)) @mkdir($client_dir,0777,true);	
// get file list
//$ret=array();
$paypal_sftp_login=$toprow["sftplogin"];
$paypal_sftp_password=$toprow["sftppwd"];
$paypal_sftp_login=escapeshellarg($paypal_sftp_login);
$paypal_sftp_password=escapeshellarg($paypal_sftp_password);
echo "fetching index\n";
$stime=get_timer();
if (!isset($force_run)) $ret=get_file_list($paypal_sftp_login, $paypal_sftp_password);	
echo " time ".((get_timer()-$stime)/1)." sec\n";
foreach ($ret as $fname) {
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
		echo " processing TRR file $fname:";
		$fid=mark_file_as_processed($fname,$merchant_cube_id,1);
		$records=process_paypal_txns($txn,$dbconn,$merchant_cube_id, $fid);
		echo " $records ";
		$data_modified=true;
	}
	if (FALSE!==strpos($fname, "DDR-")) {
		echo " processing DDR file $fname:";
		$fid=mark_file_as_processed($fname,$merchant_cube_id,2);
		$records=process_paypal_cases($txn, $dbconn, $merchant_cube_id, $fid);
		echo " $records ";
		$data_modified=true;
	}
	echo "\n";
} // file list processing
// processing data
echo "linking data";
if ($data_modified) {
$linked_count=link_merchant_data($merchant_cube_id,$dbconn);
echo $linked_count." done\n"; 
pg_query($dbconn, "update client set todo=1 where iparent=$merchant_cube_id"); // since new data arrived - force update
} // new data
else echo " skipped\n"; 
} // should we load data?
if (0==$parent_key) {
	$source_id=$merchant_cube_id;
} else {
	$source_id=$parent_key;
}
$stime=get_timer();$time1=$stime-$time1;

//generate report.csv
if (($avoid_xsolla_datasource_upload)&&(1591==$merchant_cube_id)) continue; //skip xsolla upload
echo "creating report for client $merchant_cube_id \n"; //die();

// load cases data
$stime=get_timer();echo "\nloading cases ($source_id) ...";
$cases=load_cases_from_db($source_id);
$dbg=$dbg."(".count($cases).") done in ".((get_timer()-$stime)/1)." sec "; // $stime=get_timer(); echo "loading cases ...";
// load merchant data
$stime=get_timer();echo "\nloading merchant data ($source_id) ...";
$ware=array();
$merchant=load_merchant_data_from_db($source_id, $ware);
$dbg=$dbg."(".count($merchant).") done in ".$time2=((get_timer()-$stime)/1)." sec "; // $stime=get_timer(); echo "loading cases ...";
$cntryarr=load_countries_from_csv("./country.csv");
// process
$client_dir=$tmp_dir."reports-".$merchant_cube_id."/";
if (!file_exists($client_dir)) @mkdir($client_dir,777,true);
$report_fname=generate_application_report_to_cvs($client_dir,$dbconn,$source_id,$cases,$merchant,$ware,$cntryarr);
$ccount=count($cases);
$mcount=count($merchant);
@unlink($client_dir."report.csv");
rename($report_fname, $client_dir."report.csv");
zip_compress_csv($client_dir."report.zip",$client_dir."report.csv");
unset($cases);
unset($merchant);
unset($ware);
$dbg=$dbg."data processing time ".((get_timer()-$stime)/1)." sec "; // $stime=get_timer();
$fsize=floor(filesize($client_dir."report.zip")/(1024*1024)); // in megs

//send file to cps
//echo "\n".date(DATE_ATOM)."\nsend file $report_fname ($fsize MB) to cps... \n"; 
$stime=get_timer();
// //$host_reply=askhost($server_url."&schemakey=".$merchant_cube_id.$schemajson, array('file_contents'=>'@'.$report_fname),"","","",1800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
// $host_reply=askhost($server_url."&schemakey=".$merchant_cube_id.$schemajson, array('file_contents'=>'@'.$report_fname),"","","",1800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
// echo " time ".$time3=((get_timer()-$stime)/1)." sec "; // $stime=get_timer();
// $result=$host_reply["data"];
// $err="cube: $merchant_cube_id"."secret: ".$secret."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
// if (500==$host_reply["httpcode"]) {
// 	echo "ERR: $err\n".$host_reply["httpcode"];
// 	log_fatal("error 500 from cps: \n $dbg");
// } // if 500
// if (401==$host_reply["httpcode"]) {
// 	echo "ERR: unauthorized $err\n".$host_reply["httpcode"];
// 	log_fatal("error 401 from cps \n $dbg");
// } // if 500
// if (200==$host_reply["httpcode"]) {
// 	echo "unlinking ".$report_fname."\n";
// unlink($report_fname);
// echo  "OK (http == 200)\n";
// } else {echo  "UNK: $err\n".$host_reply["httpcode"];}
pg_query("update client set pdate=CURRENT_TIMESTAMP, todo=0 where ikey=".$merchant_cube_id);
$time4=(get_timer()-$time4); //total timer
$mem=floor(memory_get_peak_usage(true)/(1024*1024));
//file_put_contents("/tmp/paypal-gen.log", date(DATE_ATOM)." client $client_name [$source_id] cases: $ccount merchant $mcount / $time2 sec]  mem: $mem MB loading: $time1 uploading: $time3 size: $fsize MB full: $time4\n", FILE_APPEND);
file_put_contents("/tmp/paypal-gen.log", date(DATE_ATOM)." link client $client_name [$source_id]   mem: $mem MB loading: $time1 full: $time4\n", FILE_APPEND);
} // select client which need update
if (isset($force_run)) {echo "forced run done\n";die();}
echo $dbg; 
if ($data_modified) $sleep_interval=1;
else $sleep_interval=30;
echo "\n sleep $sleep_interval min";
sleep(60*$sleep_interval);
echo ".";
} // forever
?>