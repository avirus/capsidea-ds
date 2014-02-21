<?php
include_once 'askhost.php';
include_once 'cbr-inc.php';
echo "init ";
$m = new MongoClient();
$db = $m->currency;
//clear old data, but preserve existing subscriptions
$db->dropCollection("cbrlastrun");
$db->dropCollection("cbr");
//select time table
$collection = $db->cbrlastrun;
$document=array("ts"=>time());
$collection->insert($document);
// select table with data
$collection = $db->cbr;
//get all stuff from 01/06/1992 till now
$date1="01/01/1990";
$kurs=download_data_from_cbr($date1, $currency);
// insert data to mongodb
echo " save";
save_array_to_mongo($kurs, $currency, $collection);
echo " done\n";
?>