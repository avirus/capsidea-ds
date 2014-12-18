<?php 
include 'cb-inc.php';
$m = new MongoClient();
$db = $m->crunchbase;
$jsoninv_collection = $db->jsoninv;
$investments_collection = $db->investments;
$investments_collection->drop();

$need_update_jsons=true;

$cursor1 = $jsoninv_collection->find();
$items_count=$cursor1->count();
$current_item=0;
$apikey=get_api_key();

foreach ($cursor1 as $doc) {
	
	
	
	$current_item++;
	if (0==fmod($current_item,100)) {echo "($current_item)\n";}
	$js=null;
	$js=json_decode(bzdecompress($doc['jsondata']->bin));
	$js2=$js;
	//print_r($js);sleep(10);
	if (!isset($js->data->uuid)) {continue;echo "#";} //bad data
	if (!isset($js->data->properties->funding_type)) {continue;echo "!";} //bad data
	
	
	//update invround
if ($need_update_jsons) {	
	
	$objname= $doc['id'];
	$url="http://api.crunchbase.com/v/2/funding-round/".$objname."?user_key=$apikey";
	//echo $doc['id'].": ".$js->data->uuid."\n";
	$res=askhost($url,FALSE, "", "", "1", 60000, "", true);
	$js=json_decode($res['data']);
	if (isset($js->data->uuid))  {save_json_to_mongo($js,$objname, $jsoninv_collection);} else {
		echo "_";
		$apikey=get_api_key();
		$js=$js2;
	}
}	
	
	$ftype=$js->data->properties->funding_type;
	if (!isset($js->data->properties->money_raised_usd)) {
		echo "no usdsum, skip\n";continue;
	}
	$usdsum=$js->data->properties->money_raised_usd;
	$fcurr=$js->data->properties->money_raised_currency_code;
	if (isset($fround->data->properties->series)) $fser=$js->data->properties->series; else $fser=null;
	$round_date=strtotime($js->data->properties->announced_on);
	$fdatetrust=$js->data->properties->announced_on_trust_code;
	if (!isset($js->data->relationships->funded_organization)) {
		//print_r($js);sleep(10);
		echo "no rcpt, skip\n";
		continue;
	}
	$rcpt_path=strip_first_part($js->data->relationships->funded_organization->items[0]->path);
	$rcpt_name=strip_characters($js->data->relationships->funded_organization->items[0]->path);
	$investments_collection->insert(array("id"=>$doc['id'],"rcptname"=> $rcpt_name, "recipient"=> $rcpt_path, "funding_type" => $ftype, "usd_sum"=> $usdsum , "curr"=> $fcurr, "ser" => $fser, "fdate"=>$round_date, "datetrust"=> $fdatetrust));
	echo ".";
}



?>