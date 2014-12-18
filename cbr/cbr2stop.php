<?php 
// cbr autofetch disabler $Author: slavik $
// https://capsidea.atlassian.net/wiki/display/DOC/Import+API
// https://capsidea.atlassian.net/wiki/display/DOC/Notifications
include_once 'cbr-inc.php';
// check deleteSchema
if (FALSE===strpos($_GET["type"], "deleteSchema")) {
	mylog("cbr2, delete ERR. no method");
	die("ERR cbr2, unknown method");}
// @todo check $capsidea_client_secret 
$ikey=(int)$_GET["obj_key"];
if (0==$ikey) {
	mylog("delete ERR. no ikey");
	die("ERR user not found");
}
$m = new MongoClient();
$db = $m->currency;
$clients_collection = $db->cbr2clients;
$clients_collection->findAndModify(array("schemakey" => $ikey), array(), null, array("remove" => true ));
mylog("cbr2, user $ikey deleted");
?>