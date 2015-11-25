<?php
	$searchTerm = $mysqli->real_escape_string($_POST['searchTerm']);
	$searchUC = strtolower($searchTerm);
	$searchLC = strtoupper($searchTerm);

	// Search Data
	$qry1 = "SELECT
				clientId,
				clientEmail,
				clientFirstName,
				clientLastName,
				CONCAT(clientFirstName,' ',clientLastName) AS theClient,
				clientCompany,
				clientBio,
				clientPhone,
				clientAvatar
			FROM
				clients
			WHERE
				isActive = 1 AND
				(clientFirstName LIKE '%".$searchTerm."%' OR clientLastName LIKE '%".$searchTerm."%' OR clientCompany LIKE '%".$searchTerm."%' OR
				clientFirstName LIKE '%".$searchUC."%' OR clientLastName LIKE '%".$searchUC."%' OR clientCompany LIKE '%".$searchUC."%' OR
				clientFirstName LIKE '%".$searchLC."%' OR clientLastName LIKE '%".$searchLC."%' OR clientCompany LIKE '%".$searchLC."%')
			GROUP BY clientId
			ORDER BY clientId";
	$res1 = mysqli_query($mysqli, $qry1) or die('-1'.mysqli_error());
	$rowstot1 = mysqli_num_rows($res1);

	if ($isAdmin == '1') {
		$qry2 = "SELECT
					clientprojects.projectId,
					clientprojects.projectName,
					clientprojects.projectDeatils,
					DATE_FORMAT(clientprojects.dueDate,'%M %e, %Y') AS dueDate,
					UNIX_TIMESTAMP(clientprojects.dueDate) AS orderDate,
					assignedprojects.assignedTo,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientId,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					clientprojects
					LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
					LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
					LEFT JOIN clients ON clientprojects.clientId = clients.clientId
				WHERE
					clientprojects.archiveProj = 0 AND
					(clientprojects.projectName LIKE '%".$searchTerm."%' OR clientprojects.projectDeatils LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR  clients.clientLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR  admins.adminLastName LIKE '%".$searchTerm."%' OR
					clientprojects.projectName LIKE '%".$searchUC."%' OR clientprojects.projectDeatils LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR  clients.clientLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR  admins.adminLastName LIKE '%".$searchUC."%' OR
					clientprojects.projectName LIKE '%".$searchLC."%' OR clientprojects.projectDeatils LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR  clients.clientLastName LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR  admins.adminLastName LIKE '%".$searchLC."%')
				ORDER BY orderDate, clientprojects.clientId";
		$res2 = mysqli_query($mysqli, $qry2) or die('-2' . mysqli_error());
		$rowstot2 = mysqli_num_rows($res2);

		$qry3 = "SELECT
					projectdiscus.discussionId,
					projectdiscus.projectId,
					projectdiscus.adminId,
					projectdiscus.clientId,
					projectdiscus.discussionTitle,
					projectdiscus.discussionText,
					DATE_FORMAT(projectdiscus.discussionDate,'%W, %M %e, %Y') AS discussionDate,
					UNIX_TIMESTAMP(projectdiscus.discussionDate) AS orderDate,
					DATE_FORMAT(projectdiscus.lastUpdated,'%W, %M %e, %Y') AS lastUpdated,
					clientprojects.projectName,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					projectdiscus
					LEFT JOIN clientprojects ON projectdiscus.projectId = clientprojects.projectId
					LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
					LEFT JOIN clients ON projectdiscus.clientId = clients.clientId
				WHERE
					(projectdiscus.discussionTitle LIKE '%".$searchTerm."%' OR projectdiscus.discussionText LIKE '%".$searchTerm."%' OR
					projectdiscus.discussionTitle LIKE '%".$searchUC."%' OR projectdiscus.discussionText LIKE '%".$searchUC."%' OR
					projectdiscus.discussionTitle LIKE '%".$searchLC."%' OR projectdiscus.discussionText LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR admins.adminLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR admins.adminLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR admins.adminLastName LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR clients.clientLastName LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR clients.clientLastName LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR clients.clientLastName LIKE '%".$searchLC."%')
				ORDER BY orderDate";
		$res3 = mysqli_query($mysqli, $qry3) or die('-3'.mysqli_error());
		$rowstot3 = mysqli_num_rows($res3);

		$qry4 = "SELECT
					projectfiles.fileId,
					projectfiles.folderId,
					projectfiles.projectId,
					projectfiles.adminId,
					projectfiles.clientId,
					projectfiles.fileTitle,
					projectfiles.fileDesc,
					DATE_FORMAT(projectfiles.fileDate,'%M %d, %Y') AS fileDate,
					UNIX_TIMESTAMP(projectfiles.fileDate) AS orderDate,
					projectfolders.folderTitle,
					clientprojects.projectName,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					projectfiles
					LEFT JOIN projectfolders ON projectfiles.folderId = projectfolders.folderId
					LEFT JOIN clientprojects ON projectfiles.projectId = clientprojects.projectId
					LEFT JOIN admins ON projectfiles.adminId = admins.adminId
					LEFT JOIN clients ON projectfiles.clientId = clients.clientId
				WHERE
					(projectfiles.fileTitle LIKE '%".$searchTerm."%' OR projectfiles.fileDesc LIKE '%".$searchTerm."%' OR
					projectfiles.fileTitle LIKE '%".$searchUC."%' OR  projectfiles.fileDesc LIKE '%".$searchUC."%' OR
					projectfiles.fileTitle LIKE '%".$searchLC."%' OR  projectfiles.fileDesc LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR admins.adminLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR  admins.adminLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR  admins.adminLastName LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR clients.clientLastName LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR  clients.clientLastName LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR  clients.clientLastName LIKE '%".$searchLC."%' OR
					projectfolders.folderTitle LIKE '%".$searchTerm."%' OR clientprojects.projectName LIKE '%".$searchTerm."%' OR
					projectfolders.folderTitle LIKE '%".$searchUC."%' OR  clientprojects.projectName LIKE '%".$searchUC."%' OR
					projectfolders.folderTitle LIKE '%".$searchLC."%' OR  clientprojects.projectName LIKE '%".$searchLC."%')
				ORDER BY orderDate, projectfiles.folderId";
		$res4 = mysqli_query($mysqli, $qry4) or die('-4'.mysqli_error());
		$rowstot4 = mysqli_num_rows($res4);

		$qry5 = "SELECT
					tasks.taskId,
					tasks.projectId,
					tasks.adminId,
					tasks.taskTitle,
					tasks.taskDesc,
					tasks.taskPriority,
					tasks.taskStatus,
					DATE_FORMAT(tasks.taskStart,'%M %d, %Y') AS startDate,
					DATE_FORMAT(tasks.taskDue,'%M %d, %Y') AS dueDate,
					UNIX_TIMESTAMP(tasks.taskDue) AS orderDate,
					clientprojects.projectName,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientId,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					tasks
					LEFT JOIN clientprojects ON tasks.projectId = clientprojects.projectId
					LEFT JOIN admins ON tasks.adminId = admins.adminId
					LEFT JOIN clients ON clientprojects.clientId = clients.clientId
				WHERE
					tasks.projectId != 0 AND tasks.isClosed = 0 AND tasks.adminId = ".$adminId." AND
					(tasks.taskTitle LIKE '%".$searchTerm."%' OR tasks.taskDesc LIKE '%".$searchTerm."%' OR
					tasks.taskTitle LIKE '%".$searchUC."%' OR  tasks.taskDesc LIKE '%".$searchUC."%' OR
					tasks.taskTitle LIKE '%".$searchLC."%' OR  tasks.taskDesc LIKE '%".$searchLC."%' OR
					clientprojects.projectName LIKE '%".$searchTerm."%' OR
					clientprojects.projectName LIKE '%".$searchUC."%' OR
					clientprojects.projectName LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR admins.adminLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR  admins.adminLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR  admins.adminLastName LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR clients.clientLastName LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR  clients.clientLastName LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR  clients.clientLastName LIKE '%".$searchLC."%')
				ORDER BY
					orderDate";
		$res5 = mysqli_query($mysqli, $qry5) or die('-5'.mysqli_error());
		$rowstot5 = mysqli_num_rows($res5);
	} else {
		$qry2 = "SELECT
					clientprojects.projectId,
					clientprojects.clientId,
					clientprojects.projectName,
					clientprojects.projectDeatils,
					DATE_FORMAT(clientprojects.dueDate,'%M %e, %Y') AS dueDate,
					UNIX_TIMESTAMP(clientprojects.dueDate) AS orderDate,
					assignedprojects.assignedTo,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					clientprojects
					LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
					LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
					LEFT JOIN clients ON clientprojects.clientId = clients.clientId
				WHERE
					clientprojects.archiveProj = 0 AND
					assignedprojects.assignedTo = ".$adminId." AND
					(clientprojects.projectName LIKE '%".$searchTerm."%' OR clientprojects.projectDeatils LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR  clients.clientLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR  admins.adminLastName LIKE '%".$searchTerm."%' OR
					clientprojects.projectName LIKE '%".$searchUC."%' OR clientprojects.projectDeatils LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR  clients.clientLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR  admins.adminLastName LIKE '%".$searchUC."%' OR
					clientprojects.projectName LIKE '%".$searchLC."%' OR clientprojects.projectDeatils LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR  clients.clientLastName LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR  admins.adminLastName LIKE '%".$searchLC."%')
				ORDER BY orderDate, clientprojects.clientId";
		$res2 = mysqli_query($mysqli, $qry2) or die('-6' . mysqli_error());
		$rowstot2 = mysqli_num_rows($res2);

		$qry3 = "SELECT
					projectdiscus.discussionId,
					projectdiscus.projectId,
					projectdiscus.adminId,
					projectdiscus.clientId,
					projectdiscus.discussionTitle,
					projectdiscus.discussionText,
					DATE_FORMAT(projectdiscus.discussionDate,'%W, %M %e, %Y') AS discussionDate,
					UNIX_TIMESTAMP(projectdiscus.discussionDate) AS orderDate,
					DATE_FORMAT(projectdiscus.lastUpdated,'%W, %M %e, %Y') AS lastUpdated,
					clientprojects.projectName,
					assignedprojects.assignedTo,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					projectdiscus
					LEFT JOIN clientprojects ON projectdiscus.projectId = clientprojects.projectId
					LEFT JOIN assignedprojects ON projectdiscus.projectId = assignedprojects.projectId
					LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
					LEFT JOIN clients ON projectdiscus.clientId = clients.clientId
				WHERE
					assignedprojects.assignedTo = ".$adminId." AND
					(projectdiscus.discussionTitle LIKE '%".$searchTerm."%' OR projectdiscus.discussionText LIKE '%".$searchTerm."%' OR
					projectdiscus.discussionTitle LIKE '%".$searchUC."%' OR projectdiscus.discussionText LIKE '%".$searchUC."%' OR
					projectdiscus.discussionTitle LIKE '%".$searchLC."%' OR projectdiscus.discussionText LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR admins.adminLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR admins.adminLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR admins.adminLastName LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR clients.clientLastName LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR clients.clientLastName LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR clients.clientLastName LIKE '%".$searchLC."%')
				ORDER BY orderDate";
		$res3 = mysqli_query($mysqli, $qry3) or die('-7'.mysqli_error());
		$rowstot3 = mysqli_num_rows($res3);

		$qry4 = "SELECT
					projectfiles.fileId,
					projectfiles.folderId,
					projectfiles.projectId,
					projectfiles.adminId,
					projectfiles.clientId,
					projectfiles.fileTitle,
					projectfiles.fileDesc,
					DATE_FORMAT(projectfiles.fileDate,'%M %d, %Y') AS fileDate,
					UNIX_TIMESTAMP(projectfiles.fileDate) AS orderDate,
					projectfolders.folderTitle,
					clientprojects.projectName,
					assignedprojects.assignedTo,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					projectfiles
					LEFT JOIN projectfolders ON projectfiles.folderId = projectfolders.folderId
					LEFT JOIN clientprojects ON projectfiles.projectId = clientprojects.projectId
					LEFT JOIN assignedprojects ON projectfiles.projectId = assignedprojects.projectId
					LEFT JOIN admins ON projectfiles.adminId = admins.adminId
					LEFT JOIN clients ON projectfiles.clientId = clients.clientId
				WHERE
					assignedprojects.assignedTo = ".$adminId." AND
					(projectfiles.fileTitle LIKE '%".$searchTerm."%' OR projectfiles.fileDesc LIKE '%".$searchTerm."%' OR
					projectfiles.fileTitle LIKE '%".$searchUC."%' OR  projectfiles.fileDesc LIKE '%".$searchUC."%' OR
					projectfiles.fileTitle LIKE '%".$searchLC."%' OR  projectfiles.fileDesc LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR admins.adminLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR  admins.adminLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR  admins.adminLastName LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR clients.clientLastName LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR  clients.clientLastName LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR  clients.clientLastName LIKE '%".$searchLC."%' OR
					projectfolders.folderTitle LIKE '%".$searchTerm."%' OR clientprojects.projectName LIKE '%".$searchTerm."%' OR
					projectfolders.folderTitle LIKE '%".$searchUC."%' OR  clientprojects.projectName LIKE '%".$searchUC."%' OR
					projectfolders.folderTitle LIKE '%".$searchLC."%' OR  clientprojects.projectName LIKE '%".$searchLC."%')
				ORDER BY orderDate, projectfiles.folderId";
		$res4 = mysqli_query($mysqli, $qry4) or die('-8'.mysqli_error());
		$rowstot4 = mysqli_num_rows($res4);

		$qry5 = "SELECT
					tasks.taskId,
					tasks.projectId,
					tasks.adminId,
					tasks.taskTitle,
					tasks.taskDesc,
					tasks.taskPriority,
					tasks.taskStatus,
					DATE_FORMAT(tasks.taskStart,'%M %d, %Y') AS startDate,
					DATE_FORMAT(tasks.taskDue,'%M %d, %Y') AS dueDate,
					UNIX_TIMESTAMP(tasks.taskDue) AS orderDate,
					clientprojects.clientId,
					clientprojects.projectName,
					assignedprojects.assignedTo,
					admins.adminFirstName,
					admins.adminLastName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					clients.clientFirstName,
					clients.clientLastName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					tasks
					LEFT JOIN clientprojects ON tasks.projectId = clientprojects.projectId
					LEFT JOIN assignedprojects ON tasks.projectId = assignedprojects.projectId
					LEFT JOIN admins ON tasks.adminId = admins.adminId
					LEFT JOIN clients ON clientprojects.clientId = clients.clientId
				WHERE
					tasks.projectId != 0 AND tasks.isClosed = 0 AND tasks.adminId = ".$adminId." AND assignedprojects.assignedTo = ".$adminId." AND
					(tasks.taskTitle LIKE '%".$searchTerm."%' OR tasks.taskDesc LIKE '%".$searchTerm."%' OR
					tasks.taskTitle LIKE '%".$searchUC."%' OR  tasks.taskDesc LIKE '%".$searchUC."%' OR
					tasks.taskTitle LIKE '%".$searchLC."%' OR  tasks.taskDesc LIKE '%".$searchLC."%' OR
					clientprojects.projectName LIKE '%".$searchTerm."%' OR
					clientprojects.projectName LIKE '%".$searchUC."%' OR
					clientprojects.projectName LIKE '%".$searchLC."%' OR
					admins.adminFirstName LIKE '%".$searchTerm."%' OR admins.adminLastName LIKE '%".$searchTerm."%' OR
					admins.adminFirstName LIKE '%".$searchUC."%' OR  admins.adminLastName LIKE '%".$searchUC."%' OR
					admins.adminFirstName LIKE '%".$searchLC."%' OR  admins.adminLastName LIKE '%".$searchLC."%' OR
					clients.clientFirstName LIKE '%".$searchTerm."%' OR clients.clientLastName LIKE '%".$searchTerm."%' OR
					clients.clientFirstName LIKE '%".$searchUC."%' OR  clients.clientLastName LIKE '%".$searchUC."%' OR
					clients.clientFirstName LIKE '%".$searchLC."%' OR  clients.clientLastName LIKE '%".$searchLC."%')
				ORDER BY
					orderDate";
		$res5 = mysqli_query($mysqli, $qry5) or die('-9'.mysqli_error());
		$rowstot5 = mysqli_num_rows($res5);
	}

	$totalResults = $rowstot1 + $rowstot2 + $rowstot3 + $rowstot4 + $rowstot5;
	if ($totalResults == 1) { $qty = $totalResults.' '.$resultsFoundText1; } else if ($totalResults > 1) { $qty = $totalResults.' '.$resultsFoundText2; } else { $qty = $totalResults.' '.$resultsFoundText2; }
	if ($totalResults < 1) {
		$msgBox = alertBox($noResultsMsg, "<i class='fa fa-warning'></i>", "default no-margin");
	}

	include 'includes/navigation.php';
?>
<div class="content last">
	<h3>
		<?php echo $pageName; ?>
		<small class="pull-right"><?php echo $qty; ?></small>
	</h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php
		while ($a = mysqli_fetch_assoc($res1)) {
			if ($a['clientPhone'] != '') { $clientPhone = decryptIt($a['clientPhone']); } else { $clientPhone = '';  }
	?>
			<div class="well well-sm search-box">
				<div class="row">
					<div class="col-md-1" data-toggle="tooltip" data-placement="left" title="<?php echo $clientText; ?>">
						<img src="<?php echo '../'.$avatarDir.$a['clientAvatar']; ?>" class="avatarSearch" />
					</div>
					<div class="col-md-11 section-box">
						<h4>
							<a href="index.php?action=viewClient&clientId=<?php echo $a['clientId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
								<?php echo clean($a['theClient']); ?>
							</a>
							<span class="pull-right"><?php echo $clientPhone; ?></span>
						</h4>
						<p>
							<?php echo nl2br(clean($a['clientBio'])); ?>
							<span class="pull-right"><?php echo clean($a['clientEmail']); ?></span>
						</p>
					</div>
				</div>
			</div>
	<?php } ?>

	<?php
		while ($b = mysqli_fetch_assoc($res2)) {
			if ($b['theAdmin'] == '') { $assigned = 'Unassigned'; } else { $assigned = clean($b['theAdmin']); }
	?>
			<div class="well well-sm search-box">
				<div class="row">
					<div class="col-md-1" data-toggle="tooltip" data-placement="left" title="<?php echo $projectText; ?>">
						<div class="icon"><i class="fa fa-folder-open-o"></i></div>
					</div>
					<div class="col-md-11 section-box">
						<h4>
							<a href="index.php?action=viewProject&projectId=<?php echo $b['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($b['projectName']); ?>
							</a>
							<span class="pull-right"><?php echo $dueByText; ?>: <?php echo $b['dueDate']; ?></span>
						</h4>
						<p>
							<strong>
								<?php echo $clientText; ?>:
								<a href="index.php?action=viewClient&clientId=<?php echo $b['clientId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
									<?php echo clean($b['theClient']); ?>
								</a>
								<span class="pull-right"><?php echo $assignedToText; ?>: <?php echo $assigned; ?></span>
							</strong>
							<br />
							<?php echo ellipsis($b['projectDeatils'], 120); ?>
						</p>
					</div>
				</div>
			</div>
	<?php } ?>

	<?php while ($c = mysqli_fetch_assoc($res3)) { ?>
		<div class="well well-sm search-box">
			<div class="row">
				<div class="col-md-1" data-toggle="tooltip" data-placement="left" title="<?php echo $discussionText; ?>">
					<div class="icon"><i class="fa fa-comments"></i></div>
				</div>
				<div class="col-md-11 section-box">
					<h4>
						<a href="index.php?action=viewDiscussion&discussionId=<?php echo $c['discussionId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewDiscussion; ?>">
							<?php echo clean($c['discussionTitle']); ?>
						</a>
						<span class="pull-right"><?php echo $eventPostedBy; ?>:
							<?php
								if ($c['adminId'] != '0') {
									echo clean($c['theAdmin']);
								} else {
							?>
									<a href="index.php?action=viewClient&clientId=<?php echo $c['clientId']; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo $pageNameviewClient; ?>">
										<?php echo clean($c['theClient']); ?>
									</a>
							<?php
								}
							?>
							<?php echo $onText.' '.$c['discussionDate']; ?>
						</span>
					</h4>
					<p>
						<strong>
							<?php echo $projectText; ?>:
							<a href="index.php?action=viewProject&projectId=<?php echo $c['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($c['projectName']); ?>
							</a>
						</strong>
						<br />
						<?php echo ellipsis($c['discussionText'], 120); ?>
					</p>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php while ($d = mysqli_fetch_assoc($res4)) { ?>
		<div class="well well-sm search-box">
			<div class="row">
				<div class="col-md-1" data-toggle="tooltip" data-placement="left" title="<?php echo $uploadedFileText; ?>">
					<div class="icon ml10"><i class="fa fa-file-text-o"></i></div>
				</div>
				<div class="col-md-11 section-box">
					<h4>
						<a href="index.php?action=viewFile&fileId=<?php echo $d['fileId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewFileText; ?>">
							<?php echo clean($d['fileTitle']); ?>
						</a>
						<span class="pull-right">
							<?php echo $projectText; ?>:
							<a href="index.php?action=viewProject&projectId=<?php echo $d['projectId']; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo $viewProject; ?>">
								<?php echo clean($d['projectName']); ?>
							</a>
						</span>
					</h4>
					<p>
						<strong>
							<?php echo $folderText; ?>:
							<a href="index.php?action=viewFolder&folderId=<?php echo $d['folderId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewFolderText; ?>">
								<?php echo clean($d['folderTitle']); ?>
							</a>
							<span class="pull-right"><?php echo $uploadedByText; ?>:
								<?php
									if ($d['adminId'] != '0') {
										echo clean($d['theAdmin']);
									} else {
								?>
										<a href="index.php?action=viewClient&clientId=<?php echo $d['clientId']; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo $pageNameviewClient; ?>">
											<?php echo clean($d['theClient']); ?>
										</a>
								<?php
									}
								?>
								<?php echo $onText.' '.$d['fileDate']; ?>
							</span>
						</strong>
						<br />
						<?php echo ellipsis($d['fileDesc'], 120); ?>
					</p>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php while ($e = mysqli_fetch_assoc($res5)) { ?>
		<div class="well well-sm search-box">
			<div class="row">
				<div class="col-md-1" data-toggle="tooltip" data-placement="left" title="<?php echo $taskText; ?>">
					<div class="icon"><i class="fa fa-tasks"></i></div>
				</div>
				<div class="col-md-11 section-box">
					<h4>
						<a href="index.php?action=viewTask&taskId=<?php echo $e['taskId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewTaskText; ?>">
							<?php echo clean($e['taskTitle']); ?>
						</a>
						<span class="pull-right"><?php echo $dueByText; ?>: <?php echo $e['dueDate']; ?></span>
					</h4>
					<p>
						<strong>
							<?php echo $projectText; ?>:
							<a href="index.php?action=viewProject&projectId=<?php echo $e['projectId']; ?>" data-toggle="tooltip" data-placement="left" title="<?php echo $viewProject; ?>">
								<?php echo clean($e['projectName']); ?>
							</a>
							<span class="pull-right">
								<?php echo $createdByTableHead; ?>: <?php echo clean($e['theAdmin']); ?>
							</span>
						</strong>
						<br />
						<?php echo ellipsis($e['taskDesc'], 120); ?>
					</p>
				</div>
			</div>
		</div>
	<?php } ?>

</div>