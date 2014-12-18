<?php 
include 'cb-inc.php';
$m = new MongoClient();
$db = $m->crunchbase;


$fulldata_collection = $db->fulldata;
$jsondata_collection = $db->jsondata;
$jsondata_collection->drop();
$jsondata_collection = $db->jsondata;
$fulljson=load_json_list_from_mongo($jsondata_collection);
$jsoninv_collection = $db->jsoninv;
//$jsoninv_collection->drop();
$fullinv=load_json_list_from_mongo($jsoninv_collection);
$investments_collection = $db->investments;
$investments=load_investments_list_from_mongo($investments_collection);
$resumerec=0;
$org_collection = $db->organizations;
$cursor1 = $org_collection->find();
$items_count=$cursor1->count();
$current_item=0;

echo "total records: ".$items_count." resuming at $resumerec\n";
foreach ($cursor1 as $doc) {
	$current_item++;
	if ($resumerec>$current_item) continue;
	if (0==fmod($current_item,100)) {echo "[$current_item]\n";}
	if ("Product"==$doc['type']) continue;
	if (isset($fulljson[$doc['id']])) {
	 		echo ",";
	 		continue; // object already exist, skip
	}
	$fname="/tmp/cb/".strip_first_part($doc['id']).".json";
	$js=null;
	if (file_exists($fname)) {
			$js=json_decode(file_get_contents($fname));
			
	} else { // no data
		echo "_";
		continue;
	} 
	if (!isset($js->data->uuid)) 	{ echo "!"; continue;} // bad data
	echo ".";
	$newitem=array("id"=>$doc['id'],"ts"=>time(), "jsondata"=>new MongoBinData(bzcompress(json_encode($js),9), MongoBinData::GENERIC));
	$jsondata_collection->insert($newitem);
}
echo "\n===investments===\n";
$current_item=0;
foreach ($investments as $key => $val) {
	$current_item++;
	if (0==fmod($current_item,100)) {echo "[$current_item] $key\n";}
	if (isset($fullinv[$key])) {
		echo ",";
		continue; // object already exist, skip
	}
	$fname="/tmp/cb/".$key.".json";
	$js=null;
	if (file_exists($fname)) {
		$js=json_decode(file_get_contents($fname));
			
	} else { // no data
		echo "_";
		continue;
	}
	if (!isset($js->data->uuid)) 	{ echo "!"; continue;} // bad data
	echo ".";
	$newitem=array("id"=>$key,  "ts"=>time(),"jsondata"=>new MongoBinData(bzcompress(json_encode($js),9), MongoBinData::GENERIC));
	$jsoninv_collection->insert($newitem);
}


?>