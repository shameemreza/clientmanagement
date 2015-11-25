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
    $smtRes = mysqli_query($mysqli, $sqlSmt) or die('-1'.mysqli_error());

	// Recent Discussions
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
				WHERE
					clientprojects.clientId = ".$clientId."
				ORDER BY
					orderDate DESC
				LIMIT 3";
	$res2 = mysqli_query($mysqli, $query2) or die('-2'.mysqli_error());

	// Recent Uploads
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
				WHERE
					clientprojects.clientId = ".$clientId."
				ORDER BY
					orderDate DESC
				LIMIT 3";
	$res3 = mysqli_query($mysqli, $query3) or die('-3'.mysqli_error());

	if ($set['enablePayments'] == '1') {
		// Get Payment Data
		$query4 = "SELECT
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
				WHERE
					clientprojects.clientId = ".$clientId."
				ORDER BY orderDate DESC
				LIMIT 5";
		$res4 = mysqli_query($mysqli, $query4) or die('-4'.mysqli_error());
	}

	include 'includes/navigation.php';
?>
<div class="content">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($smtRes) > 0) { ?>
		<div class="row">
			<div class="col-lg-6">
				<p><?php echo $dashboardWelcomeMsg; ?></p>
			</div>
			<div class="col-lg-6">
				<?php while ($row = mysqli_fetch_assoc($smtRes)) { ?>
					<h4 class="bg-info">
						<i class="fa fa-bullhorn"></i> <?php echo clean($row['alertTitle']); ?>
						<small class="pull-right text-white"><?php echo $row['alertDate']; ?></small>
					</h4>
					<p><?php echo nl2br(clean($row['alertText'])); ?></p>
				<?php } ?>
			</div>
		</div>
	<?php } else { ?>
		<p><?php echo $dashboardWelcomeMsg; ?></p>
	<?php } ?>
</div>

<div class="contentAlt">
	<div class="row">
		<div class="col-lg-6">
			<div class="content no-margin">
				<h3><?php echo $dbRecentDisc; ?></h3>
				<?php
					if(mysqli_num_rows($res2) > 0) {
						while ($disc = mysqli_fetch_assoc($res2)) {
				?>
							<div class="well well-xs comments">
								<?php if ($disc['adminId'] == '0') { ?>
									<img src="<?php echo $avatarDir.$disc['clientAvatar']; ?>" alt="<?php echo clean($disc['theClient']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($disc['theClient']); ?>" />
								<?php } else { ?>
									<img src="<?php echo $avatarDir.$disc['adminAvatar']; ?>" alt="<?php echo clean($disc['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($disc['theAdmin']); ?>" />
								<?php } ?>
								<h4>
									<a href="index.php?page=viewDiscussion&discussionId=<?php echo $disc['discussionId']; ?>"><?php echo ellipsis($disc['discussionTitle'],35); ?></a>
									<small class="pull-right"><?php echo $disc['discussionDate']; ?></small>
								</h4>
								<p><?php echo ellipsis($disc['discussionText'],160); ?></p>
							</div>
				<?php
						}
					} else {
				?>
					<div class="alertMsg default no-margin">
						<i class="fa fa-minus-square-o"></i> <?php echo $noDiscFound; ?>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="content no-margin">
				<h3><?php echo $dbRecentUplds; ?></h3>
				<?php
					if(mysqli_num_rows($res3) > 0) {
						while ($file = mysqli_fetch_assoc($res3)) {
				?>
							<div class="well well-xs comments">
								<?php if ($file['adminId'] == '0') { ?>
									<img src="<?php echo $avatarDir.$file['clientAvatar']; ?>" alt="<?php echo clean($file['theClient']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($file['theClient']); ?>" />
								<?php } else { ?>
									<img src="<?php echo $avatarDir.$file['adminAvatar']; ?>" alt="<?php echo clean($file['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($file['theAdmin']); ?>" />
								<?php } ?>
								<h4>
									<a href="index.php?page=viewFile&fileId=<?php echo $file['fileId']; ?>"><?php echo ellipsis($file['fileTitle'],35); ?></a>
									<small class="pull-right">Project: <?php echo clean($file['projectName']); ?></small>
								</h4>
								<p><?php echo ellipsis($file['fileDesc'],160); ?></p>
							</div>
				<?php
						}
					} else {
				?>
					<div class="alertMsg default no-margin">
						<i class="fa fa-minus-square-o"></i> <?php echo $noRecentUplds; ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<?php
	if ($set['enablePayments'] == '1') {
		if(mysqli_num_rows($res4) < 1) {
?>
			<div class="content last">
				<h3><?php echo $dbRecentPymnts; ?></h3>
				<div class="alertMsg default no-margin">
					<i class="fa fa-info-circle"></i> <?php echo $noRecentPymnts; ?>
				</div>
			</div>
<?php
		} else {
?>
			<div class="content last">
				<h3><?php echo $dbRecentPymnts; ?></h3>
				<table class="rwd-table">
					<tbody>
						<tr>
							<th class="text-left"><?php echo $projectTableHead; ?></th>
							<th><?php echo $paymentDateTableHead; ?></th>
							<th><?php echo $receivedByTableHead; ?></th>
							<th><?php echo $paymentAmountTableHead; ?></th>
							<th><?php echo $feeAmountTableHead; ?></th>
							<th><?php echo $totalPaidTableHead; ?></th>
							<th></th>
						</tr>
						<?php
							while ($line = mysqli_fetch_assoc($res4)) {
								// Format the Amounts
								$paymentAmount = $curSym.format_amount($line['paymentAmount'], 2);
								if ($line['additionalFee'] != '') { $additionalFee = $curSym.format_amount($line['additionalFee'], 2); $highlight = 'class="text-danger"'; } else { $additionalFee = ''; $highlight = ''; }
								$totreceived = $line['paymentAmount'] + $line['additionalFee'];
								$totalPaid = $curSym.format_amount($totreceived, 2);

						?>
								<tr>
									<td class="text-left" data-th="<?php echo $projectTableHead; ?>">
										<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
											<a href="index.php?page=viewProject&projectId=<?php echo $line['projectId']; ?>"><?php echo clean($line['projectName']); ?></a>
										</span>
									</td>
									<td data-th="<?php echo $paymentDateTableHead; ?>"><?php echo $line['paymentDate']; ?></td>
									<td data-th="<?php echo $receivedByTableHead; ?>"><?php echo clean($line['receivedBy']); ?></td>
									<td data-th="<?php echo $paymentAmountTableHead; ?>">
										<span data-toggle="tooltip" data-placement="left" title="<?php echo clean($line['paymentNotes']); ?>">
											<?php echo $paymentAmount; ?>
										</span>
									</td>
									<td <?php echo $highlight; ?> data-th="<?php echo $feeAmountTableHead; ?>"><?php echo $additionalFee; ?></td>
									<td data-th="<?php echo $totalPaidTableHead; ?>"><?php echo $totalPaid; ?></td>
									<td class="text-right" data-th="Invoice">
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $printReceiptTooltip; ?>">
											<a href="index.php?page=receipt&paymentId=<?php echo $line['paymentId']; ?>"><i class="fa fa-print print"></i></a>
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
?>