<?php
	$datePicker = 'true';
	$jsFile = 'tasks';
	$pagPages = '10';

	// Complete Task
	if (isset($_POST['submit']) && $_POST['submit'] == 'completeTask') {
		$completeId = $mysqli->real_escape_string($_POST['completeId']);
		$taskStatus = 'Closed';
		$dateClosed = date("Y-m-d H:i:s");

		$stmt = $mysqli->prepare("UPDATE
									tasks
								SET
									taskStatus = ?,
									isClosed = 1,
									dateClosed = ?
								WHERE
									taskId = ?"
		);
		$stmt->bind_param('sss', $taskStatus, $dateClosed, $completeId);
		$stmt->execute();
		$msgBox = alertBox($taskCompletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Delete Task
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTask') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM tasks WHERE taskId = ?");
		$stmt->bind_param('s', $deleteId);
		$stmt->execute();
		$msgBox = alertBox($taskDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Add New Task
    if (isset($_POST['submit']) && $_POST['submit'] == 'addNewTask') {
        // Validation
		if($_POST['taskTitle'] == "") {
            $msgBox = alertBox($taskTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['taskDesc'] == "") {
            $msgBox = alertBox($taskDescReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['taskPriority'] == "") {
            $msgBox = alertBox($taskPriorityReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['taskStatus'] == "") {
            $msgBox = alertBox($taskStatusReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['taskDue'] == "") {
            $msgBox = alertBox($taskDueByReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$projectId = $mysqli->real_escape_string($_POST['projectId']);
			if($_POST['projectId'] == "...") {
				$projectId = '0';
			} else {
				$projectId = $mysqli->real_escape_string($_POST['projectId']);
			}

			$taskTitle = $mysqli->real_escape_string($_POST['taskTitle']);
			$taskDesc = $_POST['taskDesc'];
			$taskPriority = $mysqli->real_escape_string($_POST['taskPriority']);
			$taskStatus = $mysqli->real_escape_string($_POST['taskStatus']);
			$taskStart = date("Y-m-d H:i:s");
			$taskDue = $mysqli->real_escape_string($_POST['taskDue']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									tasks(
										projectId,
										adminId,
										taskTitle,
										taskDesc,
										taskPriority,
										taskStatus,
										taskStart,
										taskDue
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssssssss',
								$projectId,
								$adminId,
								$taskTitle,
								$taskDesc,
								$taskPriority,
								$taskStatus,
								$taskStart,
								$taskDue
			);
			$stmt->execute();
			$stmt->close();

			if (isset($_POST['addCal']) && $_POST['addCal'] == '1') {
				$startDate = $endDate = $taskDue.' 00:00:00';
				$eventTitle = 'Task: '.$taskTitle;
				$eventDesc = $mysqli->real_escape_string($_POST['taskDesc']);
				$eventColor = '#91c04a';

				$stmt = $mysqli->prepare("
									INSERT INTO
										adminevents(
											adminId,
											startDate,
											endDate,
											eventTitle,
											eventDesc,
											eventColor
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssssss',
									$adminId,
									$startDate,
									$endDate,
									$eventTitle,
									$eventDesc,
									$eventColor
				);
				$stmt->execute();
				$stmt->close();
				$msgBox = alertBox($newTaskAddedCalMsg, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($newTaskAddedMsg, "<i class='fa fa-check-square'></i>", "success");
			}
			// Clear the Form of values
			$_POST['taskTitle'] = $_POST['taskDesc'] = $_POST['taskPriority'] = $_POST['taskStatus'] = $_POST['taskDue'] = '';
		}
	}

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM tasks WHERE projectId = 0 AND adminId = ".$adminId." AND isClosed = 0");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$query = "SELECT
				taskId,
				adminId,
				taskTitle,
				taskDesc,
				taskPriority,
				taskStatus,
				DATE_FORMAT(taskStart,'%M %d, %Y') AS startDate,
				DATE_FORMAT(taskDue,'%M %d, %Y') AS dueDate,
				UNIX_TIMESTAMP(taskDue) AS orderDate
			FROM
				tasks
			WHERE
				projectId = 0 AND adminId = ".$adminId." AND isClosed = 0
			ORDER BY
				orderDate ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#personal" data-toggle="tab"><i class="fa fa-user"></i> <?php echo $personalTasksTabLink; ?></a></li>
		<li><a href="index.php?action=projectTasks"><i class="fa fa-folder-open"></i> <?php echo $projectTaskTabLink; ?></a></li>
		<li><a href="index.php?action=closedTasks"><i class="fa fa-check-circle"></i> <?php echo $closedTasksTabLink; ?></a></li>
		<li class="pull-right"><a href="#newTask" data-toggle="tab"><i class="fa fa-plus"></i> <?php echo $newTaskTabLink; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="tab-content">
		<div class="tab-pane in active no-padding" id="personal">
			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noPersonalFound; ?>
				</div>
			<?php } else { ?>
				<table class="rwd-table no-margin">
					<tbody>
						<tr class="primary">
							<th><?php echo $taskTableHead; ?></th>
							<th><?php echo $priorityField; ?></th>
							<th><?php echo $statusText; ?></th>
							<th><?php echo $createdOnText; ?></th>
							<th><?php echo $dueOnText; ?></th>
							<th></th>
						</tr>
						<?php while ($row = mysqli_fetch_assoc($res)) { ?>
							<tr>
								<td data-th="<?php echo $taskTableHead; ?>">
									<a href="index.php?action=viewTask&taskId=<?php echo $row['taskId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewTaskText; ?>">
										<?php echo clean($row['taskTitle']); ?>
									</a>
								</td>
								<td data-th="<?php echo $priorityField; ?>"><?php echo clean($row['taskPriority']); ?></td>
								<td data-th="<?php echo $statusText; ?>"><?php echo clean($row['taskStatus']); ?></td>
								<td data-th="<?php echo $createdOnText; ?>"><?php echo $row['startDate']; ?></td>
								<td data-th="<?php echo $dueOnText; ?>"><?php echo $row['dueDate']; ?></td>
								<td data-th="<?php echo $actionsText; ?>">
									<a href="index.php?action=viewTask&taskId=<?php echo $row['taskId']; ?>">
										<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $editTaskTooltip; ?>"></i>
									</a>
									<a data-toggle="modal" href="#completeTask<?php echo $row['taskId']; ?>">
										<i class="fa fa-check-square-o text-success" data-toggle="tooltip" data-placement="left" title="<?php echo $markCompletedTooltip; ?>"></i>
									</a>
									<a data-toggle="modal" href="#deleteTask<?php echo $row['taskId']; ?>">
										<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteTaskTooltip; ?>"></i>
									</a>
								</td>
							</tr>
							<div class="modal fade" id="completeTask<?php echo $row['taskId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $completeTaskText; ?>: <?php echo clean($row['taskTitle']); ?>?</p>
											</div>
											<div class="modal-footer">
												<input name="completeId" type="hidden" value="<?php echo $row['taskId']; ?>" />
												<button type="input" name="submit" value="completeTask" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
												<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
											</div>
										</form>
									</div>
								</div>
							</div>
							<div class="modal fade" id="deleteTask<?php echo $row['taskId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $taskDeleteConfQuip.': '.clean($row['taskTitle']); ?>?</p>
											</div>
											<div class="modal-footer">
												<input name="deleteId" type="hidden" value="<?php echo $row['taskId']; ?>" />
												<button type="input" name="submit" value="deleteTask" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
												<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
											</div>
										</form>
									</div>
								</div>
							</div>
						<?php } ?>
					</tbody>
				</table>
			<?php
					if ($total > $pagPages) {
						echo $pages->page_links();
					}
				}
			?>
		</div>
		<div class="tab-pane vert-pane fade no-padding" id="newTask">
			<p class="lead"><?php echo $newTaskTitle; ?></p>
			<form action="" method="post">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="taskTitle"><?php echo $taskTitleField; ?></label>
							<input type="text" class="form-control" name="taskTitle" required="" maxlength="50" value="<?php echo isset($_POST['taskTitle']) ? $_POST['taskTitle'] : ''; ?>" />
							<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="taskPriority"><?php echo $priorityField; ?></label>
							<input type="text" class="form-control" name="taskPriority" required="" value="<?php echo isset($_POST['taskPriority']) ? $_POST['taskPriority'] : ''; ?>" />
						</div>
						<div class="form-group">
							<label for="taskDue"><?php echo $dueDateField; ?></label>
							<input type="text" class="form-control" name="taskDue" id="taskDue" required="" value="<?php echo isset($_POST['taskDue']) ? $_POST['taskDue'] : ''; ?>" />
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" name="addCal" value="1">
								<?php echo $addToCalendarField; ?>
							</label>
						</div>
						<span class="help-block"><?php echo $addToCalendarFieldHelp; ?></span>
					</div>
					<div class="col-md-6">
						<?php if ($isAdmin == '1') { ?>
							<div class="form-group">
								<label for="projectId"><?php echo $selectProjectField; ?></label>
								<select class="form-control" name="projectId">
									<?php
										$qryc = "SELECT projectId, projectName FROM clientprojects WHERE archiveProj = 0";
										$resc = mysqli_query($mysqli, $qryc) or die('-2'.mysqli_error());
									?>
									<option value="..."><?php echo $selectOption; ?></option>
									<?php while ($c = mysqli_fetch_assoc($resc)) { ?>
										<option value="<?php echo $c['projectId']; ?>"><?php echo clean($c['projectName']); ?></option>
									<?php } ?>
								</select>
								<span class="help-block"><?php echo $selectProjectFieldHelp; ?></span>
							</div>
						<?php } else { ?>
							<div class="form-group">
								<label for="projectId"><?php echo $selectProjectField; ?></label>
								<select class="form-control" name="projectId">
									<?php
										$qryc = "SELECT
													clientprojects.projectId, clientprojects.projectName
												FROM
													clientprojects
													LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
													LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
												WHERE
													assignedprojects.assignedTo = ".$adminId." AND clientprojects.archiveProj = 0";
										$resc = mysqli_query($mysqli, $qryc) or die('-3'.mysqli_error());
									?>
									<option value="..."><?php echo $selectOption; ?></option>
									<?php while ($c = mysqli_fetch_assoc($resc)) { ?>
										<option value="<?php echo $c['projectId']; ?>"><?php echo clean($c['projectName']); ?></option>
									<?php } ?>
								</select>
								<span class="help-block"><?php echo $selectProjectFieldHelp; ?></span>
							</div>
						<?php } ?>
						<div class="form-group">
							<label for="taskStatus"><?php echo $statusField; ?></label>
							<input type="text" class="form-control" name="taskStatus" required="" value="<?php echo isset($_POST['taskStatus']) ? $_POST['taskStatus'] : ''; ?>" />
						</div>
						<div class="form-group">
							<label for="taskDesc"><?php echo $taskDescField; ?></label>
							<textarea class="form-control" required="" name="taskDesc" rows="5"><?php echo isset($_POST['taskDesc']) ? $_POST['taskDesc'] : ''; ?></textarea>
						</div>
					</div>
				</div>
				<hr />
				<button type="input" name="submit" value="addNewTask" class="btn btn-success btn-lg btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveTaskBtn; ?></button>
			</form>
		</div>
	</div>
</div>