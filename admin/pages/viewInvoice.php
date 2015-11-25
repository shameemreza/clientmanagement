<?php
	$invoiceId = $_GET['invoiceId'];
	$datePicker = 'true';
	$jsFile = 'viewInvoice';
	$additionalFee = '0';

	// Edit Line Item
    if (isset($_POST['submit']) && $_POST['submit'] == 'editLine') {
		// Validation
		if($_POST['itemName'] == "") {
            $msgBox = alertBox($lineItemNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['itemqty'] == "") {
            $msgBox = alertBox($qtyReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['itemAmount'] == "") {
            $msgBox = alertBox($lineItemAmtReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['itemDesc'] == "") {
            $msgBox = alertBox($lineItemDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$itemId = $mysqli->real_escape_string($_POST['itemId']);
			$itemName = $mysqli->real_escape_string($_POST['itemName']);
			$itemqty = $mysqli->real_escape_string($_POST['itemqty']);
			$itemAmount = $mysqli->real_escape_string($_POST['itemAmount']);
			$itemDesc = $mysqli->real_escape_string($_POST['itemDesc']);
			$lastUpdated = date("Y-m-d H:i:s");

			 $stmt = $mysqli->prepare("UPDATE
										invitems
									SET
										itemName = ?,
										itemDesc = ?,
										itemAmount = ?,
										itemqty = ?,
										lastUpdated = ?
									WHERE
										itemId = ?"
			);
			$stmt->bind_param('ssssss',
									$itemName,
									$itemDesc,
									$itemAmount,
									$itemqty,
									$lastUpdated,
									$itemId
			);
			$stmt->execute();
			$msgBox = alertBox($lineItemUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['itemName'] = $_POST['itemDesc'] = $_POST['itemAmount'] = $_POST['itemqty'] = '';
			$stmt->close();
		}
	}

	// Delete Line Item
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteLine') {
		$itemId = $mysqli->real_escape_string($_POST['itemId']);
		$stmt = $mysqli->prepare("DELETE FROM invitems WHERE itemId = ?");
		$stmt->bind_param('s', $_POST['itemId']);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($lineItemDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Edit Invoice
    if (isset($_POST['submit']) && $_POST['submit'] == 'editInvoice') {
        // Validation
		if($_POST['invoiceDue'] == "") {
            $msgBox = alertBox($invoiceDueDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['invoiceTitle'] == "") {
            $msgBox = alertBox($invoiceTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$invoiceDue = $mysqli->real_escape_string($_POST['invoiceDue']);
			$invoiceTitle = $mysqli->real_escape_string($_POST['invoiceTitle']);
			$invoiceNotes = $_POST['invoiceNotes'];
			$lastUpdated = date("Y-m-d H:i:s");

            $stmt = $mysqli->prepare("UPDATE
										invoices
									SET
										invoiceTitle = ?,
										invoiceNotes = ?,
										invoiceDue = ?,
										lastUpdated = ?
									WHERE
										invoiceId = ?"
			);
			$stmt->bind_param('ssss',
									$invoiceTitle,
									$invoiceNotes,
									$invoiceDue,
									$lastUpdated,
									$invoiceId
			);
			$stmt->execute();
			$msgBox = alertBox($invUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Add Line Item
    if (isset($_POST['submit']) && $_POST['submit'] == 'addItem') {
        // Validation
		if($_POST['itemName'] == "") {
            $msgBox = alertBox($lineItemNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['itemqty'] == "") {
            $msgBox = alertBox($qtyReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['itemAmount'] == "") {
            $msgBox = alertBox($lineItemAmtReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['itemDesc'] == "") {
            $msgBox = alertBox($lineItemDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$itemName = $mysqli->real_escape_string($_POST['itemName']);
			$itemqty = $mysqli->real_escape_string($_POST['itemqty']);
			$itemAmount = $mysqli->real_escape_string($_POST['itemAmount']);
			$itemDesc = $mysqli->real_escape_string($_POST['itemDesc']);
			$itemDate = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									invitems(
										invoiceId,
										itemName,
										itemDesc,
										itemAmount,
										itemqty,
										itemDate
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssssss',
								$invoiceId,
								$itemName,
								$itemDesc,
								$itemAmount,
								$itemqty,
								$itemDate
			);
			$stmt->execute();
			$msgBox = alertBox($newLineItemAddedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['itemName'] = $_POST['itemDesc'] = $_POST['itemAmount'] = $_POST['itemqty'] = '';
			$stmt->close();
		}
	}

	// Add New Invoice payment
    if (isset($_POST['submit']) && $_POST['submit'] == 'recordPayment') {
        // Validation
		if($_POST['paymentFor'] == "") {
            $msgBox = alertBox($paymentForReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['paymentDate'] == "") {
            $msgBox = alertBox($paymentDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['paidBy'] == "") {
            $msgBox = alertBox($paidByReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['paymentAmount'] == "") {
            $msgBox = alertBox($paymentAmountReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$cId = $mysqli->real_escape_string($_POST['cId']);
			$pId = $mysqli->real_escape_string($_POST['pId']);
			$paymentFor = $mysqli->real_escape_string($_POST['paymentFor']);
			$paymentDate = $mysqli->real_escape_string($_POST['paymentDate']);
			$paidBy = $mysqli->real_escape_string($_POST['paidBy']);
			$paymentAmount = $mysqli->real_escape_string($_POST['paymentAmount']);
			$additionalFee = $mysqli->real_escape_string($_POST['additionalFee']);
			$paymentNotes = $_POST['paymentNotes'];

			// Update the Invoice as paid
			$isPaid = '1';
			$stmt = $mysqli->prepare("UPDATE invoices SET isPaid = ?, datePaid = ? WHERE invoiceId = ?");
			$stmt->bind_param('sss', $isPaid, $paymentDate, $invoiceId);
			$stmt->execute();
			$stmt->close();

			// Save the Payment Data
			$stmt = $mysqli->prepare("
								INSERT INTO
									projectpayments(
										clientId,
										projectId,
										invoiceId,
										enteredBy,
										paymentFor,
										paymentDate,
										paidBy,
										paymentAmount,
										additionalFee,
										paymentNotes
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssssssssss',
								$cId,
								$pId,
								$invoiceId,
								$adminId,
								$paymentFor,
								$paymentDate,
								$paidBy,
								$paymentAmount,
								$additionalFee,
								$paymentNotes
			);
			$stmt->execute();
			$msgBox = alertBox($invPaymentSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['paymentFor'] = $_POST['paymentDate'] = $_POST['paidBy'] = $_POST['paymentAmount'] = $_POST['additionalFee'] = $_POST['paymentNotes'] = '';
			$stmt->close();
		}
	}

	// Get Data
	$query = "SELECT
				invoices.invoiceId,
				invoices.projectId,
				invoices.adminId,
				invoices.clientId,
				invoices.invoiceTitle,
				invoices.invoiceNotes,
				DATE_FORMAT(invoices.invoiceDate,'%M %d, %Y') AS invoiceDate,
				DATE_FORMAT(invoices.invoiceDue,'%M %d, %Y') AS invoiceDue,
				DATE_FORMAT(invoices.invoiceDue,'%Y-%m-%d') AS invDue,
				invoices.isPaid,
				DATE_FORMAT(invoices.datePaid,'%M %d, %Y') AS datePaid,
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clients.clientCompany,
				clients.clientAddress,
				clients.clientPhone
			FROM
				invoices
				LEFT JOIN clientprojects ON invoices.projectId = clientprojects.projectId
				LEFT JOIN admins ON invoices.adminId = admins.adminId
				LEFT JOIN clients ON invoices.clientId = clients.clientId
			WHERE invoices.invoiceId = ".$invoiceId;
	$res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data for display
	if ($row['clientAddress'] != '') { $clientAddress = decryptIt($row['clientAddress']); } else { $clientAddress = ''; }
	if ($row['clientPhone'] != '') { $clientPhone = decryptIt($row['clientPhone']); } else { $clientPhone = ''; }

	if ($row['isPaid'] == '1') {
		$paid = "SELECT
					paymentId,
					clientId,
					invoiceId,
					paymentFor,
					DATE_FORMAT(paymentDate,'%M %d, %Y') AS paymentDate,
					paidBy,
					paymentAmount,
					additionalFee,
					invoicepayNotes
				FROM
					projectpayments
				WHERE invoiceId = ".$invoiceId;
		$results = mysqli_query($mysqli, $paid) or die('-2'.mysqli_error());
		$cols = mysqli_fetch_assoc($results);

		if ($cols['additionalFee'] != '') {
			$additionalFee = format_amount($cols['additionalFee'], 2);
		} else {
			$additionalFee = '0';
		}
		$invAmountPaid = $curSym.format_amount($cols['paymentAmount'] + $additionalFee, 2);
	}

	$qry = "SELECT
				itemId,
				invoiceId,
				itemName,
				itemDesc,
				itemAmount,
				itemqty
			FROM
				invitems
			WHERE invoiceId = ".$invoiceId;
	$result = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());

	// Get Site Alert Data
    $alert = "SELECT
                    isActive,
                    invoicePrint,
                    alertText,
					UNIX_TIMESTAMP(alertDate) AS orderDate,
					alertExpires
                FROM
                    sitealerts
                WHERE
					invoicePrint = 1
				ORDER BY
					orderDate DESC";
    $alertres = mysqli_query($mysqli, $alert) or die('-4'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="content last">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="invoice">
		<div class="row">
			<div class="col-md-8 invoice-col">
				<img src="../images/footer_logo.png" alt="<?php echo $set['siteName']; ?>" />
			</div>
			<div class="col-md-4 invoice-col">
				<p class="no-margin text-muted text-right"><?php echo $printedOnText.' '.date('F d, Y'); ?></p>
			</div>
		</div>

		<div class="row invoice-info">
			<div class="col-md-4 invoice-col">
				<address>
					<h5 class="invoice-head"><?php echo $remitPymntToText; ?></h5>
					<strong><?php echo $set['businessName']; ?></strong><br>
					<?php echo nl2br($set['businessAddress']); ?><br>
					<?php echo $set['businessPhone']; ?><br/>
					<?php echo $set['businessEmail']; ?>
				</address>
			</div>
			<div class="col-md-4 invoice-col">
				<h5 class="invoice-head"><?php echo $billedToText; ?></h5>
				<address>
					<strong><?php echo clean($row['theClient']); ?></strong><br/>
					<?php echo clean($row['clientCompany']); ?><br>
					<?php echo nl2br($clientAddress); ?><br>
					<?php echo $clientPhone; ?>
				</address>
			</div>
			<div class="col-md-4 invoice-col">
				<table class="rwd-table invoice-table">
					<tr>
						<td class="no-print" style="width:50%"><?php echo $invoiceIdText; ?></td>
						<td class="text-right" data-th="<?php echo $invoiceIdText; ?>"><?php echo $invoiceId; ?></td>
					</tr>
					<tr>
						<td class="no-print"><?php echo $invoiceDateText; ?></td>
						<td class="text-right" data-th="<?php echo $invoiceDateText; ?>"><?php echo $row['invoiceDate']; ?></td>
					</tr>
					<tr>
						<td class="no-print"><?php echo $projectText; ?></td>
						<td class="text-right" data-th="<?php echo $projectText; ?>"><?php echo clean($row['projectName']); ?></td>
					</tr>
					<tr>
						<td class="no-print"><strong><?php echo $dateDueText; ?></strong></td>
						<td class="text-right" data-th="<?php echo $dateDueText; ?>"><strong><?php echo $row['invoiceDue']; ?></strong></td>
					</tr>
					<?php if ($row['isPaid'] == '1') { ?>
						<tr>
							<td class="no-print"><strong><?php echo $datePaidText; ?></strong></td>
							<td class="text-right" data-th="<?php echo $datePaidText; ?>"><strong><?php echo $row['datePaid']; ?></strong></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>

		<table class="rwd-table">
			<thead>
				<tr>
					<th><?php echo $itemText; ?></th>
					<th><?php echo $descriptionText; ?></th>
					<?php if ($row['isPaid'] == '0') { ?>
						<th class="no-print"></th>
					<?php } ?>
					<th><?php echo $amountText; ?></th>
					<th><?php echo $qtyText; ?></th>
					<th><?php echo $subtotalText; ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					while ($rows = mysqli_fetch_assoc($result)) {
						$itemAmount = number_format($rows['itemAmount'], 2, '.', '');
						$lineTotal = $rows['itemAmount'] * $rows['itemqty'];
						$lineTotal = number_format($lineTotal, 2, '.', '');
				?>
						<tr>
							<td data-th="<?php echo $itemText; ?>"><?php echo clean($rows['itemName']); ?></td>
							<td data-th="<?php echo $descriptionText; ?>"><?php echo clean($rows['itemDesc']); ?></td>
							<?php if ($row['isPaid'] == '0') { ?>
								<td data-th="<?php echo $actionsText; ?>" class="no-print">
									<a data-toggle="modal" href="#editLine<?php echo $rows['itemId']; ?>">
										<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $editItemTooltip; ?>"></i>
									</a>
									<a data-toggle="modal" href="#deleteLine<?php echo $rows['itemId']; ?>">
										<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteItemTooltip; ?>"></i>
									</a>
								</td>
							<?php } ?>
							<td data-th="<?php echo $amountText; ?>"><?php echo $itemAmount; ?></td>
							<td data-th="<?php echo $qtyText; ?>"><?php echo clean($rows['itemqty']); ?></td>
							<td data-th="<?php echo $subtotalText; ?>"><strong class="line-total"><?php echo $lineTotal; ?></strong></td>
						</tr>

						<?php if ($row['isPaid'] == '0') { ?>
							<div class="modal fade" id="editLine<?php echo $rows['itemId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
											<h4 class="modal-title"><?php echo $editItemTooltip; ?></h4>
										</div>
										<form action="" method="post">
											<div class="modal-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="itemName"><?php echo $itemNameText; ?></label>
															<input type="text" class="form-control" required="" name="itemName" value="<?php echo clean($rows['itemName']); ?>" />
															<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label for="itemqty"><?php echo $quantityText; ?></label>
															<input type="text" class="form-control" required="" name="itemqty" value="<?php echo clean($rows['itemqty']); ?>" />
														</div>
													</div>
													<div class="col-md-4">
														<div class="form-group">
															<label for="itemAmount"><?php echo $amountText; ?></label>
															<input type="text" class="form-control" required="" name="itemAmount" value="<?php echo clean($rows['itemAmount']); ?>" />
															<span class="help-block"><?php echo $invNumbersOnlyText; ?></span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label for="itemDesc"><?php echo $itemDescText; ?></label>
													<input type="text" class="form-control" required="" name="itemDesc" value="<?php echo clean($rows['itemDesc']); ?>" />
													<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
												</div>
											</div>
											<div class="modal-footer">
												<input name="itemId" type="hidden" value="<?php echo $rows['itemId']; ?>" />
												<button type="input" name="submit" value="editLine" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
												<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
											</div>
										</form>

									</div>
								</div>
							</div>

							<div class="modal fade" id="deleteLine<?php echo $rows['itemId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteLineItemConf.' '.clean($rows['itemName']); ?>?</p>
											</div>
											<div class="modal-footer">
												<input name="itemId" type="hidden" value="<?php echo $rows['itemId']; ?>" />
												<button type="input" name="submit" value="deleteLine" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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

		<input id="curSym" type="hidden" value="<?php echo $curSym; ?>" />
		<div class="row receipt-footer">
			<div class="col-xs-8">
				<?php if(mysqli_num_rows($alertres) > 0) { ?>
					<div class="well well-sm bg-trans no-margin">
						<?php
							while ($col = mysqli_fetch_assoc($alertres)) {
								echo nl2br(clean($col['alertText'])).'<br />';
							}
						?>
					</div>
				<?php
					}
					if ($row['invoiceNotes'] != '') {
				?>
						<p class="no-margin mt10"><?php echo nl2br(clean($row['invoiceNotes'])); ?></p>
				<?php
					}
					if ($row['isPaid'] == '1') {
						if ($cols['invoicepayNotes'] != '') {
				?>
							<p class="no-margin mt10"><?php echo nl2br(clean($cols['invoicepayNotes'])); ?></p>
				<?php
						}
					}
				?>
			</div>
			<div class="col-xs-4">
				<table class="rwd-table invoice-table">
					<tr>
						<td class="no-print" style="width:50%"><?php echo $subtotalText; ?></td>
						<td class="text-right" data-th="<?php echo $subtotalText; ?>"><span class="invoice-total"></span></td>
					</tr>
					<?php if ($row['isPaid'] == '1') { ?>
						<tr>
							<td class="no-print" style="width:50%"><?php echo $feesPaidText; ?></td>
							<td class="text-right" data-th="<?php echo $feesPaidText; ?>"><span class="invoice-fees"><?php echo $additionalFee; ?></span></td>
						</tr>
					<?php } else { ?>
						<span class="invoice-fees invisible"><?php echo $additionalFee; ?></span>
					<?php } ?>
					<tr>
						<td class="no-print"><strong><?php echo $totalDueText; ?></strong></td>
						<td class="text-right" data-th="<?php echo $totalDueText; ?>"><strong class="grand-total"></strong></td>
					</tr>
					<?php if ($row['isPaid'] == '1') { ?>
						<tr>
							<td class="no-print"><strong><?php echo $totalPaidText; ?></strong></td>
							<td class="text-right" data-th="<?php echo $totalPaidText; ?>"><strong><?php echo $invAmountPaid; ?></strong></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>

		<div class="clearfix"></div>
		<?php if ($row['isPaid'] == '0') { ?>
			<a data-toggle="modal" href="#editInvoice" class="btn btn-default btn-icon no-print mt10"><i class="fa fa-edit"></i> <?php echo $editInvoiceBtn; ?></a>
			<a data-toggle="modal" href="#addItem" class="btn btn-default btn-icon no-print mt10"><i class="fa fa-plus"></i> <?php echo $addLineItemBtn; ?></a>
			<a data-toggle="modal" href="#recordPayment" class="btn btn-default btn-icon no-print mt10"><i class="fa fa-money"></i> <?php echo $recordInvPaymentBtn; ?></a>
		<?php } ?>
		<button class="btn btn-default btn-icon no-print mt10" onclick="window.print();"><i class="fa fa-print"></i> <?php echo $printInvBtn; ?></button>
	</div>
</div>

<div id="editInvoice" class="modal fade no-print" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $editInvoiceModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="invoiceTitle"><?php echo $invoiceTitleField; ?></label>
								<input type="text" class="form-control" required="" name="invoiceTitle" value="<?php echo clean($row['invoiceTitle']); ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="invoiceDue"><?php echo $invoiceDateDueField; ?></label>
								<input type="text" class="form-control" required="" name="invoiceDue" id="invDue" value="<?php echo $row['invDue']; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="invoiceNotes"><?php echo $invoiceNotesField; ?></label>
						<textarea class="form-control" name="invoiceNotes" rows="2"><?php echo clean($row['invoiceNotes']); ?></textarea>
						<span class="help-block"><?php echo $invoiceNotesFieldHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="editInvoice" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>

<div id="addItem" class="modal fade no-print" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $addLineItemModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="itemName"><?php echo $itemNameText; ?></label>
								<input type="text" class="form-control" required="" name="itemName" value="<?php echo isset($_POST['itemName']) ? $_POST['itemName'] : ''; ?>" />
								<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label for="itemqty"><?php echo $quantityText; ?></label>
								<input type="text" class="form-control" required="" name="itemqty" value="<?php echo isset($_POST['itemqty']) ? $_POST['itemqty'] : ''; ?>" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="itemAmount"><?php echo $amountText; ?></label>
								<input type="text" class="form-control" required="" name="itemAmount" value="<?php echo isset($_POST['itemAmount']) ? $_POST['itemAmount'] : ''; ?>" />
								<span class="help-block"><?php echo $invNumbersOnlyText; ?></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="itemDesc"><?php echo $itemDescText; ?></label>
						<input type="text" class="form-control" required="" name="itemDesc" value="<?php echo isset($_POST['itemDesc']) ? $_POST['itemDesc'] : ''; ?>" />
						<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="addItem" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="recordPayment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $recordInvPaymentModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="paymentFor"><?php echo $paymentForField; ?></label>
						<input type="text" class="form-control" required="" name="paymentFor" value="Invoice ID#<?php echo $invoiceId; ?> Payment: <?php echo clean($row['invoiceTitle']); ?>" />
						<span class="help-block"><?php echo $invPaymentForHelp; ?></span>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="paymentDate"><?php echo $datePayReceivedField; ?></label>
								<input type="text" class="form-control" required="" name="paymentDate" id="paymentDate" value="<?php echo isset($_POST['paymentDate']) ? $_POST['paymentDate'] : ''; ?>" />
								<span class="help-block"><?php echo $dateFormatHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="paidBy"><?php echo $paidByText; ?></label>
								<input type="text" class="form-control" required="" name="paidBy" value="<?php echo isset($_POST['paidBy']) ? $_POST['paidBy'] : ''; ?>" />
								<span class="help-block"><?php echo $paidByFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="paymentAmount"><?php echo $baseAmountField; ?></label>
								<input type="text" class="form-control" required="" name="paymentAmount" value="<?php echo isset($_POST['paymentAmount']) ? $_POST['paymentAmount'] : ''; ?>" />
								<span class="help-block"><?php echo $baseAmountFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="additionalFee"><?php echo $feesAmountField; ?></label>
								<input type="text" class="form-control" name="additionalFee" value="<?php echo isset($_POST['additionalFee']) ? $_POST['additionalFee'] : ''; ?>" />
								<span class="help-block"><?php echo $feesAmountFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="paymentNotes"><?php echo $paymentNotesField; ?></label>
						<textarea class="form-control" name="paymentNotes" rows="2"><?php echo isset($_POST['paymentNotes']) ? $_POST['paymentNotes'] : ''; ?></textarea>
						<span class="help-block"><?php echo $paymentNotesFieldHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<input name="cId" type="hidden" value="<?php echo $row['clientId']; ?>" />
					<input name="pId" type="hidden" value="<?php echo $row['projectId']; ?>" />
					<button type="input" name="submit" value="recordPayment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>