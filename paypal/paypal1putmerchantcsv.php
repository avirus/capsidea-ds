<?php 
// accept merchant data
//app.capsidea.com/paypal1putmerchantcsv.php?startdate=1/1/2014&enddate=21/1/2014&key=123&hash=123
include_once 'csv2arr.php';
include_once 'paypal-inc.php';
error_reporting(0);
ini_set('display_errors', 0);
$my_data_dir=$my_data_dir."/merchant";
@mkdir($my_data_dir,0777,true);
file_put_contents("$my_data_dir/paypal-m.log", date(DATE_ATOM)." ".print_r($_REQUEST,true)."  \n", FILE_APPEND);
$stime=get_timer();
$dbconn = pg_connect($pg_host) or die('Could not connect: ' . pg_last_error());
$key=(int)$_GET["key"];
//$_FILES["file"]["tmp_name"]
if (!check_credentials($_GET["key"], $_GET["hash"], $dbconn)) {log_fatal("ERR hash incorrect for key=$key, your hash: ".$_GET["hash"]);}
//if ((123!=$key)||(123!=$_GET["hash"])) die("ERR hash incorrect for key=$key, your hash: ".$_GET["hash"]);
if (isset($_GET["truncate"])) { // truncate all
	@pg_query("delete from merchant where ikey=$key;");
	@pg_query("commit;");
	log_fatal("all customer merchant records deleted");
}
if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) != 'post' || empty( $_FILES )) {
//	file_put_contents($my_data_dir."/paypal-m.log", date(DATE_ATOM)."merchant $key no file attached\n", FILE_APPEND);
	log_fatal("ERR no file attached");
}; // это тут, чтобы работал truncate
//$startdate=date("Y-m-d H:00:00O",strtotime($_GET["startdate"]));
//$enddate=date("Y-m-d H:00:00O",strtotime($_GET["enddate"]));
$d="";
foreach ($_FILES as $this_item) {
$fname=$this_item["tmp_name"];
$rname=$this_item["name"];
$fsize=$this_item["size"];
if ($fsize > 20000000) { // too big file
	file_put_contents($my_data_dir."/paypal-m.log", date(DATE_ATOM)."merchant too big file $key $rname $fsize\n", FILE_APPEND);
	die("ERR too large file, please use bz2 to compress it");
}
break;
}
$nfname="$my_data_dir/ppmd_{$key}_".time().".csv";
if (FALSE!==strpos($rname, ".bz2")) {
    $d="decompressed";
	$data=bzdecompress(file_get_contents($fname));
	file_put_contents($nfname, $data);
	unset($data);
	unlink($fname);
}
// process file
else  {
	move_uploaded_file($fname, $nfname);
}
$csv = csv_to_array($nfname);
//echo array_keys($csv);
//echo file_get_contents($fname)."\n";

if (0==array_count_values($csv)) {
	file_put_contents("$my_data_dir/paypal-m.log", date(DATE_ATOM)."  client $key ERR empty arr, rname: $rname $d file: $nfname size: $fsize\n", FILE_APPEND);
	die("ERR unable to load data from empty file $rname ($fsize)");
} 

//@pg_query("delete from merchant where ikey=$key and idate=>'$startdate' and idate<=$enddate");
@pg_query("commit;");
@pg_query("begin;");
$i=0;
$lines=0;
foreach ($csv as $this_item) {
	//	if (!isset($this_item["mid"])) {die ("no mid column: ".array_keys($this_item)."values".implode(",",$this_item));};
	//	if (!isset($this_item["prj"])) {die ("no prj column: ".array_keys($this_item)."values".implode(",",$this_item));};
	//	if (!isset($this_item["country"])) {die ("no country column: ".array_keys($this_item)."values".implode(",",$this_item));};
	$i++;
	$lines++;
	$qry="insert into merchant (ikey, mid, prj, cntry, idate, src ) values ($key,'".pg_escape_string($this_item["mid"])."', '".pg_escape_string($this_item["prj"])."', '".pg_escape_string($this_item["country"])."',null, 1);";
	$res=@pg_query($qry);
	if (false===$res) {
		$qr=pg_errormessage($dbconn);
		$line=implode(",", $this_item);
		file_put_contents("$my_data_dir/paypal-m.log", date(DATE_ATOM)." (line: $line) client $key ERR $qr in query: $qry file: $nfname\n", FILE_APPEND);
		die("ERR error in $line");
		}
// 	if ($i>10000) {
// 		@pg_query("commit;");
// 		echo ".";
// 		@pg_query("begin;");
// 		$i=0;
// 	}
} 
@pg_query("commit;");
echo "\nOK data accepted $rname";
@pg_query("update client set todo=1 where ikey=$key;"); // flag client as updated
@pg_query("update client set todo=1 where iparent=$key;"); // flag childs as updated
$time=(get_timer()-$stime); //total timer
$mem=floor(memory_get_peak_usage(true)/(1024*1024));
file_put_contents("$my_data_dir/paypal-stat.log", date(DATE_ATOM)."MERCHANT: $key lines: $lines time: $time sec mem: $mem MB $nfname\n", FILE_APPEND);
?>