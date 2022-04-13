<?php
  if(!isset($_SESSION)) session_start();
	require_once 'conn.php'; 
	

	
	if( ISSET($_REQUEST['action']) ){
		
		switch ($_REQUEST['action']) {
					
				case 'add_task':
				  
						if(ISSET($_POST['add'])){
							
							if( !empty($_POST['title']) && !empty($_POST['todo_id']) ){
								
								$title = $_POST['title'];
								$todo_id = $_POST['todo_id'];
								
								$task=add_task($title,$todo_id);
								
								header('location:todo.php?todo_id='.$todo_id.'&task=true');
								die();
								
							}else{
							header('location:index.php');
							die();
							}
						}else{
						header('location:index .php');
						die();
						}
					 
					 break;
					 
				 case 'add_todo':
				  
						  if(ISSET($_POST['add'])){
							  
							if( !empty($_POST['title']) ){
								$title = $_POST['title'];
								
								$todo=add_todo($title);			
													
								header('location:index.php');
								die();
							}
						 }else{
							header('location:index .php');
							die();
						}
					
					
				 break;
					 
				 case 'delete_task':
				  
							$return = 'index.php';
							if( isset($_SERVER['HTTP_REFERER']) ){
							$url = $_SERVER['HTTP_REFERER'];
							$tokens = explode('/', $url);
							$return =  $tokens[sizeof($tokens)-1];
							}
							if( !empty($_GET['task_id']) ){
								
									$task_id = $_GET['task_id'];
									$todo_id = $_GET['todo_id'];
									
									$delete=delete_task($task_id,$todo_id);	
								
								header("location: ". $return );
								die();
							}	
							header("location: ". $return );
							die();
							
				 break;
					 
				 case 'delete_todo':
				 
                        $return = 'index.php';
								if( isset($_SERVER['HTTP_REFERER']) ){
								$url = $_SERVER['HTTP_REFERER'];
								$tokens = explode('/', $url);
								$return =  $tokens[sizeof($tokens)-1];
								}
								if( !EMPTY($_GET['todo_id']) ){
										$mode=( !EMPTY($_GET['mode']) ? $_GET['mode'] : null );
										$todo_id = $_GET['todo_id'];
										
										if($mode){
										$delete=delete_todo($todo_id,$mode);
										}else{
										$delete=delete_todo($todo_id);
										}
									
									
									header("location: ". $return );
									die();
								}	
								
								header("location: ". $return );
								die();
				  
					
					 
				  break;
					 
				  case 'link_todo':
				  
								if(ISSET($_POST['link'])){
										if( !empty($_POST['todo_id']) && !empty($_POST['link_todo_title']) ){
											$link_to_title = $_POST['link_todo_title'];
											$todo_id = $_POST['todo_id'];
											$todo_title = $_POST['todo_title'];
											$task=( empty($_POST['task']) ? '' : '&task=true');
											$TODO['todo_id']=$todo_id;
											$TODO['title']=$todo_title;
											$todoidtolinkto=getTodoIdByTitle($link_to_title);
										
											if($todoidtolinkto){
														$oid = [];
														array_push($oid,$todoidtolinkto);
														$branches=array_walk_recursive($oid,'getchildTasks',$TODO);
														 $link=link_todo($todo_id,$link_to_title);
														 header('location:todo.php?todo_id='.$todo_id.$task);
											}
											header('location:todo.php?todo_id='.$todo_id.$task);
											die();
										}else{
										header('location:index.php');
										die();
										}
									}else{
									header('location:index .php');
									die();
									}				  	
					 
					 break;
					
					case 'update_task':
					
								$return = 'index.php';
								if( isset($_SERVER['HTTP_REFERER']) ){
								$url = $_SERVER['HTTP_REFERER'];
								$tokens = explode('/', $url);
								$return =  $tokens[sizeof($tokens)-1];
								}
								
								if( !empty($_GET['todo_id']) &&  !empty($_GET['status']) ){
										$todo_id = $_GET['todo_id'];
										$status = $_GET['status'];
										
										switch ($status) {
												  case 'Done':
													 $statusfilter='Open';
													 break;
												  case 'Open':
													$statusfilter='Done';
													 break;
												  case 'HIDDEN':
													 $statusfilter='Done';
													 break;
												  default:
													$statusfilter='HIDDEN';
										} 

										
										$statusupdate=update_status($todo_id,$statusfilter);
										
										
									header('location: '. $return);
									die();
								}else{
									
									header('location: index.php');
	
								}	  
					 
					 
					 break;
					 
					 default:
					 
					 echo 'no action';
					 
					 break;
					
					
					
					
				} 	
		
		
	
		
	}else{
		
	header('location:index .php');
	
	}
?>