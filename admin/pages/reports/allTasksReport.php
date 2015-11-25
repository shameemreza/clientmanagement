<?php
	// Report Options
	$completedtasks = $_POST['completedtasks'];
	if (isset($completedtasks) && $completedtasks == '0') {
		$isClosed = "'0'";
		$includeCompleted = $openText;
		$incDate = 'false';
	} else {
		$isClosed = "'0','1'";
		$includeCompleted = $openClosedText;
		$incDate = 'true';
	}
	
    $sql = "SELECT
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
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				tasks
				LEFT JOIN clientprojects ON tasks.projectId = clientprojects.projectId
				LEFT JOIN admins ON tasks.adminId = admins.adminId
			WHERE
				tasks.isClosed IN (".$isClosed.") AND
				tasks.projectId != 0
			ORDER BY tasks.isClosed, orderDate";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
	
	include 'includes/navigation.php';
?>
<div class="content last">
	<h4><?php echo $pageName; ?></h4>
	<p>
		<span class="label label-default preview-label"><?php echo $reportOptionsLabel.': '.$includeCompleted.' '.$tasksText; ?></span>
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
					<th><?php echo $projectText; ?></th>
					<th><?php echo $managerText; ?></th>
					<th><?php echo $priorityField; ?></th>
					<th><?php echo $statusText; ?></th>
					<th><?php echo $dateDueText; ?></th>
					<?php if ($incDate == 'true') { ?>
						<th><?php echo $openClosedText; ?></th>
						<th><?php echo $dateClosedText; ?></th>
					<?php } ?>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						if ($row['isClosed'] == '1') { $closed = '<strong class="text-danger">'.$closedText.'</strong>'; } else { $closed = $openText; }
				?>
					<tr>
						<td data-th="<?php echo $taskTitleField; ?>"><?php echo clean($row['taskTitle']); ?></td>
						<td data-th="<?php echo $projectText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $managerText; ?>"><?php echo clean($row['theAdmin']); ?></td>
						<td data-th="<?php echo $priorityField; ?>"><?php echo clean($row['taskPriority']); ?></td>
						<td data-th="<?php echo $statusText; ?>"><?php echo clean($row['taskStatus']); ?></td>
						<td data-th="<?php echo $dateDueText; ?>"><?php echo $row['taskDue']; ?></td>
						<?php if ($incDate == 'true') { ?>
							<td data-th="<?php echo $openClosedText; ?>"><?php echo $closed; ?></td>
							<td data-th="<?php echo $dateClosedText; ?>"><?php echo $row['dateClosed']; ?></td>
						<?php } ?>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=allTasksExport" method="post" class="mt10" target="_blank">
			<input type="hidden" name="completedtasks" value="<?php echo $completedtasks; ?>" />
			<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
		</form>
		<div class="clearfix"></div>
	<?php } ?>
</div>