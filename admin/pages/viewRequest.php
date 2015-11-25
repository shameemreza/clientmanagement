<?php
	$requestId = $_GET['requestId'];
	$datePicker = 'true';
	$jsFile = 'newProject';

	// Edit Request
    if (isset($_POST['submit']) && $_POST['submit'] == 'editRequest') {
        // Validation
		if($_POST['requestTitle'] == "") {
            $msgBox = alertBox($projTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['requestDesc'] == "") {
            $msgBox = alertBox($projDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);
			$requestDesc = $_POST['requestDesc'];
			if ($set['enablePayments'] == '1') {
				$requestBudget = $mysqli->real_escape_string($_POST['requestBudget']);
			} else {
				$requestBudget = '';
			}
			$timeFrame = $mysqli->real_escape_string($_POST['timeFrame']);
			$requestAccepted = $mysqli->real_escape_string($_POST['requestAccepted']);
			$openDiscussion = $mysqli->real_escape_string($_POST['openDiscussion']);
			$dateUpdated = date("Y-m-d H:i:s");

            $stmt = $mysqli->prepare("UPDATE
										projectrequests
									SET
										requestTitle = ?,
										requestDesc = ?,
										requestBudget = ?,
										timeFrame = ?,
										requestAccepted = ?,
										openDiscussion = ?,
										dateUpdated = ?
									WHERE
										requestId = ?"
			);
			$stmt->bind_param('ssssssss',
									$requestTitle,
									$requestDesc,
									$requestBudget,
									$timeFrame,
									$requestAccepted,
									$openDiscussion,
									$dateUpdated,
									$requestId
			);
			$stmt->execute();
			$msgBox = alertBox($projRequestUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}
	
	// Accept Request
    if (isset($_POST['submit']) && $_POST['submit'] == 'acceptRequest') {
        // Validation
        if($_POST['projectName'] == "") {
            $msgBox = alertBox($projTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dueDate'] == "") {
            $msgBox = alertBox($projDueByDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if(($set['enablePayments'] == "1") && ($_POST['projectFee'] == "")) {
			$msgBox = alertBox($projFeeReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['projectDeatils'] == "") {
            $msgBox = alertBox($projDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$sendEmail = $mysqli->real_escape_string($_POST['sendEmail']);
			$dueDate = $mysqli->real_escape_string($_POST['dueDate']);
			$projectName = $mysqli->real_escape_string($_POST['projectName']);
			if ($set['enablePayments'] == '1') {
				$projectFee = $mysqli->real_escape_string($_POST['projectFee']);
			} else {
				$projectFee = '0';
			}
			$projectDeatils = htmlentities($_POST['projectDeatils']);
			$projectNotes = htmlentities($_POST['projectNotes']);
			$clientId = $mysqli->real_escape_string($_POST['clientId']);
			$theClient = $mysqli->real_escape_string($_POST['theClient']);
			$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);
			$startDate = date("Y-m-d H:i:s");
			$fromRequest = $requestAccepted = '1';
			
			// Mark the Request as Accepted
			$stmt = $mysqli->prepare("UPDATE
										projectrequests
									SET
										requestAccepted = ?
									WHERE
										requestId = ?"
			);
			$stmt->bind_param('ss',
									$requestAccepted,
									$requestId
			);
			$stmt->execute();
			$stmt->close();

			// Create the New Project
            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    clientprojects(
                                        createdBy,
                                        clientId,
                                        projectName,
										projectFee,
										startDate,
										dueDate,
										projectDeatils,
										projectNotes,
										fromRequest
                                    ) VALUES (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
										?,
										?,
										?
                                    )");
            $stmt->bind_param('sssssssss',
				$adminId,
                $clientId,
                $projectName,
                $projectFee,
                $startDate,
                $dueDate,
				$projectDeatils,
				$projectNotes,
				$fromRequest
            );
            $stmt->execute();

			if ($sendEmail == '1') {
				// Send out the email in HTML
				$installUrl = $set['installUrl'];
				$siteName = $set['siteName'];
				$businessEmail = $set['businessEmail'];

				$subject = $newProjEmailSubject;

				$message = '<html><body>';
				$message .= '<h3>'.$subject.'</h3>';
				$message .= '<hr>';
				$message .= '<p>'.$projectText.': '.$projectName.'</p>';
				$message .= '<p>'.$projectDeatils.'</p>';
				$message .= '<hr>';
				$message .= '<p>'.$emailLink.'</p>';
				$message .= '<p>'.$emailThankYou.'</p>';
				$message .= '</body></html>';

				$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
				$headers .= "Reply-To: ".$businessEmail."\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				if (mail($clientEmail, $subject, $message, $headers)) {
					$msgBox = alertBox($projReqAcceptedEmailSent, "<i class='fa fa-check-square'></i>", "success");
				} else {
					$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-warning'></i>", "warning");
				}
			} else {
				$msgBox = alertBox($projReqAccepted, "<i class='fa fa-check-square'></i>", "success");
			}
			// Clear the form of Values
			$_POST['dueDate'] = $_POST['projectName'] = $_POST['projectFee'] = $_POST['projectDeatils'] = $_POST['projectNotes'] = '';
            $stmt->close();
		}
	}

	// Edit Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'editComment') {
        // Validation
		if($_POST['reqDiscText'] == "") {
            $msgBox = alertBox($commentsReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$reqDiscText = $_POST['reqDiscText'];
			$reqDiscId = $mysqli->real_escape_string($_POST['reqDiscId']);

            $stmt = $mysqli->prepare("UPDATE
										requestdisc
									SET
										reqDiscText = ?
									WHERE
										reqDiscId = ?"
			);
			$stmt->bind_param('ss',
									$reqDiscText,
									$reqDiscId
			);
			$stmt->execute();
			$msgBox = alertBox($commentsUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['reqDiscText'] = '';
			$stmt->close();
		}
	}

	// Delete Comment
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteComment') {
		$reqDiscId = $mysqli->real_escape_string($_POST['reqDiscId']);
		$stmt = $mysqli->prepare("DELETE FROM requestdisc WHERE reqDiscId = ?");
		$stmt->bind_param('s', $reqDiscId);
		$stmt->execute();
		$msgBox = alertBox($commentDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Add New Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'newComment') {
        // Validation
		if($_POST['reqDiscText'] == "") {
            $msgBox = alertBox($commentsReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$reqDiscText = $_POST['reqDiscText'];
			$requestDiscDate = date("Y-m-d H:i:s");
			$adminFullName = $mysqli->real_escape_string($_POST['adminFullName']);
			$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);
			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									requestdisc(
										requestId,
										adminId,
										reqDiscText,
										requestDiscDate
									) VALUES (
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssss',
								$requestId,
								$adminId,
								$reqDiscText,
								$requestDiscDate
			);
			$stmt->execute();

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $projReqEmailSubject1.' '.$adminFullName.' '.$projReqEmailSubject2.' '.$requestTitle;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$requestText.': '.$requestTitle.'</p>';
			$message .= '<p>'.$fromText.': '.$adminFullName.'</p>';
			$message .= '<p>'.$reqDiscText.'</p>';
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
			$_POST['reqDiscText'] = '';
            $stmt->close();
		}
	}

	// Get Data
	$sqlStmt = "SELECT
					projectrequests.requestId,
					projectrequests.clientId,
					projectrequests.requestTitle,
					projectrequests.requestDesc,
					projectrequests.requestBudget,
					projectrequests.timeFrame,
					DATE_FORMAT(projectrequests.requestDate,'%M %d, %Y') AS requestDate,
					projectrequests.requestAccepted,
					projectrequests.openDiscussion,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
					clients.clientEmail
				FROM
					projectrequests
					LEFT JOIN clients ON projectrequests.clientId = clients.clientId
				WHERE projectrequests.requestId = ".$requestId;
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	$new = $accepted = $declined = $open = $closed = '';
	if ($row['requestBudget'] == '') {
		$requestBudget = $notSpecifiedText;
	} else {
		$requestBudget = $curSym.format_amount($row['requestBudget'], 2);
	}
	if ($row['timeFrame'] == '') { $timeFrame = $notSpecifiedText; } else { $timeFrame = clean($row['timeFrame']); }
	if ($row['requestAccepted'] == '0') {
		$new = 'selected';
		$requestAccepted = '<strong class="text-info">'.$newText.'</strong>';
	} else if ($row['requestAccepted'] == '1') {
		$accepted = 'selected';
		$requestAccepted = '<strong class="text-success">'.$acceptedText.'</strong>';
	} else {
		$declined = 'selected';
		$requestAccepted = '<strong class="text-danger">'.$declinedText.'</strong>';
	}
	if ($row['openDiscussion'] == '1') {
		$open = 'selected';
		$openDiscussion = '<strong class="text-success">'.$openText.'</strong>';
	} else {
		$closed = 'selected';
		$openDiscussion = '<strong class="text-danger">'.$closedText.'</strong>';
	}

	// Get Replies Data
	$qry = "SELECT
				requestdisc.reqDiscId,
				requestdisc.requestId,
				requestdisc.adminId,
				requestdisc.clientId,
				requestdisc.reqDiscText,
				DATE_FORMAT(requestdisc.requestDiscDate,'%b %d %Y at %h:%i %p') AS requestDiscDate,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clients.clientAvatar,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
				admins.adminAvatar
			FROM
				requestdisc
				LEFT JOIN clients ON requestdisc.clientId = clients.clientId
				LEFT JOIN admins ON requestdisc.adminId = admins.adminId
			WHERE requestdisc.requestId = ".$requestId;
	$results = mysqli_query($mysqli, $qry) or die('-2'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="content">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="row">
		<div class="col-md-6">
			<table class="infoTable">
				<tr>
					<td class="infoKey"><i class="fa fa-folder-open"></i> <?php echo $requestText; ?>:</td>
					<td class="infoVal"><?php echo clean($row['requestTitle']); ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-user"></i> <?php echo $requestedByText; ?>:</td>
					<td class="infoVal"><?php echo clean($row['theClient']); ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-info-circle"></i> <?php echo $statusText; ?>:</td>
					<td class="infoVal"><?php echo $requestAccepted; ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-clock-o"></i> <?php echo $timeFrameText; ?>:</td>
					<td class="infoVal"><?php echo $timeFrame; ?></td>
				</tr>
			</table>
		</div>
		<div class="col-md-6">
			<table class="infoTable">
				<tr>
					<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateRequestedText; ?>:</td>
					<td class="infoVal"><?php echo $row['requestDate']; ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-envelope"></i> <?php echo $clientEmailText; ?>:</td>
					<td class="infoVal"><?php echo clean($row['clientEmail']); ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-comments"></i> <?php echo $commentsText; ?>:</td>
					<td class="infoVal"><?php echo $openDiscussion; ?></td>
				</tr>
				<?php if ($set['enablePayments'] == '1') { ?>
					<tr>
						<td class="infoKey"><i class="fa fa-money"></i> <?php echo $busdgetText; ?>:</td>
						<td class="infoVal"><?php echo $requestBudget; ?></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>

	<div class="well well-sm bg-trans no-margin mt20"><?php echo nl2br(clean($row['requestDesc'])); ?></div>

	<a data-toggle="modal" data-target="#editRequest" class="btn btn-info btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editRequestBtn; ?></a>
	<a data-toggle="modal" data-target="#acceptRequest" class="btn btn-success btn-icon mt20"><i class="fa fa-check"></i> <?php echo $acceptRequestBtn; ?></a>

	<div id="editRequest" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $editRequestModal; ?></h4>
				</div>

				<form action="" method="post">
					<div class="modal-body">
						<p><?php echo $editRequestQuip; ?></p>
						<?php if ($set['enablePayments'] == '1') { ?>
							<div class="form-group mt10">
								<label for="requestTitle"><?php echo $projectTitleText; ?></label>
								<input type="text" class="form-control" name="requestTitle" value="<?php echo $row['requestTitle']; ?>">
								<span class="help-block"><?php echo $projectTitleTextHelp; ?></span>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="timeFrame"><?php echo $projTimeFrameText; ?></label>
										<input type="text" class="form-control" name="timeFrame" value="<?php echo $row['timeFrame']; ?>">
										<span class="help-block"><?php echo $projTimeFrameTextHelp; ?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="requestBudget"><?php echo $projBudgetText; ?></label>
										<input type="text" class="form-control" name="requestBudget" value="<?php echo $row['requestBudget']; ?>">
										<span class="help-block"><?php echo $projBudgetTextHelp; ?></span>
									</div>
								</div>
							</div>
						<?php } else { ?>
							<div class="row mt10">
								<div class="col-md-6">
									<div class="form-group">
										<label for="requestTitle"><?php echo $projectTitleText; ?></label>
										<input type="text" class="form-control" name="requestTitle" value="<?php echo $row['requestTitle']; ?>">
										<span class="help-block"><?php echo $projectTitleTextHelp; ?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="timeFrame"><?php echo $projTimeFrameText; ?></label>
										<input type="text" class="form-control" name="timeFrame" value="<?php echo $row['timeFrame']; ?>">
										<span class="help-block"><?php echo $projTimeFrameTextHelp; ?></span>
									</div>
								</div>
							</div>
						<?php } ?>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="requestAccepted"><?php echo $requestStatusText; ?></label>
									<select class="form-control" name="requestAccepted">
										<option value="0" <?php echo $new; ?>><?php echo $newText; ?></option>
										<option value="1" <?php echo $accepted; ?>><?php echo $acceptedText; ?></option>
										<option value="2" <?php echo $declined; ?>><?php echo $declinedText; ?></option>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="openDiscussion"><?php echo $discussionsText; ?></label>
									<select class="form-control" name="openDiscussion">
										<option value="0" <?php echo $closed; ?>><?php echo $closedText; ?></option>
										<option value="1" <?php echo $open; ?>><?php echo $openText; ?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="requestDesc"><?php echo $projectDescField; ?></label>
							<textarea class="form-control" name="requestDesc" id="requestDesc" rows="6"><?php echo $row['requestDesc']; ?></textarea>
							<span class="help-block"><?php echo $projectDescriptionHelp; ?></span>
						</div>
					</div>

					<div class="modal-footer">
						<button type="input" name="submit" value="editRequest" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateRequestBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>

			</div>
		</div>
	</div>
	
	<div id="acceptRequest" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $acceptRequestModal; ?></h4>
				</div>

				<form action="" method="post">
					<div class="modal-body">
						<p><?php echo $acceptRequestQuip; ?></p>
						
						<div class="row mt10">
							<div class="col-md-6">
								<div class="form-group">
									<label for="sendEmail"><?php echo $sendEmailToClient; ?></label>
									<select class="form-control" name="sendEmail">
										<option value="0" selected><?php echo $noBtn; ?></option>
										<option value="1"><?php echo $yesBtn; ?></option>
									</select>
									<span class="help-block"><?php echo $sendEmailToClientHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="dueDate"><?php echo $ProjDueDateField; ?></label>
									<input type="text" class="form-control" name="dueDate" id="dueDate" value="">
									<span class="help-block"><?php echo $dateFormatHelp; ?></span>
								</div>
							</div>
						</div>
						<?php if ($set['enablePayments'] == '1') { ?>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="projectName"><?php echo $projectNameField; ?></label>
										<input type="text" class="form-control" name="projectName" value="<?php echo $row['requestTitle']; ?>">
										<span class="help-block"><?php echo $projectTitleTextHelp; ?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="projectFee"><?php echo $projectFeeText; ?></label>
										<input type="text" class="form-control" name="projectFee" value="<?php echo $row['requestBudget']; ?>">
										<span class="help-block"><?php echo $invNumbersOnlyText; ?></span>
									</div>
								</div>
							</div>
						<?php } else { ?>
							<div class="form-group">
								<label for="projectName"><?php echo $projectNameField; ?></label>
								<input type="text" class="form-control" name="projectName" value="<?php echo $row['requestTitle']; ?>">
								<span class="help-block"><?php echo $projectTitleTextHelp; ?></span>
							</div>
						<?php } ?>
						<div class="form-group">
							<label for="projectDeatils"><?php echo $projectDescField; ?></label>
							<textarea class="form-control" name="projectDeatils" rows="6"></textarea>
							<span class="help-block"><?php echo $projectDescFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="projectNotes"><?php echo $projectNotesField; ?></label>
							<textarea class="form-control" name="projectNotes" rows="6"><?php echo $row['requestDesc']; ?></textarea>
							<span class="help-block"><?php echo $projectNotesFieldHelp; ?></span>
						</div>
					</div>

					<div class="modal-footer">
						<input type="hidden" name="clientId" value="<?php echo $row['clientId']; ?>" />
						<input type="hidden" name="theClient" value="<?php echo clean($row['theClient']); ?>" />
						<input type="hidden" name="clientEmail" value="<?php echo clean($row['clientEmail']); ?>" />
						<button type="input" name="submit" value="acceptRequest" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $acceptRequestBtn; ?></button>
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
							<small class="text-muted"><?php echo $onText.' '.$rows['requestDiscDate']; ?></small>
							<?php if ($row['openDiscussion'] == '1') { ?>
								<small class="pull-right">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentTooltip; ?>">
										<a class="text-success" data-toggle="modal" href="#editComment<?php echo $rows['reqDiscId']; ?>"><i class="fa fa-edit"></i></a>
									</span>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentTooltip; ?>">
										<a class="text-danger" data-toggle="modal" href="#deleteComment<?php echo $rows['reqDiscId']; ?>"><i class="fa fa-times"></i></a>
									</span>
								</small>
							<?php } ?>
						</h4>
						<small><?php echo nl2br(clean($rows['reqDiscText'])); ?></small>
					</div>
	<?php
				} else {
	?>
					<div class="well well-xs comments">
						<img src="<?php echo '../'.$avatarDir.$rows['adminAvatar']; ?>" alt="<?php echo clean($rows['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($rows['theAdmin']); ?>" />
						<h4>
							<?php echo clean($rows['theAdmin']).' '.$commentedText; ?>
							<small class="text-muted"><?php echo $onText.' '.$rows['requestDiscDate']; ?></small>
							<?php if ($row['openDiscussion'] == '1') { ?>
								<small class="pull-right">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentTooltip; ?>">
										<a class="text-success" data-toggle="modal" href="#editComment<?php echo $rows['reqDiscId']; ?>"><i class="fa fa-edit"></i></a>
									</span>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentTooltip; ?>">
										<a class="text-danger" data-toggle="modal" href="#deleteComment<?php echo $rows['reqDiscId']; ?>"><i class="fa fa-times"></i></a>
									</span>
								</small>
							<?php } ?>
						</h4>
						<small><?php echo nl2br(clean($rows['reqDiscText'])); ?></small>
					</div>
	<?php
				}
	?>
				<?php if ($row['openDiscussion'] == '1') { ?>
						<div id="editComment<?php echo $rows['reqDiscId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">

									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
										<h4 class="modal-title"><?php echo $editCommentTooltip; ?></h4>
									</div>

									<form action="" method="post">
										<div class="modal-body">
											<div class="form-group">
												<textarea class="form-control" required="" name="reqDiscText" rows="6"><?php echo clean($rows['reqDiscText']); ?></textarea>
											</div>
										</div>

										<div class="modal-footer">
											<input type="hidden" name="reqDiscId" value="<?php echo $rows['reqDiscId']; ?>" />
											<button type="input" name="submit" value="editComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>

								</div>
							</div>
						</div>

						<div class="modal fade" id="deleteComment<?php echo $rows['reqDiscId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteCommentConf; ?></p>
										</div>
										<div class="modal-footer">
											<input type="hidden" name="reqDiscId" value="<?php echo $rows['reqDiscId']; ?>" />
											<button type="input" name="submit" value="deleteComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
	<?php
				}
			}
		} else {
	?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noCommentsFound; ?>
			</div>
	<?php } ?>
</div>

<div class="content last">
	<?php if ($row['openDiscussion'] == '1') { ?>
		<h4><?php echo $addCommentTitle; ?></h4>
		<form action="" method="post">
			<div class="form-group">
				<textarea class="form-control" name="reqDiscText" rows="6"><?php echo isset($_POST['reqDiscText']) ? $_POST['reqDiscText'] : ''; ?></textarea>
			</div>
			<input type="hidden" name="adminFullName" value="<?php echo $adminFullName; ?>" />
			<input type="hidden" name="clientEmail" value="<?php echo clean($row['clientEmail']); ?>" />
			<input type="hidden" name="requestTitle" value="<?php echo clean($row['requestTitle']); ?>" />
			<button type="input" name="submit" value="newComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addCommentBtn; ?></button>
		</form>
	<?php } else { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> <?php echo $commentsClosedMsg; ?>
		</div>
	<?php } ?>
</div>
