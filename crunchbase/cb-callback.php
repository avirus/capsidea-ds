<?php
// callback file, requested by capsidea.com when user needs new data 
include 'cb-inc.php';
$logfile=$tmp_dir."/crunchbase-callback.log";
file_put_contents($logfile, date(DATE_ATOM)."hit".print_r($_REQUEST,true)." \n", FILE_APPEND);

if (FALSE!==strpos($_GET["type"], "updateschema")) { // update dataset
	$key=(int)$_GET["obj_key"];
	$m = new MongoClient();
	$db = $m->crunchbase;
	$report_fname=prepare_report($db);
	$secret=sha1($capsidea_client_secret.$capsidea_permanent_access_token);
	$host_reply=askhost($server_url."&schemakey=".$key.$schemajson, array('file_contents'=>'@'.$report_fname),"","","",1800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
	//echo " time ".$time3=((get_timer()-$stime)/1)." sec "; // $stime=get_timer();
	$result=$host_reply["data"];
	$error_log="cube: $key"."secret: ".$secret."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
	if (500==$host_reply["httpcode"]) {
		echo "ERR: $error_log\n".$host_reply["httpcode"];
		log_fatal("error 500 from cps: \n $error_log");
	} // if 500
	if (401==$host_reply["httpcode"]) {
	echo "ERR: unauthorized $error_log\n".$host_reply["httpcode"];
			log_fatal("error 401 from cps \n $error_log");
	} // if 500
			if (200==$host_reply["httpcode"]) {
			echo "unlinking ".$report_fname."\n";
			unlink($report_fname);
			echo  "OK (http == 200)\n";
	} else {echo  "UNK: $error_log\n".$host_reply["httpcode"];}
	file_put_contents($logfile, date(DATE_ATOM)." client-updated $key  \n", FILE_APPEND);
	die();


} // of update dataset




?>