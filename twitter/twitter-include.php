<?php 
include_once 'twitterclass1.php';
include_once 'askhost.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
$pg_host="dbname=twitter user=capsidea password=31337 connect_timeout=30";
$dbconn = pg_connect($pg_host) or log_fatal('Could not connect to application database, please contact support.');
$tmp_dir="/tmp/";
date_default_timezone_set("UTC");

$schemajson="&fields=".urlencode('[{Name: "ts", TypeName:"timestamp" },{Name: "location", TypeName:"string"}
		,{Name: "userid", TypeName:"string"},{Name: "rtc", TypeName:"double" },{Name: "placecntry", TypeName:"string"},{Name: "placename", TypeName:"string"}]');

$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";
$capsidea_appid=3097;
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

$base_url="http://beta.capsidea.com/api";
$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=NULL&withheader=1&reload=1&frequency=daily";

function convert_to_cps_date($string)
{
	return date("Y-m-d 0:00:00", strtotime((string)$string));
}

function prepare_report($shemaid)
{
	$fname=tempnam("/tmp", "twrep");
	//$report_fname=$client_dir."/".$fname.".csv";
	$fw=fopen($fname, "w");
	fputs($fw, "ts,location,userid,rtc,placecntry,placename\n");
		
	$row=pg_fetch_row(pg_query("select t_id from scheme where id=$shemaid"));
	$tag_id=(int)$row[0];
	$res=pg_query("select ts, location, userid, rtc, placecntry, placename from twits where t_id=$tag_id");
	while ($row = @pg_fetch_assoc($res)) {
	// export row
		$line=array();
		$line[]=convert_to_cps_date($row["ts"]);
		$line[]=$row["location"];
		$line[]=$row["userid"];
		$line[]=$row["rtc"];
		$line[]=$row["placecntry"];
		$line[]=$row["placename"];
		fputcsv($fw, $line);
	}
	fclose($fw);
	$zip = new ZipArchive();
	$filename = "/tmp/twr".$shemaid.".zip";
	if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
		die("cant open <$filename>\n");
	}
	$zip->addFile($fname, "data.csv");
	$zip->close();
	@unlink($fname);
	return $filename;
}

function save_json_to_mongo($js,$id, $jsondata_collection){
	$newitem=array("id"=>$id, "ts"=>time(), "jsondata"=>new MongoBinData(bzcompress(json_encode($js),9), MongoBinData::GENERIC));
	$jsondata_collection->remove(array("id"=>$id));
	$jsondata_collection->insert($newitem);

}


function process_tag($tag, $twitterApi, $lastid,$tagid, $tweets_collection)
{
	
	

	$json = $twitterApi->get('search/tweets', [
			//'q' => '#RealMadrid',
			'q' => $tag,
			//'result_type' => 'recent',
			'count' => '2000',
			//		'until'=>"2014-04-14",
	//		'max_id' => '12345',
			'since_id' => $lastid
			]);
	
	//echo $json;
	$result=json_decode($json);
	//$arr=array();
	//$res=json_encode($arr, JSON_PRETTY_PRINT);
	//print_r($result);
	$i=0;
	$id=0;
	foreach ($result->statuses as $arval)
	{
		$tid=0;
		$place_name="null";
		$place_c="null";
	
		$id=$arval->id;
		save_json_to_mongo($arval, $id, $tweets_collection);
		$ts=date("Y-m-d H:i:sO",strtotime( $arval->created_at));
	
		$text= $arval->text;
		// @todo process mensions
	
		//$text=base64_encode($text);
		$username=$arval->user->screen_name;
		$location=$arval->user->location;
		$retweet_count=(int)$arval->retweet_count;
	
		//if (0!=$retweet_count)
		if (isset($arval->retweeted_status->id))
		{ // this is retweet
			$tid=$arval->retweeted_status->id;
			pg_query("update twits set rtc=$retweet_count where id=$tid");
			echo "@";
			continue;
		}
		if (isset($arval->place->id)) { // have place?
			$place_name=$arval->place->name;
			$place_c=$arval->place->country_code;
			echo "\n $place_name $place_c\n";
		}
		//echo "[$id] $ts $retweet_count : $text \n";
		echo ".";
	
		//$rj=base64_encode(bzcompress( print_r($result, true)));
		$text=pg_escape_string( $text);
		$location=pg_escape_string( $location);
		//$location=pg_escape_string(preg_replace('/[^A-Za-z0-9\ \-]/', '', $location));
		//CURRENT_TIMESTAMP
		pg_query("insert into twits (t_id, id, userid, location, ttext, rtc, ts, placename, placecntry) values ($tagid, $id, '$username', '$location', '$text', 0, '$ts', '$place_name', '$place_c' );");
		if ($lastid<$id) $lastid=$id;
		$i++;
	
	} // each twit
	//file_put_contents("/tmp/t/$id.log", print_r($result, true) , FILE_APPEND);
	//echo "\n";print_r($result);echo "\n";
	//$json
	//echo "($i) sleep 10 sec ... ";
	return $lastid;
}

function init_twapi()
{
	
	$twitterApi = new Tang\TwitterRestApi\TwitterApi([
			'api_key' => "N0Zj...w4YcMb",
			'api_secret' => "gq49ZVB9l...z0aO2iTBmX5h8"
			]);
	$twitterApi->authenticate();
	return $twitterApi;
}

function  mylog($line) {
	global $my_data_dir;
	file_put_contents("/tmp/twitter.log", date(DATE_ATOM)." $line\n", FILE_APPEND);
}

function log_fatal($msg)
{
	mylog($msg);
	die($msg);
}

?>