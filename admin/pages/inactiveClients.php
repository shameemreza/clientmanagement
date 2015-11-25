<?php
	$pagPages = '10';

	// Resend Activation Link to Client
	if (isset($_POST['submit']) && $_POST['submit'] == 'resendLink') {
		$clientEmail = htmlspecialchars($_POST['clientEmail']);
		$hash = htmlspecialchars($_POST['hash']);

		// Send out the email in HTML
		$installUrl = $set['installUrl'];
		$siteName = $set['siteName'];
		$businessEmail = $set['businessEmail'];

		$subject = $reactivateEmailSubject;

		// -------------------------------
		// ---- START Edit Email Text ----
		// -------------------------------
		$message = '<html><body>';
		$message .= '<h3>'.$subject.'</h3>';
		$message .= '<hr>';
		$message .= '<p>'.$reactivateEmail1.$installUrl.'activate.php?clientEmail='.$clientEmail.'&hash='.$hash.'</p>';
		$message .= '<hr>';
		$message .= '<p>'.$reactivateEmail2.'</p>';
		$message .= '<p>'.$emailLink.'</p>';
		$message .= '<p>'.$emailThankYou.'</p>';
		$message .= '</body></html>';
		// -------------------------------
		// ---- END Edit Email Text ----
		// -------------------------------

		$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
		$headers .= "Reply-To: ".$businessEmail."\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		if (mail($clientEmail, $subject, $message, $headers)) {
			$msgBox = alertBox($reactivateEmailSentMsg, "<i class='fa fa-check-square'></i>", "success");
		} else {
			$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}

	// Delete Client Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteClient') {
		$clientId = $mysqli->real_escape_string($_POST['clientId']);
		$stmt = $mysqli->prepare("DELETE FROM clients WHERE clientId = ?");
		$stmt->bind_param('s', $clientId);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($clientAccountDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM clients WHERE isActive = 0");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$query = "SELECT
				clientId,
				clientEmail,
				CONCAT(clientFirstName,' ',clientLastName) AS theClient,
				clientCompany,
				hash,
				isArchived,
				DATE_FORMAT(archiveDate,'%M %d, %Y') AS archiveDate,
				UNIX_TIMESTAMP(archiveDate) AS orderDate
			FROM
				clients
			WHERE
				isActive = 0
			ORDER BY
				orderDate ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li><a href="index.php?action=activeClients"><i class="fa fa-user"></i> <?php echo $activeClientsTabLink; ?></a></li>
		<li class="active"><a href="" data-toggle="tab"><i class="fa fa-archive"></i> <?php echo $inactiveClientsTabLink; ?></a></li>
		<li class="pull-right"><a href="index.php?action=newClient"><i class="fa fa-plus"></i> <?php echo $newClientTabLink; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> <?php echo $noInactiveClients; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table no-margin">
			<tbody>
				<tr class="primary">
					<th><?php echo $clientText; ?></th>
					<th><?php echo $companyText; ?></th>
					<th><?php echo $emailText; ?></th>
					<th><?php echo $archivedText; ?>?</th>
					<th><?php echo $dateArchivedText; ?></th>
					<th></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						if ($row['isArchived'] == '0') { $isArchived = $noBtn; $dateArchived = ''; } else { $isArchived = $yesBtn; $dateArchived = $row['archiveDate']; }
				?>
						<tr>
							<td data-th="<?php echo $clientText; ?>">
								<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
									<?php echo clean($row['theClient']); ?>
								</a>
							</td>
							<td data-th="<?php echo $companyText; ?>"><?php echo clean($row['clientCompany']); ?></td>
							<td data-th="<?php echo $emailText; ?>"><?php echo clean($row['clientEmail']); ?></td>
							<td data-th="<?php echo $archivedText; ?>?"><?php echo $isArchived; ?></td>
							<td data-th="<?php echo $dateArchivedText; ?>"><?php echo $dateArchived; ?></td>
							<td data-th="<?php echo $actionsText; ?>">
								<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>">
									<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $pageNameviewClient; ?>"></i>
								</a>
								<a data-toggle="modal" href="#resendLink<?php echo $row['clientId']; ?>">
									<i class="fa fa-exchange text-success" data-toggle="tooltip" data-placement="left" title="<?php echo $resendEmailTooltip; ?>"></i>
								</a>
								<a data-toggle="modal" href="#deleteClient<?php echo $row['clientId']; ?>">
									<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteClientTooltip; ?>"></i>
								</a>
							</td>
						</tr>

						<div class="modal fade" id="resendLink<?php echo $row['clientId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $resendEmailConf.' '.clean($row['theClient']); ?>?</p>
										</div>
										<div class="modal-footer">
											<input name="clientEmail" type="hidden" value="<?php echo $row['clientEmail']; ?>" />
											<input name="hash" type="hidden" value="<?php echo $row['hash']; ?>" />
											<button type="input" name="submit" value="resendLink" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>

						<div class="modal fade" id="deleteClient<?php echo $row['clientId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteClientConf.' '.clean($row['theClient']); ?>?</p>
										</div>
										<div class="modal-footer">
											<input name="clientId" type="hidden" value="<?php echo $row['clientId']; ?>" />
											<button type="input" name="submit" value="deleteClient" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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