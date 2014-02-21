<?php 
// cbr autofetcher $Author: slavik $ 
include_once 'cbr-inc.php';
$m = new MongoClient();
$db = $m->currency;
$collection = $db->cbrlastrun;
$this_item = $collection->findone();
$last_ts=($this_item["ts"]);
//echo date(DATE_ATOM,$last_ts)." last update\n";
$date1=date("d/m/Y",$last_ts);
$date2=date("d/m/Y");
if ($date1==$date2) die("I wont run more than once a day\n");
//update full dataset
$data_collection = $db->cbr;
$kurs=array();
echo " download";
$kurs=download_data_from_cbr($date1, $currency);
// insert data to mongodb
echo " save";
save_array_to_mongo($kurs, $currency, $data_collection);
$collection->update(array(),array("ts"=>time()) );
echo " updating capsidea.com datasets:";
$secret=sha1($capsidea_client_secret.$capsidea_permanent_access_token);
$clients_collection = $db->cbrclients;
$cursor = $clients_collection->find();
foreach ($cursor as $this_item) {
	$selected=unserialize(base64_decode($this_item["selected"]));
	$schemakey=$this_item["schemakey"];
	echo " $schemakey"; // show my progress
	$rangeQuery = array('ts' => array( '$gt' => $last_ts, '$lt' => time() )); // select all new records
	$kurs=load_array_from_mongo($data_collection, $rangeQuery,$selected); 
	// save array as csv
	$fname=save_array_as_csv($selected, $kurs);
	$stime=get_timer();
	$host_reply=askhost($server_url."&schemakey=".$schemakey, array('file_contents'=>'@'.$fname),"","","",80000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
	$capsidea_time=get_timer()-$stime;
	unlink($fname);
	$result=$host_reply["data"];
	$err="secret: $secret response: \n".$host_reply["data"]."\nconnection debug:\n".$host_reply["d"];
	if (200!=$host_reply["httpcode"]) {
		mylog("ERR: $err ".$host_reply["httpcode"]);
		die;
	} // if !200
	mylog("OK: $schemakey in $capsidea_time sec");
}
echo "complete\n";
?>