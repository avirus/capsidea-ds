<?php 
// twitter data loader for capsidea
include_once 'twitter-include.php';
$m = new MongoClient();
$db = $m->twitter;
$tweets_collection = $db->tweets;


$dbconn = pg_connect($pg_host) or log_fatal('Could not connect to application database, please contact support.');

$seconds=60;
$twitterApi=init_twapi();
 
while (true) {
	
$res_first=pg_query("select * from tags where startts<CURRENT_TIMESTAMP and stopts>CURRENT_TIMESTAMP;");	
while ($row_first = @pg_fetch_assoc($res_first)) {
	$tag=$row_first["tag"];
	$tagid=$row_first["id"];
	$lastid=$row_first["lastid"];
	$recordedid=$lastid;

$lastid=process_tag($tag, $twitterApi, $lastid,$tagid, $tweets_collection);

if ($recordedid<$lastid) pg_query("update tags set lastid=$lastid where id=$tagid"); // write lastid if it advance	
} // this tag
echo "!";
sleep($seconds);

pg_free_result($res_first);
} // main loop

?>