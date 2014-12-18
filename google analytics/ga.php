<?php 
// google analytics fetcher (oauth2 token version) $Author: slavik $
header('Access-Control-Allow-Origin: *');
set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/html/google-api-php-client/src');
error_reporting(E_ERROR);
ini_set('display_errors', 1);
$my_data_dir="/tmp";
// Visit capsidea.com to generate your client_secret, and app id.
$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";
$capsidea_appid=2226;

// Visit https://cloud.google.com/console to generate your client id, client secret, and to register your redirect uri.
$ga_client_secret="Ae...ZKJ";
$ga_client_id="6824...1tg.apps.googleusercontent.com";
$ga_redirect_uri="http://app.capsidea.com/ga.php";
$ga_appname="CAPSIDEA";

function  mylog($line) {
	file_put_contents("/tmp/ga.log", date(DATE_ATOM)." $line\n", FILE_APPEND);
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


$server_url="http://beta.capsidea.com/api?s=ImportService&delimeter=,&nullstr=NULL&withheader=1&name=GoogleAnalytics&reload=1";
$ga_scopes=array('https://www.googleapis.com/auth/analytics.readonly','https://www.googleapis.com/auth/userinfo.profile','https://www.googleapis.com/auth/userinfo.email');
//include_once '../askhost.php';
include_once 'askhost.php';
require_once('Google/Client.php');
require_once('Google/Service/Analytics.php');

//$js=json_decode($_POST['a']);
$cdata=get_capsidea_data($capsidea_client_secret);
//$metrics=$js->metrics;
$metrics=$_REQUEST['metrics'];
foreach ($metrics as &$thismetric) {
	$thismetric="ga:".$thismetric;
}
$dimensions=$_REQUEST['dimensions'];
foreach ($dimensions as &$thisdim) {
	$thisdim="ga:".$thisdim;
}
$metrics=implode(",", $metrics);
$dimensions=implode(",", $dimensions);
$date1=date("Y-m-d", strtotime($_REQUEST['datefrom']));
$date2=date("Y-m-d", strtotime($_REQUEST['datetill']));
//$date1="2014-01-01";
//$date2="2014-05-01";
//echo "$date1 $date2<br>";
$code=$_REQUEST['token'];
$t2=$_REQUEST['rancid'];
$profileId=$_REQUEST['prof_id'];
$t=time();
$texp=$t2-$t;
$token="{\"access_token\":\"$code\",\"token_type\":\"Bearer\",\"expires_in\":$texp,\"created\":$t}";
$secret=$cdata["t"];
$client = new Google_Client();
$client->setApplicationName($ga_appname);
$client->setClientId($ga_client_id);
$client->setClientSecret($ga_client_secret);
$client->setScopes($ga_scopes);
$client->setRedirectUri($ga_redirect_uri);
$client->setAccessToken($token);
$mdnames=json_decode( askhost("https://www.googleapis.com/analytics/v3/metadata/ga/columns?pp=1"));


try {
	$analytics = new Google_Service_Analytics($client);
} catch (Exception $e) {
	unset($client);
	$client = new Google_Client();
	$client->setApplicationName($ga_appname);
	$client->setClientId($ga_client_id);
	$client->setClientSecret($ga_client_secret);
	$client->setScopes($ga_scopes);
	$client->setRedirectUri($ga_redirect_uri);
}

if (!$client->getAccessToken()) {
	die("incorrect access token");
} else {
// 	$secret=$_SESSION['secret'];
// 	$metrics=$_SESSION['metrics'];
// 	$dimensions=$_SESSION['dimensions'];
// 	$date1=$_SESSION['date1'];
// 	$date2=$_SESSION['date2'];

	// Create analytics service object. See next step below.
	$analytics = new Google_Service_Analytics($client);
	
	if (isset($profileId)) {
		// Query the Core Reporting API. (2014-01-20)
		try {
			$results = $analytics->data_ga->get(
					'ga:' . $profileId,
					$date1,
					$date2,
					$metrics,  array(
							'dimensions' => $dimensions,
							// 		'sort' => 'ga:date',
					// 		'filters' => 'ga:medium==organic',
							'max-results' => '10000')); // total maximum
		} catch (Exception $e) {
			die('no data returned from google, please review your selection');
		}
		
	
		// Output the results.
		if (count($results->getRows()) > 0) {
				
			$rows = $results->getRows();
			//prepare csv
			$fname=tempnam("/tmp", "ga_rep");
			
			$fp=fopen("$fname", "w");
			$hdr=$results->getColumnHeaders();
			$hedr=array();
			$i=0;
			$date_loc=-1;
			$schema=array();
			$schemajson="";
			//$schemajson="&fields=";
			foreach ($hdr as $header) {
				$name=(string)$header->name;
				
				foreach ($mdnames->items as $item) {
					if (FALSE!==stripos($name,$item->id)) {
					//	if ("METRIC"==$item->attributes->type) $itype="string"; else $itype="double";
//						$idescr=$item->attributes->description;
						if (FALSE!==stripos($name,"ga:Date")) {$date_loc=$i;$itype="timestamp";}
						$name=$item->attributes->uiName;
	//					$schema[]=array('Name'=>$name, 'TypeName'=>$itype, 'Description'=>$idescr );

						break;
					}
				}
		//		$schemajson=$schemajson.urlencode(json_encode($schema));
				$name=str_replace(":", "_", $name);
				//echo $name;
				$hedr[$i]=$name;
				$i++;				
			}
			fputcsv($fp, $hedr);
			foreach($rows as $row) {
				if ($date_loc>-1) $row[$date_loc]=date("Y-m-d H:i:s",strtotime($row[$date_loc])); //postgres import date format
				fputcsv($fp, $row);
			}
			fclose($fp);

			$zip = new ZipArchive();
			$filename = $fname.".zip";
			if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
				die("cant open <$filename>\n");
			}
			$zip->addFile($fname, "data.csv");
			$zip->close();
			@unlink($fname);
				
			// upload to capsidea
			$host_reply=askhost($server_url.$schemajson, array('file_contents'=>'@'.$filename),"","","",60000,array("appid: $capsidea_appid","sig: $secret"),true);
			$result=$host_reply["data"];
			$httpcode=$host_reply["httpcode"];
			$jsonres=json_decode($result, true);
			$key=$jsonres["Key"];
			$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
			mylog($error_log);
if (200==$httpcode) {
// 				if (isset($_POST["element_5_1"])) {
// 					//save autoupdate
// 					$dbconn = pg_connect($pg_host) //defintd in cbr-inc.php
// 					or die('Could not connect: ' . pg_last_error());
// 					$sdata=base64_encode(serialize(array('m'=>$metrics,'d'=>$dimensions, 't'=>$_SESSION['token'])));
// 					@pg_query("delete from updates where ikey=$key");
// 					@pg_query("insert into updates (ikey, ival, idate, iapp) values ($key, '$sdata', CURRENT_TIMESTAMP, $capsidea_appid);");
// 					$err=$err."<br>delete from updates where ikey=$key<br>insert into updates (ikey, ival, idate, iapp) values ($key, '$sdata', CURRENT_TIMESTAMP, $capsidea_appid);<br>".pg_last_error()."\n";
// 				}
echo $key;
@unlink($filename);
die();
// echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
// <html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
// <script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script><title>Success</title>
// <link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head><body id=\"main_body\" ";
// echo "onload=\"CI.openSource($key)\"";
// echo "><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
// <h1><b>Source $key created</b></h1><br>RES: $err <div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
//unset($_SESSION['secret']);
//session_destroy();
die;}
else {
echo "<br>ERROR $httpcode<br>debug info: $error_log <br>secret:".$cdata["t"];
//unset($_SESSION['secret']);
//session_destroy();

die;} 

		} else { print '<p>No results found.</p>'; 	}
	} // isset($profileId)
} // client->getAccessToken
?>