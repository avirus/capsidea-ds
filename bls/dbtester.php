<?php 
$fields=unserialize(gzuncompress(file_get_contents("/tmp/save-array.tmp")));
$filename="/tmp/ce.datatype";
$fp=fopen($filename, "r");
$tmp=fgetcsv($fp,null,"\t");
$dt=array();
while ($tmp=fgetcsv($fp,null,"\t")) {
	$dt[$tmp[0]]=$tmp[1];
}
$filename="/tmp/ce.industry";
$fp=fopen($filename, "r");
$tmp=fgetcsv($fp,null,"\t");
$industry=array();
while ($tmp=fgetcsv($fp,null,"\t")) {
	$industry[$tmp[0]]=$tmp[3];
}

$indname=$industry["42443000"];
$datatype=$dt["08"];
echo $indname."\n".$datatype."\n";
print_r($fields["1998-02-01"][$indname][$datatype]);

?>