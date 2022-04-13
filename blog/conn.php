<?php


function runBetaSFSQL($json,$encode=false) {
	
	$api_key = APIKEY;
	$url = APIENDPOINT;
	
	$ch = curl_init($url);
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
?>