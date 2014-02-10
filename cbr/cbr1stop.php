<?php 
// cbr autofetch disabler $Author: slavik $
include_once 'cbr-inc.php';
$ikey=$_GET["obj_key"];
// check deleteSchema
if (FASLE===strpos($_GET["typet"], "deleteSchema")) die("unknown method");
// check $capsidea_client_secret 

$dbconn = pg_connect($pg_host) //defintd in cbr-inc.php 
or die('Could not connect: ' . pg_last_error());
@pg_query("delete from updates where iapp=$capsidea_appid and ikey=$ikey;");



?>