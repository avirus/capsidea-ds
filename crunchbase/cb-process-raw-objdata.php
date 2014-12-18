<?php 
include 'cb-inc.php';
$m = new MongoClient();
$db = $m->crunchbase;
$fulldata_collection = $db->fulldata;
$fulldata_collection->drop();
$jsondata_collection = $db->jsondata;
$jsoninv_collection = $db->jsoninv;

$apikey=get_api_key();
$investments_collection = $db->investments;

$current_item=0;
$resumerec=0;
$need_update_inv=false;

while (true) {
$cursor1 = $jsondata_collection->find();
$items_count=$cursor1->count();
$items_count=$cursor1->count();
echo "total records: ".$items_count." resuming at $resumerec\n";
$investments=load_investments_list_from_mongo($investments_collection);

foreach ($cursor1 as $doc) {
	$current_item++;
	if ($resumerec>$current_item) continue;
	if (0==fmod($current_item,100)) {echo "($current_item)\n";}
	$js=null;
	$js=json_decode(bzdecompress($doc['jsondata']->bin));
	//print_r($js);sleep(10);
	if (!isset($js->data->uuid)) {continue;echo "#";} //bad data
	if ("Person"==$js->data->type) $objname=strip_characters($js->data->properties->last_name." ".$js->data->properties->first_name);
	if ("Organization"==$js->data->type) $objname=strip_characters($js->data->properties->name);
	$markets=array();
	if (isset($js->data->relationships->markets)) {
		foreach ($js->data->relationships->markets->items as $market) {
			$markets[]= $market->name;
		} // each market
	} else {
		$markets[]="null";
	}
	$fund=array();
	if (isset($js->data->relationships->investments)) foreach (	$js->data->relationships->investments->items as $funding) {
		$round_uuid=strip_first_part($funding->funding_round->path) ;
		$fund[]=$round_uuid;
	
	}
	
	//$markets=json_encode($markets);
	$fulldata_collection->insert(array("id"=>$doc['id'],"markets"=>$markets, "type"=>$js->data->type, "name"=>$objname, "investments"=>$fund ));
	echo ".";
	if ( $need_update_inv) update_related($js);
	if ($current_item>$items_count) die("done!\n");
}

echo "cursor finished\n";
$resumerec=$current_item;

}

?>