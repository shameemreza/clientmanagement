<?php
	$datePicker = 'true';
	$jsFile = 'timeTracking';
	$isRecord = '';

	// Delete Time Entry
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTime') {
		$entryId = $mysqli->real_escape_string($_POST['entryId']);
		$stmt = $mysqli->prepare("DELETE FROM timeentry WHERE entryId = ?");
		$stmt->bind_param('s', $entryId);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($deleteTimeEntryMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Add New Time Entry
    if (isset($_POST['submit']) && $_POST['submit'] == 'newEntry') {
        // Validation
		if($_POST['aId'] == "...") {
            $msgBox = alertBox($selectManagerReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['pId'] == "...") {
            $msgBox = alertBox($selectProjReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateIn'] == "") {
            $msgBox = alertBox($dateInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeIn'] == "") {
            $msgBox = alertBox($timeInReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dateOut'] == "") {
            $msgBox = alertBox($dateOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['timeOut'] == "") {
            $msgBox = alertBox($timeOutReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$aId = $mysqli->real_escape_string($_POST['aId']);
			$pId = $mysqli->real_escape_string($_POST['pId']);
			$dateIn = $mysqli->real_escape_string($_POST['dateIn']);
			$timeIn = $mysqli->real_escape_string($_POST['timeIn']);
			$dateOut = $mysqli->real_escape_string($_POST['dateOut']);
			$timeOut = $mysqli->real_escape_string($_POST['timeOut']);
			$clockYear	= date("Y", strtotime($dateIn));
			$weekNo	= date("W", strtotime($dateIn));
			$entryDate = date("Y-m-d");

			// Check if a Time Clock Record all ready exists
			$check = $mysqli->query("SELECT clockId FROM timeclock WHERE adminId = ".$aId." AND weekNo = '".$weekNo."' AND clockYear = '".$clockYear."'");
			$rows = mysqli_fetch_assoc($check);
			if ($check->num_rows) {
				$isRecord = 'true';
				$clockId = $rows['clockId'];
			}

			$entryType = $adminManualEntryText;
			$startTime = $dateIn.' '.$timeIn.':00';
			$endTime = $dateOut.' '.$timeOut.':00';

			if ($isRecord == 'true') {
				// Time Clock Record exists, Add the Manual Time Entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											projectId,
											adminId,
											entryDate,
											startTime,
											endTime,
											entryType
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('sssssss',
									$clockId,
									$pId,
									$aId,
									$entryDate,
									$startTime,
									$endTime,
									$entryType
				);
				$stmt->execute();
				$msgBox = alertBox($timeEntrySavedMsg, "<i class='fa fa-check-square'></i>", "success");
				// Clear the Form of values
				$_POST['dateIn'] = $_POST['timeIn'] = $_POST['dateOut'] = $_POST['timeOut'] = '';
				$stmt->close();
			} else {
				// Time Clock Record does NOT exists, Create a new Time Clock record and Add the Manual Time Entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeclock(
											projectId,
											adminId,
											weekNo,
											clockYear
										) VALUES (
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('ssss',
										$pId,
										$aId,
										$weekNo,
										$clockYear
				);
				$stmt->execute();
				$stmt->close();

				// Get the new Time Clock clockId
				$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE adminId = ".$aId." AND weekNo = '".$weekNo."' AND clockYear = ".$clockYear);
				$id = mysqli_fetch_assoc($track_id);
				$newId = $id['clockId'];

				// Add the New Manual Time Entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											projectId,
											adminId,
											entryDate,
											startTime,
											endTime,
											entryType
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('sssssss',
									$newId,
									$pId,
									$aId,
									$entryDate,
									$startTime,
									$endTime,
									$entryType
				);
				$stmt->execute();
				$msgBox = alertBox($timeEntrySavedMsg, "<i class='fa fa-check-square'></i>", "success");
				// Clear the Form of values
				$_POST['dateIn'] = $_POST['timeIn'] = $_POST['dateOut'] = $_POST['timeOut'] = '';
				$stmt->close();
			}
		}
	}

	// Clock a Manager Out
	if (isset($_POST['submit']) && $_POST['submit'] == 'stopClock') {
		$clockId = $mysqli->real_escape_string($_POST['clockId']);
		$entryId = $mysqli->real_escape_string($_POST['entryId']);
		$endTime = date("Y-m-d H:i:s");

		// Stop Clock - Update the timeclock Record
		$sqlstmt = $mysqli->prepare("
							UPDATE
								timeclock
							SET
								running = 0
							WHERE
								clockId = ?
		");
		$sqlstmt->bind_param('s',$clockId);
		$sqlstmt->execute();
		$sqlstmt->close();

		// Stop Clock - Update the time entry
		$stmt = $mysqli->prepare("
							UPDATE
								timeentry
							SET
								endTime = ?
							WHERE
								entryId = ?
		");
		$stmt->bind_param('ss',
							$endTime,
							$entryId
		);
		$stmt->execute();
		$msgBox = alertBox($mngrClockedOutMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();

	}

	// Get a list of all the Years
	$a = "SELECT clockYear FROM timeclock GROUP BY clockYear";
	$b = mysqli_query($mysqli, $a) or die('-1'.mysqli_error());
	$yrs = array();
	// Set each Year in an array
	while($year = mysqli_fetch_assoc($b)) {
		$yrs[] = $year['clockYear'];
	}

	// Get a list of all the Managers
	$c = "SELECT adminId FROM admins WHERE isActive = 1";
	$d = mysqli_query($mysqli, $c) or die('-2' . mysqli_error());
	// Set each Manager in an array
	$mgrs = array();
	while($mgr = mysqli_fetch_assoc($d)) {
		$mgrs[] = $mgr['adminId'];
	}

	// Get Clocked In Managers
	$sqlStmt = "SELECT
					timeentry.entryId,
					timeentry.clockId,
					timeentry.projectId,
					timeentry.adminId,
					DATE_FORMAT(timeentry.startTime,'%M %d, %Y') AS dateStarted,
					DATE_FORMAT(timeentry.startTime,'%h:%i %p') AS hourStarted,
					timeclock.weekNo,
					timeclock.clockYear,
					timeclock.running,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clientprojects.projectName
				FROM
					timeentry
					LEFT JOIN timeclock ON timeentry.clockId = timeclock.clockId
					LEFT JOIN admins ON timeentry.adminId = admins.adminId
					LEFT JOIN clientprojects ON timeentry.projectId = clientprojects.projectId
				WHERE timeclock.running = 1
				GROUP BY timeentry.clockId";
	$smtres = mysqli_query($mysqli, $sqlStmt) or die('-3' . mysqli_error());

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
	<div class="contentAlt">
		<ul class="nav nav-tabs">
			<?php
				// Create the Year Tabs
				foreach ($yrs as $years) {
					// Set the Current Year Tab Button as Active
					if ($years == $currentYear) { $setActive = 'class="active"'; } else { $setActive = ''; }
			?>
					<li <?php echo $setActive; ?>><a href="#<?php echo $years; ?>" data-toggle="tab"><?php echo $years; ?></a></li>
			<?php
				}
			?>
			<li class="pull-right"><a  href="#addTime" data-toggle="modal"><?php echo $addTimeBtn; ?></a></li>
		</ul>
	</div>

	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="tab-content">
			<?php
				if (empty($yrs)) {
					echo '
							<div class="alertMsg default no-margin">
								<i class="fa fa-warning"></i> '.$noTimeFound.'
							</div>
						';
				}
				// Create the Tab Content
				foreach ($yrs as $year) {
					// Set the Current Year Tab as Active
					if ($year == $currentYear) { $inActive = ' in active'; } else { $inActive = ''; }
			?>
					<div class="tab-pane<?php echo $inActive; ?> no-padding" id="<?php echo $year; ?>">
						<div class="row">
							<div class="col-md-2">
								<nav class="nav-sidebar">
									<ul class="nav tabs">
										<?php
											$setActive = 'class="active"';
											foreach ($mgrs as $mgr) {
												// Get the Manager's name
												$e = "SELECT CONCAT(adminFirstName,' ',adminLastName) AS theAdmin FROM admins WHERE isActive = 1 AND adminId = ".$mgr;
												$f = mysqli_query($mysqli, $e) or die('-4' . mysqli_error());
												$row = mysqli_fetch_assoc($f);
										?>
												<li <?php echo $setActive; ?>><a href="#adminId<?php echo $year.$mgr; ?>" data-toggle="tab"><?php echo clean($row['theAdmin']); ?></a></li>
										<?php
												$setActive = '';
											}
										?>
									</ul>
								</nav>
							</div>
							<div class="col-md-10">
								<div class="tab-content vert-tabs">
									<?php
										$setIn = 'in active';
										foreach ($mgrs as $mgr) {
											// Get the Manager's name
											$g = "SELECT CONCAT(adminFirstName,' ',adminLastName) AS theAdmin FROM admins WHERE adminId = ".$mgr;
											$h = mysqli_query($mysqli, $g) or die('-5' . mysqli_error());
											$rows = mysqli_fetch_assoc($h);
									?>
											<div class="tab-pane vert-pane <?php echo $setIn; ?>" id="adminId<?php echo $year.$mgr; ?>">
												<h4><?php echo clean($rows['theAdmin']); ?></h4>
												<?php
													// Get the Week Numbers
													$i = "SELECT weekNo FROM timeclock WHERE adminId = ".$mgr." AND clockYear = ".$year." GROUP BY weekNo";
													$j = mysqli_query($mysqli, $i) or die('-6' . mysqli_error());

													// Set each year in an array
													$weeks = array();
													while($k = mysqli_fetch_assoc($j)) {
														$weeks[] = $k['weekNo'];
													}
													if (empty($weeks)) {
														echo '
																<div class="alertMsg default no-margin">
																	<i class="fa fa-warning"></i> '.$noTimeEntriesFor.' '.clean($rows['theAdmin']).'
																</div>
															';
													} else {
														echo '<dl class="accordion">';
														foreach ($weeks as $weekTab) {
												?>
															<dt><a><?php echo $weekText.' '.$weekTab; ?> &mdash; <?php echo $yearText.' '.$year; ?><span><i class="fa fa-angle-right"></i></span></a></dt>
															<dd class="hideIt">
																<?php
																	// Get Data
																	$sql = "SELECT
																				timeclock.clockId,
																				timeclock.adminId,
																				timeclock.weekNo,
																				timeclock.clockYear,
																				timeentry.entryId,
																				timeentry.projectId,
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
																				LEFT JOIN clientprojects ON timeentry.projectId = clientprojects.projectId
																				LEFT JOIN admins ON timeclock.adminId = admins.adminId
																			WHERE
																				timeclock.adminId = ".$mgr." AND
																				timeclock.weekNo = ".$weekTab." AND
																				clockYear = ".$year." AND
																				timeentry.endTime != '0000-00-00 00:00:00'
																			ORDER BY orderDate";
																	$res = mysqli_query($mysqli, $sql) or die('-7'.mysqli_error());

																	// Get the Total Time Worked
																	$qry = "SELECT
																				TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
																			FROM
																				timeclock
																				LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
																			WHERE
																				timeclock.adminId = ".$mgr." AND
																				timeclock.weekNo = ".$weekTab." AND
																				clockYear = ".$year." AND
																				timeentry.endTime != '0000-00-00 00:00:00'";
																	$result = mysqli_query($mysqli, $qry) or die('-8'.mysqli_error());
																	$times = array();
																	while ($u = mysqli_fetch_assoc($result)) {
																		$times[] = $u['diff'];
																	}
																	$totalTime = sumHours($times);
																?>
																<table class="rwd-table no-margin">
																	<tbody>
																		<tr>
																			<th><?php echo $projectText; ?></th>
																			<th><?php echo $yearText; ?></th>
																			<th><?php echo $dateInText; ?></th>
																			<th><?php echo $timeInText; ?></th>
																			<th><?php echo $dateOutText; ?></th>
																			<th><?php echo $timeOutText; ?></th>
																			<th><?php echo $hoursText; ?></th>
																			<th></th>
																		</tr>
																		<?php
																			while ($col = mysqli_fetch_assoc($res)) {
																				// Get the Time Total for each Time Entry
																				$tot = "SELECT timeentry.startTime, timeentry.endTime FROM timeentry WHERE entryId = ".$col['entryId'];
																				$results = mysqli_query($mysqli, $tot) or die('-9'.mysqli_error());
																				$rows = mysqli_fetch_assoc($results);

																				// Convert it to HH:MM
																				$from = new DateTime($rows['startTime']);
																				$to = new DateTime($rows['endTime']);
																				$lineTotal = $from->diff($to)->format('%h:%i');
																		?>
																				<tr>
																					<td data-th="Project Name">
																						<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
																							<a href="index.php?action=viewProject&projectId=<?php echo $col['projectId']; ?>"><?php echo clean($col['projectName']); ?></a>
																						</span>
																					</td>
																					<td data-th="<?php echo $yearText; ?>"><?php echo $col['clockYear']; ?></td>
																					<td data-th="<?php echo $dateInText; ?>"><?php echo $col['dateStarted']; ?></td>
																					<td data-th="<?php echo $timeInText; ?>"><?php echo $col['hourStarted']; ?></td>
																					<td data-th="<?php echo $dateOutText; ?>"><?php echo $col['dateEnded']; ?></td>
																					<td data-th="<?php echo $timeOutText; ?>"><?php echo $col['hourEnded']; ?></td>
																					<td data-th="<?php echo $hoursText; ?>"><?php echo $lineTotal; ?></td>
																					<td data-th="<?php echo $actionsText; ?>">
																						<a href="index.php?action=viewTime&entryId=<?php echo $col['entryId']; ?>">
																							<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $editTimeEntryTooltip; ?>"></i>
																						</a>
																						<a data-toggle="modal" href="#deleteTime<?php echo $col['entryId']; ?>">
																							<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteTimeEntryTooltip; ?>"></i>
																						</a>
																					</td>
																				</tr>

																				<div class="modal fade" id="deleteTime<?php echo $col['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
																					<div class="modal-dialog">
																						<div class="modal-content">
																							<form action="" method="post">
																								<div class="modal-body">
																									<p class="lead">
																										<?php echo $deleteTimeEntryConf1; ?><br /><?php echo $col['dateStarted']; ?> &mdash; <?php echo $totalHoursText.' '.$lineTotal; ?>?
																									</p>
																								</div>
																								<div class="modal-footer">
																									<input name="entryId" type="hidden" value="<?php echo $col['entryId']; ?>" />
																									<button type="input" name="submit" value="deleteTime" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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
																	sumHours($times);
																	echo '
																			<p class="mt20">
																				<span class="label label-default preview-label" data-toggle="tooltip" data-placement="right" title="hh:mm:ss">
																					Total: '.$totalTime.'
																				</span>
																			</p>
																		';
																?>
															</dd>
									<?php
														}
														echo '</dl>';
													}
												?>
											</div>
									<?php
											$setIn = '';
										}
									?>
								</div>
							</div>
						</div>
					</div>
			<?php
				}
			?>
		</div>
	</div>

	<div class="content last">
		<h4><?php echo $msgrsClockedInTitle; ?></h4>
		<?php if(mysqli_num_rows($smtres) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noMngrsClockedIn; ?>
			</div>
		<?php } else { ?>
			<p><?php echo $msgrsClockedInQuip; ?></p>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $managerText; ?></th>
						<th><?php echo $projectText; ?></th>
						<th><?php echo $weekNoText; ?></th>
						<th><?php echo $clockYearText; ?></th>
						<th><?php echo $dateInText; ?></th>
						<th><?php echo $timeInText; ?></th>
						<th></th>
					</tr>
					<?php while ($clk = mysqli_fetch_assoc($smtres)) { ?>
						<tr>
							<td data-th="<?php echo $managerText; ?>"><?php echo clean($clk['theAdmin']); ?></td>
							<td data-th="<?php echo $projectText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
									<a href="index.php?action=viewProject&projectId=<?php echo $clk['projectId']; ?>"><?php echo clean($clk['projectName']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $weekNoText; ?>"><?php echo $clk['weekNo']; ?></td>
							<td data-th="<?php echo $clockYearText; ?>"><?php echo $clk['clockYear']; ?></td>
							<td data-th="<?php echo $dateInText; ?>"><?php echo $clk['dateStarted']; ?></td>
							<td data-th="<?php echo $timeInText; ?>"><?php echo $clk['hourStarted']; ?></td>
							<td data-th="<?php echo $actionsText; ?>">
								<a data-toggle="modal" data-target="#stopClock<?php echo $clk['entryId']; ?>" class="btn btn-warning btn-xs btn-icon"><?php echo $clockMngrOutBtn; ?></a>
							</td>
						</tr>

						<div class="modal fade" id="stopClock<?php echo $clk['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $clockMngrOutConf1.' '.clean($clk['theAdmin']).' '.$clockMngrOutConf2; ?>?</p>
										</div>
										<div class="modal-footer">
											<input name="clockId" type="hidden" value="<?php echo $clk['clockId']; ?>" />
											<input name="entryId" type="hidden" value="<?php echo $clk['entryId']; ?>" />
											<button type="input" name="submit" value="stopClock" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>

	<div id="addTime" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $addMngrTimeModal; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<?php
										$getMgrs = "SELECT adminId, CONCAT(adminFirstName, ' ', adminLastName) as admin FROM admins WHERE isActive = 1";
										$mgrres = mysqli_query($mysqli, $getMgrs) or die('-10'.mysqli_error());
									?>
									<label for="aId"><?php echo $selectManageField; ?></label>
									<select class="form-control" name="aId">
										<option value="...">...</option>
										<?php while ($l = mysqli_fetch_assoc($mgrres)) { ?>
											<option value="<?php echo $l['adminId']; ?>"><?php echo clean($l['admin']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="pId"><?php echo $selectProjectField; ?></label>
									<select class="form-control" name="pId">
										<option value="...">...</option>
										<?php
											// Get the Project List
											$getProj = "SELECT
														clientprojects.projectId,
														clientprojects.projectName,
														clientprojects.archiveProj,
														CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
													FROM
														clientprojects
														LEFT JOIN clients ON clientprojects.clientId = clients.clientId
													WHERE archiveProj = 0
													ORDER BY clientprojects.projectId";
											$prejres = mysqli_query($mysqli, $getProj) or die('-11'.mysqli_error());
											while ($m = mysqli_fetch_assoc($prejres)) {
										?>
												<option value="<?php echo $m['projectId']; ?>"><?php echo clean($m['projectName']); ?> &mdash; <?php echo $clientText.': '.clean($m['theClient']); ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="dateIn"><?php echo $dateInText; ?></label>
									<input type="text" class="form-control" name="dateIn" id="dateIn" required="" value="<?php echo isset($_POST['dateIn']) ? $_POST['dateIn'] : ''; ?>" />
									<span class="help-block"><?php echo $dateFormat; ?></span>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="timeIn"><?php echo $timeInText; ?></label>
									<input type="text" class="form-control" name="timeIn" id="timeIn" value="<?php echo isset($_POST['timeIn']) ? $_POST['timeIn'] : ''; ?>" />
									<span class="help-block"><?php echo $timeFormat2; ?></span>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-lg-6">
								<div class="form-group">
									<label for="dateOut"><?php echo $dateOutText; ?></label>
									<input type="text" class="form-control" name="dateOut" id="dateOut" required="" value="<?php echo isset($_POST['dateOut']) ? $_POST['dateOut'] : ''; ?>" />
									<span class="help-block"><?php echo $dateFormat; ?></span>
								</div>
							</div>
							<div class="col-lg-6">
								<div class="form-group">
									<label for="timeOut"><?php echo $timeOutText; ?></label>
									<input type="text" class="form-control" name="timeOut" id="timeOut" value="<?php echo isset($_POST['timeOut']) ? $_POST['timeOut'] : ''; ?>" />
									<span class="help-block"><?php echo $timeFormat2; ?></span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="newEntry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveTimeEntryBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>