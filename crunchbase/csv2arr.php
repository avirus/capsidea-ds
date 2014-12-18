<?php 
/**
 * Convert a comma separated file into an associated array.
 * The first row should contain the array keys.
 *
 * Example:
 *
 * @param string $filename Path to the CSV file
 * @param string $delimiter The separator used in the file
 * @return array
 * @link http://gist.github.com/385876
 * @author Jay Williams <http://myd3.com/>
 * @copyright Copyright (c) 2010, Jay Williams
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

function csv_to_array($filename='', $delimiter=',', $enclosure='"')
{
	if(!file_exists($filename) || !is_readable($filename))
	{log_fatal( "file $filename not found"); return FALSE;}
	
	$header = NULL;
	$hcount=0;
	$lcount=0;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 0, $delimiter, $enclosure)) !== FALSE)
		{
			if(!$header) {
				$header = $row;
				$hcount = count($header);
			}
			else {
				if ($hcount!=count($row)) {echo (implode(",",$row)."\n$filename: array broken, header $hcount != row ".count($row)."\n");continue;}
				$data[] = array_combine($header, $row);
			}
		}
		fclose($handle);
	}
	return $data;
}

?>
