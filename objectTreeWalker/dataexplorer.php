<?php
$rootnode=true;                                                                             
function SFSQL($data,$format='var') {

	
$apikey = APIKEY;
$apiurl = ENDPOINT;
 
		$ch = curl_init($apiurl);                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data),
			 'x-sfsql-apikey: ' . $apikey)                                                                       
		); 
		curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);	
      $result = curl_exec($ch);
						
						if(curl_error($ch)){
							echo curl_error($ch);
							exit();
						}	
						curl_close($ch);	
						$var = json_decode($result,true);        
						if($format=='var'){
	                    return $var;
						}else{
						return $result;	
						}
 }
 
  
function showatt($maxdepth=0,$rootattr='',$poid=0) {
	
$root=($rootattr!='' ? ',"rootattr":"'.$rootattr.'"' : '');

	$query='[
			{
				"showattrs": {
					"include-name": true,
					"include-depth": true,
					"include-type": true,
					"include-type-prefix" : false,
					"maxdepth":'. $maxdepth .''.$root .'
				}
			}
		]';
		$query2='[
			{
				"showattrs": {
					"include-name": true,
					"include-depth": true,
					"include-type": true,
					"include-type-prefix" : false,
					"maxdepth":'. $maxdepth .',
					"poid": '.$poid.'
				}
			}
		]';

        $graph=SFSQL($query);
        return $graph;
}

function DisplayJSON($json) {
 $display='
  <script type="text/javascript">
   var data ='. $json .';
   var id=\'json\';
   document.getElementById(id).innerHTML = JSONTree.create(data);
  </script>';
 
 return $display;
 
 }

$getroot = (isset($_GET['name']) ? $_GET['name'] : '');
$maxdepth=(isset($_GET['name']) ? 1 : 1);
$poid=(isset($_GET['name']) ? $_GET['poid'] : 0);
$graph=showatt($maxdepth,$getroot,$poid);

if(isset($graph[0]['data'][0])){
	$multi=true;
}else{
	$multi=false;
}

if(isset($_GET['name']))
{   
	$path= $_GET['path'];
 
   $rootnode=false;
   $getroot = $_GET['name'];	
	$poid=$_GET['poid'];
	$ppoid=$_GET['ppoid'];
	$depthfilter=$_GET['depth'];
	$graph=showatt($depthfilter,$getroot,$poid);
   echo'<html>
			<head>
			<title>SchemafreeSQL - View Object Graph</title>';
include("stdhead.php");
echo'
<link href="js/jsonTree/jsontree.css" rel="stylesheet" />
<script type="text/javascript" src="js/jsonTree/jsontree.js"></script>
</head>
<body>';
 include("accountnav.php");
	echo '
	<div class="content">
			<div class="contentWrap, flex-vert-wrapper">
					<div id="doccontent">
					  <div class="subsection">
	
	
	
	<a href="dataexplorer.php">ROOT</a>&nbsp;&nbsp;<a href="dataexplorer.php?name='.trim($_GET['name']).'&depth='.trim($_GET['depth']).'&path='.$path.'&poid='.$_GET['poid'].'&ppoid='.$ppoid.'">'.trim($_GET['name']).'</a><br><br>';
	echo 'Object Name:'.trim($_GET['name']) . '<br>';
	echo 'OID:'.$poid. '<br>';
	echo 'POID:'.$ppoid. '<br>';
	echo 'PATH:'.$path . '<br><br>';
	
	$qstring='';
	if($multi){
		 
				$i=0;   
				while( $i < count($graph[0]['data']))  { 
								if($graph[0]['data'][$i]["type"] != 'o' && $graph[0]['data'][$i]["depth"]==$depthfilter)
												 {
														 $type=$graph[0]['data'][$i]["type"];
														 $attrpath=$graph[0]['data'][$i]["path"];
														 $pos='$'.$type.':.'.$attrpath.'.pos(),';
														 $qstring .='$$'.$type.':.'.$attrpath.', '.$pos;
														 
												 }
				$i++;
				}
       }else{
		   //echo 'not multi<br>';
		   if($graph[0]['data']["type"] != 'o' && $graph[0]['data']["depth"]==$depthfilter)
												 {
														 $type=$graph[0]['data']["type"];
														 $attrpath=$graph[0]['data']["path"];
														 $pos='$'.$type.':.'.$attrpath.'.pos(),';
														 $qstring .='$$'.$type.':.'.$attrpath.', '.$pos;;
														 
												 }
		   
		   
	   }
	                            $query=	
												 '[
													 {
													  "query": {
													  "sfsql": "SELECT  '.substr($qstring, 0, -1).' where $o:.'.trim($_GET['name']).'.oid()='.$poid.'",
                                                      "_sfsql": "!@sfsql@!"													  
													  }
													 }				
 												 ]';
												//echo 'string:'. $query;
												//die();
												 
			if($qstring !=''){					
				$attvalues=SFSQL($query,'json');
				
				echo '<div  class="payload" id="json"></div>';
                echo DisplayJSON($attvalues);  

            }
echo'<!--hi--></body></html>';			
}else{
$rootnode=true;
$path ='';
$poid=0;
$depthfilter=0;
$maxdepth=0;
$poid=0;
$getroot = '';

   echo'<html>
			<head>
			<title>SchemafreeSQL - View Object Graph</title>';
include("stdhead.php");
echo'
<link href="js/jsonTree/jsontree.css" rel="stylesheet" />
<script type="text/javascript" src="js/jsonTree/jsontree.js"></script>
</head>
<body>';
 include("accountnav.php");
	echo '
	<div class="content">
			<div class="contentWrap, flex-vert-wrapper">
					<div id="doccontent">
					  <div class="subsection">
					  
					  	<div class="title2">Object Explorer</div>
						<br>
						<div>
							NOTE: Demonstration. This is an experimental web app written in PHP that is not yet formatted for style nor written for efficiency.
							<br><br>It allows you to manually walk through your entire dataset by querying the SFSQL API for all children under each object level.
							<br><br>
						</div>



<a href="dataexplorer.php">ROOT</a><br><br>';

		 //echo 'multi<br>';
		      $select='';
				$where='';
				$i=0; 
				while( $i < count($graph[0]['data']))  { 
								if($graph[0]['data'][$i]["type"] != 'o' && $graph[0]['data'][$i]["depth"]==0)
												 {
														 $type=$graph[0]['data'][$i]["type"];
														 $attrpath=$graph[0]['data'][$i]["path"];
														 $qstring ='$'.$type.':'. $attrpath;
														 $pos ='$'.$type.':'. $attrpath.'.pos()';
														 
														 $select.=  $qstring .' as \''.$attrpath.'\', '.$pos.' as \''.$attrpath.'.pos\',';
														 
														 $where.= '$'.$type.':'.$attrpath.'.poid()=0';
														 
														  $queryOLD=	
														 '[
															 {
															  "query": {
															  "sfsql": "SELECT  '.$qstring.' as \''.$attrpath.'\', '.$pos.' as \''.$attrpath.'.pos\' where $'.$type.':'.$attrpath.'.poid()=0",
															  "_sfsql": "!@sfsql@!"													  
															  }
															 }				
														 ]';
														   $query2=	
														 '[
															 {
															  "query": {
															  "sfsql": "SELECT  '.$qstring.', '.$pos.' where $'.$type.':'.$attrpath.'.poid()=0",
															  "_sfsql": "!@sfsql@!"													  
															  }
															 }				
														 ]';
														 $query=	
														 '[
															 {
															  "query": {
															  "sfsql": "SELECT  '.$qstring.', '.$pos.' ",
															  "_sfsql": "!@sfsql@!"													  
															  }
															 }				
														 ]';
													 
															if($qstring !=''){					
																$attvalues=SFSQL($query);
																
																 echo $query.'<br>';
																  if(isset($attvalues[0]['data'])) {
																
																 
																		if (!isset($attvalues[0]['data'][0])){
																			foreach($attvalues[0]['data'] as $key => $value) {
																			  echo "$key : $value <br>";
																			}
																			echo '<br>';
																		}else{
																			$k=0;   
																				while( $k < count($attvalues[0]['data']))  {
																					
																					foreach($attvalues[0]['data'][$k] as $key => $value) {
																					  echo "  $key : $value   <br>";
																					}
																					
																					$k++;
																				}
																		}
																	echo '<br><br>';	
																 }
																		
							 
													}
											
											}
											
					$i++;		
					 
					 
					}


     
}
  
 
 if($multi){  

$i=0; 

		while( $i < count($graph[0]['data']))  { 

					if($graph[0]['data'][$i]["type"] == 'o' && $graph[0]['data'][$i]["depth"]==$depthfilter)
						
					{
					
					$name=trim($graph[0]['data'][$i]["name"]);
			
					$depth = ($rootnode ? 1 : $depthfilter);
												$objpath=$graph[0]['data'][$i]["path"];
												
												 $query=	
												 '[
													 {
													  "query": {
													  "sfsql": "SELECT  $o:.'.$name.'.oid(), $o:.'.$name.'.pos() as \'pos\', $o:.'.$name.'.poid() where $o:.'.$name.'.poid() ='.$poid.'",
													  "_sfsql": "!@sfsql@!"
													  }
													 }				
												 ]';
	
													$qoid=SFSQL($query);

													if(isset($qoid[0]['data'][0])){
														$j=0;
														echo '<br>Query:' . $qoid[0]['_sfsql'] .'<br>';
														echo 'Object Count:' . count($qoid[0]['data']).'<br>';
														while( $j < count($qoid[0]['data']))  {
														$oid=$qoid[0]['data'][$j]['o:'.$name.'.oid()'];
														$poid=$qoid[0]['data'][$j]['o:'.$name.'.poid()'];
														$pos = $qoid[0]['data'][$j]['pos'];
														echo '<a href="dataexplorer.php?name='.$name.'&type='.$graph[0]['data'][$i]["type"].'&depth='.($depth).'&path='.$path.'.'.$name.'&poid='.$oid.'&ppoid='.$poid.'">'.$graph[0]['data'][$i]["type"].':'.$name.'</a><br>';
														echo 'Name:'.$graph[0]['data'][$i]["name"].'<br>';	
														echo 'Path:'.$graph[0]['data'][$i]["path"].'<br>';	
														echo 'OID:'.$oid.'<br>';
														echo 'POID:'.$poid.'<br>';
														echo 'POS:'.$pos.'<br><br>';
														 $j++;
														 }
														
													}else{
														if(!$rootnode && isset($qoid[0]['data']['o:'.$name.'.oid()'])){
					
														echo 'Object Count: 1<br>';
														}
														if(isset($qoid[0]['data']['o:'.$name.'.oid()'])){
													
                                          echo 'Object Count: 1<br>';														
														$oid=$qoid[0]['data']['o:'.$name.'.oid()'];
														$poid=$qoid[0]['data']['o:'.$name.'.poid()'];
														$pos=$qoid[0]['data']['pos'];
														echo '<a href="dataexplorer.php?name='.$name.'&type='.$graph[0]['data'][$i]["type"].'&depth='.($depth).'&path='.$path.'.'.$name.'&poid='.$oid.'&ppoid='.$poid.'">'.$graph[0]['data'][$i]["type"].':'.$name.'</a><br>';
														echo 'Name:'.$graph[0]['data'][$i]["name"].'<br>';	
														echo 'Path:'.$graph[0]['data'][$i]["path"].'<br>';	
														echo 'OID:'.$oid.'<br>';
														echo 'POID:'.$poid.'<br>';
														echo 'POS:'.$pos.'<br><br>';
														}
													}
													
																		
						
					}
		                                 
		$i++;
		
		}
		
 }else{

	if(!empty($graph[0]['data']["type"])){
		
		if($graph[0]['data']["type"] == 'o' && $graph[0]['data']["depth"]==$depthfilter){
			$name=trim($graph[0]['data']["name"]);
			$depth = ($rootnode ? 1 : $depthfilter);	
			echo '<a href="dataexplorer.php?name='.$name.'&type='.$graph[0]['data']["type"].'&depth='.$depth.'&path='.$path.'.'.trim($graph[0]['data']["name"]).'">'.$graph[0]['data']["type"].':'.trim($graph[0]['data']["name"]).'</a><br>';
            echo '333'.$graph[0]['data'][$i]["path"].'</a><br><br>';	
		}
		if($graph[0]['data']["type"] != 'o' && $graph[0]['data']["depth"]==$depthfilter){
			$name=trim($graph[0]['data']["name"]);
			echo '<a href="dataexplorer.php?name='.$name.'&type='.$graph[0]['data']["type"].'&depth=1&path='.$path.'.'.trim($graph[0]['data']["name"]).'">'.$graph[0]['data']["type"].':'.trim($graph[0]['data']["name"]).'</a><br>';
            echo '444'.$graph[0]['data'][$i]["path"].'</a><br><br>';	
		}
	}
 }	
 echo'
                 </div><!--subsection-->
					</div><!--doccontent-->';
					
					include("footer.php");
echo'
			</div><!--contentWrap-->
		</div><!--content-->
	</body>
</html>';
?>
