<?php
	$validReport = '';
	
	// Server Side validation
	if($_POST['task'] == "...") {
		$msgBox = alertBox($reportError4, "<i class='fa fa-warning'></i>", "default");
		$validReport = 'false';
	} else {
		// Report Options
		$projectId = $mysqli->real_escape_string($_POST['task']);
		$projectName = $mysqli->real_escape_string($_POST['taskFullName']);
		
		// Get Data
		$query = "SELECT
					tasks.taskId,
					tasks.projectId,
					tasks.adminId,
					tasks.taskTitle,
					tasks.taskPriority,
					tasks.taskStatus,
					DATE_FORMAT(tasks.taskDue,'%M %d, %Y') AS taskDue,
					UNIX_TIMESTAMP(tasks.taskDue) AS orderDate,
					tasks.isClosed,
					DATE_FORMAT(tasks.dateClosed,'%M %d, %Y') AS dateClosed,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
				FROM
					tasks
					LEFT JOIN admins ON tasks.adminId = admins.adminId
				WHERE
					tasks.projectId = ".$projectId."
				ORDER BY tasks.isClosed, orderDate";
		$res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
	}

	include 'includes/navigation.php';
?>
<div class="content last">
	<h4><?php echo $pageName; ?></h4>
	<?php if ($validReport == '') { ?>
		<p>
			<span class="label label-default preview-label">
				<?php echo $projectText; ?>: <a href="index.php?action=viewProject&projectId=<?php echo $projectId; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>"><?php echo $projectName; ?></a>
			</span>
			<span class="label label-default preview-label ml5"><?php echo $totalRecordsLabel.': '.$totalRecs; ?></span>
			<span class="label label-default preview-label pull-right"><a href="index.php?action=reports"><i class="fa fa-bar-chart-o"></i> <?php echo $reportsLabel; ?></a></span>
		</p>
	
		<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin mt20">
			<i class="fa fa-warning"></i> <?php echo $noReportResults; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table">
			<tbody>
				<tr class="primary">
					<th><?php echo $taskTitleField; ?></th>
					<th><?php echo $managerText; ?></th>
					<th><?php echo $priorityField; ?></th>
					<th><?php echo $statusText; ?></th>
					<th><?php echo $dateDueText; ?></th>
					<th><?php echo $openClosedText; ?></th>
					<th><?php echo $dateClosedText; ?></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						if ($row['isClosed'] == '1') { $closed = '<strong class="text-danger">Closed</strong>'; } else { $closed = 'Open'; }
				?>
					<tr>
						<td data-th="<?php echo $taskTitleField; ?>"><?php echo clean($row['taskTitle']); ?></td>
						<td data-th="<?php echo $managerText; ?>"><?php echo clean($row['theAdmin']); ?></td>
						<td data-th="<?php echo $priorityField; ?>"><?php echo clean($row['taskPriority']); ?></td>
						<td data-th="<?php echo $statusText; ?>"><?php echo clean($row['taskStatus']); ?></td>
						<td data-th="<?php echo $dateDueText; ?>"><?php echo $row['taskDue']; ?></td>
						<td data-th="<?php echo $openClosedText; ?>"><?php echo $closed; ?></td>
						<td data-th="<?php echo $dateClosedText; ?>"><?php echo $row['dateClosed']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=projectTasksExport" method="post" class="mt10" target="_blank">
			<input type="hidden" name="projectId" value="<?php echo $projectId; ?>" />
			<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
		</form>
		<div class="clearfix"></div>
	<?php
			}
		} else {
	?>
		<p class="clearfix"><span class="label label-default preview-label pull-right"><a href="index.php?action=reports"><i class="fa fa-bar-chart-o"></i> <?php echo $reportsLabel; ?></a></span></p>
		<div class="mt20">
	<?php
		if ($msgBox) { echo $msgBox; }
	}
	?>
		</div>
</div>