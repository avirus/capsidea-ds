<?php 
// crunchbase daily updater
// http://static.crunchbase.com/daily/content_web.html
include_once 'cb-inc.php';

$m = new MongoClient();
$db = $m->crunchbase;
$jsoninv_collection = $db->jsoninv;
$jsondata_collection = $db->jsondata;
$jsoninv_collection = $db->jsoninv;
$fulldata_collection = $db->fulldata;
$investments_collection = $db->investments;
$investments=load_investments_from_mongo($investments_collection);
$fulljson=load_objs_list_from_mongo($jsondata_collection);


$data=askhost("http://static.crunchbase.com/daily/content_web.html");
// person 
// organization
$ret=$data;
while ($ret=stristr($ret, "person/")) {
	$pos=strpos($ret, "\"");
	$obj=substr($ret, 0, $pos);
	$ret=substr($ret, $pos+1);
	echo $obj."\n";
	update_object_and_related($obj);
}

$ret=$data;
while ($ret=stristr($ret, "organization/")) {
	$pos=strpos($ret, "\"");
	$obj=substr($ret, 0, $pos);
	$ret=substr($ret, $pos+1);
	echo $obj."\n";
	update_object_and_related($obj);
}



?>