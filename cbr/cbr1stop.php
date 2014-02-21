<?php 
// cbr autofetch disabler $Author: slavik $
include_once 'cbr-inc.php';
// check deleteSchema
if (FALSE===strpos($_GET["type"], "deleteSchema")) {
	mylog("delete ERR. no method");
	die("ERR unknown method");}
// @todo check $capsidea_client_secret 
$ikey=(int)$_GET["obj_key"];
if (0==$ikey) {
	mylog("delete ERR. no ikey");
	die("ERR user not found");
}
$m = new MongoClient();
$db = $m->currency;
$clients_collection = $db->cbrclients;
$clients_collection->findAndModify(array("schemakey" => $ikey), array(), null, array("remove" => true ));
mylog("user $ikey deleted");
?>