<?php 
// accept merchant data
//app.capsidea.com/paypal1puttxncsv.php?key=your_key&hash=your_hash 
//error_reporting(0);
//ini_set('display_errors', 0);

include_once 'csv2arr.php';
include_once 'paypal-inc.php';
$my_data_dir=$my_data_dir."/mtxn";
@mkdir($my_data_dir,0777,true);
file_put_contents("$my_data_dir/paypal-m.log", date(DATE_ATOM)." ".print_r($_REQUEST,true)."  \n".print_r($_FILES,true)."\n", FILE_APPEND);
$stime=get_timer();
$dbconn = pg_connect($pg_host) or log_fatal('Could not connect: ' . pg_last_error());
$key=(int)$_GET["key"];
if (!check_credentials($_GET["key"], $_GET["hash"], $dbconn)) {
	log_fatal("ERR hash incorrect for key=$key, your hash: ".$_GET["hash"]);
}
if (isset($_GET["truncate"])) {
	@pg_query("delete from txn where ikey=$key and ifile=0;");
	@pg_query("delete from merchant where ikey=$key and src=2;");
	@pg_query("delete from cases where ikey=$key and ifile=0;");
	@pg_query("commit;");
	log_fatal("all customer txn records deleted");
}
if( strtolower( $_SERVER[ 'REQUEST_METHOD' ] ) != 'post' || empty( $_FILES )) {log_fatal("ERR no file attached");}; // это тут, чтобы работал truncate
//$startdate=date("Y-m-d H:00:00O",strtotime($_GET["startdate"]));
//$enddate=date("Y-m-d H:00:00O",strtotime($_GET["enddate"]));

foreach ($_FILES as $this_item) {
$fname=$this_item["tmp_name"];
$rname=$this_item["name"];
$fsize=$this_item["size"];
if ($fsize > 20000000) log_fatal("ERR too large file, please use bz2 to compress it");
break;
}
$nfname=$my_data_dir."/pptxn_{$key}_".time().".csv";
// unpack file
file_put_contents("$my_data_dir/paypal-m.log", date(DATE_ATOM)." $rname $nfname $fsize \n", FILE_APPEND);
if (FALSE!==stripos($rname, ".bz2")) {
	$data=bzdecompress(file_get_contents($fname));
	file_put_contents($nfname, $data);
	unset($data);
	unlink($fname);
}
else  {
move_uploaded_file($fname, $nfname); 
}
// process file
$csv = csv_to_array($nfname);
// if (0==array_count_values($csv)) {
// 	file_put_contents("$my_data_dir/error.log", date(DATE_ATOM)."  client $key ERR empty arr, rname: $rname file: $nfname size: $fsize\n", FILE_APPEND);
// 	die("ERR unable to load data from incorrect file $rname ($fsize)");
// }
@pg_query("commit;");
@pg_query("begin;");
$lines_processed=0;
$i=0;
// if (!isset($this_item["mid"])) {die ("no mid column: ".array_keys($this_item)."values".implode(",",$this_item));};
//	if (!isset($this_item["prj"])) {die ("no prj column: ".array_keys($this_item)."values".implode(",",$this_item));};
//	if (!isset($this_item["country"])) {die ("no country column: ".array_keys($this_item)."values".implode(",",$this_item));};
foreach ($csv as $this_item) {
	$i++;
	if (!isset($this_item["mid"])) {die ("no mid column: ".array_keys($this_item)."values".implode(",",$this_item));};
	if ($this_item["amount"]>1000000) continue; //fake entry 
	$txn_date=strtotime($this_item["txn_date"]);
	$case_date=strtotime($this_item["case_date"]);
	$txn_startts=date("Y-m-d H:i:sO",$txn_date);
	$case_filldate=date("Y-m-d H:i:sO",$case_date);
	$case_lag=floor(($case_date-$txn_date)/(24*60*60));
	if (FALSE===stripos($this_item["country"], "NULL")) {$country="'".pg_escape_string($this_item["country"])."'";} else {$country="null";}
	
	//(ppid, mid, refid, fee, start_ts, stop_ts, amount, status, tcode, currency, ikey, ifile, txnlag )
	$qry="insert into txn (ikey, mid, ifile, start_ts, currency, fee, amount) values ($key,'".pg_escape_string($this_item["mid"])."', 0, '$txn_startts' ,'".pg_escape_string($this_item["currency"])."',".pg_escape_string($this_item["fee"]).",".pg_escape_string($this_item["amount"])." );"; 
	$res=@pg_query($qry);
	
	if (false===$res) {
		$qr=pg_errormessage($dbconn);
		$line=implode(",", $this_item);
		file_put_contents("$my_data_dir/loader.log", date(DATE_ATOM)." (line: $line) client $key ERR $qr in query: $qry file: $nfname\n", FILE_APPEND);
		log_fatal("ERR error in ".implode(",",array_keys($this_item))."values".implode(",",$this_item));
	}
 	$qry="insert into merchant (ikey, mid, prj, cntry, idate , src) values ($key,'".pg_escape_string($this_item["mid"])."', '".pg_escape_string($this_item["prj"])."', $country,null,2);";
 	$res=@pg_query($qry);
 	if (false===$res) {
 		$qr=pg_errormessage($dbconn);
		$line=implode(",", $this_item);
 		file_put_contents("$my_data_dir/loader.log", date(DATE_ATOM)." (line: $line) client $key ERR $qr in query: $qry file: $nfname\n", FILE_APPEND);
 		log_fatal("ERR error in ".implode(",",array_keys($this_item))."values".implode(",",$this_item));
 	}
	if (false===stripos($this_item["case_type"], "NULL"))  { // @todo probably bug there
		file_put_contents("$my_data_dir/paypal-m.log", serialize($this_item,true)." \n", FILE_APPEND);
	$qry="insert into cases (cid, creason, cstatus, cmm, camount, mid,  ifile ,ikey, filldate, ctype , clag) 
			values ('".pg_escape_string($this_item["mid"])."','".pg_escape_string($this_item["creason"])."','".pg_escape_string($this_item["cstatus"])."',
					'".pg_escape_string($this_item["moneymove"])."',".pg_escape_string($this_item["camount"]).",".pg_escape_string($this_item["mid"]).",
	0, $key, '$case_filldate', '".pg_escape_string($this_item["case_type"])."',$case_lag)";

	$res=@pg_query($qry);
	if (false===$res) {
		$qr=pg_errormessage($dbconn);
		$line=implode(",", $this_item);
		file_put_contents("$my_data_dir/loader.log", date(DATE_ATOM)." (line: $line) client $key ERR $qr in query: $qry file: $nfname\n", FILE_APPEND);
		log_fatal("ERR error in ".implode(",",array_keys($this_item))."values".implode(",",$this_item));
	}
	} // case?
 	if ($i>10000) {
// 		@pg_query("commit;");
 		echo ".";
 		ob_flush();
 		flush();
 		ob_flush();
 		flush();
 		//@pg_query("begin;");
		$lines_processed=$lines_processed+$i;
 		$i=0;
 	}
} 
@pg_query("commit;");
$lines_processed=$lines_processed+$i;
echo "\nOK $lines_processed records accepted";
@pg_query("update client set todo=1 where ikey=$key;"); // flag client as updated
@pg_query("update client set todo=1 where iparent=$key;");
$time=(get_timer()-$stime); //total timer
$mem=floor(memory_get_peak_usage(true)/(1024*1024));
file_put_contents("$my_data_dir/txn-stat.log", date(DATE_ATOM)." client $key lines: $lines_processed time: $time sec mem: $mem MB\n", FILE_APPEND);

?>