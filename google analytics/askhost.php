<?php 
// function ASKHOST
// input: 
// $url - URL
// $srvd - post data 
// $srvauth - "user:password"
// $certpem - certificate pem file path
// $certpwd - certificate password
// $tmoutms - timeout in milliseconds 
// $headers - http headers
// $httpcode_needed - true, returns array(data,http_code) / false - returns only data
// output: data, or array(data,http_code)

function askhost($url, $srvd=FALSE, $srvauth="", $certpem="", $certpwd="1",  $tmoutms = 60000, $headers="", $httpcode_needed=false) {
	$fp=curl_init();
	$verbose = fopen('php://temp', 'rw+');
	if (0!=strlen($srvauth)) {
		curl_setopt($fp, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($fp, CURLOPT_USERPWD, $srvauth);
	}
	if (FALSE!==$srvd) {
		curl_setopt($fp, CURLOPT_POST, TRUE);
		@curl_setopt($fp, CURLOPT_POSTFIELDS, $srvd);
	}
	if (0!=strlen($certpem)) {
		curl_setopt($fp, CURLOPT_SSLCERT, $certpem);
		curl_setopt($fp, CURLOPT_SSLCERTPASSWD, $certpwd);
		curl_setopt($fp, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($fp, CURLOPT_SSL_VERIFYHOST, FALSE);
	}
	curl_setopt($fp, CURLOPT_URL, $url);
	curl_setopt($fp, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($fp, CURLOPT_NOPROGRESS, TRUE);
	@curl_setopt($fp, CURLOPT_TIMEOUT, ($tmoutms/1000));
	@curl_setopt($fp, CURLOPT_CONNECTTIMEOUT_MS, $tmoutms);
	if (""!=$headers) curl_setopt($fp, CURLOPT_HTTPHEADER,$headers);
	curl_setopt($fp, CURLOPT_VERBOSE, TRUE);
	curl_setopt($fp,CURLOPT_STDERR, $verbose);
		//      curl_setopt($fp, CURLOPT_CERTINFO, TRUE);
	$data = curl_exec($fp);
	$httpcode = curl_getinfo($fp, CURLINFO_HTTP_CODE);
	curl_close($fp);
	rewind($verbose);
	$verb=stream_get_contents($verbose);
	//!rewind($verbose)
	if ($httpcode_needed) return array("data"=>$data, "httpcode"=>$httpcode, "d"=>$verb);
	return $data; 	//otherwise
};
?>