<?php
	$validReport = '';
	
	// Server Side validation
	if(($_POST['timeProj1'] == "") && ($_POST['timeProj2'] == "")) {
		$msgBox = alertBox($reportError5, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else if($_POST['projTimeFromDate'] == "") {
		$msgBox = alertBox($reportError2, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else if($_POST['projTimeToDate'] == "") {
		$msgBox = alertBox($reportError3, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else {
		// Report Options
		if (!empty($_POST['projTimeFromDate'])) {
			$projTimeFromDate = $mysqli->real_escape_string($_POST['projTimeFromDate']);
			$fdate = date('F d, Y', strtotime($projTimeFromDate));
		}
		if (!empty($_POST['projTimeToDate'])) {
			$projTimeToDate = $mysqli->real_escape_string($_POST['projTimeToDate']);
			$tdate = date('F d, Y', strtotime($projTimeToDate));
		}
		if (!empty($_POST['timeProj1'])) {
			$projId = $mysqli->real_escape_string($_POST['timeProj1']);
			$projName = $mysqli->real_escape_string($_POST['projClientName']);
		} else {
			$projId = $mysqli->real_escape_string($_POST['timeProj2']);
			$projName = $mysqli->real_escape_string($_POST['clientProjName']);
		}
		
		// Get Data
		$sql = "SELECT
					timeclock.clockId,
					timeclock.projectId,
					timeclock.adminId,
					timeclock.weekNo,
					timeclock.clockYear,
					timeentry.entryId,
					timeentry.startTime,
					DATE_FORMAT(timeentry.startTime,'%M %d, %Y') AS dateStarted,
					DATE_FORMAT(timeentry.startTime,'%h:%i %p') AS hourStarted,
					timeentry.endTime,
					DATE_FORMAT(timeentry.endTime,'%M %d, %Y') AS dateEnded,
					DATE_FORMAT(timeentry.endTime,'%h:%i %p') AS hourEnded,
					UNIX_TIMESTAMP(timeentry.startTime) AS orderDate,
					clientprojects.projectName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
				FROM
					timeclock
					LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
					LEFT JOIN clientprojects ON timeclock.projectId = clientprojects.projectId
					LEFT JOIN admins ON timeclock.adminId = admins.adminId
				WHERE
					timeclock.projectId = ".$projId." AND
					timeentry.endTime != '0000-00-00 00:00:00' AND
					timeentry.startTime >= '".$projTimeFromDate."' AND timeentry.startTime <= '".$projTimeToDate."'
				ORDER BY timeclock.adminId, orderDate";
		$res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		
		// Get the Total Time Worked
		$qry = "SELECT
					TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
				FROM
					timeclock
					LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
				WHERE
					timeclock.projectId = ".$projId." AND
					timeentry.endTime != '0000-00-00 00:00:00' AND
					timeentry.startTime >= '".$projTimeFromDate."' AND timeentry.startTime <= '".$projTimeToDate."'";
		$result = mysqli_query($mysqli, $qry) or die('-2'.mysqli_error());
		$times = array();
		while ($u = mysqli_fetch_assoc($result)) {
			$times[] = $u['diff'];
		}
		$totalTime = sumHours($times);
	}

	include 'includes/navigation.php';
	
	if ($isAdmin != '1') {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="content last">
		<h4><?php echo $pageName; ?></h4>
		<p>
			<span class="label label-default preview-label">
				<?php echo $projectText; ?>:
				<a href="index.php?action=viewProject&projectId=<?php echo $projId; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>"><?php echo $projName; ?></a>
			</span>
			<span class="label label-default preview-label ml5"><?php echo $dateSpanText.': '.$fdate.' &mdash; '.$tdate; ?></span>
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
						<th><?php echo $managerText; ?></th>
						<th><?php echo $yearText; ?></th>
						<th><?php echo $weekNoText; ?></th>
						<th><?php echo $dateInText; ?></th>
						<th><?php echo $timeInText; ?></th>
						<th><?php echo $dateOutText; ?></th>
						<th><?php echo $timeOutText; ?></th>
						<th><?php echo $totalHoursText; ?></th>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							// Get the Time Total for each Time Entry
							$tot = "SELECT timeentry.startTime, timeentry.endTime FROM timeentry WHERE entryId = ".$row['entryId'];
							$results = mysqli_query($mysqli, $tot) or die('-3'.mysqli_error());
							$rows = mysqli_fetch_assoc($results);
							
							// Convert it to HH:MM
							$from = new DateTime($rows['startTime']);
							$to = new DateTime($rows['endTime']);
							$lineTotal = $from->diff($to)->format('%h:%i');
					?>
							<tr>
								<td data-th="<?php echo $managerText; ?>"><?php echo clean($row['theAdmin']); ?></td>
								<td data-th="<?php echo $yearText; ?>"><?php echo $row['clockYear']; ?></td>
								<td data-th="<?php echo $weekNoText; ?>"><?php echo $row['weekNo']; ?></td>
								<td data-th="<?php echo $dateInText; ?>"><?php echo $row['dateStarted']; ?></td>
								<td data-th="<?php echo $timeInText; ?>"><?php echo $row['hourStarted']; ?></td>
								<td data-th="<?php echo $dateOutText; ?>"><?php echo $row['dateEnded']; ?></td>
								<td data-th="<?php echo $timeOutText; ?>"><?php echo $row['hourEnded']; ?></td>
								<td data-th="<?php echo $totalHoursText; ?>"><?php echo $lineTotal; ?></td>
							</tr>
					<?php } ?>
				</tbody>
			</table>
			<form action="index.php?action=projectTimeExport" method="post" class="mt10" target="_blank">
				<input type="hidden" name="projTimeFromDate" value="<?php echo $projTimeFromDate; ?>" />
				<input type="hidden" name="projTimeToDate" value="<?php echo $projTimeToDate; ?>" />
				<input type="hidden" name="projId" value="<?php echo $projId; ?>" />
				<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
				<span class="label label-default preview-label pull-right"><strong><?php echo $totalTimeText; ?>:</strong> <?php echo $totalTime; ?></strong></span>
			</form>
			<div class="clearfix"></div>
		<?php } ?>
	</div>
<?php } ?>