<?php
if(!isset($_SESSION)) session_start();
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once($_SERVER['DOCUMENT_ROOT'] . '/admin/.include/admin.php');
function shopAdminSFSQL($json,$encode=false) {
if (!isDemoCreated()) {
	$demo = false;
	echo $demotext = "Start/Resume Demo to Run App";
	exit;
} else {
	$demoid = getDemoId();
	$demoUrl = 'https://'.$GLOBALS['demoapidomain'].'/' . $demoid . '/api/v1/run';
}
	
	
   $ch = curl_init($demoUrl);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FAILONERROR, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		 'Content-Type: application/json',
		 'Content-Length: ' . strlen($json),
		 'X-Sfsql-Apikey: ' . $_SESSION['demokey']
		 )
	);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); # needed so dns lookup does only v4 and not v4 and v6 lookup which fails after many seconds and doesn't return till after that failure
	$result = curl_exec($ch);

	if (curl_error($ch)) {
		curl_close($ch);
		return false;
	} else {
		curl_close($ch);
		header("Cache-Control: no-cache, no-store, must-revalidate", true);
		header("Pragma: no-cache", true);
	     if($encode){
				return json_decode($result,true);
			}else{
			   return 	$result;
			}
	}
}
/*
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
*/
?>