<?php

function runTODOSFSQL($json,$encode=false) {
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
		  'X-Sfsql-Apikey: ' . $api_key;
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
			   return $result;
			}
	}
	
}


Function getbranchIds2($oid){
	
		foreach( $oid as $key => $todoid ) {	
			
			
			$json='[
							{
								"query": {
									"sfsql": "SELECT $o:.task.oid() as toid,  $o:.task.pos() as pos, $s:.task.title as title, $s:.task.status as status WHERE  $o:.task.poid()='.$todoid.'"
								}
							}
					  ]';
					  
				$query=runTODOSFSQL($json,true);

				$empty = [];

				$toids=( empty($query[0]['data'][0]) ? $empty : array_column($query[0]['data'],'toid') );
				$title=( empty($query[0]['data'][0]) ? $empty : array_column($query[0]['data'],'title') );
				$status=( empty($query[0]['data'][0]) ? $empty : array_column($query[0]['data'],'status') );

				$count=count($toids);


				echo '<ul>';
				if($count){

						for($i=0; $i< $count; $i++){  
							
							if($i==($count-1)){
										$taskscounts=getTaskCounts($toids[$i]);
										echo'<li><span><a href="todo.php?todo_id='.$toids[$i].'&task=true">'.$title[$i].'</a>|'.$status[$i].'|';
										echo $taskscounts['total'].'|'.$taskscounts['open'].'|'.$taskscounts['done'];
										echo'</span></li>';
							}else{
									  $taskscounts=getTaskCounts($toids[$i]);
										echo'<li><span><a href="todo.php?todo_id='.$toids[$i].'&task=true">'.$title[$i].'</a>|'.$status[$i].'|';
										echo $taskscounts['total'].'|'.$taskscounts['open'].'|'.$taskscounts['done'];
										echo'</span></li>';
							}
							
							$oid = [];
							array_push($oid,$toids[$i]);
							getbranchIds2($oid);
								
						}
						
						
				}else{
							echo '<li>';
						}

				echo '</ul>';
				
		}
		
return true;

}


Function getchildTasks($todoid,$key,$TODO=null){
	

	
	$json='[
					{
						"query": {
							"sfsql": "SELECT $o:.task.oid() as toid, $s:.task.title as title WHERE  $o:.task.poid()='.$todoid.'"
						}
					}
		     ]';
			  
$query=runTODOSFSQL($json,true);

$empty = [];

$toids=( empty($query[0]['data'][0]) ? $empty : array_column($query[0]['data'],'toid') );
$title=( empty($query[0]['data'][0]) ? $empty : array_column($query[0]['data'],'title') );


$count=count($toids);

	if($count){

			for($i=0; $i< $count; $i++){
				
						if(!$TODO){     	
								echo $title[$i].',';
								$oid = [];
								array_push($oid,$toids[$i]);
								array_walk_recursive($oid,'getchildTasks');
						}else{
							if($TODO['title'] == $title[$i]){
								//echo $TODO['title']. ' is it equal to(true) ' .$title[$i].'<br>';  
							  $_SESSION['updatemessg']="Sorry the current task is deeply nested as a sub task in the task you selected. Bad things will happen if we allow this task to be added as a sub task.";
						     header('location:todo.php?todo_id='.$TODO['todo_id']);
							  
							  die();
						     }
							 // echo $TODO['title']. ' is it equal to(false) ' .$title[$i].'<br>';  
						     $oid = [];
							  array_push($oid,$toids[$i]);
							  array_walk_recursive($oid,'getchildTasks',$TODO);
							
						}
													  
			}
	}
}


Function UpdateTodo($todoid,$title,$comment){
						$json='[
									{
										"modify": {
											"data": {
												
													"o:.todo": {
															"#update": {
																	"where": "$o:.todo.oid()='.$todoid.'"
																},
														"title": "'.$title.'",
														"comment":'.$comment.'
												 }
												
											}
										}
									}
								]';
								//echo $json;
								//die();
					
$query=runTODOSFSQL($json);

return $query;	
}

function GetTodos($status=null,$todoid=null) {
$andstatus = ($status ? 'AND $s:TODOS.todo.status  <> \''.$status.'\'' : '' );
$andtodo = ($todoid ? 'AND $o:TODOS.todo.oid()  = '.$todoid : '' );

					$json='[
							{
								"query": {
									"sfsql": "SELECT $o:TODOS.todo.oid() as todo_id,$s:TODOS.todo.title as title,$s:TODOS.todo.status as status, $b:TODOS.todo.toplevel as toplevel  WHERE $b:TODOS.todo.toplevel = true AND $s:TODOS.todo.status <> \'HIDDEN\' '.$andstatus.$andtodo.' ORDER BY \'todo_id\' DESC"
								}
							}
						]';
				
$query=runTODOSFSQL($json,true);
//echo $query;
//die();
//var_dump($query[0]['data']);
//die();
if( empty($query[0]['data']) )
   return null;

return $query[0]['data'];

}

function GetHiddenTodos() {

					$json='[
							{
								"query": {
									"sfsql": "SELECT $o:TODOS.todo.oid() as todo_id,$s:TODOS.todo.title as title,$s:TODOS.todo.status as status  WHERE $s:TODOS.todo.status = \'HIDDEN\'  ORDER BY todo_id DESC"
								}
							}
						]';
				
$query=runTODOSFSQL($json,true);
//var_dump($query[0]['data']);
//die();
if( empty($query[0]['data']) )
   return null;

return $query[0]['data'];

}

function getTodoIdByTitle($link_to_title) {

					$json='[
							{
								"query": {
									"sfsql": "SELECT $o:.todo.oid() as todo_id  WHERE  $s:.todo.title = \''.$link_to_title.'\'"
								}
							}
						]';
			
		
$query=runTODOSFSQL($json,true);

if( empty($query[0]['data']) )
   return null;

$id=array_unique(array_column($query[0]['data'],'todo_id'));
//var_dump($id);
//die();
return $id[0];

}

function GetTasks($todooid=null) {
	
	$where=($todooid ? ' AND $o:.task.poid()='.$todooid  : '');
	
	      $json='[
							{
								"query": {
									"sfsql": "SELECT DISTINCT $o:.task.oid() as todo_id,$s:.task.title as title,$s:.task.status as status WHERE $s:.task.status <> \'HIDDEN\''.$where.' ORDER BY \'todo_id\' DESC"
								}
							}
						]';
		//echo $json;
      //die();		
$query=runTODOSFSQL($json,true);

if(!$query)
   return null;
	
return $query[0]['data'];
}


function getTaskCounts($oid) {
	
	$json='[
									{
										"query": {
											"sfsql": "SELECT $o:.task.oid() as oid, $s:.task.status as status WHERE $s:.task.status <> \'HIDDEN\' and   $o:.task.poid()='.$oid.'"
										}
									}
			]';
					
$tasks=runTODOSFSQL($json,true);

$taskcount['total']=0;
$taskcount['open']=0;
$taskcount['done']=0;

if( !empty($tasks[0]['data']) ){
$taskcount['total']=count($tasks[0]['data']);
$taskvaluecount=array_count_values(array_column($tasks[0]['data'], 'status'));
$taskcount['open']= (!empty($taskvaluecount['Open']) ? $taskvaluecount['Open'] : 0) ;
$taskcount['done']= (!empty($taskvaluecount['Done']) ? $taskvaluecount['Done'] : 0) ;
}

return $taskcount;	
	
}

function getParents($oid) {
	
$json='[
	{
		"query": {
			"sfsql": "SELECT $o:.todo.oid() as \'todo_id\', $s:.todo.title as \'title\'  WHERE $s:.todo.status <> \'HIDDEN\'  AND $o:.todo.task.oid() = '.$oid.' ORDER BY \'title\' DESC"
		}
	}
]';	
	
$parents=runTODOSFSQL($json,true);
//var_dump($parents);
//die();
return $parents[0]['data'];
}

function isTask($oid) {
	
	$json='[
									{
										"query": {
											"sfsql": "SELECT $o:.task.oid() as oid,  $s:.task.status as status  WHERE   $o:.task.oid()='.$oid.'"
										}
									}
			]';
					
$task=runTODOSFSQL($json,true);

if( empty($task[0]['data'][0]['oid']) )
		return false;

return true;

}

function toDo($todoid,$parentnotin=''){
	
	       $json='[
							{
								"query": {
									"sfsql": "SELECT $o:.todo.oid() as todo_id, $s:.todo.title as title, $s:.todo.status as status, $s:.todo.comment as comment  WHERE $s:.todo.status <> \'HIDDEN\' and $o:.todo.oid()='.$todoid.'"
								}
							},
							{
								"query": {
									"sfsql": "SELECT $o:.task.oid() as \'todo_id\', $s:.task.title as \'title\', $s:.task.status as \'status\'  WHERE $s:.task.status <> \'HIDDEN\' and  $o:.task.poid()='.$todoid.' ORDER BY \'todo_id\' DESC"
								}
							},
							{
								"query": {
									"sfsql": "SELECT DISTINCT $o:.todo.task.oid() as \'todo_id\', $s:.todo.task.title as \'title\'   WHERE $s:.todo.task.status <> \'HIDDEN\' and  $o:.todo.task.oid() <> '.$todoid.' and $o:.todo.task.poid() <> '.$todoid.'  '.$parentnotin.' ORDER BY \'todo_id\' DESC"
								}
							}
							
						]';
						
						//echo $json;
                  //die();
					
$query=runTODOSFSQL($json,true);
//var_dump($query);
//die();
return $query;
		
}

function add_task($title,$todo_id){
	
	$json='[
					           {
										"modify": {
											"data": {
												"TODOS": {
													"todo": [
													{
														"#append": {}
													},
														{
															"__todooid": "!@result.oid@!",
															"title": "'.$title.'",
															"status": "Open",
															"toplevel":false,
															"comment":""
															
														}
													]
												}
											}
										}
									},
									{
										"modify": {
											"data": {
												
													"o:.todo": {
															"#update": {
																	"where": "$o:.todo.oid()='.$todo_id.'"
																},
																"status": "Open",
																"task":[
																			{
																				"#append": {}
																			},
																				{
																					"#ref": "SELECT $o:.todo.oid() WHERE $o:.todo.oid()=!@__todooid@!"
																				}
																       ]
													  
												 }
												
											}
										}
									}
								]';
													
			$query=runTODOSFSQL($json);
			
			return $query;
	
}
	

function add_todo($title){
	
	$json='[
									{
										"modify": {
											"data": {
												"TODOS": {
													"todo": [
													{
														"#append": {}
													},
														{
															"title": "'.$title.'",
															"status": "Open",
															"toplevel":true,
															"comment":""
															
														}
													]
												}
											}
										}
									}
								]';
								
								//echo $json;
								//die();
								
			$query=runTODOSFSQL($json);
			
			return $query;
	
}

function delete_task($task_id,$todo_id){
$json='[
								
									{
										"delete": {
											"objfilter": "SELECT $o:.task.attrset(\'delete\') WHERE $o:.task.oid()='.$task_id.' and $o:.task.poid()='. $todo_id.'"
										}
									},
									{
									"purge": {}
								   }
		]';
								//echo $json;
								//die();
								
			$query=runTODOSFSQL($json);
			//var_dump($query);
			//die(); 
			return $query;
}

function delete_todo($todo_id,$mode='hidden'){
	
	          $deletejson='[
								
									{
										"delete": {
											"objfilter": "SELECT $o:TODOS.todo.attrset(\'delete\') WHERE $o:TODOS.todo.oid()='.$todo_id.'"
										}
									},
									{
									"purge": {}
								   }
								]';
								
					$json='[
									{
										"modify": {
											"data": {
												
													"o:.todo": {
															"#update": {
																	"where": "$o:.todo.oid()='.$todo_id.'"
																},
														"status": "HIDDEN"
												 }
												
											}
										}
									}
								]';
								
			if($mode=='hidden'){	
			
				$query=runTODOSFSQL($json);
				
			}else{
						$query=runTODOSFSQL($deletejson);
						
						echo $query.'<br><br>';
						
						$json='[
										{
											"query": {
												"sfsql": "SELECT $o:.task.poid() as poid  WHERE $o:.task.oid() = '.$todo_id.'"
											}
										}
								 ]';
						
					$query=runTODOSFSQL($json,true);
					
						//var_dump($query);
					
					//die();
					if( !empty($query[0]['data'][0]['poid']) ) {
						
					$taskoids=array_unique(array_column($query[0]['data'],'poid'));
						$count=count($taskoids);
							for($i=0; $i< $count; $i++){
								
								$json='[
												{
													"delete": {
														"objfilter": "SELECT $o:.task.attrset(\'delete\') WHERE $o:.task.oid()='.$todo_id.' and $o:.task.poid()='.$taskoids[$i].'"
													}
												},
												{
												"purge": {}
												}
											]';
										
										
							 $query=runTODOSFSQL($json);
							 //echo $query.'<br><br>';
							 //die();
							 
							} 
					}
			}
		
	 return true;
}

function link_todo($todo_id,$link_to_title){
	
$json='[
									{
										"modify": {
											"data": {
												
													"o:.todo": {
															"#update": {
																	"where": "$o:.todo.oid()='.$todo_id.'"
																},
																"status": "Open",
																"task":[
																      {
																			"#append": {}
																		},
																			{
																				"#ref": "SELECT $o:.todo.oid() WHERE $s:.todo.title=\''.$link_to_title.'\'",
																				"status": "Open"
																				
																			}
																
																
																]
													
												 }
												
											}
										}
									}
								]';
								
								
$query=runTODOSFSQL($json);	
return $query;
	
}

function update_status($todo_id,$status){
	
	        $json='[
									{
										"modify": {
											"data": {
												
													"o:.todo": {
															"#update": {
																	"where": "$o:.todo.oid()='.$todo_id.'"
																},
														"status": "'.$status.'"
													
												 }
												
											}
										}
									}
						]';
						


			$query=runTODOSFSQL($json);
			return $query;
}

?>
