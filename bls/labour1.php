<?php
// bls data loader for capsidea
//http://www.bls.gov/help/hlpforma.htm
//BLS part 1, National Employment, Hours, and Earnings 
include_once 'askhost.php';
include_once 'bls1inc.php';
function get_timer()
{
	$mtime = microtime ();
	$mtime = explode ( " ", $mtime );
	$mtime = $mtime [1] + $mtime [0];
	return  $mtime;
}
//'Content-Type: application/json'
// {"seriesid":["LEU0254555900", "APU0000701111"], "startyear":"2002", "endyear":"2012"}
// http://api.bls.gov/publicAPI/v1/timeseries/data/
// datasets  http://www.bls.gov/help/hlpforma.htm#CE
// ce codes ftp://ftp.bls.gov/pub/time.series/ce/ce.industry
// datatypes ftp://ftp.bls.gov/pub/time.series/ce/ce.datatype

$dt=get_datatype1();
$industry=get_datatype2();
// connect
$m = new MongoClient();
// select a database
$db = $m->labour1;
// select a collection (analogous to a relational database's table)
$collection = $db->ee;

$fields=array();
//print_r($industry);
//$data="";
$keys_dt=$dt;
$keys_ind=$industry;
$need_recover=0;
//$fields=array("date","value")
$url="http://api.bls.gov/publicAPI/v1/timeseries/data/";

// if (file_exists("/tmp/save-array.tmp")) {
// 	$fields=unserialize(gzuncompress(file_get_contents("/tmp/save-array.tmp")));
// // find last complete data
// 	foreach ($industry as $series =>$value) {
// 		if (isset ($fields["2013-12-01"][$value]["date"])) {
// 			$last_ind1=$value;
// 			continue;
// 		} else break;
// 	}	
// 	$last_ind=unserialize(file_get_contents("/tmp/save-industry.tmp"));
// 	//if (FALSE===strpos($last_ind,$last_ind1)) {echo "db contains errors, factically $last_ind1 : $last_ind, \nusing $last_ind1"; $last_ind=$last_ind1; };
// 	echo "recovering to position $last_ind\n";
// 	$need_recover=1;
// }
//file_put_contents("/tmp/save-array.tmp", serialize($fields));
//file_put_contents("/tmp/save-industry.tmp", serialize($value));
$save_counter=0;
$counter=1;
foreach ($industry as $series =>$value) {
	//break;
	if ((1==$need_recover)&&($last_ind==$value)) {$need_recover=0;}; // position recovered, proceed
	if (1==$need_recover) continue;
	//if (("41424800"!=$series)&&("20237110"!=$ series)&&("10212100"!=$series)) continue;
	echo "\n[$series] $value ($save_counter)";
	//$keys_ind[]=$value;
	//if (0==$counter) {echo $resp['data'];die("\nerr no data in whole industry!");}  // debug
	if ($save_counter>0) {
	//if (10==$save_counter) {
		echo "\nautosaving work ";
		$stime=get_timer();
	file_put_contents("/tmp/save-array.tmp", gzcompress(serialize($fields)));
	file_put_contents("/tmp/save-industry.tmp", serialize($value));
	echo (get_timer()-$stime)."sec\n";
	$save_counter=0;
	}
	$save_counter++;
	$counter=0;
	
//	$years=array(1930,1941,1952,1963,1974,1985,1996,2007);
	$years=array(2014);
	foreach ($years as $this_year) {
		$sy=$this_year;
		$ey=$this_year+10;
		if ($ey>(int)date("Y") ) $ey=(int)date("Y");
$series_num=1;		
$list_series="";
//\"CEU{$series}{$datatype}\"
echo "\n>$sy ";		
foreach ($dt as $datatype => $dtvalue) {	
//$keys_dt[$datatype]=$dtvalue;
if (1!=$series_num) $list_series=$list_series.",";
$series_num++;
$list_series=$list_series."\"CEU{$series}{$datatype}\"";
if ((21==$series_num)||(99==$datatype)) {
//if ((25==$series_num)||(99==$datatype)) {
	echo "($series_num)";
	$series_num=1;
$json_rq="{\"seriesid\":[$list_series], \"startyear\":\"$sy\", \"endyear\":\"$ey\"}";
$list_series="";	
//echo $json_rq;
//die();
$resp=askhost($url,$json_rq, "","","",90000, array("Content-Type: application/json"), true);
//if (2007==$sy) echo "\n$json_rq\n".$resp['data'];
$res=json_decode($resp['data']);
//print_r($res);
// seriesID
//print_r($res->Results->series);

//die();
$counter=0;
//if ("REQUEST_FAILED"==$res->status)
if (FALSE===strpos($res->status, "REQUEST_SUCCEEDED")) {
	//file_put_contents("/tmp/save-array-err.tmp", serialize($fields));
	//file_put_contents("/tmp/save-industry-err.tmp", serialize($value));
	//echo "\n".$res->status."\n aborting!\n";
	//echo "\n $json_rq \n";
	//echo $resp['data']."\n".$resp['d']."\n";
	
	while (true) {
		echo ",";
		sleep(60);
		$resp=askhost($url,$json_rq, "","","",90000, array("Content-Type: application/json"), true);
		$res=json_decode($resp['data']);
		if (FALSE!==strpos($res->status, "REQUEST_SUCCEEDED")) break; // ok!
	} 
	//die();
}

foreach ($res->Results->series as $result_series ) {
if (!isset($result_series->data[0])) {
	echo "!";
	continue; // no data
	
}
//echo $result_series."\n";
$result_id=substr((string)$result_series->seriesID, 11);
$result_sname=$dt[$result_id];
//echo "[$result_id]  $result_sname\n";
echo ".";
//print_r($res->Results->series[0]->data);

foreach ($result_series->data as $item) {
	if ("M13"==$item->period) continue; //annual
	$year=$item->year;
	$month=substr($item->period, 1);
	//$val=$item->value;
	$ts="{$year}-{$month}-01";
	$fields[$ts][$value]["date"]="{$year}-{$month}-01";
	$fields[$ts][$value][$result_sname]=$item->value;
	
	$document=array();
	$document["idate"]=$ts;
	$document["industry"]=$value;
	foreach ($dt as $dt_key => $dt_val)
	{
		//echo ".";
		if (isset($field[$ind_val][$dt_val])) {
			$document[$dt_key]=$field[$ind_val][$dt_val] ;
		}
		//$document = array( "idate" => "$thisdate", "industry" => $idn_key , $dt_key => $val );
	}
	
	
	$collection->findAndModify(array("idate" => $ts, "industry"=>$value),  array('$set' =>  $document),  null, array("upsert" => true ));
	$counter++;
//	$fields[$ts][$value]["industry"]=$value;
	//$fields[$ts]=array("date"=>$ts,$dtvalue=>$val,"industry"=>$value);
	//fputcsv($fw, $fields);
	//$ts=strtotime("{$year}-{$month}-01");
	//echo date(DATE_ATOM,$ts )." $val\n";
} //  process data
} // process series

} // ask data
} // dt
} // years
//die();
echo "*";
//print_r($res);
//die();

} // industry
//echo "writing data";
//$fw=fopen("/tmp/1.csv", "w");
//$keys=array_keys($fields);
//print_r($keys);
//die();
//$array1=$keys_dt;
//$i=1;
// foreach ($array1 as $key => &$value) {
// 	$value=$key.$value;
// 	//$i++;
// }
// array_push($array1, "industry");
// array_unshift($array1, "date");
// fputcsv($fw, $array1);
// foreach ($fields as $thisdate => $item1) {  // down time
// 	foreach ($item1 as $indname => $item2 ) { // down industry
// 		$this_line=array();
// 		$this_line[]=$thisdate;
// 	foreach ($keys_dt as $key) {
// 		if (!isset($item2[$key])) $this_line[]=""; else $this_line[]=$item2[$key];
// 	}
// 	$this_line[]=$indname;
// 	//array_push($item2, $indname);
// 	fputcsv($fw, $this_line);
// 	}
// }

// fclose($fw);
echo "done";
?>