<?php
	$datePicker = 'true';
	$jsFile = 'invoices';
	$pagPages = '10';

	// Delete Invoice
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteInvoice') {
		$invoiceId = $mysqli->real_escape_string($_POST['invoiceId']);

		// Delete the Invoice
		$stmt = $mysqli->prepare("DELETE FROM invoices WHERE invoiceId = ?");
		$stmt->bind_param('s', $_POST['invoiceId']);
		$stmt->execute();
		$stmt->close();

		// Delete all of the Invoice items
		$stmt = $mysqli->prepare("DELETE FROM invitems WHERE invoiceId = ?");
		$stmt->bind_param('s', $_POST['invoiceId']);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($invoiceDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Add New Invoice
    if (isset($_POST['submit']) && $_POST['submit'] == 'newInvoice') {
        // Validation
		if($_POST['projectId'] == "...") {
            $msgBox = alertBox($invoiceSelectProjectReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['invoiceDue'] == "") {
            $msgBox = alertBox($invoiceDueDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['invoiceTitle'] == "") {
            $msgBox = alertBox($invoiceTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$projectId = $mysqli->real_escape_string($_POST['projectId']);
			$invoiceDue = $mysqli->real_escape_string($_POST['invoiceDue']);
			$invoiceTitle = $mysqli->real_escape_string($_POST['invoiceTitle']);
			$invNotes = $mysqli->real_escape_string($_POST['invoiceNotes']);
			$invoiceNotes = $_POST['invoiceNotes'];
			$invoiceDate = date("Y-m-d H:i:s");

			// Get some Client data
			$cd = 	"SELECT
						clients.clientId,
						clients.clientEmail,
						clientprojects.projectId
					FROM
						clients
						LEFT JOIN clientprojects ON clients.clientId = clientprojects.clientId
					WHERE
						clientprojects.projectId = ".$projectId;
			$cdres = mysqli_query($mysqli, $cd) or die('-1' . mysqli_error());
			$col = mysqli_fetch_assoc($cdres);
			$clientId = $col['clientId'];
			$clientEmail = $col['clientEmail'];

			$stmt = $mysqli->prepare("
								INSERT INTO
									invoices(
										projectId,
										adminId,
										clientId,
										invoiceTitle,
										invoiceNotes,
										invoiceDate,
										invoiceDue
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
								$projectId,
								$adminId,
								$clientId,
								$invoiceTitle,
								$invoiceNotes,
								$invoiceDate,
								$invoiceDue
			);
			$stmt->execute();
			$stmt->close();

			if (isset($_POST['notifyClient']) && $_POST['notifyClient'] == '1') {
				// Send out the email in HTML
				$installUrl = $set['installUrl'];
				$siteName = $set['siteName'];
				$businessEmail = $set['businessEmail'];

				$subject = $newInvoiceEmailSubject1.' '.$siteName.' '.$newInvoiceEmailSubject2;

				$message = '<html><body>';
				$message .= '<h3>'.$subject.'</h3>';
				$message .= '<hr>';
				$message .= '<p>'.$invoiceTitleField.': '.$invoiceTitle.'</p>';
				$message .= '<p>'.$invNotes.'</p>';
				$message .= '<hr>';
				$message .= '<p>'.$emailLink.'</p>';
				$message .= '<p>'.$emailThankYou.'</p>';
				$message .= '</body></html>';

				$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
				$headers .= "Reply-To: ".$businessEmail."\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				if (mail($clientEmail, $subject, $message, $headers)) {
					$msgBox = alertBox($invCreatedEmailSent, "<i class='fa fa-check-square'></i>", "success");
				} else {
					$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-warning'></i>", "warning");
				}
			} else {
				$msgBox = alertBox($invCreatedNoEmail, "<i class='fa fa-check-square'></i>", "success");
			}
			// Clear the Form of values
			$_POST['invoiceDue'] = $_POST['invoiceTitle'] = $_POST['invoiceNotes'] = '';
		}
	}

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$cols = $mysqli->query("SELECT * FROM invoices");
	$total = mysqli_num_rows($cols);

	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$query = "SELECT
				invoices.invoiceId,
				invoices.projectId,
				invoices.adminId,
				invoices.clientId,
				invoices.invoiceTitle,
				invoices.invoiceNotes,
				DATE_FORMAT(invoices.invoiceDue,'%M %d, %Y') AS invoiceDue,
				UNIX_TIMESTAMP(invoices.invoiceDue) AS orderDate,
				invoices.isPaid,
				clientprojects.projectName,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				invoices
				LEFT JOIN clientprojects ON invoices.projectId = clientprojects.projectId
				LEFT JOIN clients ON invoices.clientId = clients.clientId
				LEFT JOIN admins ON invoices.adminId = admins.adminId
			ORDER BY invoices.isPaid, orderDate ".$pages->get_limit();
	$res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li class="pull-right"><a data-toggle="modal" href="#newInvoice"><i class="fa fa-plus"></i> <?php echo $createNewInvoiceTabLink; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-info-circle"></i> <?php echo $noInvoicesFound; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table no-margin">
			<tbody>
				<tr class="primary">
					<th><?php echo $invoiceTableHead; ?></th>
					<th><?php echo $clientText; ?></th>
					<th><?php echo $projectText; ?></th>
					<th><?php echo $createdByTableHead; ?></th>
					<th><?php echo $paymentDueText; ?></th>
					<th><?php echo $viewInvoiceText; ?></th>
					<th><?php echo $statusText; ?></th>
					<th></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						// Get the Invoice Total
						$qry = "SELECT
									itemAmount,
									itemqty
								FROM
									invitems
								WHERE invoiceId = ".$row['invoiceId'];
						$result = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());

						$lineTotal = 0;
						while ($col = mysqli_fetch_assoc($result)) {
							$lineItem = $col['itemAmount'] * $col['itemqty'];
							$lineTotal += $lineItem;
						}
						$lineTotal = $curSym.format_amount($lineTotal, 2);

						if ($row['isPaid'] == '1') { $status = $paidText; $highlight = 'class="text-success"'; } else { $status = $unpaidText; $highlight = 'class="text-danger"'; }
				?>
						<tr>
							<td data-th="<?php echo $invoiceTableHead; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewInvoiceTooltip; ?>">
									<a href="index.php?action=viewInvoice&invoiceId=<?php echo $row['invoiceId']; ?>"><?php echo clean($row['invoiceTitle']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $clientText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
									<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>"><?php echo clean($row['theClient']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $projectText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
									<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $createdByTableHead; ?>"><?php echo clean($row['theAdmin']); ?></td>
							<td data-th="<?php echo $paymentDueText; ?>"><?php echo $row['invoiceDue']; ?></td>
							<td data-th="<?php echo $viewInvoiceText; ?>"><?php echo $lineTotal; ?></td>
							<td data-th="<?php echo $statusText; ?>"><strong <?php echo $highlight; ?>><?php echo $status; ?></strong></td>
							<td data-th="<?php echo $actionsText; ?>">
								<a href="index.php?action=viewInvoice&invoiceId=<?php echo $row['invoiceId']; ?>">
									<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewInvoiceTooltip; ?>"></i>
								</a>
								<?php if ($row['isPaid'] == '1') { ?>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $noInvoiceDeleteTooltip; ?>">
										<i class="fa fa-lock disabled"></i>
									</span>
								<?php } else { ?>
									<a data-toggle="modal" href="#deleteInvoice<?php echo $row['invoiceId']; ?>">
										<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteInvoiceTooltip; ?>"></i>
									</a>
								<?php } ?>
							</td>
						</tr>

						<?php if ($row['isPaid'] == '0') { ?>
							<div class="modal fade" id="deleteInvoice<?php echo $row['invoiceId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteInvoiceConf.' '.clean($row['invoiceTitle']); ?>?</p>
											</div>
											<div class="modal-footer">
												<input name="invoiceId" type="hidden" value="<?php echo $row['invoiceId']; ?>" />
												<button type="input" name="submit" value="deleteInvoice" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
												<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
											</div>
										</form>
									</div>
								</div>
							</div>
				<?php
						}
					}
				?>
			</tbody>
		</table>
	<?php
			if ($total > $pagPages) {
				echo $pages->page_links();
			}
		}
	?>
</div>

<div id="newInvoice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $createNewInvoiceTabLink; ?></h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<p><?php echo $createNewInvoiceQuip; ?></p>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="projectId"><?php echo $selectProjectField; ?></label>
								<select class="form-control" name="projectId" id="projectId">
								<?php
									// Get the Project List
									$a = "SELECT
											clientprojects.projectId,
											clientprojects.projectName,
											CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
										FROM
											clientprojects
											LEFT JOIN clients ON clientprojects.clientId = clients.clientId
										WHERE clientprojects.archiveProj = 0";
									$b = mysqli_query($mysqli, $a) or die('-4'.mysqli_error());
								?>
									<option value="..."><?php echo $selectOption; ?></option>
									<?php while ($c = mysqli_fetch_assoc($b)) { ?>
										<option value="<?php echo $c['projectId']; ?>"><?php echo clean($c['projectName']); ?> &mdash; <?php echo $clientText.': '.clean($c['theClient']); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="invoiceDue"><?php echo $dateInvDueBy; ?></label>
								<input type="text" class="form-control" required="" name="invoiceDue" id="invoiceDue" value="<?php echo isset($_POST['invoiceDue']) ? $_POST['invoiceDue'] : ''; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="invoiceTitle"><?php echo $invoiceTitleText; ?></label>
						<input type="text" class="form-control" required="" name="invoiceTitle" value="<?php echo isset($_POST['invoiceTitle']) ? $_POST['invoiceTitle'] : ''; ?>" />
					</div>
					<div class="form-group">
						<label for="invoiceNotes"><?php echo $invoiceNotesField; ?></label>
						<textarea class="form-control" name="invoiceNotes" rows="2"><?php echo isset($_POST['invoiceNotes']) ? $_POST['invoiceNotes'] : ''; ?></textarea>
						<span class="help-block"><?php echo $invoiceNotesFieldHelp; ?></span>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" name="notifyClient" value="1">
							<?php echo $notifyClientCheckbox; ?>
						</label>
					</div>
					<span class="help-block"><?php echo $notifyClientCheckboxHelp; ?></span>
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="newInvoice" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>