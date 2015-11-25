<?php
	$discussionId = $_GET['discussionId'];

	// Edit Topic
    if (isset($_POST['submit']) && $_POST['submit'] == 'editTopic') {
        // Validation
		if($_POST['discussionTitle'] == "") {
            $msgBox = alertBox($discTopicReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['discussionText'] == "") {
            $msgBox = alertBox($discTextReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$discussionTitle = $mysqli->real_escape_string($_POST['discussionTitle']);
			$discussionText = $_POST['discussionText'];
			$lastUpdated = date("Y-m-d H:i:s");

            $stmt = $mysqli->prepare("UPDATE
										projectdiscus
									SET
										discussionTitle = ?,
										discussionText = ?,
										lastUpdated = ?
									WHERE
										discussionId = ?"
			);
			$stmt->bind_param('ssss',
									$discussionTitle,
									$discussionText,
									$lastUpdated,
									$discussionId
			);
			$stmt->execute();
			$msgBox = alertBox($discTopicUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Edit Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'editComment') {
        // Validation
		if($_POST['replyText'] == "") {
            $msgBox = alertBox($commentsReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$replyText = $_POST['replyText'];
			$replyId = $mysqli->real_escape_string($_POST['replyId']);

            $stmt = $mysqli->prepare("UPDATE
										replies
									SET
										replyText = ?
									WHERE
										replyId = ?"
			);
			$stmt->bind_param('ss',
									$replyText,
									$replyId
			);
			$stmt->execute();
			$msgBox = alertBox($commentsUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['replyText'] = '';
			$stmt->close();
		}
	}

	// Delete Comment
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteComment') {
		$replyId = $mysqli->real_escape_string($_POST['replyId']);
		$stmt = $mysqli->prepare("DELETE FROM replies WHERE replyId = ?");
		$stmt->bind_param('s', $replyId);
		$stmt->execute();
		$msgBox = alertBox($commentDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Add New Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'newComment') {
        // Validation
		if($_POST['replyText'] == "") {
            $msgBox = alertBox($commentsReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$replyText = $_POST['replyText'];
			$replyDate = $lastUpdated = date("Y-m-d H:i:s");
			$adminFullName = $mysqli->real_escape_string($_POST['adminFullName']);
			$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);
			$projectId = $mysqli->real_escape_string($_POST['projectId']);
			$projectName = $mysqli->real_escape_string($_POST['projectName']);
			$discussionTitle = $mysqli->real_escape_string($_POST['discussionTitle']);

			// Update the Last Updated for the Topic
			$stmt = $mysqli->prepare("UPDATE projectdiscus SET lastUpdated = ? WHERE discussionId = ?");
			$stmt->bind_param('ss', $lastUpdated, $discussionId);
			$stmt->execute();
			$stmt->close();

			// Add the Client's Comment
			$stmt = $mysqli->prepare("
								INSERT INTO
									replies(
										discussionId,
										projectId,
										adminId,
										replyText,
										replyDate
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssss',
								$discussionId,
								$projectId,
								$adminId,
								$replyText,
								$replyDate
			);
			$stmt->execute();

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $newDiscCmtEmailSubject1.' '.$adminFullName.' '.$newDiscCmtEmailSubject2.' '.$discussionTitle;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$projectText.': '.$projectName.'</p>';
			$message .= '<p>'.$newDiscCmtEmailSubject2.': '.$discussionTitle.'</p>';
			$message .= '<p>'.$fromText.': '.$adminFullName.'</p>';
			$message .= '<p>'.$replyText.'</p>';
			$message .= '<hr>';
			$message .= '<p>'.$emailLink.'</p>';
			$message .= '<p>'.$emailThankYou.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($clientEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($newCommentSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['replyText'] = '';
            $stmt->close();
		}
	}

	// Get Project Discussion
	$sql = "SELECT
				projectdiscus.discussionId,
				projectdiscus.projectId,
				projectdiscus.adminId,
				projectdiscus.clientId,
				projectdiscus.discussionTitle,
				projectdiscus.discussionText,
				DATE_FORMAT(projectdiscus.discussionDate,'%W, %M %e, %Y') AS discussionDate,
				DATE_FORMAT(projectdiscus.lastUpdated,'%W, %M %e, %Y') AS lastUpdated,
				clientprojects.projectName,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clients.clientEmail,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectdiscus
				LEFT JOIN clientprojects ON projectdiscus.projectId = clientprojects.projectId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
				LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
			WHERE
				projectdiscus.discussionId = ".$discussionId;
	$res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Get Comment Data
	$sqlStmt = "SELECT
					replies.replyId,
					replies.discussionId,
					replies.projectId,
					replies.adminId,
					replies.clientId,
					replies.replyText,
					DATE_FORMAT(replies.replyDate,'%W, %M %e, %Y at %l:%i %p') AS replyDate,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
					clients.clientAvatar,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					admins.adminAvatar
				FROM
					replies
					LEFT JOIN clients ON replies.clientId = clients.clientId
					LEFT JOIN admins ON replies.adminId = admins.adminId
				WHERE replies.discussionId = ".$discussionId;
	$results = mysqli_query($mysqli, $sqlStmt) or die('-2'.mysqli_error());

	// Only allow access to the Assigned Manager or Admins
	$qry = "SELECT
				assignedprojects.assignedTo,
				clientprojects.projectName
			FROM
				assignedprojects
				LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
			WHERE assignedprojects.projectId = ".$row['projectId'];
	$result = mysqli_query($mysqli, $qry) or die('-3' . mysqli_error());
	$rows = mysqli_fetch_assoc($result);
	$assignedTo = $rows['assignedTo'];

	include 'includes/navigation.php';

	if (($isAdmin != '1') && ($rows['assignedTo'] != $adminId)) {
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
			<li><a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><i class="fa fa-folder-open"></i> <?php echo clean($row['projectName']); ?></a></li>
			<li><a href="index.php?action=projectDiscussions&projectId=<?php echo $row['projectId']; ?>"><i class="fa fa-comments"></i> <?php echo $viewAllProjDisc; ?></a></li>
		</ul>
	</div>

	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row mt10">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-quote-left"></i> <?php echo $discTopicText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['discussionTitle']); ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateCreatedTableHead; ?>:</td>
						<td class="infoVal"><?php echo $row['discussionDate']; ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-user"></i> <?php echo $createdByTableHead; ?>:</td>
						<td class="infoVal">
							<?php
								if ($row['adminId'] != '0') {
									echo clean($row['theAdmin']);
								} else {
									echo clean($row['theClient']);
								}
							?>
						</td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $lastUpdatedText; ?>:</td>
						<td class="infoVal"><?php echo $row['lastUpdated']; ?></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="well well-sm bg-trans no-margin mt20">
			<?php echo nl2br(clean($row['discussionText'])); ?>
		</div>

		<a data-toggle="modal" data-target="#editTopic" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editTopicBtn; ?></a>

		<div class="modal fade" id="editTopic" tabindex="-1" role="dialog" aria-labelledby="editTopic" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $editDiscTopicModal; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="discussionTitle"><?php echo $discTopicText; ?></label>
								<input type="text" class="form-control" name="discussionTitle" required="" value="<?php echo clean($row['discussionTitle']); ?>">
								<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
							</div>
							<div class="form-group">
								<label for="discussionText"><?php echo $discTextText; ?></label>
								<textarea class="form-control" name="discussionText" required="" rows="6"><?php echo clean($row['discussionText']); ?></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="editTopic" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="content">
		<?php
			if(mysqli_num_rows($results) > 0) {
				while ($rows = mysqli_fetch_assoc($results)) {
					if ($rows['adminId'] == '0') {
		?>
						<div class="well well-xs comments">
							<img src="<?php echo '../'.$avatarDir.$rows['clientAvatar']; ?>" alt="<?php echo clean($rows['theClient']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($rows['theClient']); ?>" />
							<h4>
								<?php echo clean($rows['theClient']).' '.$commentedText; ?>
								<small class="text-muted"><?php echo $onText.' '.$rows['replyDate']; ?></small>
								<small class="pull-right">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentTooltip; ?>">
										<a class="text-success" data-toggle="modal" href="#editComment<?php echo $rows['replyId']; ?>"><i class="fa fa-edit"></i></a>
									</span>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentTooltip; ?>">
										<a class="text-danger" data-toggle="modal" href="#deleteComment<?php echo $rows['replyId']; ?>"><i class="fa fa-times"></i></a>
									</span>
								</small>
							</h4>
							<small><?php echo nl2br(clean($rows['replyText'])); ?></small>
						</div>
		<?php
					} else {
		?>
						<div class="well well-xs comments">
							<img src="<?php echo '../'.$avatarDir.$rows['adminAvatar']; ?>" alt="<?php echo clean($rows['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($rows['theAdmin']); ?>" />
							<h4>
								<?php echo clean($rows['theAdmin']).' '.$commentedText; ?> <small class="text-muted"><?php echo $onText.' '.$rows['replyDate']; ?></small>
								<small class="pull-right">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentTooltip; ?>">
										<a class="text-success" data-toggle="modal" href="#editComment<?php echo $rows['replyId']; ?>"><i class="fa fa-edit"></i></a>
									</span>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentTooltip; ?>">
										<a class="text-danger" data-toggle="modal" href="#deleteComment<?php echo $rows['replyId']; ?>"><i class="fa fa-times"></i></a>
									</span>
								</small>
							</h4>
							<small><?php echo nl2br(clean($rows['replyText'])); ?></small>
						</div>
		<?php
					}
		?>
						<div id="editComment<?php echo $rows['replyId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">

									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
										<h4 class="modal-title"><?php echo $editCommentTooltip; ?></h4>
									</div>

									<form action="" method="post">
										<div class="modal-body">
											<div class="form-group">
												<textarea class="form-control" required="" name="replyText" rows="6"><?php echo clean($rows['replyText']); ?></textarea>
											</div>
										</div>

										<div class="modal-footer">
											<input type="hidden" name="replyId" value="<?php echo $rows['replyId']; ?>" />
											<button type="input" name="submit" value="editComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>

								</div>
							</div>
						</div>

						<div class="modal fade" id="deleteComment<?php echo $rows['replyId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteCommentConf; ?></p>
										</div>
										<div class="modal-footer">
											<input type="hidden" name="replyId" value="<?php echo $rows['replyId']; ?>" />
											<button type="input" name="submit" value="deleteComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
		<?php
				}
			} else {
		?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noCommentsFound; ?>
				</div>
		<?php } ?>
	</div>

	<div class="content last">
		<h4><?php echo $addCommentTitle; ?></h4>
		<form action="" method="post">
			<div class="form-group">
				<textarea class="form-control" name="replyText" rows="6"><?php echo isset($_POST['replyText']) ? $_POST['replyText'] : ''; ?></textarea>
			</div>
			<input type="hidden" name="adminFullName" value="<?php echo $adminFullName; ?>" />
			<input type="hidden" name="clientEmail" value="<?php echo clean($row['clientEmail']); ?>" />
			<input type="hidden" name="projectName" value="<?php echo clean($row['projectName']); ?>" />
			<input type="hidden" name="projectId" value="<?php echo clean($row['projectId']); ?>" />
			<input type="hidden" name="discussionTitle" value="<?php echo clean($row['discussionTitle']); ?>" />
			<button type="input" name="submit" value="newComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addCommentBtn; ?></button>
		</form>
	</div>
<?php } ?>