<?php 
require_once 'twitter-include.php';
error_reporting(E_ERROR);
$m = new MongoClient();
$db = $m->twitter;
$tweets_collection = $db->tweets;

$cdata=get_capsidea_data($capsidea_client_secret);
$secret=$cdata["t"];
$tag=$_POST['element_1'];

$twitterApi=init_twapi();
$dbconn = pg_connect($pg_host) or log_fatal('Could not connect to application database, please contact support.');
$host_reply=askhost($server_url."&name=".preg_replace('/[^A-Za-z0-9]/', '',$tag), array('file_contents'=>'@'."twitter.csv"),"","","",60000,array("appid: $capsidea_appid","sig: $secret"),true);
$result=$host_reply["data"];
$httpcode=$host_reply["httpcode"];
$jsonres=json_decode($result, true);
$key=$jsonres["Key"];
$error_log="secret: ".$cdata["c"]."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (200!=$httpcode) { 	echo "<br>ERROR $httpcode<br>debug info: $error_log <br>secret:".$cdata["t"]; }


$ts1= strtotime( $_POST["element_2_3"]."-".sprintf("%02d",$_POST["element_2_2"])."-".sprintf("%02d",$_POST["element_2_1"]));
$ts2= strtotime( $_POST["element_3_3"]."-".sprintf("%02d",$_POST["element_3_2"])."-".sprintf("%02d",$_POST["element_3_1"]));

$row=pg_fetch_row(pg_query("select count(id) from tags where tag='".$tag."'"));
$tagcount=(int)$row[0];

if (0==$tagcount) {

$ts1=date("Y-m-d H:i:s",$ts1);
$ts2=date("Y-m-d H:i:s",$ts2);	
$row=pg_fetch_row(pg_query("insert into tags (id, tag, lastid, startts, stopts) values (nextval('sq_tag'), '".$tag."',0, '".$ts1."', '".$ts2."') returning id"));
$tagid=(int)$row[0];
// get real data
$lastid=process_tag($tag, $twitterApi, 0, $tagid,$tweets_collection);
} else 
{
	$row=pg_fetch_assoc(pg_query("select id, startts, stopts,lastid from tags where tag='".$tag."'"));
	$tagid=(int)$row['id'];
	$tag_ts1=(int)$row['startts'];
	$tag_ts2=(int)$row['stopts'];
	$currid=(int)$row['lastid'];
	
	if ($ts1<$tag_ts1) pg_query("update tags set startts='".date("Y-m-d H:i:s",$ts1)."' where id= $tagid");
	if ($ts2>$tag_ts2) pg_query("update tags set stopts='".date("Y-m-d H:i:s",$ts2)."' where id= $tagid");

	// get real data
	$lastid=process_tag($tag, $twitterApi, $currid,$tagid,$tweets_collection);
	if ($currid<$lastid) pg_query("update tags set lastid=$lastid where id=$tagid"); // write lastid if it advance
	
}
//getUserInfo
$url=$base_url."?s=CommonService&m=getUserInfo";
$post_data="[]";
$res=askhost($url,$post_data,"","","",180000,array("appid: $capsidea_appid","sig: $secret","Content-Type: application/json;charset=UTF-8"),true);
//mylog($res['d']."| ".$post_data." json: ".$res['data']);
$js=json_decode($res['data']);
echo "<br>".$res['data']."<br>".$res['d']."<br>";
$username=$js->Login;

pg_query("insert into scheme (id, username, t_id) values ($key, '".$username."', $tagid)");


$report_fname=prepare_report($key);
$host_reply=askhost($server_url."&reloaddim=1&schemakey=".$key.$schemajson, array('file_contents'=>'@'.$report_fname),"","","",1800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
//echo " time ".$time3=((get_timer()-$stime)/1)." sec "; // $stime=get_timer();
$result=$host_reply["data"];
$error_log="cube: $key"."secret: ".$secret."<br>response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
if (500==$host_reply["httpcode"]) {
	echo "ERR: $error_log\n".$host_reply["httpcode"];
	log_fatal("error 500 from cps: \n $error_log");
} // if 500
	if (401==$host_reply["httpcode"]) {
			echo "ERR: unauthorized $error_log\n".$host_reply["httpcode"];
					log_fatal("error 401 from cps \n $error_log");
			} // if 500
			if (200==$host_reply["httpcode"]) {
			echo "unlinking ".$report_fname."\n";
					unlink($report_fname);
					echo  "OK (http == 200)\n";
			} else {echo  "UNK: $error_log\n".$host_reply["httpcode"];}

			
//die();
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\"><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<script type=\"text/javascript\" src=\"http://beta.capsidea.com/api.js\"></script><title>Success</title>
<link rel=\"stylesheet\" type=\"text/css\" href=\"view.css\" media=\"all\"></head><body id=\"main_body\" ";
echo "onload=\"CI.openSource($key)\"";
echo "><img id=\"top\" src=\"top.png\" alt=\"\"><div id=\"form_container\">
<h1><b>Source $key created</b></h1><br>RES: $error_log <div id=\"footer\"></div></div><img id=\"bottom\" src=\"bottom.png\" alt=\"\"></body></html>";
	//unset($_SESSION['secret']);
	//session_destroy();
	die;

	//unset($_SESSION['secret']);
	//session_destroy();

die;


?>