<?php
	$requestId = $_GET['requestId'];

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
			$dateUpdated = date("Y-m-d H:i:s");

            $stmt = $mysqli->prepare("UPDATE
										projectrequests
									SET
										requestTitle = ?,
										requestDesc = ?,
										requestBudget = ?,
										timeFrame = ?,
										dateUpdated = ?
									WHERE
										requestId = ?"
			);
			$stmt->bind_param('ssssss',
									$requestTitle,
									$requestDesc,
									$requestBudget,
									$timeFrame,
									$dateUpdated,
									$requestId
			);
			$stmt->execute();
			$msgBox = alertBox($projQuoteUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Edit Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'editComment') {
        // Validation
		if($_POST['reqDiscText'] == "") {
            $msgBox = alertBox($commentsRecMsg, "<i class='fa fa-times-circle'></i>", "danger");
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
			$msgBox = alertBox($commentUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
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
            $msgBox = alertBox($commentsRecMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$reqDiscText = $_POST['reqDiscText'];
			$requestDiscDate = date("Y-m-d H:i:s");
			$clientFullName = $mysqli->real_escape_string($_POST['clientFullName']);
			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									requestdisc(
										requestId,
										clientId,
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
								$clientId,
								$reqDiscText,
								$requestDiscDate
			);
			$stmt->execute();

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $newCommentEmailSubject1.' '.$clientFullName.' '.$projReqEmail2.' '.$requestTitle;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$requestText.': '.$requestTitle.'</p>';
			$message .= '<p>'.$fromText.': '.$clientFullName.'</p>';
			$message .= '<p>'.$reqDiscText.'</p>';
			$message .= '<hr>';
			$message .= $emailLink;
			$message .= $emailThankYou;
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($managers, $subject, $message, $headers)) {
				$msgBox = alertBox($commentsSavedMsg, "<i class='fa fa-check-square'></i>", "success");
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
					requestId,
					clientId,
					requestTitle,
					requestDesc,
					requestBudget,
					timeFrame,
					DATE_FORMAT(requestDate,'%M %d, %Y') AS requestDate,
					requestAccepted,
					openDiscussion
				FROM
					projectrequests
				WHERE requestId = ".$requestId;
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['requestBudget'] == '') {
		$requestBudget = $notSpecifiedText;
	} else {
		$requestBudget = $curSym.format_amount($row['requestBudget'], 2);
	}
	if ($row['timeFrame'] == '') { $timeFrame = $notSpecifiedText; } else { $timeFrame = clean($row['timeFrame']); }
	if ($row['requestAccepted'] == '0') {
		$requestAccepted = '<strong class="text-info">'.$openText.'</strong>';
	} else if ($row['requestAccepted'] == '1') {
		$requestAccepted = '<strong class="text-success">'.$acceptedText.'</strong>';
	} else {
		$requestAccepted = '<strong class="text-danger">'.$declinedText.'</strong>';
	}
	if ($row['openDiscussion'] == '1') {
		$openDiscussion = '<strong class="text-success">'.$openText.'</strong>';
	} else {
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

	if ($row['clientId'] != $clientId) {
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
			<li><a href="index.php?page=openProjects"><i class="fa fa-folder-open-o"></i> <?php echo $openProjectsLink; ?></a></li>
			<li><a href="index.php?page=closedProjects"><i class="fa fa-check-square-o"></i> <?php echo $closedProjectsLink; ?></a></li>
			<li><a href="index.php?page=myRequests"><i class="fa fa-comments-o"></i> <?php echo $projectRequestsLink; ?></a></li>
		</ul>
	</div>

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
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateReqText; ?>:</td>
						<td class="infoVal"><?php echo $row['requestDate']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-comments"></i> <?php echo $commentsText; ?>:</td>
						<td class="infoVal"><?php echo $openDiscussion; ?></td>
					</tr>
					<?php if ($set['enablePayments'] == '1') { ?>
						<tr>
							<td class="infoKey"><i class="fa fa-money"></i> <?php echo $budgetText; ?>:</td>
							<td class="infoVal"><?php echo $requestBudget; ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>

		<div class="well well-sm bg-trans no-margin mt20"><?php echo nl2br(clean($row['requestDesc'])); ?></div>

		<?php if ($row['requestAccepted'] == '0') { ?>
			<a data-toggle="modal" data-target="#editRequest" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $updateReqBtn; ?></a>

			<div id="editRequest" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">

						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><?php echo $updateReqModal; ?></h4>
						</div>

						<form action="" method="post">
							<div class="modal-body">
								<?php if ($set['enablePayments'] == '1') { ?>
									<div class="form-group">
										<label for="requestTitle"><?php echo $projTitleField; ?></label>
										<input type="text" class="form-control" name="requestTitle" value="<?php echo $row['requestTitle']; ?>">
										<span class="help-block"><?php echo $projTitleHelp; ?></span>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="timeFrame"><?php echo $projTimeFrameField; ?></label>
												<input type="text" class="form-control" name="timeFrame" value="<?php echo $row['timeFrame']; ?>">
												<span class="help-block"><?php echo $projTimeFrameFieldHelp; ?></span>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="requestBudget"><?php echo $projBudgetField; ?></label>
												<input type="text" class="form-control" name="requestBudget" value="<?php echo $row['requestBudget']; ?>">
												<span class="help-block"><?php echo $projBudgetFieldHelp; ?></span>
											</div>
										</div>
									</div>
								<?php } else { ?>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label for="requestTitle"><?php echo $projTitleField; ?></label>
												<input type="text" class="form-control" name="requestTitle" value="<?php echo $row['requestTitle']; ?>">
												<span class="help-block"><?php echo $projTitleHelp; ?></span>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="timeFrame"><?php echo $projTimeFrameField; ?></label>
												<input type="text" class="form-control" name="timeFrame" value="<?php echo $row['timeFrame']; ?>">
												<span class="help-block"><?php echo $projTimeFrameFieldHelp; ?></span>
											</div>
										</div>
									</div>
								<?php } ?>
								<div class="form-group">
									<label for="requestDesc"><?php echo $projDescText; ?></label>
									<textarea class="form-control" name="requestDesc" id="requestDesc" rows="6"><?php echo $row['requestDesc']; ?></textarea>
									<span class="help-block"><?php echo $projDescFieldHelp; ?></span>
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
		<?php } ?>
	</div>

	<div class="content">
		<?php
			if(mysqli_num_rows($results) > 0) {
				while ($rows = mysqli_fetch_assoc($results)) {
					if ($rows['adminId'] == '0') {
		?>
						<div class="well well-xs comments">
							<img src="<?php echo $avatarDir.$rows['clientAvatar']; ?>" alt="<?php echo clean($rows['theClient']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($rows['theClient']); ?>" />
							<h4>
								<?php echo clean($rows['theClient']).' '.$commentedText; ?>
								<small class="text-muted">on <?php echo $rows['requestDiscDate']; ?></small>
								<?php if ($row['openDiscussion'] == '1') { ?>
									<small class="pull-right">
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentText; ?>">
											<a class="text-success" data-toggle="modal" href="#editComment<?php echo $rows['reqDiscId']; ?>"><i class="fa fa-edit"></i></a>
										</span>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentText; ?>">
											<a class="text-danger" data-toggle="modal" href="#deleteComment<?php echo $rows['reqDiscId']; ?>"><i class="fa fa-times"></i></a>
										</span>
									</small>
								<?php } ?>
							</h4>
							<small><?php echo nl2br(clean($rows['reqDiscText'])); ?></small>
						</div>

						<?php if ($row['openDiscussion'] == '1') { ?>
							<div id="editComment<?php echo $rows['reqDiscId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">

										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
											<h4 class="modal-title"><?php echo $editCommentText; ?></h4>
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
					} else {
		?>
						<div class="well well-xs comments">
							<img src="<?php echo $avatarDir.$rows['adminAvatar']; ?>" alt="<?php echo clean($rows['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($rows['theAdmin']); ?>" />
							<h4>
								<?php echo clean($rows['theAdmin']).' '.$commentedText; ?> <small class="text-muted"><?php echo $onText.' '.$rows['requestDiscDate']; ?></small>
							</h4>
							<small><?php echo nl2br(clean($rows['reqDiscText'])); ?></small>
						</div>
		<?php
					}
				}
			} else {
		?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noCommentsMsg; ?>
				</div>
		<?php } ?>
	</div>

	<div class="content last">
		<?php if ($row['openDiscussion'] == '1') { ?>
			<h4><?php echo $addCommentText; ?></h4>
			<form action="" method="post">
				<div class="form-group">
					<textarea class="form-control" name="reqDiscText" rows="6"><?php echo isset($_POST['reqDiscText']) ? $_POST['reqDiscText'] : ''; ?></textarea>
				</div>
				<input type="hidden" name="clientFullName" value="<?php echo $clientFullName; ?>" />
				<input type="hidden" name="requestTitle" value="<?php echo clean($row['requestTitle']); ?>" />
				<button type="input" name="submit" value="newComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addCommentBtn; ?></button>
			</form>
		<?php } else { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $commentsClosedMsg; ?>
			</div>
		<?php } ?>
	</div>
<?php } ?>