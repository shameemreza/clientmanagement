<?php
	$invoiceId = $_GET['invoiceId'];
	$jsFile = 'viewInvoice';
	$additionalFee = '0';

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
				invoices.isPaid,
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

		//$invAmountPaid = $curSym.format_amount($cols['paymentAmount'], 2);
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

	if ($row['clientId'] != $clientId) {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="content last">
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="invoice">
			<div class="row">
				<div class="col-md-8 invoice-col">
					<img src="images/footer_logo.png" alt="<?php echo $set['siteName']; ?>" />
				</div>
				<div class="col-md-4 invoice-col">
					<p class="no-margin text-muted text-right"><?php echo $printedOnText.' '.date('F d, Y'); ?></p>
				</div>
			</div>

			<div class="row invoice-info">
				<div class="col-md-4 invoice-col">
					<address>
						<h5 class="invoice-head"><?php echo $remitPaymentToText; ?></h5>
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
							<td class="no-print"><?php echo $invDateText; ?></td>
							<td class="text-right" data-th="<?php echo $invDateText; ?>"><?php echo $row['invoiceDate']; ?></td>
						</tr>
						<tr>
							<td class="no-print"><?php echo $projectNameText; ?></td>
							<td class="text-right" data-th="<?php echo $projectNameText; ?>"><?php echo clean($row['projectName']); ?></td>
						</tr>
						<tr>
							<td class="no-print"><strong><?php echo $dateDueText; ?></strong></td>
							<td class="text-right" data-th="<?php echo $dateDueText; ?>"><strong><?php echo $row['invoiceDue']; ?></strong></td>
						</tr>
						<?php if ($row['isPaid'] == '1') { ?>
							<tr>
								<td class="no-print"><strong><?php echo $dateReceived; ?></strong></td>
								<td class="text-right" data-th="<?php echo $dateReceived; ?>"><strong><?php echo $cols['paymentDate']; ?></strong></td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>

			<table class="rwd-table">
				<thead>
					<tr>
						<th><?php echo $itemText; ?></th>
						<th><?php echo $descText; ?></th>
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
								<td data-th="<?php echo $descText; ?>"><?php echo clean($rows['itemDesc']); ?></td>
								<td data-th="<?php echo $amountText; ?>"><?php echo $itemAmount; ?></td>
								<td data-th="<?php echo $qtyText; ?>"><?php echo clean($rows['itemqty']); ?></td>
								<td data-th="<?php echo $subtotalText; ?>"><strong class="line-total"><?php echo $lineTotal; ?></strong></td>
							</tr>
					<?php } ?>
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
				<a href="index.php?page=newPayment&projectId=<?php echo $row['projectId']; ?>&invoiceId=<?php echo $invoiceId; ?>" class="btn btn-default btn-icon no-print mt10"><i class="fa fa-credit-card"></i> <?php echo $payInvoiceBtn; ?></a>
			<?php } ?>
			<button class="btn btn-default btn-icon no-print mt10" onclick="window.print();"><i class="fa fa-print"></i> <?php echo $printInvoiceBtn; ?></button>
		</div>
	</div>
<?php } ?>