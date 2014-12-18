<?php 

include 'cb-inc.php';
$m = new MongoClient();
$db = $m->crunchbase;
//$collection = $db->lastrun;
$cb_cache_time=14*24*60*60; // 7 days
$resumerec=(int)$argv[1];
$keycnt=count($apikeys);
echo $keycnt." keys loaded, maximum reqests: ".($keycnt*2500)."\n";

$org_collection = $db->organizations;
// get orgs
$i=0;
$apikey=get_api_key();
//$found_objects=load_objs_from_mongo($org_collection);
while (false) {
$res=askhost("http://api.crunchbase.com/v/2/organizations?user_key=$apikey&page=$i&order=created_at+DESC",FALSE, "", "", "1", 60000, "", true);
if (500==$res['httpcode']) responsedie($res,'error 500');
$js=json_decode($res['data']);
$pages=$js->data->paging->number_of_pages;
echo "\n $i: (orgs of $pages) ";
if ($i>$pages) {echo "complete\n";break;}
if (!isset($js->data->items[0]->name)) responsedie($res, "empty response");
foreach ($js->data->items as $org)
{
	if (isset($found_objects[$org->path])) {
		continue;
	$org_collection->findAndModify(array("id" => $org->path), array("id" => $org->path, "name"=>$org->name, "type"=>$org->type), null);
	echo ".";
	}
	else {
		$org_collection->insert(array("id" => $org->path, "name"=>$org->name, "type"=>$org->type));
		echo "+";
	}
}
$i++;
} // org list
// get peoples
$i=0;
while (false) {
	$res=askhost("http://api.crunchbase.com/v/2/people?user_key=$apikey&page=$i&order=created_at+DESC",FALSE, "", "", "1", 60000, "", true);
	if (500==$res['httpcode']) responsedie($res,'error 500');
	$js=json_decode($res['data']);
	$pages=$js->data->paging->number_of_pages;
	echo "\n $i: (ppls of $pages) ";
	if ($i>$pages) {echo "complete\n";break;}
	if (!isset($js->data->items[0]->name)) responsedie($res, "empty response");
	foreach ($js->data->items as $org)
	{
		if (isset($found_objects[$org->path])) {
		continue;
	$org_collection->findAndModify(array("id" => $org->path), array("id" => $org->path, "name"=>$org->name, "type"=>$org->type), null);
	echo ".";
	}
	else {
		$org_collection->insert(array("id" => $org->path, "name"=>$org->name, "type"=>$org->type));
		echo "+";
	}
			} // foreach
	$i++;
}
// get products "http://api.crunchbase.com/v/2/products?user_key=fefe934cf6f3fe2545a50955ee054baf&page=1&order=created_at+DESC"
$i=0;
while (false) {

	$res=askhost("http://api.crunchbase.com/v/2/products?user_key=$apikey&page=$i&order=created_at+DESC",FALSE, "", "", "1", 60000, "", true);
	if (500==$res['httpcode']) responsedie($res,'error 500');
	$js=json_decode($res['data']);
	$pages=$js->data->paging->number_of_pages;
	echo "\n $i: (products of $pages) ";
	if ($i>$pages) {echo "complete\n";break;}
	if (!isset($js->data->items[0]->name)) responsedie($res, "empty response");
	foreach ($js->data->items as $org)
	{
		if (isset($found_objects[$org->path])) {
		continue;
	$org_collection->findAndModify(array("id" => $org->path), array("id" => $org->path, "name"=>$org->name, "type"=>$org->type), null);
	echo ".";
	}
	else {
		$org_collection->insert(array("id" => $org->path, "name"=>$org->name, "type"=>$org->type));
		echo "+";
	}
			}
	$i++;
}
//$fulldata_collection = $db->fulldata;
//$cursor = $fulldata_collection->find();

//$cursor->reset();
//$fulldata_collection->drop();
$fulldata_collection = $db->fulldata;
//$fullobj=load_objs_list_from_mongo($fulldata_collection);
$investments_collection = $db->investments;
//$investments_collection->drop();
$investments_collection = $db->investments;
$investments=load_investments_list_from_mongo($investments_collection);
$jsondata_collection = $db->jsondata;
$jsoninv_collection = $db->jsoninv;
//$objjsons=load_objs_list_from_mongo($jsondata_collection);
//$invjsons=load_objs_list_from_mongo($jsoninv_collection);

while (true) {
$cursor1 = $org_collection->find();
$items_count=$cursor1->count();
$current_item=0;

$fulljson=load_objs_list_from_mongo($jsondata_collection);
$fullinv=load_objs_list_from_mongo($jsoninv_collection);


echo "total records: ".$items_count." resuming at $resumerec\n";
foreach ($cursor1 as $doc) {
	$current_item++;
	if ($resumerec>$current_item) continue;
// 	if (isset($fullobj[$doc['id']])) {
// 		echo "\n already exist, skipping\n"; 
// 		continue; // object already exist
// 	}
	// get org data
	//echo "!";
	if (0==fmod($current_item,100)) {echo "($current_item)\n";}
	if ("Product"==$doc['type']) continue;
	//sleep(5);
	$js=null;
	$objid=$doc['id'];
	if (isset($fulljson[$objid])) { // test it
//		if (filemtime($fname)>(time()-$cb_cache_time)) { // file newer than cache life time? @todo change to mongo
		$mngarr=load_json_from_mongo($objid,$jsondata_collection);
		//echo bzdecompress($mngarr['jsondata']->bin);
		$js=json_decode(bzdecompress($mngarr['jsondata']->bin));
		echo ",";
	} 
	if (!isset($js->data->uuid)) 	{ echo "\n# [$current_item] (".$doc['id'].") bad data object ".print_r($js,true).", retry"; $js=null;}
	if (null==$js) {
	$res=askhost("http://api.crunchbase.com/v/2/".$doc['id']."?user_key=$apikey",FALSE, "", "", "1", 60000, "", true);
	if (500==$res['httpcode']) continue; 
		//responsedie($res,'error 500 (get org data)');
	$js=json_decode($res['data']);
	echo ".";
	} // get object from api
	if (!isset($js->data->uuid)) { // incorrect response, wait and try again
		while (true) {
			//file_put_contents("/tmp/cb/wait/$current_item.txt", $res['data'] );
			$apikey=get_api_key();
			echo "\n [$current_item] null response detected, try new key ";
			sleep(1);
			$thisurl="http://api.crunchbase.com/v/2/".$doc['id']."?user_key=$apikey";
			echo "try to resume request: $thisurl \n";
			$res=askhost($thisurl,FALSE, "", "", "1", 60000, "", true);
			$js=null;
			$js=json_decode($res['data']);
			if (isset($js->data)) break;
			echo print_r($js, true)."\n";
			if (!isset($js->data->uuid)) {
				echo "still shit, ";
				$js=null;
				break;
			}
		}
	} // null response
	if (null==$js) {
		echo "skip\n";
		continue; // skip this element
	}
	save_json_to_mongo($js,$objid, $jsondata_collection);
	if (!isset($js->data->properties->role_investor)) continue;
	if (!isset($js->data->relationships->investments)) continue;
	//if (true!=$js->data->properties->role_investor) continue;
	if ("Person"==$doc['type']) $objname=strip_characters($js->data->properties->last_name." ".$js->data->properties->first_name);
	if ("Organization"==$doc['type']) $objname=strip_characters($js->data->properties->name);
	echo "[".$current_item."/".$items_count."] : $objname\n";
	$fund=array();
	$fund_num=0;
foreach (	$js->data->relationships->investments->items as $funding) {
	$round_uuid=strip_first_part($funding->funding_round->path) ;
	$fund[$fund_num]=$round_uuid;
	$fund_num++;
	if (!isset($investments[$round_uuid])) { // add new investment
		
		$newround=array();
		$newround['id']=$round_uuid;
		//$round_fname="/tmp/cb/$round_uuid.json";
	//$newround['recipient']=strip_characters($funding->invested_in->name);
	$rcpt_path=strip_first_part($funding->invested_in->path);
	$newround['recipient']=$rcpt_path;
	$fround=null;
	if (isset($fullinv[$round_uuid])) {
		$mnginv=load_json_from_mongo($round_uuid, $jsoninv_collection);
		$fround=json_decode(bzdecompress($mnginv['jsondata']->bin));
		echo  "+";
	} 
	if (!isset($fround->data->uuid)) {
		echo "\nbad investment data: ".print_r($fround, true)."\ntrying to reload ";
	$res2=askhost("http://api.crunchbase.com/v/2/funding-round/".$round_uuid."?user_key=$apikey",FALSE, "", "", "1", 60000, "", true);
	$fround=json_decode($res2['data']);
	if (isset($fround->data->uuid)) {
		save_json_to_mongo($fround,$round_uuid, $jsoninv_collection);
	//file_put_contents($round_fname, json_encode($fround,JSON_PRETTY_PRINT) );
	echo  "-";} else {echo "!";$apikey=get_api_key();}
	
	}

	if (null==$fround) {
		echo "no investment data - skip";
		//print_r($fround);
		continue;} // skip "closed"
	if (!isset($fround->data->properties->money_raised_usd)) {
		echo "no amount data - skip";
		//print_r($fround);
		continue;} // skip "unknown amount"	
	
	$newround['funding_type']=$fround->data->properties->funding_type;
	$newround['usd_sum']=$fround->data->properties->money_raised_usd;
	$newround['curr']=$fround->data->properties->money_raised_currency_code;
	if (isset($fround->data->properties->series)) $doc['ser']=$fround->data->properties->series;
	$round_date_str=$fround->data->properties->announced_on;
	$round_date=strtotime($round_date_str);
	//echo "[$round_date] $round_date_str\n";
	$newround['fdate']=$round_date;
	$newround['datetrust']=$fround->data->properties->announced_on_trust_code;
	$investments_collection->remove(array("id"=>$round_uuid));
	if (FALSE===$investments_collection->insert($newround)) die('error inserting inv round'.print_r($newround));
	unset($res2);
	unset($newround);
	unset($fround);
	$investments[$round_uuid]=1;
	} // add new investment round
	else { // investment round cached
		echo "$";};
	unset($round_uuid);
	
} // foreach investments element
	//$fulldata_collection->findAndModify(array("uuid" => $js->data->uuid), array($js->data), null, array("upsert" => true ));
//$fulldata_collection->findAndModify(array("id" => $doc['id']), array($js->data), null, array("upsert" => true ));
	$fulldata_collection->remove(array("id"=>$doc['id']));
	$fulldata_collection->insert(array("id"=>$doc['id'], "type"=>$js->data->type, "name"=>$objname, "investments"=>$fund ));
	//echo $js->data->type ."#".  $js->data->permalink ."\n";
}

$resumerec=$current_item;
if ($current_item<$items_count) {
echo "cursor finished, but count = $current_item, recovering\n";
} else {die ("done, stopping\n");}
} // while true


?>