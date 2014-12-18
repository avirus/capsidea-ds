<?php
require_once 'paypal-inc.php';
$hash=$_GET['key'];
header("Content-type: text/plain");
echo file_get_contents($my_data_dir."/".$hash.".prc");
?>