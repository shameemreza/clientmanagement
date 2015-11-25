<?php
	// Get Site Alerts
    $sqlSmt  = "SELECT
					alertTitle,
					alertText,
					DATE_FORMAT(alertDate,'%M %d, %Y') AS alertDate,
					UNIX_TIMESTAMP(alertDate) AS orderDate,
					alertStart,
					alertExpires
				FROM
					sitealerts
				WHERE
					alertStart <= DATE_SUB(CURDATE(),INTERVAL 0 DAY) AND
					alertExpires >= DATE_SUB(CURDATE(),INTERVAL 0 DAY) OR
					isActive = 1
				ORDER BY
					orderDate";
    $smtRes = mysqli_query($mysqli, $sqlSmt) or die('-1' . mysqli_error());

	// Get Total Time Worked for the Current Week
	if ($isAdmin == '1') {
		// Get all Time Worked
		$checktime = $mysqli->query("SELECT 'X' FROM timeclock WHERE weekNo = '".$weekNum."'");
		if ($checktime->num_rows) {
			$sqlstmt1 = "SELECT
							TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
						FROM
							timeclock
							LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
						WHERE
							timeclock.weekNo = '".$weekNum."' AND
							timeclock.clockYear = '".$currentYear."' AND
							timeentry.endTime != '0000-00-00 00:00:00'";
			$sqlres1 = mysqli_query($mysqli, $sqlstmt1) or die('-2'.mysqli_error());
			$times = array();
			while ($u = mysqli_fetch_assoc($sqlres1)) {
				$times[] = $u['diff'];
			}
			$totalTime = sumHours($times);
		} else {
			$totalTime = '00:00:00';
		}
	} else {
		// Get Total Time for the logged in Manager
		$checktime = $mysqli->query("SELECT 'X' FROM timeclock WHERE adminId = ".$adminId." AND weekNo = '".$weekNum."'");
		if ($checktime->num_rows) {
			$sqlstmt1 = "SELECT
							TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
						FROM
							timeclock
							LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
						WHERE
							timeclock.adminId = ".$adminId." AND
							timeclock.weekNo = '".$weekNum."' AND
							timeclock.clockYear = '".$currentYear."' AND
							timeentry.endTime != '0000-00-00 00:00:00'";
			$sqlres1 = mysqli_query($mysqli, $sqlstmt1) or die('-3'.mysqli_error());
			$times = array();
			while ($u = mysqli_fetch_assoc($sqlres1)) {
				$times[] = $u['diff'];
			}
			$totalTime = sumHours($times);
		} else {
			$totalTime = '00:00:00';
		}
	}

	if ($isAdmin == '1') {
		// Get Total Project Count
		$projcountsql = "SELECT
							'X'
						FROM
							clientprojects
						WHERE
							archiveProj = 0";
		$projcounttotal = mysqli_query($mysqli, $projcountsql) or die('-4'.mysqli_error());
		$projcount = mysqli_num_rows($projcounttotal);
		$projBoxText = $projBoxText1;
		$projTotalTooltip = $projTotalTooltip1;
	} else {
		// Get Assigned Project Count
		$projcountsql = "SELECT
							'X'
						FROM
							clientprojects
							LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
						WHERE
							assignedprojects.assignedTo = ".$adminId." AND clientprojects.archiveProj = 0";
		$projcounttotal = mysqli_query($mysqli, $projcountsql) or die('-5'.mysqli_error());
		$projcount = mysqli_num_rows($projcounttotal);
		$projBoxText = $projBoxText2;
		$projTotalTooltip = $projTotalTooltip2;
	}

	// Recent Discussions
	if ($isAdmin == '1') {
		$query2 = "SELECT
						projectdiscus.discussionId,
						projectdiscus.adminId,
						projectdiscus.clientId,
						projectdiscus.projectId,
						projectdiscus.discussionTitle,
						projectdiscus.discussionText,
						DATE_FORMAT(projectdiscus.discussionDate,'%b %d %Y') AS discussionDate,
						UNIX_TIMESTAMP(projectdiscus.discussionDate) AS orderDate,
						clientprojects.clientId,
						CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
						clients.clientAvatar,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
						admins.adminAvatar
					FROM
						projectdiscus
						LEFT JOIN clientprojects ON projectdiscus.projectId = clientprojects.projectId
						LEFT JOIN clients ON projectdiscus.clientId = clients.clientId
						LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
					ORDER BY
						orderDate DESC
					LIMIT 3";
		$res2 = mysqli_query($mysqli, $query2) or die('-6'.mysqli_error());
	} else {
		$query2 = "SELECT
						projectdiscus.discussionId,
						projectdiscus.adminId,
						projectdiscus.clientId,
						projectdiscus.projectId,
						projectdiscus.discussionTitle,
						projectdiscus.discussionText,
						DATE_FORMAT(projectdiscus.discussionDate,'%b %d %Y') AS discussionDate,
						UNIX_TIMESTAMP(projectdiscus.discussionDate) AS orderDate,
						clientprojects.clientId,
						CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
						clients.clientAvatar,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
						admins.adminAvatar
					FROM
						projectdiscus
						LEFT JOIN clientprojects ON projectdiscus.projectId = clientprojects.projectId
						LEFT JOIN clients ON projectdiscus.clientId = clients.clientId
						LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
						LEFT JOIN assignedprojects ON projectdiscus.projectId = assignedprojects.projectId
					WHERE
						assignedprojects.assignedTo = ".$adminId."
					ORDER BY
						orderDate DESC
					LIMIT 3";
		$res2 = mysqli_query($mysqli, $query2) or die('-7'.mysqli_error());
	}

	// Recent Uploads
	if ($isAdmin == '1') {
		$query3 = "SELECT
						projectfiles.fileId,
						projectfiles.folderId,
						projectfiles.projectId,
						projectfiles.adminId,
						projectfiles.clientId,
						projectfiles.fileTitle,
						projectfiles.fileDesc,
						UNIX_TIMESTAMP(projectfiles.fileDate) AS orderDate,
						CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
						clients.clientAvatar,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
						admins.adminAvatar,
						clientprojects.clientId,
						clientprojects.projectName
					FROM
						projectfiles
						LEFT JOIN clients ON projectfiles.clientId = clients.clientId
						LEFT JOIN admins ON projectfiles.adminId = admins.adminId
						LEFT JOIN clientprojects ON projectfiles.projectId = clientprojects.projectId
						LEFT JOIN assignedprojects ON projectfiles.projectId = assignedprojects.projectId
					ORDER BY
						orderDate DESC
					LIMIT 3";
		$res3 = mysqli_query($mysqli, $query3) or die('-8'.mysqli_error());
	} else {
		$query3 = "SELECT
						projectfiles.fileId,
						projectfiles.folderId,
						projectfiles.projectId,
						projectfiles.adminId,
						projectfiles.clientId,
						projectfiles.fileTitle,
						projectfiles.fileDesc,
						UNIX_TIMESTAMP(projectfiles.fileDate) AS orderDate,
						CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
						clients.clientAvatar,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
						admins.adminAvatar,
						clientprojects.clientId,
						clientprojects.projectName
					FROM
						projectfiles
						LEFT JOIN clients ON projectfiles.clientId = clients.clientId
						LEFT JOIN admins ON projectfiles.adminId = admins.adminId
						LEFT JOIN clientprojects ON projectfiles.projectId = clientprojects.projectId
						LEFT JOIN assignedprojects ON projectfiles.projectId = assignedprojects.projectId
					WHERE
						assignedprojects.assignedTo = ".$adminId."
					ORDER BY
						orderDate DESC
					LIMIT 3";
		$res3 = mysqli_query($mysqli, $query3) or die('-9'.mysqli_error());
	}

	if ($isAdmin == '1') {
		if ($set['enablePayments'] == '1') {
			// Get Payment Data
			$qry = "SELECT
						projectpayments.paymentId,
						projectpayments.projectId,
						projectpayments.enteredBy,
						projectpayments.paymentFor,
						DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
						UNIX_TIMESTAMP(projectpayments.paymentDate) AS orderDate,
						projectpayments.paidBy,
						projectpayments.paymentAmount,
						projectpayments.additionalFee,
						projectpayments.paymentNotes,
						clientprojects.projectName,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS receivedBy
					FROM
						projectpayments
						LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
						LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
					ORDER BY orderDate DESC
					LIMIT 5";
			$payres = mysqli_query($mysqli, $qry) or die('-10'.mysqli_error());
		}
	}

	include 'includes/navigation.php';
?>
<div class="content">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php
		if(mysqli_num_rows($smtRes) > 0) {
			$padIt = '';
	?>
			<div class="row">
				<div class="col-lg-6">
					<p><?php echo $dashboardWelcomeMsg; ?></p>
				</div>
				<div class="col-lg-6">
					<?php while ($row = mysqli_fetch_assoc($smtRes)) { ?>
						<h4 class="bg-info <?php echo $padIt; ?>">
							<i class="fa fa-bullhorn"></i> <?php echo clean($row['alertTitle']); ?>
							<small class="pull-right text-white"><?php echo $row['alertDate']; ?></small>
						</h4>
						<p><?php echo nl2br(clean($row['alertText'])); ?></p>
					<?php
						$padIt = 'mt10';
						}
					?>
				</div>
			</div>
	<?php } else { ?>
		<p><?php echo $dashboardWelcomeMsg; ?></p>
	<?php } ?>
</div>

<div class="contentAlt">
	<div class="row">
		<?php if ($isAdmin == '1') { ?>
			<div class="col-lg-3">
				<div class="small-box bg-white">
					<div class="inner" data-toggle="tooltip" data-placement="top" title="<?php echo $managerHoursText; ?>">
						<h3 class="text-orange"><?php echo $totalTime; ?></h3>
						<p><?php echo $workedTitle; ?></p>
					</div>
					<div class="icon icon-sm"><i class="fa fa-clock-o"></i></div>
					<a href="index.php?action=timeTracking" class="small-box-footer"><?php echo $viewTimeLogsText ?> <i class="fa fa-long-arrow-right"></i></a>
				</div>
			</div>
		<?php } else { ?>
			<div class="col-lg-3">
				<div class="small-box bg-white">
					<div class="inner" data-toggle="tooltip" data-placement="top" title="<?php echo $allHoursText; ?>">
						<h3 class="text-orange"><?php echo $totalTime; ?></h3>
						<p><?php echo $workedTitle; ?></p>
					</div>
					<div class="icon icon-sm"><i class="fa fa-clock-o"></i></div>
					<a href="index.php?action=myTimecards" class="small-box-footer"><?php echo $viewTimeLogsText; ?> <i class="fa fa-long-arrow-right"></i></a>
				</div>
			</div>
		<?php } ?>
		<div class="col-lg-3">
			<div class="small-box bg-white">
				<div class="inner">
					<h3 class="text-orange"><?php echo $unread; ?></h3>
					<p><?php echo $unreadMsgText; ?></p>
				</div>
				<div class="icon icon-sm"><i class="fa fa-envelope-o"></i></div>
				<a href="index.php?action=inbox" class="small-box-footer"><?php echo $viewMsgText; ?> <i class="fa fa-long-arrow-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="small-box bg-white">
				<div class="inner">
					<h3 class="text-orange"><?php echo $taskcount; ?></h3>
					<p><?php echo $assignedTasksText; ?></p>
				</div>
				<div class="icon"><i class="fa fa-tasks"></i></div>
				<a href="index.php?action=personalTasks" class="small-box-footer"><?php echo $viewTasksText; ?> <i class="fa fa-long-arrow-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="small-box bg-white">
				<div class="inner" data-toggle="tooltip" data-placement="top" title="<?php echo $projTotalTooltip; ?>">
					<h3 class="text-orange"><?php echo $projcount; ?></h3>
					<p><?php echo $projBoxText; ?></p>
				</div>
				<div class="icon"><i class="fa fa-folder-open-o"></i></div>
				<a href="index.php?action=openProjects" class="small-box-footer"><?php echo $viewProjectsText; ?> <i class="fa fa-long-arrow-right"></i></a>
			</div>
		</div>
	</div>
</div>

<div class="contentAlt no-margin">
	<div class="row">
		<div class="col-lg-6">
			<div class="content no-margin">
				<h3><?php echo $recentDiscText; ?></h3>
				<?php
					if(mysqli_num_rows($res2) > 0) {
						while ($disc = mysqli_fetch_assoc($res2)) {
				?>
							<div class="well well-xs comments">
								<?php if ($disc['adminId'] == '0') { ?>
									<img src="<?php echo '../'.$avatarDir.$disc['clientAvatar']; ?>" alt="<?php echo clean($disc['theClient']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($disc['theClient']); ?>" />
								<?php } else { ?>
									<img src="<?php echo '../'.$avatarDir.$disc['adminAvatar']; ?>" alt="<?php echo clean($disc['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($disc['theAdmin']); ?>" />
								<?php } ?>
								<h4>
									<a href="index.php?action=viewDiscussion&discussionId=<?php echo $disc['discussionId']; ?>"><?php echo ellipsis($disc['discussionTitle'],35); ?></a>
									<small class="pull-right"><?php echo $disc['discussionDate']; ?></small>
								</h4>
								<p><?php echo ellipsis($disc['discussionText'],160); ?></p>
							</div>
				<?php
						}
					} else {
				?>
					<div class="alertMsg default no-margin">
						<i class="fa fa-minus-square-o"></i> <?php echo $noDiscMsg; ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="content no-margin">
				<h3><?php echo $recentUploadsText; ?></h3>
				<?php
					if(mysqli_num_rows($res3) > 0) {
						while ($file = mysqli_fetch_assoc($res3)) {
				?>
							<div class="well well-xs comments">
								<?php if ($file['adminId'] == '0') { ?>
									<img src="<?php echo '../'.$avatarDir.$file['clientAvatar']; ?>" alt="<?php echo clean($file['theClient']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($file['theClient']); ?>" />
								<?php } else { ?>
									<img src="<?php echo '../'.$avatarDir.$file['adminAvatar']; ?>" alt="<?php echo clean($file['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($file['theAdmin']); ?>" />
								<?php } ?>
								<h4>
									<a href="index.php?action=viewFile&fileId=<?php echo $file['fileId']; ?>"><?php echo ellipsis($file['fileTitle'],35); ?></a>
									<small class="pull-right"><?php echo $projectText; ?>: <?php echo clean($file['projectName']); ?></small>
								</h4>
								<p><?php echo ellipsis($file['fileDesc'],160); ?></p>
							</div>
				<?php
						}
					} else {
				?>
					<div class="alertMsg default no-margin">
						<i class="fa fa-minus-square-o"></i> <?php echo $noUploadsMsg; ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php
	if ($isAdmin == '1') {
		if ($set['enablePayments'] == '1') {
			if(mysqli_num_rows($payres) < 1) {
?>
				<div class="content last">
					<h3><?php echo $recentPymtsRecvd; ?></h3>
					<div class="alertMsg default no-margin">
						<i class="fa fa-info-circle"></i> <?php echo $noRecentPymntsMsg; ?>
					</div>
				</div>
<?php
			} else {
?>
				<div class="content last">
					<h3><?php echo $recentPymtsRecvd; ?></h3>
					<table class="rwd-table">
						<tbody>
							<tr>
								<th class="text-left"><?php echo $projectText; ?></th>
								<th><?php echo $paymentDateText; ?></th>
								<th><?php echo $receivedByText; ?></th>
								<th><?php echo $paymentAmtText; ?></th>
								<th><?php echo $feeAmtText; ?></th>
								<th><?php echo $totalPaidText; ?></th>
								<th></th>
							</tr>
							<?php
								while ($line = mysqli_fetch_assoc($payres)) {
									// Format the Amounts
									$paymentAmount = $curSym.format_amount($line['paymentAmount'], 2);
									if ($line['additionalFee'] != '') { $additionalFee = $curSym.format_amount($line['additionalFee'], 2); $highlight = 'class="text-danger"'; } else { $additionalFee = ''; $highlight = ''; }
									$totreceived = $line['paymentAmount'] + $line['additionalFee'];
									$totalPaid = $curSym.format_amount($totreceived, 2);

							?>
									<tr>
										<td class="text-left" data-th="<?php echo $projectText; ?>">
											<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProjectText; ?>">
												<a href="index.php?action=viewProject&projectId=<?php echo $line['projectId']; ?>"><?php echo clean($line['projectName']); ?></a>
											</span>
										</td>
										<td data-th="<?php echo $paymentDateText; ?>"><?php echo $line['paymentDate']; ?></td>
										<td data-th="<?php echo $receivedByText; ?>"><?php echo clean($line['receivedBy']); ?></td>
										<td data-th="<?php echo $paymentAmtText; ?>">
											<span data-toggle="tooltip" data-placement="left" title="<?php echo clean($line['paymentNotes']); ?>">
												<?php echo $paymentAmount; ?>
											</span>
										</td>
										<td <?php echo $highlight; ?> data-th="<?php echo $feeAmtText; ?>"><?php echo $additionalFee; ?></td>
										<td data-th="<?php echo $totalPaidText; ?>"><?php echo $totalPaid; ?></td>
										<td class="text-right" data-th="Invoice">
											<span data-toggle="tooltip" data-placement="left" title="<?php echo $printReceiptTooltip; ?>">
												<a href="index.php?action=receipt&paymentId=<?php echo $line['paymentId']; ?>"><i class="fa fa-print print"></i></a>
											</span>
										</td>
									</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
<?php
			}
		}
	} else {
		echo '<div class="clearfix mt20"></div>';
	}
?>