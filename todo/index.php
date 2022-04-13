<?php
if(!isset($_SESSION)) session_start();
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'conn.php';

$upademessg = ( !empty($_SESSION['updatemessg']) ? $_SESSION['updatemessg'] : '');

$todofilteroid=null;
$statusfilter=null;
$postfilter=null;

if( !empty($_POST['todoselect']) &&  $_POST['todoselect'] != 'all'){
	$todofilteroid = $_POST['todoselect'];
}

if( !empty($_POST['statusselect']) ){
$postfilter=$_POST['statusselect'];
switch ($_POST['statusselect']) {
  case 'Done':
    $statusfilter='Open';
	 
    break;
  case 'Open':
   $statusfilter='Done';
    break;
  case 'all':
    $statusfilter=null;
    break;
  default:
   $statusfilter=null;
} 

}
$fetch=GetTodos($statusfilter,null);
//var_dump($fetch);
//die();
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"/>
	</head>
<body>

	
	<div class="col-md-6 well">
		<h3 class="text-primary">TODOS</h3>
		<form id="statusselect" name="statusselect" method="POST" class="form-inline" action="index.php">
		<select name="statusselect" id="statusselect" >
		<option value="all" <?php if($postfilter == 'all')echo 'selected';?>>Show All</option>
		<option value="Open" <?php if($postfilter == 'Open')echo 'selected';?>>Show Open TODOS</option>
		<option value="Done" <?php if($postfilter == 'Done')echo 'selected';?>>Show Complete TODOS</option>
		<select>
		<br><br>
		<button class="btn btn-primary form-control" name="select">Display TODOS by Status</button>
		</form>
		<hr style="border-top:1px dotted #ccc;"/>

		<div class="col-md-8">
			<center>
				<form id="addtodo" method="POST" class="form-inline" action="action.php">
					<input type="text" class="form-control" name="title" required/>
				   <input type="hidden" name="action" value="add_todo"/>
					<button class="btn btn-primary form-control" name="add">Add TODO</button>
				</form>
			</center>
			
		</div>
		<br><br><br>
		<table class="table">
			<thead>
				<tr>
				   <th>#</th>
					<th>TODOS</th>
					<th>Status</th>
					<th>Total Tasks</th>
					<th>Open Tasks</th>
					<th>Closed Tasks</th>
					<th>Action</th>
				</tr>
			</thead> 
			<tbody>
				<?php
					$todofilter='';
					if($fetch){
							
					$count=count($fetch);
					$i=0;
					$n=1;
					$todofilter .=
					'<select name="todoselect" id="todoselect" >
					<option value="all">Display all Tasks</option>';
					
					while($i < $count){
						
					//$istask=isTask($fetch[$i]['todo_id']);
						
					
							//if( !$istask && $fetch[$i]['status'] != 'HIDDEN' ){
								if($fetch[$i]['todo_id'] == $todofilteroid){
							      $todofilter .= '<option value="'.$fetch[$i]['todo_id'].'" selected>'.$fetch[$i]['title'].'</option>';
								}else{	
								   $todofilter .= '<option value="'.$fetch[$i]['todo_id'].'">'.$fetch[$i]['title'].'</option>';
							  }
								
								$taskscounts=getTaskCounts($fetch[$i]['todo_id']);
								
								
								?>
								
									<tr>
									   <td><?php echo $n ; ?></td>
										<td><?php echo '<a href="todo.php?todo_id='.$fetch[$i]['todo_id'].'">'.$fetch[$i]['title'].'</a>';?></td>
										<td><?php echo $fetch[$i]['status'];?></td>
										<td><?php echo $taskscounts['total'];?></td>
										<td><?php echo $taskscounts['open'];?></td>
										<td><?php echo $taskscounts['done'];?></td>
										<td colspan="2">
											<center>
											<?php 
											   if($taskscounts['open'] == 0){
												echo '<a href="action.php?action=update_task&todo_id='.$fetch[$i]['todo_id'].'&status='.$fetch[$i]['status'].'" class="btn btn-success"><span class="glyphicon glyphicon-check"></span></a>| ';
												
												 if($fetch[$i]['status'] == 'Done' )
													echo '| <a href="action.php?action=delete_todo&todo_id='.$fetch[$i]['todo_id'].'" class="btn btn-danger"><span class="glyphicon glyphicon-remove">
													</span></a>';
													
											   }else{
													echo '<span class="glyphicon glyphicon-check"></span> |';
													
												 }            
												
												?>
											</center>
										</td>
									</tr>
								<?php
								$n++;
								//}
				$i++;
					}
				$todofilter .='</select>';
				}else{
				?>
					<tr>
					
					<td colspan="4"> NO TODO's</td>
					
				  </tr>
					
			<?php } ?>
		</tbody>
		<br><br><br>
		</table>
		<h3 class="text-primary">TASKS</h3>
		<form id="todoselect" name="todoselect" method="POST" class="form-inline" action="index.php">
		
		<?php 
		  echo $todofilter;
		  ?>
		<br><br>
		<button class="btn btn-primary form-control" name="select">Filter Task By Selected TODO</button>
		</form>
		<hr style="border-top:1px dotted #ccc;"/>
			
		<table class="table">
			<thead>
				<tr>
				   <th>#</th>
					<th>TASKS</th>
					<th>Parents TODOS</th>
					<th>Status</th>
					<th>Total Tasks</th>
					<th>Open Tasks</th>
					<th>Closed Tasks</th>
					<th>Action</th>
				</tr>
			</thead> 
			<tbody>
				<?php
				   $fetch=GetTasks($todofilteroid);
					if($fetch){	
					$count=count($fetch);
					$i=0;
					$n=1;
	
					
					while($i < $count){
						$parents=getParents($fetch[$i]['todo_id']);
					   $parentmarkup =('<span>');
					   $pcount=count($parents);
						
						for ($x = 0; $x < $pcount; $x++) {
			
							$parents[$x]['title'];
							$parents[$x]['todo_id'];
							$parentmarkup .='<a href="todo.php?todo_id='.$parents[$x]['todo_id'].'&task=true">'.$parents[$x]['title'].'</a><br>  ';
						}
						$parentmarkup .='</span>';
							
					
								$taskscounts=getTaskCounts($fetch[$i]['todo_id']);
								
								?>
								
									<tr>
										<td><?php echo $n ; ?></td>
										<td><?php echo '<a href="todo.php?todo_id='.$fetch[$i]['todo_id'].'&task=true">'.$fetch[$i]['title'].'</a>';?></td>
										<td><?php echo $parentmarkup;?></td>
										<td><?php echo $fetch[$i]['status'];?></td>
										<td><?php echo $taskscounts['total'];?></td>
										<td><?php echo $taskscounts['open'];?></td>
										<td><?php echo $taskscounts['done'];?></td>
										<td colspan="2">
											<center>
												<?php
													
													 if($taskscounts['open'] == 0){
														echo '<a href="action.php?action=update_task&todo_id='.$fetch[$i]['todo_id'].'&status='.$fetch[$i]['status'].'" class="btn btn-success"><span class="glyphicon glyphicon-check"></span></a>';
														
														   if($fetch[$i]['status']=='Done'  )
															echo ' |<a href="action.php?action=delete_todo&todo_id='.$fetch[$i]['todo_id'].'" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span></a>';
														 
														}else{
														 echo '<span class="glyphicon glyphicon-check"></span> ';
														}						
												?>
								
											</center>
										</td>
									</tr>
								<?php
							$n++;	
				$i++;
					}
					
				}else{
				?>
					<tr>
					
					<td colspan="7"> NO Tasks</td>
					
				  </tr>
					
			<?php } ?>
			</tbody>
		</table>
		<a href="hidden.php">Hidden TODOs</a>
		</div>
</body>
</html>