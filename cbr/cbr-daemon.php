<?php 
// cbr autofetcher $Author: slavik $ 

include_once 'cbr-inc.php';
$secret=sha1($capsidea_client_secret.$capsidea_permanent_access_token);
//:TODO make autoupdate https://docs.google.com/document/d/1NifHVKaVaikNhs_UR-5Fhi91vA46UHYsMIci5FE-UYE/pub
$dbconn = pg_connect($pg_host) //defintd in cbr-inc.php
or die('Could not connect: ' . pg_last_error());
$res=pg_query("select ikey, ival, idate from updates;");
while ($row = pg_fetch_row($res)) {
	$selected=array();
	echo "id: $row[0]  data: $row[1] time: $row[2]\n"; // debug
	$selected=unserialize(base64_decode($row[1]));
	$date2=date("d/m/Y");
	$date1=date("d/m/Y", strtotime($row[2]) );
	foreach ($selected as $key => $value) {
		$data=askhost("http://www.cbr.ru/scripts/XML_dynamic.asp?date_req1=$date1&date_req2=$date2&VAL_NM_RQ=$value");
		echo $data."\n"; // debug
		$xml = simplexml_load_string($data);
		foreach($xml->Record as $item) {
			$kdate=(string)$item['Date'];
			$kval=(string)$item->Value;
			$kq=(string)$item->Nominal;
			$kval=floatval(str_replace(",", ".", $kval))/$kq;
			//echo "$kdate: $kval\n"; // debug
			$kurs[$kdate][$value]=array('price'=>$kval);
		} // foreach record
	} // foreach curr
	$fname=create_csv_file($selected, $kurs,$currency);
	$host_reply=askhost($server_url, array('extra_info' => '123456','file_contents'=>'@'.$fname),"","","",8000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in cbr-inc.php
	$result=$host_reply["data"];
	if (500==$host_reply["httpcode"]) {
		echo "ERR: $result\n".$host_reply["httpcode"];
		die;
	} // if 500
	echo "OK: $result\n".$host_reply["httpcode"];
	$dbconn2 = pg_connect($pg_host); //defintd in cbr-inc.php
	pg_query("update updates set idate=CURRENT_TIMESTAMP where ikey=".$row[0]);
	
} // while more rows


?>