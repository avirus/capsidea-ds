<?php 
include_once 'bls1inc.php';
file_put_contents("/tmp/bls-callback.log", date(DATE_ATOM)." hit ".print_r($_REQUEST,true)." \n", FILE_APPEND);
if (FALSE!==strpos($_GET["type"], "deleteSchema")) { die("OK");};
if (FALSE!==strpos($_GET["type"], "updateschema")) { // do it 
	$key=(int)$_GET["obj_key"];
	include_once 'askhost.php';
	
	$dt=get_datatype1();
	$industry=get_datatype2();
	$fname=tempnam("/tmp", "bls1");
	$csvname=get_data_from_mongo($dt, $industry, $fname);
	zip_compress_csv($fname."zip",$csvname);
	@unlink($fname);
	
	$secret=sha1($capsidea_client_secret.$capsidea_permanent_access_token);

	$array1=create_list_of_measures($dt);
	$measures="";
	foreach ($array1 as $val) {
		$measures=$measures."{Name: \"$val\", TypeName:\"double\" },";
	}
	
	$schemajson="&fields=".urlencode('[{Name: "date", TypeName:"timestamp"},'.$measures.'{Name: "industry", TypeName:"string"}]');
	
	$host_reply=askhost($server_url."&schemakey=".$key.$schemajson, array('file_contents'=>'@'.$fname.".zip"),"","","",800000,array("appid: $capsidea_appid","sig: $secret"),true);// defined in askhost.php
	
	
	
	//echo "done\n";
	$httpcode=$host_reply["httpcode"];
//	$jsonres=json_decode($host_reply["data"], true);
//	$key=(int)$jsonres["Key"];
//	if (0==$key) $key=$cdata["k"];
	$error_log="response:<pre>".$host_reply["data"]."</pre>"."<br>connection debug:<pre>".$host_reply["d"]."</pre>";
	if (200==$httpcode) {
		unlink($fname.".zip");
	} else {
		file_put_contents("/tmp/bls-errors.log", date(DATE_ATOM).print_r($_REQUEST,true)."\n".print_r($host_reply,true)." \n", FILE_APPEND);
	}
	
	
	
	
	die("OK");
};

?>