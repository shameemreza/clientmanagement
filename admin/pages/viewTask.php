<?php
	$taskId = $_GET['taskId'];
	$datePicker = 'true';
	$jsFile = 'tasks';

	// Edit Task
    if (isset($_POST['submit']) && $_POST['submit'] == 'editTask') {
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
			$taskTitle = $mysqli->real_escape_string($_POST['taskTitle']);
			$taskDesc = $_POST['taskDesc'];
			$taskNotes = $_POST['taskNotes'];
			$taskPriority = $mysqli->real_escape_string($_POST['taskPriority']);
			$taskStatus = $mysqli->real_escape_string($_POST['taskStatus']);
			$taskDue = $mysqli->real_escape_string($_POST['taskDue']);
			$isClosed = $mysqli->real_escape_string($_POST['isClosed']);

            $stmt = $mysqli->prepare("UPDATE
										tasks
									SET
										taskTitle = ?,
										taskDesc = ?,
										taskNotes = ?,
										taskPriority = ?,
										taskStatus = ?,
										taskDue = ?,
										isClosed = ?
									WHERE
										taskId = ?"
			);
			$stmt->bind_param('ssssssss',
									$taskTitle,
									$taskDesc,
									$taskNotes,
									$taskPriority,
									$taskStatus,
									$taskDue,
									$isClosed,
									$taskId
			);
			$stmt->execute();
			$msgBox = alertBox($personalTaskUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Get Data
	$query = "SELECT
				tasks.taskId,
				tasks.projectId,
				tasks.adminId,
				tasks.taskTitle,
				tasks.taskDesc,
				tasks.taskNotes,
				tasks.taskPriority,
				tasks.taskStatus,
				DATE_FORMAT(tasks.taskStart,'%M %d, %Y') AS startDate,
				DATE_FORMAT(tasks.taskDue,'%M %d, %Y') AS dueDate,
				DATE_FORMAT(tasks.taskDue,'%Y-%m-%d') AS showDue,
				tasks.isClosed,
				DATE_FORMAT(tasks.dateClosed,'%M %d, %Y') AS dateClosed,
				clientprojects.projectName
			FROM
				tasks
				LEFT JOIN clientprojects ON tasks.projectId = clientprojects.projectId
			WHERE
				taskId = ".$taskId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['projectId'] == '0') {
		$taskType = $taskType1;
	} else {
		$taskType = $taskType2.': <a href="index.php?action=viewProject&projectId='.$row['projectId'].'" data-toggle="tooltip" data-placement="right" title="'.$viewProject.'">'.clean($row['projectName']).'</a>';
	}
	if ($row['isClosed'] == '1') {
		$taskType = '<strong class="text-success">'.$closedTaskText.'</strong>';
		$displayDate = $row['dateClosed'];
		$dateText = $dateClosedText;
	} else {
		$displayDate = $row['dueDate'];
		$dateText = $dateDueText;
	}

	include 'includes/navigation.php';

	if ($row['adminId'] != $adminId) {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="contentAlt">
		<ul class="nav nav-tabs">
			<li><a href="index.php?action=personalTasks"><i class="fa fa-user"></i> <?php echo $personalTasksTabLink; ?></a></li>
			<li><a href="index.php?action=projectTasks"><i class="fa fa-folder-open"></i> <?php echo $projectTaskTabLink; ?></a></li>
			<li><a href="index.php?action=closedTasks"><i class="fa fa-check-circle"></i> <?php echo $closedTasksTabLink; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-folder-open-o"></i> <?php echo $typeText; ?>:</td>
						<td class="infoVal"><?php echo $taskType; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-list-ol"></i> <?php echo $priorityText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['taskPriority']); ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateText; ?>:</td>
						<td class="infoVal"><?php echo $displayDate; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-info-circle"></i> <?php echo $statusField; ?>:</td>
						<td class="infoVal"><?php echo clean($row['taskStatus']); ?></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="well well-sm bg-trans mt20">
			<strong><?php echo $taskDescField; ?>:</strong> <?php echo nl2br(clean($row['taskDesc'])); ?>
		</div>

		<?php if (!empty($row['taskNotes'])) { ?>
			<div class="well well-sm bg-trans mt20">
				<strong><?php echo $taskNotesText; ?>:</strong> <?php echo nl2br(clean($row['taskNotes'])); ?>
			</div>
		<?php } ?>

		<a data-toggle="modal" data-target="#editTask" class="btn btn-success btn-icon"><i class="fa fa-edit"></i> <?php echo $editTaskBtn; ?></a>

		<div id="editTask" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $editTaskBtn; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="taskTitle"><?php echo $taskTitleField; ?></label>
										<input type="text" class="form-control" required="" name="taskTitle" value="<?php echo clean($row['taskTitle']); ?>" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="taskDue"><?php echo $dueByText; ?></label>
										<input type="text" class="form-control" required="" name="taskDue" id="taskDue" value="<?php echo $row['showDue']; ?>" />
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="taskPriority"><?php echo $priorityText; ?></label>
										<input type="text" class="form-control" required="" name="taskPriority" value="<?php echo clean($row['taskPriority']); ?>" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="taskStatus"><?php echo $statusText; ?></label>
										<input type="text" class="form-control" required="" name="taskStatus" value="<?php echo clean($row['taskStatus']); ?>" />
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="taskDesc"><?php echo $taskDescField; ?></label>
								<textarea class="form-control" required="" name="taskDesc" rows="4"><?php echo clean($row['taskDesc']); ?></textarea>
							</div>
							<div class="form-group">
								<label for="taskNotes"><?php echo $taskNotesText; ?></label>
								<textarea class="form-control" name="taskNotes" rows="5"><?php echo clean($row['taskNotes']); ?></textarea>
							</div>
							<?php if ($row['isClosed'] == '0') { ?>
								<div class="form-group">
									<label for="isClosed"><?php echo $markTaskCompleted; ?>?</label>
									<select class="form-control" name="isClosed">
										<option value="0"><?php echo $noBtn; ?></option>
										<option value="1"><?php echo $yesBtn; ?></option>
									</select>
								</div>
							<?php } else { ?>
								<div class="form-group">
									<label for="isClosed"><?php echo $reopenTaskTooltip; ?>?</label>
									<select class="form-control" name="isClosed">
										<option value="1"><?php echo $noBtn; ?></option>
										<option value="0"><?php echo $yesBtn; ?></option>
									</select>
								</div>
							<?php } ?>
						</div>

						<div class="modal-footer">
							<button type="input" name="submit" value="editTask" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateTaskBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
<?php } ?>