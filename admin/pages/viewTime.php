<?php
	$entryId = $_GET['entryId'];
	$datePicker = 'true';
	$jsFile = 'viewTime';

	// Edit Time Entry
    if (isset($_POST['submit']) && $_POST['submit'] == 'editEntry') {
        // Validation
		if($_POST['editReason'] == "") {
            $msgBox = alertBox($editReasonReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateIn'] == "") {
            $msgBox = alertBox($dateInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeIn'] == "") {
            $msgBox = alertBox($timeInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateOut'] == "") {
            $msgBox = alertBox($dateOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeOut'] == "") {
            $msgBox = alertBox($timeOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$dateIn = $mysqli->real_escape_string($_POST['dateIn']);
			$timeIn = $mysqli->real_escape_string($_POST['timeIn']);
			$dateOut = $mysqli->real_escape_string($_POST['dateOut']);
			$timeOut = $mysqli->real_escape_string($_POST['timeOut']);
			$startTime = $dateIn.' '.$timeIn.':00';
			$endTime = $dateOut.' '.$timeOut.':00';
			$editReason = $mysqli->real_escape_string($_POST['editReason']);
			$editTime = date("Y-m-d H:i:s");
			$entryType = 'Edited';

			// Edit the Record
            $stmt = $mysqli->prepare("UPDATE
										timeentry
									SET
										startTime = ?,
										endTime = ?,
										entryType = ?
									WHERE
										entryId = ?"
			);
			$stmt->bind_param('ssss',
									$startTime,
									$endTime,
									$entryType,
									$entryId
			);
			$stmt->execute();
			$stmt->close();

			// Add a record of the Edit
			$stmt = $mysqli->prepare("
								INSERT INTO
									timeedits(
										entryId,
										editedBy,
										editTime,
										editReason
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssss',
								$entryId,
								$adminId,
								$editTime,
								$editReason
			);
			$stmt->execute();
			$msgBox = alertBox($timeEntryUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['editReason'] = '';
			$stmt->close();
		}
	}

	// Get Data
	$query = "SELECT
				timeentry.entryId,
				timeentry.clockId,
				timeentry.projectId,
				timeentry.adminId,
				DATE_FORMAT(timeentry.entryDate,'%M %d, %Y') AS entryDate,
				timeentry.startTime,
				DATE_FORMAT(timeentry.startTime,'%Y-%m-%d') AS startDate,
				DATE_FORMAT(timeentry.startTime,'%M %d, %Y') AS dateStarted,
				DATE_FORMAT(timeentry.startTime,'%h:%i %p') AS hourStarted,
				DATE_FORMAT(timeentry.startTime,'%h:%i') AS hourIn,
				timeentry.endTime,
				DATE_FORMAT(timeentry.endTime,'%Y-%m-%d') AS endDate,
				DATE_FORMAT(timeentry.endTime,'%M %d, %Y') AS dateEnded,
				DATE_FORMAT(timeentry.endTime,'%h:%i %p') AS hourEnded,
				DATE_FORMAT(timeentry.endTime,'%h:%i') AS hourOut,
				timeentry.entryType,
				timeclock.weekNo,
				timeclock.clockYear,
				timeclock.running,
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				timeentry
				LEFT JOIN timeclock ON timeentry.clockId = timeclock.clockId
				LEFT JOIN clientprojects ON timeentry.projectId = clientprojects.projectId
				LEFT JOIN admins ON timeentry.adminId = admins.adminId
			WHERE
				timeentry.entryId = ".$entryId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['running'] == '1') { $isRunning = $yesBtn; } else { $isRunning = $noBtn; }

	// Get any Previous Edit data
	$sqlStmt = "SELECT
					timeedits.editedBy,
					DATE_FORMAT(timeedits.editTime,'%M %d, %Y at %h:%i %p') AS editTime,
					timeedits.editReason,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS editedBy
				FROM
					timeedits
					LEFT JOIN admins ON timeedits.editedBy = admins.adminId
				WHERE timeedits.entryId = ".$entryId;
	$results = mysqli_query($mysqli, $sqlStmt) or die('-2' . mysqli_error());

	// Get the Total Time Worked
	$qry = "SELECT
				TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
			FROM
				timeclock
				LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
			WHERE
				timeentry.entryId = ".$entryId;
	$result = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());
	$times = array();
	while ($u = mysqli_fetch_assoc($result)) {
		$times[] = $u['diff'];
	}
	$totalTime = sumHours($times);

	include 'includes/navigation.php';

	if (($isAdmin != '1') && ($row['adminId'] != $adminId)) {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $managerText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['theAdmin']); ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $projectText; ?>:</td>
						<td class="infoVal">
							<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($row['projectName']); ?>
							</a>
						</td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $clockYearText; ?>:</td>
						<td class="infoVal"><?php echo $row['clockYear']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $dateInText; ?>:</td>
						<td class="infoVal"><?php echo $row['dateStarted']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $dateOutText; ?>:</td>
						<td class="infoVal"><?php echo $row['dateEnded']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $entryTypeText; ?>:</td>
						<td class="infoVal"><?php echo $row['entryType']; ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $recordDateText; ?>:</td>
						<td class="infoVal"><?php echo $row['entryDate']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $clockRunningText; ?>:</td>
						<td class="infoVal"><?php echo $isRunning; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $weekNoText; ?>:</td>
						<td class="infoVal"><?php echo $row['weekNo']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $timeInText; ?>:</td>
						<td class="infoVal"><?php echo $row['hourStarted']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $timeOutText; ?>:</td>
						<td class="infoVal"><?php echo $row['hourEnded']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-caret-right"></i> <?php echo $totalHoursText; ?>:</td>
						<td class="infoVal"><strong><?php echo $totalTime; ?></strong></td>
					</tr>
				</table>
			</div>
		</div>

		<a data-toggle="modal" data-target="#editEntry" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editTimeEntryBtn; ?></a>
		<a href="index.php?action=timeTracking" class="btn btn-default btn-icon mt20"><i class="fa fa-clock-o"></i> <?php echo $timeTrackNavLink; ?></a>
	</div>

	<div class="content last">
		<h4><?php echo $previousEditsTitle; ?></h4>
		<?php if(mysqli_num_rows($results) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noTimeEditsFound; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $dateOfEditText; ?></th>
						<th><?php echo $editedByText; ?></th>
						<th><?php echo $editReasonText; ?></th>
					</tr>
					<?php while ($rows = mysqli_fetch_assoc($results)) { ?>
						<tr>
							<td data-th="<?php echo $dateOfEditText; ?>"><?php echo $rows['editTime']; ?></td>
							<td data-th="<?php echo $editedByText; ?>"><?php echo clean($rows['editedBy']); ?></td>
							<td data-th="<?php echo $editReasonText; ?>"><?php echo clean($rows['editReason']); ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>

	<div id="editEntry" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $editTimeEntryBtn; ?></h4>
				</div>

				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="editReason"><?php echo $editReasonText; ?></label>
							<input type="text" class="form-control" required="" name="editReason" value="" />
							<span class="help-block"><?php echo $editReasonHelp; ?></span>
						</div>
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="dateIn"><?php echo $dateInText; ?></label>
									<input type="text" class="form-control" name="dateIn" id="dateIn" required="" value="<?php echo $row['startDate']; ?>" />
									<span class="help-block"><?php echo $dateFormat; ?></span>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="timeIn"><?php echo $timeInText; ?></label>
									<input type="text" class="form-control" name="timeIn" id="timeIn" value="<?php echo $row['hourIn']; ?>" />
									<span class="help-block"><?php echo $timeFormat2; ?></span>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="dateOut"><?php echo $dateOutText; ?></label>
									<input type="text" class="form-control" name="dateOut" id="dateOut" required="" value="<?php echo $row['endDate']; ?>" />
									<span class="help-block"><?php echo $dateFormat; ?></span>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="timeOut"><?php echo $timeOutText; ?></label>
									<input type="text" class="form-control" name="timeOut" id="timeOut" value="<?php echo $row['hourOut']; ?>" />
									<span class="help-block"><?php echo $timeFormat2; ?></span>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button type="input" name="submit" value="editEntry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateTimeEntryBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>

			</div>
		</div>
	</div>
<?php } ?>