<?php
function shopAdminSFSQL($json,$encode=false) {

/*
Example secrets file
{
    "sfsql_id":"",
    "sfsql_key":"",
	 "sfsql_host":"",
	 "sfsql_path":"/api/v1/run",
}

*/
	
	
	
$SecretFilePath = '';
$SecretFileContents = file_get_contents($SecretFilePath);
$secrets = (array) json_decode($SecretFileContents);
	
$url='https://'.$secrets['sfsql_host'].'/'.$secrets['sfsql_id'].$secrets['sfsql_path'];
$api_key=$secrets['sfsql_key'];
	
   $ch = curl_init($Url);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		 'Content-Type: application/json',
		 'Content-Length: ' . strlen($json),
		 'X-Sfsql-Apikey: ' . $api_key
		 )
	);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); 
	$result = curl_exec($ch);

	if (curl_error($ch)) {
		curl_close($ch);
		return false;
	} else {
		curl_close($ch);
	     if($encode){
				return json_decode($result,true);
			}else{
			   return 	$result;
			}
	}
}

function getRandomChars($charCnt) {
	return getRandomCharsForCharset($charCnt, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz');
}

function getRandomCharsForCharset($charCnt, $charset) {
	$rtnChars = '';
	$csLen = strlen($charset);
	for ($i = 0; $i < $charCnt; $i++) {
		$randCPos = rand() % $csLen;
		$rtnChars .= $charset[$randCPos];
	}
	return $rtnChars;
}

?>
