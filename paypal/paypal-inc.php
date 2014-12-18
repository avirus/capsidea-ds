<?php 
require_once 'csv2arr.php';
$curr=array("USD"=>1,"EUR"=>1.3609, "CAD"=>0.9224, "PLN"=> 0.3258, "DKK"=> 0.1823 , "SEK"=> 0.1532,
		"NOK" => 0.161665,"SGD" => 0.787183,"AUD" => 0.889441,"ILS" => 0.285963,"HUF" => 0.0045405,
		"MXN" => 0.0764597,"PHP" => 0.0223764,"BRL" => 0.418382,"CHF" => 1.10169,"CZK" => 0.049640,	"NZD" => 0.821239, "JPY"=>0.0085, "GBP"=> 1.57, "RUB"=> 0.019, "THB"=> 0.031,
    "HKD" => 0.13
);
$schemajson="&fields=".urlencode('[{Name: "txn_lag", TypeName:"double" },{Name: "fee", TypeName:"double" }, {Name: "amount", TypeName:"double" },
{Name: "start_ts", TypeName:"timestamp" }, {Name: "stop_ts", TypeName:"timestamp" },
{Name: "status", TypeName:"string"},{Name: "tcode", TypeName:"string"},{Name: "currency", TypeName:"string"},{Name: "case_reason", TypeName:"string"},
{Name: "case_status", TypeName:"string"},{Name: "case_money_move", TypeName:"string"},
{Name: "due_date", TypeName:"timestamp" },{Name: "case_type", TypeName:"string"},{Name: "case_lag", TypeName:"double" },{Name: "case_date", TypeName:"timestamp", Description:"Case date" },
{Name: "case_amount", TypeName:"double",Description:"disputed amount" }, {Name: "days_before_case_closure", TypeName:"double", Description:"days befo case closure" },
{Name: "project", TypeName:"string"},{Name: "country", TypeName:"string", TopobaseId:"world"} ]');

$pg_host="dbname=paypal user=capsidea password=33137 connect_timeout=30";
//$tmp_dir="/tmp/paypal/";
$my_data_dir="/tmp/paypalapp";
$wwwrealpath="/var/www/html";
$tmp_dir="/tmp/paypal/"; // hardcoded value
@mkdir($tmp_dir,0777,true);
$base_url="http://beta.capsidea.com/api";
$server_url=$base_url."?s=ImportService&delimeter=,&nullstr=null&reload=1&withheader=1&name=paypal&reloaddim=0&frequency=daily";
$capsidea_appid=1465;
$capsidea_client_secret="put-your-data-here";
$capsidea_permanent_access_token="put-your-data-here";


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


function process_prj_names($input, $fake=false) {
	global $curr;
$cyr  = array('Р°','Р±','РІ','Рі','Рґ','e','Р¶','Р·','Рё','Р№','Рє','Р»','Рј','РЅ','Рѕ','Рї','СЂ','СЃ','С‚','Сѓ',
			'С„','С…','С†','С‡','С€','С‰','СЉ','СЊ', 'СЋ','СЏ','Рђ','Р‘','Р’','Р“','Р”','Р•','Р–','Р—','Р�','Р™','Рљ','Р›','Рњ','Рќ','Рћ','Рџ','Р ','РЎ','Рў','РЈ',
			'Р¤','РҐ','Р¦','Р§','РЁ','Р©','РЄ','Р¬', 'Р®','РЇ', 'СЌ', 'С‹' );	
$lat = array( 'a','b','v','g','d','e','zh','z','i','y','k','l','m','n','o','p','r','s','t','u',
		'f' ,'h' ,'ts' ,'ch','sh' ,'sht' ,'a' ,'y' ,'yu' ,'ya','A','B','V','G','D','E','Zh',
		'Z','I','Y','K','L','M','N','O','P','R','S','T','U',
		'F' ,'H' ,'Ts' ,'Ch','Sh' ,'Sht' ,'A' ,'Y' ,'Yu' ,'Ya','a','yj' );
$fake_goods= array("rubber","mercury","cosmetic dyes","deodorants","vegan food","furniture","bandages","fireworks","jewellery","knives","tobacco","watches","cereal");
//$arr_replaces=//array('USD','EUR');
	$input=str_replace($cyr, $lat, $input); // transliterate
	$input=str_replace(array_keys($curr),"",$input); //strip currency names
	if ($fake) $input=$fake_goods[(fmod(strlen($input), count($fake_goods)))];
	return $input; 
}
function convert_to_cps_date($string)
{
	return date("Y-m-d 0:00:00", strtotime((string)$string));
}

function generate_application_report_to_cvs($client_dir,$dbconn,$source_id,$cases,&$merchant,$ware,$cntryarr) {
	global $dbg;
	$fname=generatehash();
	$report_fname=$client_dir."/".$fname.".csv";
	$fw=fopen($report_fname, "w");
	fputs($fw, "txn_lag,fee,amount,start_ts,stop_ts,status,tcode,currency,case_reason,case_status,case_money_move,due_date,case_type,case_lag,case_date,case_amount,days_before_case_closure,project,country\n");
	
	$qry="select * from txn where mid<>'' and amount>0 and refid='' and ikey=$source_id and stop_ts>(date_trunc('month',CURRENT_TIMESTAMP)- interval '1 month') order by stop_ts;";
	//$qry="select * from txn where mid<>'' and amount>0 and (refid='' or refid is null) and ikey=1591 and (stop_ts>(date_trunc('month',CURRENT_TIMESTAMP)- interval '1 month') or (stop_ts between (date_trunc('month',CURRENT_TIMESTAMP)- interval '13 month') and (CURRENT_TIMESTAMP - interval '12 month'))) order by stop_ts;";
	//$qry="select * from txn where mid<>'' and amount>0 and (refid='' or refid is null) and ikey=1591 order by stop_ts;";
	//$dbg=$dbg."\n executing txn qry $qry... ";
	//$stime=get_timer();
	$pgres=pg_query($qry);
	if (false===$pgres) {
		$qr=pg_errormessage($dbconn);
		log_fatal("generate_application_report_to_cvs:  src $source_id ERR $qr in query: $qry ");
	}
	//$tcount=pg_num_rows($pgres);
	//$dbg=$dbg."($tcount) time ".((get_timer()-$stime)/1)." sec "; // $stime=get_timer();
	$stime=get_timer();
	while($row = pg_fetch_assoc($pgres))
	{ 	// process txn row
	$line=array();
	//if (isset($row["txnlag"])) $line["lag"]=$row["txnlag"]; else $line["lag"]="null"; 
	if (isset($row["txnlag"])) $line["lag"]=$row["txnlag"]; else $line["lag"]="0";
	$line["fee"]=-1*(((int)$row["fee"])/100);
	$line["amount"]=((int)$row["amount"])/100;
	$line["stopts"]=convert_to_cps_date($row["stop_ts"]);
	
	if (isset($row["start_ts"])) $line["startts"]=convert_to_cps_date($row["start_ts"]); else $line["startts"]=$line["stopts"];
	$line["status"]=(string)$row["status"];
	if (isset($row["tcode"])) $line["tcode"]=(string)$row["tcode"]; else $line["tcode"]=" ";
	$line["currency"]=(string)$row["currency"];
	$ppid=$row["ppid"];
	$mid=$row["mid"];
	if (isset($cases[$mid]))
	{
		$row2 = $cases[$mid];
		$line["creason"]=$row2["creason"];
		$line["cstatus"]=$row2["cstatus"];
		$line["cmm"]=$row2["cmm"];
		$line["cdd"]=convert_to_cps_date($row2["cdd"]);
		$line["ctype"]=$row2["ctype"];
		$line["clag"]=$row2["clag"];
		$line["cdate"]=convert_to_cps_date($row2["cdate"]);
		$line["camount"]=$row2["camount"];
		$line["cdays"]=$row2["cdays"];
		//$dbg=$dbg."*";
	} // have case? get last, and write info
	else { // null values
		$line["creason"]="null";
		$line["cstatus"]="null";
		$line["cmm"]=" ";
		$line["cdd"]=convert_to_cps_date("0");
		$line["ctype"]=" ";
		$line["clag"]="0";
		$line["cdate"]=convert_to_cps_date("0");
		$line["camount"]="0";
		$line["cdays"]="0";
	
	}// no case - empty values
	if  (isset($merchant[$mid])) {
		$row2 = $merchant[$mid];
		$line["prj"]=process_prj_names($ware[$row2[1]]);
		if (!isset($cntryarr[$row2[2]])) {
			//$dbg=$dbg."country not found! '".$row2[2]."' \n";
			$line["country"]="unknown country";
		} else {
			$line["country"]=$cntryarr[$row2[2]];
		}
	} else
	{
		$line["prj"]="null";
		$line["country"]="null";
	}
	fputcsv($fw, $line);
	} // process txn's
	fclose($fw);
	return $report_fname;
}

function load_countries_from_csv($file_name)
{
	$tmp=csv_to_array($file_name);
	$cntryarr=array();
	foreach ($tmp as $line) {
		$cntryarr[$line["iso"]]=$line["name_en"];
	}
	unset($tmp);
	return $cntryarr;
}

function load_merchant_data_from_db($source_id,&$ware){
	global $pg_host;
	$merchant=array();
	$wareindex=0;
	$dbconn2 = pg_connect($pg_host) or log_fatal('Could not connect second connection: ' . pg_last_error());
	$pgres2=pg_query($dbconn2,"select * from merchant where mid<>'' and ikey=$source_id and idate>(date_trunc('month',CURRENT_TIMESTAMP)- interval '1 month') order by idate;");
	$mcount=pg_num_rows($pgres2);
	while ($row2 = @pg_fetch_assoc($pgres2)) {
		$sres=array_search($row2["prj"], $ware);
		if (FALSE===$sres) { // new ware
			$wareindex++;
			$ware[$wareindex]=$row2["prj"];
			$sres=$wareindex;
		}
		$mid=$row2["mid"];
		$merchant[$mid]=array(1=>$sres, 2=>$row2["cntry"]);
	}
	pg_free_result($pgres2);
	return $merchant;
}

function load_cases_from_db($source_id)
{
	global $pg_host;
	$cases=array();
	$dbconn2 = pg_connect($pg_host) or log_fatal('Could not connect second connection: ' . pg_last_error());
	$pgres2=pg_query($dbconn2, "select * from cases where ikey=$source_id and cid<>'' order by ifile;");
	$ccount=pg_num_rows($pgres2);
	while ($row2 = @pg_fetch_assoc($pgres2)) {
		//$ppid=$row2["ppid"];
		$mid=$row2["mid"];
		$cases[$mid]=array("creason"=>$row2["creason"], "cstatus"=>$row2["cstatus"], "cmm"=> $row2["cmm"],"ctype"=>$row2["ctype"],
				"clag"=>$row2["clag"],	"cdd"=> $row2["duedate"],	"cdate"=>$row2["filldate"], 	"camount"=>((int)$row2["camount"])/100,  "cdays"=>$row2["havedays"]	);
	}
	pg_free_result($pgres2);
	return $cases;
}

function get_file_list($paypal_sftp_login, $paypal_sftp_password) {
	$ret=array();
	$result=exec("./getpaypal-list-files.sh $paypal_sftp_login"."@reports.paypal.com $paypal_sftp_password 2>&1", $ret);
	return $ret;
}
function get_file_content($paypal_sftp_login, $paypal_sftp_password, $realfname,$client_dir,$fname)
{
	global $tmp_dir;
	if(!file_exists($client_dir.$fname) || !is_readable($client_dir.$fname)) { // file not found locally
	$ret2=array();
	$result=exec("./getpaypal-get-file.sh $paypal_sftp_login"."@reports.paypal.com $paypal_sftp_password $realfname 2>&1", $ret2);
	// @todo check is file received?
	rename($tmp_dir.$fname, $client_dir.$fname);
	//	print_r($ret2);
	prepare_pp_csv($client_dir.$fname);
	} // not loaded from sftp
	$txn=csv_to_array($client_dir.$fname);
	return $txn;
}

function mark_file_as_processed($fname,$merchant_cube_id,$file_type)
{
	$pgres=pg_query("insert into ppfiles (fname, idate, itype, id, ikey) values ('".pg_escape_string($fname)."', CURRENT_TIMESTAMP, $file_type, nextval('sq_files'),$merchant_cube_id);select currval('sq_files');");
	$row2 = pg_fetch_row($pgres);
	$fid=$row2[0];
	pg_free_result($pgres);
	return $fid;
}

function link_merchant_data($merchant_cube_id,$dbconn)
{
	global $dbg;
	$merchant=array();
	$txn=array();
	$dbg=$dbg."linking client data $merchant_cube_id \n"; //die();
	$dbg=$dbg."\nloading merchant data ...";
	$pgres=pg_query($dbconn,"select mid from merchant where mid<>'' and idate is null and ikey=$merchant_cube_id;");
	$mcount=pg_num_rows($pgres);
	while ($row2 = @pg_fetch_assoc($pgres)) {
		$merchant[$row2["mid"]]=0;
	}
	pg_free_result($pgres);
	$dbg=$dbg."($mcount) done";
	$dbg=$dbg."\nloading txn data ...";
	$qry="select mid, stop_ts from txn where ikey=$merchant_cube_id and mid<>'';";
	$res=@pg_query($qry);
	if (false===$res) {
		$qr=pg_errormessage($dbconn);
		log_fatal("ERR error $qr \n count=".count($merchant));
	}
	$tcount=pg_num_rows($res);
	while ($row2 = @pg_fetch_assoc($res)) {
		if (isset($merchant[$row2["mid"]])) $merchant[$row2["mid"]]=$row2["stop_ts"];
	}
	pg_free_result($res);
	$dbg=$dbg."($tcount) done";
	$i=0;
	$lines=0;
	foreach ($merchant as $key=>$value) {
		if (0==$value) continue; // empty data
		$qry="update merchant set idate='".$value."' where ikey=$merchant_cube_id and mid='$key'";
		$res=pg_query($qry);
		if (false===$res) {
			$qr=pg_errormessage($dbconn);
			log_fatal("ERR error in $qry \n$qr");
		}
		$lines++;
		$i++;
		if ($i>10000) {
			@pg_query("commit;");
			//echo "*";
			@pg_query("begin;");
			$i=0;
		}
	}
	@pg_query("commit;");
	unset($txn);
	unset($merchant);
	$mem=floor(memory_get_peak_usage(true)/(1024*1024));
	mystat("link merchant stat $merchant_cube_id lines: $mcount rlines: $lines txn: $tcount  mem: $mem MB\n");
	return $lines;
}

function process_paypal_cases($txn, $dbconn, $merchant_cube_id, $fid) {
	global $dbg;
	global $curr;
	$i=0;
	$cases_count=0;
	@pg_query("begin;");
	foreach ($txn as $this_item) {
		// insert into database
		//"CH","Case type","Case ID","Original transaction ID","Transaction date","Transaction invoice ID","Card type","Case reason","Claimant name","Claimant email address","Case filing date",
		//"Case status","Response due date","Disputed amount","Disputed currency","Disputed transaction ID","Money movement","Settlement type","Seller protection","Seller protection payout amount",
		//"Seller protection currency","Payment tracking ID","Buyer comments","Store ID","Chargeback Reason Code","Outcome"
		$case_type=$this_item["Case type"];
		$case_id=pg_escape_string($this_item["Case ID"]);
		$case_txnid=pg_escape_string($this_item["Original transaction ID"]);
		$case_txndate=date("Y-m-d H:i:sO",strtotime($this_item["Transaction date"]));
		$case_txnmid=$this_item["Transaction invoice ID"];
		$case_reason=pg_escape_string($this_item["Case reason"]);
		$case_filldate=date("Y-m-d H:i:sO",strtotime($this_item["Case filing date"]));
		$case_lag=floor((strtotime($this_item["Case filing date"])-strtotime($this_item["Transaction date"]))/(24*60*60));
		$case_status=pg_escape_string($this_item["Case status"]);
		if (strlen($this_item["Response due date"])>0) {$case_duedays=floor((strtotime($this_item["Response due date"])-time())/(24*60*60)); // in days
		if ($case_duedays<0) $case_duedays=0;
		$case_dd=date("Y-m-d H:i:sO",strtotime($this_item["Response due date"]));
		} else {$case_duedays=0;$case_dd=date("Y-m-d H:i:sO", time());}
		$case_amount=convert_to_usd( $this_item["Disputed currency"],$this_item["Disputed amount"]);
		$case_mmove=pg_escape_string($this_item["Money movement"]);
		//$case_=$this_item["Transaction"];
		//$case_=$this_item["Transaction"];
		$qry="insert into cases (ppid, cid, creason, cstatus, havedays, cmm, camount, mid,  ifile ,ikey, filldate, txndate, ctype , clag, duedate)
		values ('$case_txnid', '$case_id', '$case_reason', '$case_status', $case_duedays,
		'$case_mmove', $case_amount , '$case_txnmid' , $fid, $merchant_cube_id, '$case_filldate', '$case_txndate', '$case_type', $case_lag, '$case_dd' );";
		$res=@pg_query($qry);
		if (false===$res) {
			$qr=pg_errormessage($dbconn);
			log_fatal("ERR error $qr in query: $qry");
		}
		$cases_count++;
		if ($i>10) {
			$dbg=$dbg.".";
			@pg_query("commit;");
			@pg_query("begin;");
			$i=0;
		}
	}
	@pg_query("commit;");
	return $cases_count;
}

function process_paypal_txns($txn,$dbconn,$merchant_cube_id, $fid)
{
	global $dbg;
	global $curr;
	$i=0;
	$txn_count=0;
	@pg_query("begin;");
	foreach ($txn as $this_item) {
		// insert into database
		$i++;
		// 		"CH","Transaction ID","Invoice ID","PayPal Reference ID","PayPal Reference ID Type","Transaction Event Code","Transaction Initiation Date","Transaction Completion Date",
		// 		"Transaction  Debit or Credit","Gross Transaction Amount","Gross Transaction Currency","Fee Debit or Credit","Fee Amount","Fee Currency","Transactional Status",
		// 		"Insurance Amount","Sales Tax Amount","Shipping Amount","Transaction Subject","Transaction Note","Payer's Account ID","Payer Address Status","Item Name","Item ID",
		// 		"Option 1 Name","Option 1 Value","Option 2 Name","Option 2 Value","Auction Site","Auction Buyer ID","Auction Closing Date","Shipping Address Line1",
		// 		"Shipping Address Line2","Shipping Address City","Shipping Address State","Shipping Address Zip","Shipping Address Country","Shipping Method","Custom Field",
		// 		"Billing Address Line1","Billing Address Line2","Billing Address City","Billing Address State","Billing Address Zip","Billing Address Country","ConsumerID","First Name","Last Name",
		// 		"Consumer Business Name","Card Type","Payment Source","Shipping Name","Authorization Review Status","Protection Eligibility","Payment Tracking ID","Store ID","Terminal ID",
		// 		"Coupons","Special Offers","Loyalty Card Number","Checkout Type","Secondary Shipping Address Line1","Secondary Shipping Address Line2","Secondary Shipping Address City",
		// 		"Secondary Shipping Address State","Secondary Shipping Address Country","Secondary Shipping Address Zip","3PL Reference ID"
		$txn_id=$this_item["Transaction ID"];
		$txn_merchantid=$this_item["Invoice ID"];
		$txn_refid=$this_item["PayPal Reference ID"];
		$txn_reftype=$this_item["PayPal Reference ID Type"];
		$txn_tcode=$this_item["Transaction Event Code"];
		$txn_startts=date("Y-m-d H:00:00O",strtotime($this_item["Transaction Initiation Date"]));
		$txn_stopts=date("Y-m-d H:i:sO",strtotime($this_item["Transaction Completion Date"]));
		$txn_lag=floor((strtotime(strtotime($this_item["Transaction Completion Date"])-$this_item["Transaction Initiation Date"]))/(60*60));
	
		$txn_cur=$this_item["Gross Transaction Currency"];
		$txn_direction=$this_item["Transaction  Debit or Credit"];
		$txn_amount=$this_item["Gross Transaction Amount"];
		$txn_fee_direction=$this_item["Fee Debit or Credit"];
		$txn_fee=$this_item["Fee Amount"];
		$txn_status=$this_item["Transactional Status"];
		// skip special cases
		if (FALSE!==strpos($txn_tcode, "T0400")) continue; // paypal withdrawal
		if (FALSE!==strpos($txn_tcode, "T0300")) continue; // paypal funding
		if (FALSE!==strpos($txn_tcode, "T2103")) continue; // paypal reserve hold
		if (FALSE!==strpos($txn_tcode, "T2104")) continue; // paypal reserve release
		if (FALSE!==strpos($txn_tcode, "T0000")) continue; // we purchased something
	
		switch ($txn_status) {
			case "P":   $txn_status="Pending";break;
			case "S":   $txn_status="Success";break;
			case "D":   $txn_status="Denied";break;
			case "V":   $txn_status="Reversed";break;
			case "F":   $txn_status="Partial refund";break;
		}
		$feedir=0;
		$txndir=0;
		if (FALSE!==strpos($txn_fee_direction, "DR")) {$feedir=-1;};
		if (FALSE!==strpos($txn_fee_direction, "CR")) {$feedir=1;};
		if (FALSE!==strpos($txn_direction, "DR")) {$txndir=-1;};
		if (FALSE!==strpos($txn_direction, "CR")) {$txndir=1;}
		if (FALSE!==strpos($txn_refid, "-")) {$txn_refid="";}	// fake parent txn, workaround
		$txnamount=convert_to_usd( $txn_cur, $txn_amount)*$txndir;
		$feeamount=convert_to_usd( $txn_cur, $txn_fee)*$feedir;
		// insert txn
		$qry="insert into txn (ppid, mid, refid, fee, start_ts, stop_ts, amount, status, tcode, currency, ikey, ifile, txnlag )
		values ('".pg_escape_string($txn_id)."', '".pg_escape_string($txn_merchantid)."', '".pg_escape_string($txn_refid)."', $feeamount, '$txn_startts', '$txn_stopts',
			$txnamount, '".pg_escape_string($txn_status)."', '".pg_escape_string($txn_tcode)."', '$txn_cur', $merchant_cube_id, $fid, $txn_lag);";
		$res=@pg_query($qry);
		
		if (false===$res) {
			$qr=pg_errormessage($dbconn);
			log_fatal("ERR error $qr in query: $qry");
		}
		$txn_count++;
		if ($i>1000) {
			$dbg=$dbg.".";
			@pg_query("commit;");
			@pg_query("begin;");
			$i=0;
		}
	} // foreach line
	@pg_query("commit;");
	return $txn_count;
}

function convert_to_usd($txn_cur, $amount)
{
	global $curr;
	if (!isset($curr[$txn_cur])) {
		$result=askhost("http://rate-exchange.appspot.com/currency?from=$txn_cur&to=USD");
		$jsonres=json_decode($result, true);
		if (!isset($jsonres['rate'])) {log_fatal("unknown currency $txn_cur");}
		$curr[$txn_cur]=$jsonres['rate'];
	} // lookup new currency
	return floor($curr[$txn_cur]*$amount);
}

function is_file_already_loaded($fname,$merchant_cube_id)
{
	$pgres=@pg_query("select idate from ppfiles where fname='$fname' and ikey=$merchant_cube_id;");
	if (0!=pg_num_rows($pgres)) return true;
	return false;
}

function prepare_pp_csv($fname) {
global $tmp_dir;	
	if(!file_exists($fname) || !is_readable($fname))
	{echo "file $fname not found"; return FALSE;}
	$fw=fopen($tmp_dir."tmp.csv", 'w');
	if (($handle = fopen($fname, 'r')) !== FALSE)	{
		while (($row = fgetcsv($handle, 0, ',', '"')) !== FALSE)
		{
			if (FALSE!==strstr($row[0],"SB")) fputcsv($fw, $row);
			if (FALSE!==strstr($row[0],"CH")) fputcsv($fw, $row);
		}
		fclose($handle);
		fclose($fw);
		unlink("$fname");
		rename($tmp_dir."tmp.csv", $fname);
		return true;
	}
	return false;
}

function myexecute($cmd,$stdin=null,$timeout=20000){
	$proc=proc_open($cmd,array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w')),$pipes);
	$write  = array($pipes[0]);
	$read   = array($pipes[1], $pipes[2]);
	$except = null;
	$stdout = '';
	$stderr = '';
	while($r = stream_select($read, $write, $except, null, $timeout)){
		foreach($read as $stream){

			// handle STDOUT
			if($stream===$pipes[1])
			/*...*/         $stdout.=stream_get_contents($stream);

			// handle STDERR
			if($stream===$pipes[2])
			/*...*/         $stderr.=stream_get_contents($stream);
		}

		// Handle STDIN (???)
		fwrite($pipes[0], $stdin);

		// the following code is temporary
		$n=isset($n) ? $n+1 : 0; if($n>10)break; // break while loop after 10 iterations
		sleep(5);

	}
	return array("o"=> $stdout, "e"=>$stderr);
}

function get_capsidea_data($capsidea_client_secret)
{
    $ret=array();
    $parsed_url=parse_url($_SERVER['HTTP_REFERER']);
    $var  = explode('&', $parsed_url['query']);
    foreach($var as $val)
    {
	$x          = explode('=', $val);
	$arr[$x[0]] = $x[1];
    }
    unset($val, $x, $var, $qry, $parsed_url, $ref);
    if (isset($arr["token"])) {$token=$arr["token"];} else {die ("cant find capsidea.com token, please contact application support");}
    if (36!=strlen($token)) {die("capsidea.com token incorrect, please contact application support");}
    $ret["c"]=$str=preg_replace('/[^A-Za-z0-9\-]/', '', $token);
    $ret["t"]=sha1($capsidea_client_secret.$token);
    if (isset($arr["schemakey"])) $ret["k"]=(int)$arr["schemakey"];
    return $ret;
}


function generatehash($length = 20) {
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, strlen($characters) - 1)];
	}
	return $randomString;
}

function generateuniqid(&$used, $length = 20) {
	// $used array contains used elements
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString = generatehash($length);
	if (!isset($used[$randomString])) {
		$used[$randomString]=1;
		return $randomString;
	} else {
		$randomString=generateuniqid($used);
		$used[$randomString]=1;
		return $randomString;
	};
}

function check_credentials($key, $hash, $dbconn)
{
	$key=(int)$key;
	$hash=pg_escape_string(preg_replace('/[^A-Za-z0-9]/', '', $hash));
	$result=@pg_query("select count(*) from client where ikey=$key and ihash='$hash'");
	$row=@pg_fetch_row($result);
	if ($row[0]>0) {return true;} else {return false;}
}

function get_timer()
{
	$mtime = microtime ();
	$mtime = explode ( " ", $mtime );
	$mtime = $mtime [1] + $mtime [0];
	return  $mtime;
}
function  mylog($line) {
	global $my_data_dir;
	global $logfile;
	if (!isset($logfile))  $logfile="$my_data_dir/paypal.log";
	file_put_contents($logfile, date(DATE_ATOM)." $line\n", FILE_APPEND);
}
function  mystat($line) {
	global $my_data_dir;
	file_put_contents("$my_data_dir/paypal-statistic.log", date(DATE_ATOM)." $line\n", FILE_APPEND);
}
function log_fatal($msg)
{
	mylog($msg);
	die($msg);
}

?>