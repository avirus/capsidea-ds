<?php 

include 'cb-inc.php';
$m = new MongoClient();
$db = $m->crunchbase;
//$collection = $db->lastrun;
$cb_cache_time=7*24*60*60; // 7 days
$fulldata_collection = $db->fulldata;
$org_collection = $db->organizations;

class workerThread extends Thread {
	public function __construct($object, $apikey,$fname){
		$this->o=$object;
		$this->k=$apikey;
		$this->f=$fname;
	}
	public function run() {
		$object=$this->o;
		$apikey=$this->k;
		$fname=$this->f;
		$res=askhost("http://api.crunchbase.com/v/2/".$object."?user_key=$apikey",FALSE, "", "", "1", 60000, "", true);
		if (500==$res['httpcode']) return false;
		//responsedie($res,'error 500 (get org data)');
		$js=json_decode($res['data']);
		$fname="/tmp/cb/".strip_first_part($object).".json";
		file_put_contents($fname, json_encode($js,JSON_PRETTY_PRINT) );
		echo ".";
		return true;
	}
}

$cursor1 = $org_collection->find();
$items_count=$cursor1->count();
$current_item=0;
$resumerec=130000;
$workers=array();
echo "total records: ".$items_count." resuming at $resumerec\n";
foreach ($cursor1 as $doc) {
	$current_item++;
	if ($resumerec>$current_item) continue;
	if ("Product"==$doc['type']) continue;
	//$fname="/tmp/cb/".strip_first_part($doc['id']).".json";
	$js=null;
	$apikey=get_api_key();
	$object=$doc['id'];
	$fname="/tmp/cb/".strip_first_part($object).".json";
	if (file_exists($fname)) {
		if (filemtime($fname)>(time()-$cb_cache_time)) { // file newer than cache life time?
			$js=json_decode(file_get_contents($fname));
			echo ",";}
			else { // remove old cache file
				unlink($fname);
				echo "?";
				// fork fetcher
				$workers[$current_item]=new workerThread($object, $apikey,$fname);
				$workers[$current_item]->start();
			}
	}
	if (!isset($js->data->uuid)) 	{
		echo "\n# [$current_item] bad data object ".print_r($js,true).", retry"; 
		$js=null;
		$workers[$current_item]=new workerThread($object, $apikey,$fname);
		$workers[$current_item]->start();
		//fork
	}
	
	
}



?>