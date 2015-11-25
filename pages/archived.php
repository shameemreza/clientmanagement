<?php
	$jsFile = 'privateMessages';
	$pagPages = '10';
	$pmPage = 'archive';

	// Send Message to Inbox
	if (isset($_POST['submit']) && $_POST['submit'] == 'sendInbox') {
		$messageId = $mysqli->real_escape_string($_POST['messageId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1, toArchived = 0 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($sentToInbox, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Mark Message as Deleted
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteMsg') {
		$messageId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("UPDATE privatemessages SET toRead = 1, toDeleted = 1 WHERE messageId = ?");
		$stmt->bind_param('s', $messageId);
		$stmt->execute();
		$msgBox = alertBox($msgIsDeleted, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Compose New Message
	if (isset($_POST['submit']) && $_POST['submit'] == 'newMessage') {
		// User Validations
		if ($_POST['messageTitle'] == '') {
			$msgBox = alertBox($pmTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($_POST['messageText'] == '') {
			$msgBox = alertBox($pmMessageReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Set some variables
			$toId = $mysqli->real_escape_string($_POST['toId']);
			$messageTitle = $mysqli->real_escape_string($_POST['messageTitle']);
			$messageText = $_POST['messageText'];
			$messageDate = date("Y-m-d H:i:s");

			// Get User's Admins Address
			$getEmail = "SELECT adminEmail AS theEmail FROM admins WHERE adminId = ".$toId;
			$emailres = mysqli_query($mysqli, $getEmail) or die('-1'.mysqli_error());
			$col = mysqli_fetch_assoc($emailres);
			$theEmail = $col['theEmail'];

			$stmt = $mysqli->prepare("
								INSERT INTO
									privatemessages(
										clientId,
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
				$clientId,
				$toId,
				$messageTitle,
				$messageText,
				$messageDate
			);
			$stmt->execute();

			// Send out a notification email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $newPMEmailSubject.' '.$clientFullName;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<p>'.$messageText.'</p>';
			$message .= '<hr>';
			$message .= $emailLink;
			$message .= $emailThankYou;
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($theEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($newPMSentConf, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['messageTitle'] = $_POST['messageText'] = '';
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
			privatemessages.toClientId = ".$clientId." AND
			privatemessages.toDeleted = 0 AND
			privatemessages.toArchived = 1
	");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

    $query = "SELECT
				privatemessages.messageId,
				privatemessages.adminId,
				privatemessages.clientId,
				privatemessages.toClientId,
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
				privatemessages.toClientId = ".$clientId." AND
				privatemessages.toDeleted = 0 AND
				privatemessages.toArchived = 1
			ORDER BY
				orderDate DESC ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());

	if ($total < '1') { $lastContent = 'last'; } else { $lastContent = ''; }

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<input name="pmPage" id="pmPage" type="hidden" value="<?php echo $pmPage; ?>" />

	<ul class="nav nav-tabs">
		<li class=""><a href="index.php?page=inbox"><i class="fa fa-inbox"></i> <?php echo $inboxLink; ?></a></li>
		<li class=""><a href="index.php?page=sent"><i class="fa fa-share"></i> <?php echo $sentItemsLink; ?></a></li>
		<li class="active"><a href="#archive" data-toggle="tab" class="showarchive"><i class="fa fa-archive"></i> <?php echo $archiveLink; ?></a></li>
		<li class="pull-right"><a href="#compose" data-toggle="tab" class="compose"><i class="fa fa-pencil"></i> <?php echo $composeLink; ?></a></li>
	</ul>
</div>

<div class="content <?php echo $lastContent; ?>">
	<div class="tab-content">
		<div class="tab-pane in active" id="inbox">
			<h3><?php echo $pageName; ?></h3>
			<?php if ($msgBox) { echo $msgBox; } ?>

			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noArchivedMsg; ?>
				</div>
			<?php } else { ?>
				<table class="rwd-table no-margin">
					<tbody>
						<tr>
							<th><strong><?php echo $fromText; ?></th>
							<th><?php echo $subjectText; ?></th>
							<th class="text-right"><?php echo $dateReceivedText; ?></th>
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
									<td class="time text-right" data-th="<?php echo $dateReceivedText; ?>"><?php echo $row['messageDate']; ?></td>
								</tr>

								<div class="modal fade" id="delete<?php echo $row['messageId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<form action="" method="post">
												<div class="modal-body">
													<p class="lead"><?php echo $pmdeleteConf.' '.clean($row['messageTitle']); ?>?</p>
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
			<h4><?php echo $composeNewMsg; ?></h4>
			<form action="" method="post">
				<div class="form-group">
					<?php
						$getMgrs = "SELECT adminId, CONCAT(adminFirstName, ' ', adminLastName) as admin FROM admins WHERE isActive = 1";
						$mgrres = mysqli_query($mysqli, $getMgrs) or die('-3'.mysqli_error());
					?>
					<label for="toId"><?php echo $selectAdminField; ?></label>
					<select class="form-control" name="toId">
						<option value=""><?php echo $selectOption; ?></option>
						<?php while ($b = mysqli_fetch_assoc($mgrres)) { ?>
							<option value="<?php echo $b['adminId']; ?>"><?php echo clean($b['admin']); ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="messageTitle"><?php echo $subjectField; ?></label>
					<input type="text" class="form-control" required="" name="messageTitle" value="<?php echo isset($_POST['messageTitle']) ? $_POST['messageTitle'] : ''; ?>" />
				</div>
				<div class="form-group">
					<label for="messageText"><?php echo $messageField; ?></label>
					<textarea class="form-control" required="" name="messageText" rows="6"><?php echo isset($_POST['messageText']) ? $_POST['messageText'] : ''; ?></textarea>
				</div>
				<button type="input" name="submit" value="newMessage" class="btn btn-success btn-icon mt20"><i class="fa fa-check-square-o"></i> <?php echo $sendMsgBtn; ?></button>
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
				<p class="msgQuip text-muted no-margin"><?php echo $selectArchivedMsgText; ?></p>
				<div class="msgContent"></div>
			</div>
		</div>
	</div>
<?php } ?>