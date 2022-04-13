<?php
if(!isset($_SESSION)) session_start();

require_once  'conn.php';
$message = ( !empty($_SESSION['updatemessg']) ? $_SESSION['updatemessg'] : '' );
unset($_SESSION['updatemessg']);
$task='';

if( !empty($_POST['title']) && !empty($_POST['todo_id'])){
	
	$task=( !empty($_POST['task']) ? $_POST['task'] : '' );	
	
	$comment=json_encode($_POST['comment'],JSON_PRETTY_PRINT); 

	
	$update=UpdateTodo($_POST['todo_id'],$_POST['title'],$comment);
			
			$_SESSION['updatemessg']="TODO Updated";
			header('location: todo.php?todo_id='.$_POST['todo_id'].$task);	
			die();
	
}

if(!empty($_GET['todo_id'])){
	
	$todoid=$_GET['todo_id'];  
	$parentmarkup ='';
	$parentnotin ='';
					
						
						$parents=getParents($todoid);
						$count=($parents ? count($parents) : -1);
						if($count != -1)
							
						{
							      $task='&task=true';
									$parentmarkup .='Parent ToDos\'s:  <ul class="wtree">';
									$parentnotin .='and $o:.todo.task.oid() NOT IN (';
									for ($x = 0; $x < $count; $x++) {
						
										$parents[$x]['title'];
										$parents[$x]['todo_id'];
										//$istask = '';
										
										$parentmarkup .='<li><span><a href="todo.php?todo_id='.$parents[$x]['todo_id'].'">'.$parents[$x]['title'].'</a>  ';
										$parentnotin .= $parents[$x]['todo_id'].',';
									}
									$parentmarkup .='</ul>';
									$parentnotin = substr($parentnotin, 0, -1);
									$parentnotin .=')';
						}			
					
							
	            
					
					$query=toDo($todoid,$parentnotin);
					$fetch1=$query[0]['data'][0];
					$taskscounts=getTaskCounts($todoid);
					$tasks=$query[1]['data'];
					$count=count($tasks);
				
					$taskmarkup ='';
						if($count)
							
						{
					
									$taskmarkup .='Tasks\'s: <ul class="wtree">
									<li><span>TASK|STATUS|TOTAL|OPEN|DONE</span></li>';
									for ($x = 0; $x < $count; $x++) {
						
										$tasks[$x]['title'];
										$tasks[$x]['todo_id'];
										$childtaskscounts=getTaskCounts($tasks[$x]['todo_id']);
										$taskmarkup .='<li><span><a href="todo.php?todo_id='.$tasks[$x]['todo_id'].'">'.$tasks[$x]['title'].'</a>|';
										$taskmarkup .= $tasks[$x]['status'].'|'.$childtaskscounts['total'].'|'.$childtaskscounts['open'].'|'.$childtaskscounts['done'].'|';
										$taskmarkup .='<a href="action.php?action=delete_task&task_id='.$tasks[$x]['todo_id'].'&todo_id='.$todoid.'">REMOVE TASK</a></span></li>';
									}
									$taskmarkup .='</ul>';
	
						}		
				
}else{
	
header('location: index.php');	
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"/>
<style>


ul {
  margin-left: 2px;
}

.wtree li {
  list-style-type: none;
  margin: 5px 0 5px 5px;
  position: relative;
}
.wtree li:before {
  content: "";
  position: absolute;
  top: -5px;
  left: -10px;
  border-left: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  width: 10px;
  height: 5px;
}
.wtree li:after {
  position: absolute;
  content: "";
  top: 5px;
  left: -5px;
  border-left: 1px solid #ddd;
  border-top: 1px solid #ddd;
  width: 10px;
  height: 100%;
}
.wtree li:last-child:after {
  display: none;
}
.wtree li span {
  display: block;
  border: 1px solid #ddd;
  padding: 5px;
  color: #888;
  text-decoration: none;
}

.wtree li span:hover, .wtree li span:focus {
  background: #eee;
  color: #000;
  border: 1px solid #aaa;
}
.wtree li span:hover + ul li span, .wtree li span:focus + ul li span {
  background: #eee;
  color: #000;
  border: 1px solid #aaa;
}
.wtree li span:hover + ul li:after, .wtree li span:hover + ul li:before, .wtree li span:focus + ul li:after, .wtree li span:focus + ul li:before {
  border-color: #aaa;
}
</style>
	</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<a class="navbar-brand" href="index.php">TODOS</a>
		</div>
	</nav>
	<div class="col-md-3"></div>
	<div class="col-md-6 well">
	                             <form method="POST" class="form-inline" action="todo.php">
		                          <h3 class="text-primary">
										  TITLE: <input type="text" id="title" name="title" value="<?php echo $fetch1['title']?>"/></h3>
										  <h3 class="text-primary">
										  COMMENTS:<br>
										  </h3>
										  <textarea  id="comment" name="comment"  rows="4" cols="80">
											<?php echo $fetch1['comment']?>
											</textarea> 
										  <input type="hidden" id="<?php echo $todoid;?>" name="todo_id" value="<?php echo $todoid;?>"/>
										   <input type="hidden" class="form-control" name="task" value="<?php echo $task;?>"/>
											 <h3 class="text-primary">Status:</h3>
											 <?php echo $fetch1['status'];
										 
										       if($taskscounts['open'] == 0)
														echo ' <a href="action.php?action=update_task&todo_id='.$todoid.'&status='.$fetch1['status'].'&task_id='.$todoid.'" class="btn btn-success"><span class="glyphicon glyphicon-check"></span></a>';
										 ?> 
											
											<br><br>
										  	<button class="btn btn-primary form-control" name="add">Update TODO</button>
										 </form>
										 <?php 
										 
										 echo $parentmarkup.$taskmarkup ; ?>
										 
										 <?php echo $message; ?>
		<hr style="border-top:1px dotted #ccc;"/>
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<center>
				<form method="POST" class="form-inline" action="action.php?action=add_task">
					 <input type="text" class="form-control" name="title" required/>
					 <input type="hidden" class="form-control" name="todo_id" value="<?php echo $todoid;?>"/>
					<button class="btn btn-primary form-control" name="add">Add New Task</button>
				</form>
			</center>
			<br><br>
			<center>       
            	
				<form method="POST" class="form-inline" action="action.php?action=link_todo">
					<?php  
					$alltasks='';
					$optionlist='';
					$oid = [];
					array_push($oid,$todoid);
					if(isset($query[2]['data'][0])){
						
	
		
					$fetch3=$query[2]['data'];
               					
					$count= count($fetch3);
					}else{
					$count = -1;	
					}
					$i=0;
			
					     while($i < $count){					  
							$alltasks .= $fetch3[$i]['title'].',';	
      
                       $i++;									 
						  }
						
					?>
					
					
								  <select name="link_todo_title" id="link_todo_title">
									
								  </select>
								
					 <input type="hidden" class="form-control" name="todo_id" value="<?php echo $todoid;?>"/>	
					  <input type="hidden" class="form-control" name="todo_title" value="<?php echo $fetch1['title'];?>"/>	
					 <input type="hidden" class="form-control" name="task" value="<?php echo 	$task ;?>"/>
					<button class="btn btn-primary form-control" name="link">Add Task from Task List</button>
					
				</form>
				<script>
					var alltaskstasks = '<?php echo $alltasks; ?>';
					
					var childtasks = '<?php array_walk_recursive($oid,"getchildTasks"); ?>'; 
					
					var childtasks2 = childtasks.replace(/,\s*$/, "");
					var childtasksArray = childtasks2.split(',');
					
					var alltaskstasks2 = alltaskstasks.replace(/,\s*$/, "");
					var alltaskstasksArray = alltaskstasks2.split(',');

					
					alltaskstasksArray = alltaskstasksArray.filter( ( el ) => !childtasksArray.includes( el ) );
			
					
					var tasks = alltaskstasksArray;     

					var sel = document.getElementById('link_todo_title');
					var fragment = document.createDocumentFragment();

					tasks.forEach(function(task, index) {
						 var opt = document.createElement('option');
						 opt.innerHTML = task;
						 opt.value = task;
						 fragment.appendChild(opt);
					});

					sel.appendChild(fragment);
					</script>
				<br><br>
				</script>   

			</center>
			
<ul class="wtree"	>	
<ul>
<li>TASK TREE</li>
<li><span>TASK|STATUS|TOTAL|OPEN|DONE</span></li>	
</ul>	
<?php
$oid = [];
array_push($oid,$todoid);
$branches=getbranchIds2($oid);
 ?>
</ul>

		</div>
	

	</div>

</body>
</html>