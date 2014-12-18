<?php 


include_once 'askhost.php';
require_once 'csv2arr.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
$tmp_dir="/tmp/";
date_default_timezone_set("UTC");
//  you should provide your spi key(s)
$apikeys=array(
		"ca468b75b8a48d431f274dd9f28f62b9","13d7212c2e645641bf2c7d33c562a314","ce55e57717b4f0c86f354efe0df8ea57","5f6f54fa06c61472d7c02f2b5e9316a8","ac4076064d2ee426815e7fc59217270f","9c96bfcbe030970befd64a10bfb15198",
		"532afe5ef22d070d12f223d7d8f14655","6f98ce969283aa7bda1d8dc21d75420d","1da20e0a7fa71b1846f944093875f47b","ea8c3908ce57868a40beef363d304323","bfdda8ad7d9dc800a22f94e78a45eb16","cccf23ab16bd4f3336541f76d5089410",
		"9ea085e97efc40f63ee8ad0de8460c3c","260c80055a67cc4aa4e76c1812c3b476","ad9f40b8239874b87b1a7e40950eea10","469ac59f1b370dab04f4f049f9fa73e9","7a9e63b2738e79791ba77ea61c5212c6","ba0438a3919b86832410006f4cafbdd0",
		"78d229a5b291f5bf5668ffa95473a317","a4f7e8dab07886d9addc0eb8d45df759","df8377e17e1b30bbeb96e91210860017","ccdfb2a13de6e88301036fee2de69043","3019dc04add6fe1d1dd837ebe175429d","1f2e2950fec6d836e4010b9f4286a7fc",
		"48a06841fb48f462c61e26776d30be57","03dee4f0740bf3b44f8204455a41f4a8","3eed25ba0f6ae0eb758ce5f4829bb8b5","d03ee138e4f8f67136bd5be6c1d44d22","c6319a04249c7a5277659b4fa3a34134","9d87c729865c76ffaa9fb223c772866c",
		"3abf2bb8e85e3f14d633599e8d711d96","675d0a74be6001072bda3d6f3fd328e4","b9f7910496c0822c848af0dd5c712ac6","c4c2e5e54d56851c73bb705fe7608123","acfd6b3d07cc04c25c7075c25e80286d","8d9b4fc3e9e77a415b00ba405b75e568",
		"7fe53d785b106064caf15b8337dca018","f458831dbc1e57330bd8b2ce96b94feb","1de9e015f9a4db6c2de860eddd5b63c6","f1a2787ebbb3af2da0d09656152a0fe0","81a53e113ba2859415e8b15b9478333e","dc475045dbec952f09ce4f3da8fdb96f",
		"952b2fd0e70fde9766e0f5339279e159","78b866fe18215e3eab0d819d9e05e3f8","a70c2fd77c2a7b2ab822e07acb4071c2","367c40c1294c45a25baac4548254c24e","3a4efff04cc055acd51a87a1a5d6306f","3b14450be4145815e437b6a33c004b13",
		"80214a18cf656b22db744fde0ce587b3","879507ae8441425dcddf1132563fd85b","34f12cdb08274a767e3d7f4da42c1c37","ca39ec201c7456be83ddbdb5bdd605c2","b6cf9afde0fcd7575f583b992afb5c6c","6e7565d490b034656cc012734e5aab8c",
		"847003099093e5fc48097572f55790b8","b2703a8c3c4f17a1a61f8ae0de8f407b","c58de509616e591452a3460498b301dc","46292fec468b7d4b5121c7ea6b23901d","b214852208c0471702ed7b08bf67c1d6","2738b418cc071f4b5479652141f50ec0",
		"75d22182e7e24141a3f5f48ac6cef8e2","fe95fcf42289eb9e8354f98f77c15bcd","9a334cc39ac8ae65fdf898cfae68db71","be3d03cfe1cfd82639c83623b88db39c","40bbf13231ad4bcd15921d407a159c9b","daa1709713dec963e3950b9b4df2bb4a",
		"fefe934cf6f3fe2545a50955ee054baf","bc7b62edc4be573a1d7fdb57755a784c","63b6d383ba4bbf5032fb431029b2ac26","1447e448895de997aa8a14ef808b09b9","e5fee89222c2a1680cfdf2278bff79df","b927cde838afa155c9dfce2e8d1afc7b",
		"f661c721c75a0cdc811321e81369c45a","853001f874f7f723713c2930c19aa78b","317abce0be0eaac321670a363cbd209a","52c7ec3bb90d930d10019ff093fa460b","e5b6ad92efccf11a114fedfa7c923add","7546e19d390735d2fb0c5b867458c677",
		"5369b6f6341f284597ae7db3a84eef00","a165753f6877cc135e2b205bdd9c5424","d1583a8f3d0e92f7b9e574be29e72d77","20ceddde2bdcaaba9befcef545bf94c6","b69d81ec915008bafc4a8051c729c077","edab3db687c6baf756f73ab4193d7bab",
		"f21ea3c2200f1762a93926df3fcb66a5","72d28607951cec6e9d158a904fe6a712","4ba295e08d8ad12cba695bee39b45044","f06959ff48bc09b3daabe667dcb1cdfb","2af646391151806444d1b1d4045641e7","9e5a108022b5d7f916463f34d2ed0426",
		"d39c2ea9dceb27efb71f6c2a0dd77cff","d60ceb7a998f2061a5c4701d229df81e","668e893e730103d8a8b952c4639961e2","70e22fc455852aa25145280c7473e887","2c3523fb2d6386b375f07dae03237cfc","306be4295482600fd98ae053553e8429",
		"0881e0dadd37cd4e1fda4e6bea7ae05d","80513b7da16582495534f53ef9367150","151ff790d8634dab3b1cb8c10edf0899","51bfe1b9a27997bc154f641f777f1290","1fd33df39488869387f1c318c4edfb0c","7b8ffade2e0dc10812d68b173f107ed7",
		"a0dd68a10d23d943d6f1b8a4f9f110f4","070bd1ef63379d50dd4174c5aa91a012","7858475bd71debe6b70a2cf238bcb1fb","1f08c7e35cc49635399aec79c607e488","c0ee799e3d0daffe93ff4547bf17e014","f1eee66ac422dfdbe35d5de72b4b41dd"
);
$current_num=0;
$curr=array("USD"=>1,"EUR"=>1.3609, "CAD"=>0.9224, "PLN"=> 0.3258, "DKK"=> 0.1823 , "SEK"=> 0.1532,
		"NOK" => 0.161665,"SGD" => 0.787183,"AUD" => 0.889441,"ILS" => 0.285963,"HUF" => 0.0045405,
		"MXN" => 0.0764597,"PHP" => 0.0223764,"BRL" => 0.418382,"CHF" => 1.10169,"CZK" => 0.049640,	"NZD" => 0.821239);
//launch_time,till_days,pname, goal, pledged, state, state_days, creator_name, bakerscount, category_name,  categoryposition, location_name,  location_state, location_country
$schemajson="&fields=".urlencode('[{Name: "investor_name", TypeName:"string", Description: "investor name" },
{Name: "investment_sum_usd", TypeName:"double", Description: "investment sum usd" },
{Name: "funding_series", TypeName:"string", Description: "funding series" },		
{Name: "funding_type", TypeName:"string", Description: "funding type" },
{Name: "recipient", TypeName:"string", Description: "funding recipient" },
{Name: "date", TypeName:"timestamp", Description: "funding announce date" },		
{Name: "year", TypeName:"string", Description: "year" },
{Name: "month", TypeName:"string", Description: "month" },
{Name: "quarter", TypeName:"string", Description: "quarter" },
{Name: "category", TypeName:"string", Description: "market name" },
{Name: "investment_range", TypeName:"string", Description: "investment range" }								
			]');

$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";
$capsidea_appid=3439;

function get_investment_round($full_uuid, $apikey)
{
	global $investments;
	global $jsoninv_collection;
	$inv_uuid=strip_first_part($full_uuid);
	if (!isset($investments[$inv_uuid])) { // get this new investment round
		$fround=null;
		$res2=askhost("http://api.crunchbase.com/v/2/funding-round/".$inv_uuid."?user_key=$apikey",FALSE, "", "", "1", 60000, "", true);
		$fround=json_decode($res2['data']);
		if (isset($fround->data->uuid)) {
			save_json_to_mongo($fround,$inv_uuid, $jsoninv_collection);
			echo  "+";
		} // valid obj
	} // new inv round
	else { 
		echo "-";
	}
	return true;

}

function update_related($js){
	global  $jsondata_collection;
	global $apikey;
	if (isset($js->data->properties->role_investor))
	{
		// investor!
		if (isset($js->data->relationships->investments))		{ // have investments
			foreach (	$js->data->relationships->investments->items as $item)	{
				get_investment_round($item->funding_round->path, $apikey);
			}
		}
	}
	if (isset($js->data->funding_rounds)) { 		// have investments
		foreach ($js->data->funding_rounds->items as $item) 		{
			get_investment_round($item->path, $apikey);
		} // foreach round
	} 		// have investments?

}
function update_object_and_related($objname){
	//global $jsoninv_collection;
	global  $jsondata_collection;
	$cachetime=12*60*60; // 12h
	$objjs=load_json_from_mongo($objname, $jsondata_collection);
	$ts=$data['ts'];
	if (($ts+$cachetime)>time()) {echo "," ;return true;} 
	$apikey=get_api_key();
	$res=askhost("http://api.crunchbase.com/v/2/".$objname."?user_key=$apikey",FALSE, "", "", "1", 60000, "", true);
	$js=json_decode($res['data']);
	if (isset($js->data->uuid)) save_json_to_mongo($js,$objname, $jsondata_collection);
	update_related($js);
}



function load_json_from_mongo($id, $jsondata_collection, $decoded=true){
	$data=$jsondata_collection->findone(array('id' => $id));
	//$js=json_decode(bzdecompress($data['jsondata']->bin));
	if ($decoded) return json_decode(bzdecompress($data['jsondata']->bin));
	return $data;
}
function save_json_to_mongo($js,$id, $jsondata_collection){
	$newitem=array("id"=>$id, "ts"=>time(), "jsondata"=>new MongoBinData(bzcompress(json_encode($js),9), MongoBinData::GENERIC));
	$jsondata_collection->remove(array("id"=>$id));
	$jsondata_collection->insert($newitem);

}


function responsedie($res, $msg)
{
	echo $res['d']."\n";
	echo $res['data']."\n";
	die("incorrect response: $msg\n");
}

function load_objs_from_mongo($org_collection)
{
	$objs=array();
	$cursor1 = $org_collection->find();
	foreach ($cursor1 as $doc) {
		$objs[$doc['id']]=1;
	}
	return $objs;
}


function load_json_list_from_mongo($jsondata_collection)
{
	$arr=array();
	$cursor=$jsondata_collection->find();
	foreach ($cursor as $doc) {
		$arr[$doc['id']]=1;
	}
	//$cursor->reset;
	//pg_free_result($pgres2);
	return $arr;
}

function get_api_key()
{
	global $current_num;
	global $apikeys;
	$current_num++;
	if ($current_num==count($apikeys)) $current_num=0;
	//echo "{".$current_num."}";
	return $apikeys[$current_num];
}
function get_capsidea_data($capsidea_client_secret)
{
	$ret=array();
	$parsed_url=parse_url($_SERVER['HTTP_REFERER']);
	$var  = explode('&', $parsed_url['query']);
	foreach($var as $val)
	{
		$x          = explode('=', $val);
		$arr[$x[0]] = $x[1];
	}
	unset($val, $x, $var, $qry, $parsed_url, $ref);
	if (isset($arr["token"])) {$token=$arr["token"];} else {die ("cant find capsidea.com token");}
	if (36!=strlen($token)) {die("capsidea.com token incorrect");}
	$ret["c"]=$token;
	$ret["t"]=sha1($capsidea_client_secret.$token);
	if (isset($arr["schemakey"])) $ret["k"]=$arr["schemakey"];
	return $ret;
}


$server_url="http://beta.capsidea.com/api?s=ImportService&delimeter=,&nullstr=NULL&withheader=1&reload=1&frequency=daily";

function load_investments_from_mongo($investments_collection)
{
	$arr=array();
	$cursor=$investments_collection->find();
	foreach ($cursor as $doc) {
		if (isset($doc['series'])) $ser=$doc['series']; else $ser=null; 
		$arr[$doc['id']]=array("s"=>$doc['usd_sum'], "t"=>$doc['funding_type'],"d"=>$doc['fdate'],"r"=>$ser	,  "c"=>$doc['rcptname']	);
	}
	//$cursor->reset;
	//pg_free_result($pgres2);
	return $arr;
	
}

function load_investments_list_from_mongo($investments_collection)
{
	$arr=array();
	$cursor=$investments_collection->find();
	foreach ($cursor as $doc) {
		$arr[$doc['id']]=1;
	}
	//$cursor->reset;
	//pg_free_result($pgres2);
	return $arr;
}
function load_objs_list_from_mongo($fulldata_collection)
{
	$arr=array();
	$cursor=$fulldata_collection->find();
	foreach ($cursor as $doc) {
		$arr[$doc['id']]=1;
	}
	//$cursor->reset;
	//pg_free_result($pgres2);
	return $arr;
}

function load_loc_from_db()
{
	$arr=array();
	$pgres2=pg_query("select id from location;");
	$ccount=pg_num_rows($pgres2);
	while ($row2 = @pg_fetch_assoc($pgres2)) {
		//$ppid=$row2["ppid"];
		$arr[$row2["id"]]=1;
	}
	pg_free_result($pgres2);
	return $arr;
}
function load_cat_from_db()
{
	$arr=array();
	$pgres2=pg_query("select id from category;");
	$ccount=pg_num_rows($pgres2);
	while ($row2 = @pg_fetch_assoc($pgres2)) {
		//$ppid=$row2["ppid"];
		$arr[$row2["id"]]=1;
	}
	pg_free_result($pgres2);
	return $arr;
}
function load_creators_from_db()
{
	$arr=array();
	$pgres2=pg_query("select id from creator;");
	$ccount=pg_num_rows($pgres2);
	while ($row2 = @pg_fetch_assoc($pgres2)) {
		//$ppid=$row2["ppid"];
		$arr[$row2["id"]]=1;
	}
	pg_free_result($pgres2);
	return $arr;
}


function convert_to_cps_date($string)
{
	return date("Y-m-d 0:00:00", strtotime((string)$string));
}

function cps_date_from_utime($unixtime)
{
	return date("Y-m-d 0:00:00", $unixtime);
}

function year_from_utime($unixtime)
{
	return date("Y", $unixtime);
}

function month_from_utime($unixtime)
{
	return date("m", $unixtime);
}

function quarter_from_utime($unixtime)
{
	$curMonth = date("m", $unixtime);
	return ceil($curMonth/3);
}

function load_countries_from_csv($file_name)
{
	$tmp=csv_to_array($file_name);
	$cntryarr=array();
	foreach ($tmp as $line) {
		$cntryarr[$line["iso"]]=$line["name_en"];
	}
	unset($tmp);
	return $cntryarr;
}

function  strip_characters($input)
{
	return preg_replace('/[^A-Za-z0-9\ \-]/', '',$input);
}

function get_range($value)
{
	if (1000000>$value) return "<1M";
	if (5000000>$value) return "<5M";
	if (25000000>$value) return "<25M";
	if (50000000>$value) return "<50M";
	return ">50M";
}

function strip_first_part($value){
	return substr(strstr($value, "/"),1);
}

function prepare_report($db)
{
$fulldata_collection = $db->fulldata;
$investments_collection = $db->investments;
$investments=load_investments_from_mongo($investments_collection);
$jsondata_collection = $db->jsondata;
$fulljson=load_objs_list_from_mongo($jsondata_collection);
$fname=tempnam("/tmp", "cb-rep");
$fw=fopen($fname, "w");
fputs($fw, "investor_name,investment_sum_usd,funding_series,funding_type,recipient,date,year,month, quarter, category, investment_range\n");
$cursor1 = $fulldata_collection->find();
foreach ($cursor1 as $doc) {
$name=$doc['name'];
$inv=$doc['investments'];
$markets=$doc['markets'];
foreach ($inv as $investment) {
	if (!isset($investments[$investment]['s'])) continue;
	$funding_sum=$investments[$investment]['s'];
	$funding_type=$investments[$investment]['t'];
	$funding_date=$investments[$investment]['d'];
	$funding_round=$investments[$investment]['r'];
	$funding_company=$investments[$investment]['c'];
	$line=array();
	$line[0]=$name;
	$line[1]=$funding_sum;
	$line[2]=$funding_round;
	$line[3]=$funding_type;
	$line[4]=$funding_company;
	$line[5]=cps_date_from_utime($funding_date);
	$line[6]=year_from_utime($funding_date);
	$line[7]=month_from_utime($funding_date);
	$line[8]=quarter_from_utime($funding_date);
	$line[9]=null;
	$line[10]=get_range($funding_sum);
	foreach ($markets as $market) {
		$line[9]= $market->name;
		fputcsv($fw, $line);
	} // each market
} // each investment
} //each investor
fclose($fw);
$zip = new ZipArchive();
$filename =$fname."-report.zip";
if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
	die("cant open <$filename>\n");
}
$zip->addFile($fname, "data.csv");
$zip->close();
@unlink($fname);
	return $filename;
}
//goal_range,pledged_range

function convert_to_usd(&$curr, $txn_cur, $amount)
{
	if (!isset($curr[$txn_cur])) {
		$result=askhost("http://rate-exchange.appspot.com/currency?from=$txn_cur&to=USD");
		$jsonres=json_decode($result, true);
		if (!isset($jsonres['rate'])) {log_fatal("unknown currency $txn_cur");}
		$curr[$txn_cur]=$jsonres['rate'];
	} // lookup new currency
	return floor($curr[$txn_cur]*$amount);
}

function get_capsidea_data2($capsidea_client_secret)
{
	$ret=array();
	if (isset($_GET["token"])) {$token=$_GET["token"];} else {die ("cant find capsidea.com token, please contact application support");}
	if (36!=strlen($token)) {die("capsidea.com token incorrect, please contact application support");}
	$ret["c"]=$str=preg_replace('/[^A-Za-z0-9\-]/', '', $token);
	$ret["t"]=sha1($capsidea_client_secret.$token);
	if (isset($_GET["schemakey"])) $ret["k"]=(int)$_GET["schemakey"];
	return $ret;
}

function  mylog($line) {
	
	file_put_contents("/tmp/crunchbase.log", date(DATE_ATOM)." $line\n", FILE_APPEND);
}
function log_fatal($msg)
{
	mylog($msg);
	die($msg);
}


?>