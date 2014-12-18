<?php
// send result to client
require_once 'paypal-inc.php';
$hash=$_GET['key'];
header("Content-Type: text/html; charset=UTF-8");
echo file_get_contents($my_data_dir."/".$hash.".resp");
unlink($my_data_dir."/".$hash.".resp");
unlink($my_data_dir."/".$hash.".prc");
?>