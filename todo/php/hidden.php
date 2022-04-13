<?php
if(!isset($_SESSION)) session_start();

require 'conn.php';

$fetch=GetHiddenTodos();

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
		<meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1"/>
	</head>
<body>
	<nav class="navbar navbar-default">
		<div class="container-fluid">
			<a class="navbar-brand" href="index.php">TODOS</a>
		</div>
	</nav>
	
	<div class="col-md-6 well">
		<h3 class="text-primary">HIDDEN TODOS</h3>
		
		<br><br><br>
		<table class="table">
			<thead>
				<tr>
				
					<th>TODOS</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead> 
			<tbody>
				<?php
					
	if($fetch)
				{
							
					$count=count($fetch);
					$i=0;

					
					while($i < $count)
					{
						
							
										?>
										
											<tr>
											
												<td><?php echo '<a href="todo.php?todo_id='.$fetch[$i]['todo_id'].'">'.$fetch[$i]['title'].'</a>';?></td>
												<td><?php echo $fetch[$i]['status'];?></td>
												<td>
													<center>
													<?php 
														
														echo '<a href="action.php?action=update_task&todo_id='.$fetch[$i]['todo_id'].'&status='.$fetch[$i]['status'].'" class="btn btn-success"><span class="glyphicon glyphicon-check"></span></a>| ';											

														echo '<a href="action.php?action=delete_todo&todo_id='.$fetch[$i]['todo_id'].'&mode=delete">DELETE PERMANENTLY</a>';
												
														?>
													</center>
												</td>
											</tr>
										<?php
									
						$i++;
					}
			
				}else{
				?>
					<tr>
					
					<td colspan="4"> NO TODO's</td>
					
				  </tr>
					
			<?php } ?>
		</tbody>
		<br><br><br>
		</table>
		
		</div>
</body>
</html>