<?php
// federalreserve data generator for capsidea
//http://www.federalreserve.gov/datadownload/Output.aspx?rel=H10&filetype=zip
include_once 'usdrates-inc.php';
include_once 'askhost.php';
function find_code($sstring, $estring, $ostring, $data) {
	//$sstring start string
	//$estring end string
	//$ostring offset from start ends
	$loc = strpos ( $data, $sstring );
	$loc = $loc + strlen ( $sstring ) + $ostring;
	$loc2 = strpos ( $data, $estring, $loc );
	$code = substr ( $data, $loc, $loc2 - $loc );
	return $code;
}

function  create_csv_file($selected, $kurs,$currency)
{
	global $my_data_dir;
	$used=array();
	$fname="$my_data_dir/usrates.csv";
	$fp=fopen("$fname", "w");
	fwrite($fp, "DATE");
	foreach ($selected as $key => $value) {
		$kname=$currency["$value"];
		fwrite($fp, ",$kname");
	}
	fwrite($fp, "\n");
	foreach ($kurs as $key => $item)
	{
		$ts=strtotime($key);
		$ts_full=date("Y-m-d H:00:00",$ts);
		fwrite($fp, "$ts_full");
		foreach ($selected as $kid => $kname)
		{
			if (!isset($item[$kname])) $val="null"; else $val=$item[$kname];
			fwrite($fp, ",$val");
		} // for
		fwrite($fp, "\n");
	}// foreach kurs
	fclose($fp);
	//$zip = new ZipArchive(); // zip support, for future use
	//$filename = $fname.".zip";
	//if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {	die("cant open <$filename>\n");}
	//$zip->addFile($fname);
	//$zip->close();
	return $fname;
}

$data=askhost("http://www.federalreserve.gov/datadownload/Output.aspx?rel=H10&filetype=zip");
$zipname="/tmp/1.zip";
file_put_contents($zipname, $data);
$zip =zip_open($zipname);
do {
	$entry = zip_read($zip);
} while ($entry && zip_entry_name($entry) != "H10_data.xml");
// open entry
zip_entry_open($zip, $entry, "r");
// read entry
$data = zip_entry_read($entry, zip_entry_filesize($entry));
zip_entry_close($entry);
// close zip
zip_close($zip);
$filename="/tmp/H10_data.xml";
file_put_contents($filename, $data);
//$data=file_get_contents($filename);
// <kf:Series CURRENCY="USD" FREQ="9" FX="EUR" SERIES_NAME="RXI$US_N.B.EU" UNIT="Currency:_Per_EUR" UNIT_MULT="1"  >
//<frb:Obs OBS_STATUS="A" OBS_VALUE="0.8929" TIME_PERIOD="2013-12-31" />
//<frb:Obs OBS_STATUS="ND" OBS_VALUE="-9999" TIME_PERIOD="2014-01-01" />
//</kf:Series>
// <kf:Series CURRENCY="VEB" FREQ="9" FX="VEB" SERIES_NAME="RXI_N.B.VE" UNIT="Currency:_Per_USD" UNIT_MULT="1"  >
$ticker="";
//gpb
$currency=array();
$keys=array();
//$cur=find_code("FREQ=\"9\" FX=\"GBP\" SERIES_NAME=\"RXI\$US_N.B.UK\" UNIT=\"Currency:_Per_GBP\" UNIT_MULT=\"0.01\"  >", "</kf:Series>", 0, $data);
foreach(preg_split("/((\r?\n)|(\r\n?))/", $data) as $line){
	if (FALSE!==strpos($line, "Series>")) { $ticker="";continue;}
	if ((""==$ticker)&&(FALSE===strpos($line, "FREQ=\"9\" FX=\""))) continue; //skip beginning
	if (FALSE!==strpos($line, "FREQ=\"9\" FX=\"")) {
		$ticker=find_code("FREQ=\"9\" FX=\"", "\" SERIES_NAME", 0, $line);
		$keys[]=$ticker;
	}
	if (FALSE===strpos($line,"OBS_STATUS")) continue; // skip garbage
	// do stuff with $line
	//<frb:Obs OBS_STATUS="A" OBS_VALUE="0.8126" TIME_PERIOD="2013-11-27" />
	//<frb:Obs OBS_STATUS="ND" OBS_VALUE="-9999" TIME_PERIOD="2013-11-28" />
	if (FALSE!==strpos($line, "-9999")) continue;
	
	
	if (strlen($line)<10) continue;
	$val=find_code("OBS_VALUE=\"", "\" TI", 0, $line);
	$tm=find_code("TIME_PERIOD=\"", "\" />", 0, $line);
	//echo $val.":".$tm."\n";
	$currency["$tm"][$ticker]=$val;
}
create_csv_file($keys, $currency,$cname);

?>