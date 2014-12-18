<?php 


include_once 'askhost.php';
require_once 'csv2arr.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
$pg_host="dbname=kickstarter user=capsidea password=31337 connect_timeout=30";
$dbconn = pg_connect($pg_host) or log_fatal('Could not connect to application database, please contact support.');
$tmp_dir="/tmp/";
date_default_timezone_set("UTC");

$curr=array("USD"=>1,"EUR"=>1.3609, "CAD"=>0.9224, "PLN"=> 0.3258, "DKK"=> 0.1823 , "SEK"=> 0.1532,
		"NOK" => 0.161665,"SGD" => 0.787183,"AUD" => 0.889441,"ILS" => 0.285963,"HUF" => 0.0045405,
		"MXN" => 0.0764597,"PHP" => 0.0223764,"BRL" => 0.418382,"CHF" => 1.10169,"CZK" => 0.049640,	"NZD" => 0.821239);
//state_days_dim, pledged_rng, goal_rng
$schemajson="&fields=".urlencode('[{Name: "id", TypeName:"string", Description: "project id" },{Name: "launch_time", TypeName:"timestamp", Description: "project launch time" },		
{Name: "till_days", TypeName:"double", Description: "project baking duration" },		
{Name: "pname", TypeName:"string", Description: "project name" }, {Name: "parent_category", TypeName:"string", Description: "parent category name"},	
{Name: "goal", TypeName:"double", Description: "goal sum" },		{Name: "pledged", TypeName:"double", Description: "pledged sum" },		
		{Name: "state", TypeName:"string", Description: "current state"},		{Name: "state_days", TypeName:"double", Description: "days from beginning to change state" },
		{Name: "creator_name", TypeName:"string", Description: "project owner name"},		{Name: "bakerscount", TypeName:"double" , Description:"current number of bakers"},		
		{Name: "category_name", TypeName:"string", Description:"category name"},	{Name: "categoryposition", TypeName:"double" ,Description:"project in category current position"},	
	{Name: "location_name", TypeName:"string", Description:"location name"},		{Name: "location_state", TypeName:"string",Description:"location state name"},		
	{Name: "location_country", TypeName:"string", Description:"location country"}, {Name: "days_till_deadline", TypeName:"string", Description: "goal sum" }
		, {Name: "pledged_pct", TypeName:"double", Description: "pledged in percent" },	
 {Name: "year", TypeName:"string", Description: "year of project start" }	, {Name: "month", TypeName:"string", Description: "month of project start" },
{Name: "goal_dim", TypeName:"string", Description: "goal sum as dimension" },
{Name: "till_days_dim", TypeName:"string", Description: "project baking duration as dimension" },{Name: "pct_dim", TypeName:"string", Description: "pledged in percent as dimension" },
		{Name: "state_days_dim", TypeName:"string", Description: "days from beginning to change state" },
		{Name: "pledged_rng", TypeName:"string", Description: "pledged range" },
		{Name: "goal_rng", TypeName:"string", Description: "goal range" } ]');
$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";
$capsidea_appid=3128;

function get_range($value)
{
	if (1000>$value) return "<1K$";
	if (10000>$value) return "1-10K$";
	if (50000>$value) return "10-50K$";
	if (100000>$value) return "50-100K$";
	if (500000>$value) return "100-500K$";
	if (1000000>$value) return "500K-1M$";
//	if (5000000>$value) return "<5M";
//	if (25000000>$value) return "<25M";
//	if (50000000>$value) return "<50M";
	return ">1M";
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

$base_url="http://beta.capsidea.com/api";
$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=NULL&withheader=1&reload=1&frequency=daily";

function load_prj_from_db()
{
	$arr=array();
	$pgres2=pg_query("select id from project;");
	$ccount=pg_num_rows($pgres2);
	while ($row2 = @pg_fetch_assoc($pgres2)) {
		//$ppid=$row2["ppid"];
		$arr[$row2["id"]]=1;
	}
	pg_free_result($pgres2);
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
function load_catnames_from_db()
{
	$arr=array();
	$pgres2=pg_query("select id, cname from category;");
	$ccount=pg_num_rows($pgres2);
	while ($row2 = @pg_fetch_assoc($pgres2)) {
		//$ppid=$row2["ppid"];
		$arr[$row2["id"]]=$row2["cname"];
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


function prepare_report()
{
	$cntryarr=load_countries_from_csv("./country.csv");
	$category=load_catnames_from_db();
	global $curr;
	$fname=tempnam("/tmp", "krep");
	//$report_fname=$client_dir."/".$fname.".csv";
	$fw=fopen($fname, "w");
	fputs($fw, "id,launch_time,till_days,pname, parent_category, goal, pledged, state, state_days, creator_name, bakerscount, category_name,  categoryposition, location_name,  location_state, location_country,days_till_deadline,pledged_pct,year,month,goal_dim, till_days_dim, pct_dim, state_days_dim, pledged_rng, goal_rng\n");

	$res=pg_query("select p.id as id,deadline, currency, pname, goal, pledged, \"state\", to_timestamp(launchedat) as launch_time, ((deadline-launchedat)/86400) as till_days,  ((statechangedat-launchedat)/86400) as state_days, cr.cname as creator_name, bakerscount, cat.cname as category_name , cat.parentid as parent_category, categoryposition, l.lname as loc_name, l.cstate as loc_state, l.ccountry as loc_country from project as p, creator as cr, category as cat, location as l where l.id=p.l_id and cr.id=p.creator_id and cat.id=p.cat_id;");
	while ($row2 = @pg_fetch_assoc($res)) {
		if (0==$row2['launch_time']) continue; // skip fake projects
		if (100<$row2['till_days']) continue;
		// export row
		$line=array();
		$line[]=$row2['id'];
		$line[]=convert_to_cps_date($row2['launch_time']);
		$line[]=$row2['till_days'];
		$line[]=preg_replace('/[^A-Za-z0-9\ \-]/', '',$row2['pname']);
		$pcat=(int)$row2['parent_category'];
		if (0==$pcat) $line[]=$row2['category_name'];
		else $line[]=$category[$pcat];
		$currency=$row2['currency'];
		//process currency
		$goal=$line[]=convert_to_usd($curr, $currency, $row2['goal']);
		$pledged=$line[]=convert_to_usd($curr, $currency,$row2['pledged']);
		// rest of
		$line[]=$row2['state'];
		$line[]=$row2['state_days'];
		$line[]=preg_replace('/[^A-Za-z0-9\ \-]/', '',$row2['creator_name']);
		$line[]=$row2['bakerscount'];
		$line[]=$row2['category_name'];
		$line[]=$row2['categoryposition'];
		$line[]=$row2['loc_name'];
		$line[]=$row2['loc_state'];
		
		if (!isset($cntryarr[$row2['loc_country']])) {
			$line[]="unknown country";
		} else {
			$line[]=$cntryarr[$row2['loc_country']];
		}
		$days_left=floor(($row2['deadline']-time())/86400);
		if (0>$days_left) $days_left=0; 
		$line[]=$days_left;
		if (0==$goal) $goal=0.00001;
		$line[]=floor(($pledged/$goal)*100);
		//$line[]=$row2['loc_country'];
		$launch_date=strtotime($row2['launch_time']);
		$line[]=date("Y",$launch_date);
		$line[]=date("M",$launch_date);
		$line[]=$goal;
		$line[]=$row2['till_days'];
		$line[]=floor(($pledged/$goal)*100);
		$line[]=$row2['state_days'];
		$line[]=get_range($pledged);
		$line[]=get_range($goal);
		fputcsv($fw, $line);
	}
	fclose($fw);
	$zip = new ZipArchive();
	$filename = $fname.".zip";
	if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
		die("cant open <$filename>\n");
	}
	$zip->addFile($fname, "data.csv");
	$zip->close();
	@unlink($fname);
	return $filename;
}
//state_days_dim, pledged_rng, goal_rng

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
	
	file_put_contents("/tmp/kickstarter.log", date(DATE_ATOM)." $line\n", FILE_APPEND);
}
function log_fatal($msg)
{
	mylog($msg);
	die($msg);
}


?>