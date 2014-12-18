<?php 
include_once 'paypal-inc.php';
include_once 'askhost.php';
$logfile="/tmp/ppcallback.log";
// check deleteSchema
mylog("hit".print_r($_REQUEST,true));

if (FALSE!==strpos($_GET["type"], "updateschema")) { // update dataset
	$key=(int)$_GET["obj_key"];
	$client_dir=$tmp_dir."reports-".$key."/";
	$report_fname=$client_dir."report.zip";
	if (file_exists($report_fname)) {
		mylog("$key report found");
		$secret=sha1($capsidea_client_secret.$capsidea_permanent_access_token);
		mylog("secret $secret");
		$data=file_get_contents($report_fname);
		$len=strlen($data);
		mylog("file $report_fname acessed , readed $len");
		$host_reply=askhost($server_url."&schemakey=".$key.$schemajson, array('file_contents'=>"@".$report_fname),"","","",1800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
		//echo " time ".$time3=((get_timer()-$stime)/1)." sec "; // $stime=get_timer();
		$result=$host_reply["data"];
		mylog("report sent, response: $result" . "debug ".$host_reply["d"]);
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
				echo  "OK uploaded\n";
	} else {echo  "UNK: $error_log\n".$host_reply["httpcode"];}
	mylog(" client-updated $key ");
	die();		
		
	}
	 else {
	 	log_fatal("no reports");
	 }
	
} // of update dataset

if (FALSE===strpos($_GET["type"], "deleteSchema")) { 
	mylog("callback ERR. no method");
	die("ERR unknown method");}
// @todo check $capsidea_client_secret
	$ikey=(int)$_GET["obj_key"];
if (0==$ikey) {
	mylog("delete ERR. ikey=0 ");
	log_fatal("ERR user not found");
}
$dbconn = pg_connect($pg_host) or log_fatal('Could not connect to DB');
// . pg_last_error())
// remove if linked
$result=@pg_query("select * from client where ikey=$ikey and iparent>0");
if ((@pg_num_rows($result)>0))  { // remove child
	$row = pg_fetch_assoc($result);
	$parent=(int)$row['iparent'];
	@pg_free_result($result);
	@pg_query("delete from client where ikey=$ikey");
	mylog("child removed: $ikey parent $parent");
	//@pg_query("update client set active=1 where ikey=$ikey");
	// is this parent disabled and dont have childs?
	$ikey=$parent;
	$result=@pg_query("select * from client where ikey=$ikey and active=0");
	if ((0==@pg_num_rows($result)))  { die("ok"); } // parent not disabled, no more actions needed 
	@pg_free_result($result);
	$result=@pg_query("select * from client where iparent=$ikey");
	if ((0!=@pg_num_rows($result)))  { die("ok"); } // parent still have childs, no more actions needed
	// no more childs, and parent disabled, erase all parent data		
	@pg_free_result($result);
	@pg_query("delete from client where ikey=$ikey");
	@pg_query("delete from merchant where ikey=$ikey");
	@pg_query("delete from ppfiles where ikey=$ikey");
	@pg_query("delete from cases where ikey=$ikey");
	@pg_query("delete from txn where ikey=$ikey");
	mylog("free parent and its data removed $parent");
	die("ok");	
} 
// parent - linked logic
$result=@pg_query("select * from client where iparent=$ikey");
if ((@pg_num_rows($result)>0))  { // parent have linked
	@pg_free_result($result);
	@pg_query("update client set active=0 where ikey=$ikey");
	mylog("parent deactivated, still have linked");
	die("ok");

} else
{ //no linked, erase all
@pg_free_result($result);
@pg_query("delete from client where ikey=$ikey");
@pg_query("delete from merchant where ikey=$ikey");
@pg_query("delete from ppfiles where ikey=$ikey");
@pg_query("delete from cases where ikey=$ikey");
@pg_query("delete from txn where ikey=$ikey");
mylog("parent $ikey and its data removed, no linked detected");
die("ok");
};

//@pg_query("delete from updates where iapp=$capsidea_appid and ikey=$ikey;");



?>