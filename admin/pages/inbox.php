<?php
	$jsFile = 'privateMessages';
	$pagPages = '10';
	$pmPage = 'inbox';

	// Mark Message as Read
	if (isset($_POST['submit']) && $_POST['submit'] == 'markRead') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($markedAsRead, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Mark Message as Archived
	if (isset($_POST['submit']) && $_POST['submit'] == 'archive') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1, toArchived = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($markedAsArchived, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Mark Message as Deleted
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteMsg') {
		$messageId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1, toDeleted = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($markedAsDeleted, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Compose New Message
	if (isset($_POST['submit']) && $_POST['submit'] == 'newMessage') {
		// User Validations
		if ($_POST['messageTitle'] == '') {
			$msgBox = alertBox($msgSubjectReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($_POST['messageText'] == '') {
			$msgBox = alertBox($msgTextReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Set some variables
			$toMgr = $mysqli->real_escape_string($_POST['toMgr']);
			$toClt = $mysqli->real_escape_string($_POST['toClt']);
			$messageTitle = $mysqli->real_escape_string($_POST['messageTitle']);
			$messageText = $_POST['messageText'];
			$messageDate = date("Y-m-d H:i:s");

			if ($toMgr != '...') {
				// Get Email Address
				$getEmail = "SELECT adminEmail AS theEmail FROM admins WHERE adminId = ".$toMgr;
				$emailres = mysqli_query($mysqli, $getEmail) or die('-1'.mysqli_error());
				$col = mysqli_fetch_assoc($emailres);
				$theEmail = $col['theEmail'];

				$stmt = $mysqli->prepare("
									INSERT INTO
										privatemessages(
											adminId,
											toAdminId,
											messageTitle,
											messageText,
											messageDate
										) VALUES (
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('sssss',
					$adminId,
					$toMgr,
					$messageTitle,
					$messageText,
					$messageDate
				);
				$stmt->execute();
			} else {
				// Get Client Data
				$getEmail = "SELECT clientEmail AS theEmail FROM clients WHERE clientId = ".$toClt;
				$emailres = mysqli_query($mysqli, $getEmail) or die('-2'.mysqli_error());
				$col = mysqli_fetch_assoc($emailres);
				$theEmail = $col['theEmail'];

				$stmt = $mysqli->prepare("
									INSERT INTO
										privatemessages(
											adminId,
											toClientId,
											messageTitle,
											messageText,
											messageDate
										) VALUES (
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('sssss',
					$adminId,
					$toClt,
					$messageTitle,
					$messageText,
					$messageDate
				);
				$stmt->execute();
			}

			// Send out a notification email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $inboxEmailSubject.' '.$adminFullName;
			if ($toMgr != '...') {
				$loginUrl = $loginURL1;
			} else {
				$loginUrl = $loginURL2;
			}

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<p>'.$messageText.'</p>';
			$message .= '<hr>';
			$message .= '<p>'.$loginUrl.'</p>';
			$message .= '<p>'.$emailThankYou.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($theEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($privateMsgSent, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['messageTitle'] = $_POST['messageText'] = '';
			$stmt->close();
		}
	}

	// Reply to Message
	if (isset($_POST['submit']) && $_POST['submit'] == 'replyToMessage') {
		$toName = $mysqli->real_escape_string($_POST['toName']);
		$isClient = $mysqli->real_escape_string($_POST['isClient']);
		$toId = $mysqli->real_escape_string($_POST['toId']);
		$origId = $mysqli->real_escape_string($_POST['origId']);
		$messageTitle = $mysqli->real_escape_string($_POST['messageTitle']);
		$messageText = $_POST['messageText'];
		$messageDate = date("Y-m-d H:i:s");

		if ($_POST['messageTitle'] == '') {
			$msgBox = alertBox($msgSubjectReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($_POST['messageText'] == '') {
			$msgBox = alertBox($msgTextReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			if ($isClient == '1') {
				// Get Client's Email Address
				$getEmail = "SELECT clientEmail AS theEmail FROM clients WHERE clientId = ".$toId;
				$emailres = mysqli_query($mysqli, $getEmail) or die('-3'.mysqli_error());
				$col = mysqli_fetch_assoc($emailres);
				$theEmail = $col['theEmail'];

				$stmt = $mysqli->prepare("
									INSERT INTO
										privatemessages(
											adminId,
											toClientId,
											origId,
											messageTitle,
											messageText,
											messageDate
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('ssssss',
					$adminId,
					$toId,
					$origId,
					$messageTitle,
					$messageText,
					$messageDate
				);
				$stmt->execute();
			} else {
				// Get Manager's Email Address
				$getEmail = "SELECT adminEmail AS theEmail FROM admins WHERE adminId = ".$toId;
				$emailres = mysqli_query($mysqli, $getEmail) or die('-4'.mysqli_error());
				$col = mysqli_fetch_assoc($emailres);
				$theEmail = $col['theEmail'];

				$stmt = $mysqli->prepare("
									INSERT INTO
										privatemessages(
											adminId,
											toAdminId,
											origId,
											messageTitle,
											messageText,
											messageDate
										) VALUES (
											?,
											?,
											?,
											?,
											?,
											?
										)");
				$stmt->bind_param('ssssss',
					$adminId,
					$toId,
					$origId,
					$messageTitle,
					$messageText,
					$messageDate
				);
				$stmt->execute();
			}

			// Send out a notification email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $inboxReplySubject.' '.$adminFullName;
			if ($isClient == '1') {
				$loginUrl = $loginURL2;
			} else {
				$loginUrl = $loginURL1;
			}

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<p>'.$messageText.'</p>';
			$message .= '<hr>';
			$message .= '<p>'.$loginUrl.'</p>';
			$message .= '<p>'.$emailThankYou.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($theEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($replyMsgSent, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['messageText'] = '';
			$stmt->close();
		}
	}

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("
		SELECT
			*
		FROM
			privatemessages
			LEFT JOIN clients ON privatemessages.clientId = clients.clientId
			LEFT JOIN admins ON privatemessages.adminId = admins.adminId
		WHERE
			privatemessages.toAdminId = ".$adminId." AND
			privatemessages.toDeleted = 0 AND
			privatemessages.toArchived = 0
	");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

    $query = "SELECT
				privatemessages.messageId,
				privatemessages.adminId,
				privatemessages.clientId,
				privatemessages.toAdminId,
				privatemessages.messageTitle,
				privatemessages.messageText,
				DATE_FORMAT(privatemessages.messageDate,'%b %d %Y %h:%i %p') AS messageDate,
				UNIX_TIMESTAMP(privatemessages.messageDate) AS orderDate,
				privatemessages.toRead,
				privatemessages.toArchived,
				privatemessages.toDeleted,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS clientSent,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS adminSent
			FROM
				privatemessages
				LEFT JOIN clients ON privatemessages.clientId = clients.clientId
				LEFT JOIN admins ON privatemessages.adminId = admins.adminId
			WHERE
				privatemessages.toAdminId = ".$adminId." AND
				privatemessages.toDeleted = 0 AND
				privatemessages.toArchived = 0
			ORDER BY
				orderDate DESC ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-5'.mysqli_error());

	if ($total < '1') { $lastContent = 'last'; } else { $lastContent = ''; }

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<input name="pmPage" id="pmPage" type="hidden" value="<?php echo $pmPage; ?>" />

	<ul class="nav nav-tabs">
		<li class="active"><a href="#inbox" data-toggle="tab" class="showinbox"><i class="fa fa-inbox"></i> <?php echo $inboxTabLink; ?></a></li>
		<li><a href="index.php?action=sent"><i class="fa fa-share"></i> <?php echo $sentTabLink; ?></a></li>
		<li><a href="index.php?action=archived"><i class="fa fa-archive"></i> <?php echo $archiveTabLink; ?></a></li>
		<li class="pull-right"><a href="#compose" data-toggle="tab" class="compose"><i class="fa fa-pencil"></i> <?php echo $composeTabLink; ?></a></li>
	</ul>
</div>

<div class="content <?php echo $lastContent; ?>">
	<div class="tab-content">
		<div class="tab-pane in active" id="inbox">
			<h3><?php echo $pageName; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>

			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noMessagesMsg; ?>
				</div>
			<?php } else { ?>
				<table class="rwd-table no-margin">
					<tbody>
						<tr>
							<th><?php echo $fromText; ?></th>
							<th><?php echo $subjectText; ?></th>
							<th class="text-right"><?php echo $dateRecvdText; ?></th>
						</tr>
						<?php
							while ($row = mysqli_fetch_assoc($res)) {
								if ($row['toRead'] == '0') { $isUnread = 'isUnread'; } else { $isUnread = ''; }
						?>
								<tr class="msgLink <?php echo $isUnread; ?>">
									<td class="name" data-th="<?php echo $fromText; ?>">
										<?php
											if ($row['adminId'] == '0') {
												echo clean($row['clientSent']);
											} else {
												echo clean($row['adminSent']);
											}
										?>
									</td>
									<td class="subject" data-th="<?php echo $subjectText; ?>"><?php echo clean($row['messageTitle']); ?></td>
									<input name="msgTxt" type="hidden" value="<?php echo nl2br(htmlspecialchars($row['messageText'])); ?>" />
									<input name="messageId" type="hidden" value="<?php echo $row['messageId']; ?>" />
									<input name="toRead" type="hidden" value="<?php echo $row['toRead']; ?>" />
									<td class="time text-right" data-th="<?php echo $dateRecvdText; ?>"><?php echo $row['messageDate']; ?></td>
								</tr>

								<div id="reply<?php echo $row['messageId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header modal-primary">
												<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
												<h4 class="modal-title"><?php echo $sendaReplyBtn; ?></h4>
											</div>
											<form action="" method="post">
												<div class="modal-body">
													<div class="form-group">
														<label for="messageTitle"><?php echo $subjectText; ?></label>
														<input type="text" class="form-control" required="" name="messageTitle" value="re: <?php echo clean($row['messageTitle']); ?>" />
													</div>
													<div class="form-group">
														<label for="messageText"><?php echo $messageText; ?></label>
														<textarea class="form-control" required="" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
													</div>
												</div>

												<div class="modal-footer">
													<?php
														if ($row['adminId'] == '0') {
															echo '
																	<input type="hidden" name="toName" value="'.$row['clientSent'].'" />
																	<input type="hidden" name="isClient" value="1" />
																	<input type="hidden" name="toId" value="'.$row['clientId'].'" />
																';
														} else {
															echo '
																	<input type="hidden" name="toName" value="'.$row['adminSent'].'" />
																	<input type="hidden" name="isClient" value="0" />
																	<input type="hidden" name="toId" value="'.$row['adminId'].'" />
																';
														}
													?>
													<input type="hidden" name="origId" value="<?php echo $row['messageId']; ?>" />
													<button type="input" name="submit" value="replyToMessage" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendReplyBtn; ?></button>
													<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
												</div>
											</form>

										</div>
									</div>
								</div>

								<div class="modal fade" id="delete<?php echo $row['messageId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<form action="" method="post">
												<div class="modal-body">
													<p class="lead"><?php echo $deleteMessageConf.' '.clean($row['messageTitle']); ?>?</p>
												</div>
												<div class="modal-footer">
													<input name="deleteId" type="hidden" value="<?php echo $row['messageId']; ?>" />
													<button type="input" name="submit" value="deleteMsg" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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
					if ($total > $pagPages) {
						echo $pages->page_links();
					}
				}
			?>
		</div>
		<div class="tab-pane vert-pane fade" id="compose">
			<h4><?php echo $composeNewModal; ?></h4>
			<form action="" method="post">
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group">
							<?php
								$getMgrs = "SELECT adminId, CONCAT(adminFirstName, ' ', adminLastName) as admin FROM admins WHERE adminId != ".$adminId." AND isActive = 1";
								$mgrres = mysqli_query($mysqli, $getMgrs) or die('-6'.mysqli_error());
							?>
							<label for="toMgr"><?php echo $selectManageField; ?></label>
							<select class="form-control" name="toMgr" id="toMgr">
								<option value="..."><?php echo $selectOption; ?></option>
								<?php while ($b = mysqli_fetch_assoc($mgrres)) { ?>
									<option value="<?php echo $b['adminId']; ?>"><?php echo clean($b['admin']); ?></option>
								<?php } ?>
							</select>
							<span class="help-block"><?php echo $selectManageFieldHelp; ?></span>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group">
							<?php
								$getClts = "SELECT clientId, CONCAT(clientFirstName, ' ', clientLastName) as admin FROM clients WHERE isActive = 1";
								$cltres = mysqli_query($mysqli, $getClts) or die('-7'.mysqli_error());
							?>
							<label for="toClt"><?php echo $selectClientField; ?></label>
							<select class="form-control" name="toClt" id="toClt">
								<option value="..."><?php echo $selectOption; ?></option>
								<?php while ($c = mysqli_fetch_assoc($cltres)) { ?>
									<option value="<?php echo $c['clientId']; ?>"><?php echo clean($c['admin']); ?></option>
								<?php } ?>
							</select>
							<span class="help-block"><?php echo $selectClientFieldHelp; ?></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="messageTitle"><?php echo $subjectText; ?></label>
					<input type="text" class="form-control" required="" name="messageTitle" value="<?php echo isset($_POST['messageTitle']) ? $_POST['messageTitle'] : ''; ?>" />
				</div>
				<div class="form-group">
					<label for="messageText"><?php echo $messageText; ?></label>
					<textarea class="form-control" required="" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
				</div>
				<button type="input" name="submit" value="newMessage" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $sendMsgBtn; ?></button>
			</form>
		</div>
	</div>
</div>

<?php if(mysqli_num_rows($res) > 0) { ?>
	<div class="content last">
		<div class="panel panel-default">
			<div class="panel-heading">
				<span class="theSubject"></span>
			</div>
			<div class="panel-body">
				<span class="pull-right msgOptions"></span>
				<span class="whoFrom"></span>
				<p class="msgQuip text-muted no-margin"><?php echo $selectInboxMsgQuip; ?></p>
				<div class="msgContent"></div>
			</div>
		</div>
	</div>
<?php } ?>