<?php 
function get_capsidea_data($capsidea_client_secret)
{
	$ret=array();
	if (isset($_GET["token"])) {$token=$_GET["token"];} else {die ("cant find capsidea.com token, please contact application support");}
	if (36!=strlen($token)) {die("capsidea.com token incorrect, please contact application support");}
	$ret["c"]=$str=preg_replace('/[^A-Za-z0-9\-]/', '', $token);
	$ret["t"]=sha1($capsidea_client_secret.$token);
	if (isset($_GET["schemakey"])) $ret["k"]=(int)$_GET["schemakey"];
	return $ret;
}

$base_url="http://beta.capsidea.com/api";
$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=&reload=1&withheader=1&name=bls1ces&reloaddim=1&frequency=daily";
$capsidea_appid=2254;
$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";


function get_datatype1()
{
	$fname="ce.datatype";
	$filename="/tmp/".$fname;
	if (!file_exists($filename)) {
		echo "fetching datatypes, ";
		system("cd /tmp;wget http://download.bls.gov/pub/time.series/ce/$fname");
		echo "done\n";
	}
	$fp=fopen($filename, "r");
	$tmp=fgetcsv($fp,null,"\t");
	$dt=array();
	while ($tmp=fgetcsv($fp,null,"\t")) {
		$dt[$tmp[0]]=$tmp[1];
	}
	return $dt;
}

function get_datatype2()
{
$fname="ce.industry";
$filename="/tmp/".$fname;
if (!file_exists($filename)) {
	echo "fetching datatypes, ";
	system("cd /tmp;wget http://download.bls.gov/pub/time.series/ce/$fname");
	echo "done\n";
}
$fp=fopen($filename, "r");
$tmp=fgetcsv($fp,null,"\t");
$industry=array();
while ($tmp=fgetcsv($fp,null,"\t")) {
	$industry[$tmp[0]]=$tmp[3];
}
return $industry;
}

function create_list_of_measures($array1)
{
	foreach ($array1 as $key => &$value) {
		$value=substr(preg_replace('/[^A-Za-z0-9\- ]/', '', $key.$value),0,55);
	}
	return $array1;
}

function get_data_from_mongo($dt, $industry, $fname)
{
	// connect
	$m = new MongoClient();
	// select a database
	$db = $m->labour1;
	// select a collection (analogous to a relational database's table)
	$collection = $db->ee;
	
//	echo "writing data";
	// prepare header
	$fw=fopen($fname.".csv", "w");
	$array1=create_list_of_measures($dt);

	array_push($array1, "industry");
	array_unshift($array1, "date");
	fputcsv($fw, $array1);
	// export data
	// find everything in the collection
	$cursor = $collection->find();
	// iterate through the results
	foreach ($cursor as $document) {
		$this_line=array();
		$c=0;
		$this_line[]=$document["idate"];
		foreach ($dt as $dt_key=>$dt_val) {
			if (null!=$document[$dt_key]) $c++;
			$this_line[]=$document[$dt_key];
		}
		$this_line[]=$industry[$document["industry"]];
		if (0!=$c) fputcsv($fw, $this_line);
		//echo $document["title"] . "\n";
	}
	fclose($fw);
	return $fname.".csv";
}

function zip_compress_csv($zipname, $csvname)
{
	$zip = new ZipArchive();
	//$filename = $fname.".zip";
	if ($zip->open($zipname, ZIPARCHIVE::CREATE)!==TRUE) {
		die("cant open <$zipname>\n");
	}
	$zip->addFile($csvname, "data.csv");
	$zip->close();
	@unlink($csvname);
}

?>