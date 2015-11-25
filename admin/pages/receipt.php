<?php
	$paymentId = $_GET['paymentId'];

	// Get Data
	$query = "SELECT
				projectpayments.paymentId,
				projectpayments.clientId,
				projectpayments.projectId,
				projectpayments.enteredBy,
				projectpayments.paymentFor,
				DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
				projectpayments.paidBy,
				projectpayments.paymentAmount,
				projectpayments.additionalFee,
				projectpayments.paymentNotes,
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clients.clientCompany,
				clients.clientAddress,
				clients.clientPhone
			FROM
				projectpayments
				LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
				LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
				LEFT JOIN clients ON projectpayments.clientId = clients.clientId
			WHERE
				projectpayments.paymentId = ".$paymentId;
	$res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data for display
	if ($row['clientAddress'] != '') { $clientAddress = decryptIt($row['clientAddress']); } else { $clientAddress = ''; }
	if ($row['clientPhone'] != '') { $clientPhone = decryptIt($row['clientPhone']); } else { $clientPhone = ''; }

	// Format the Amounts
	$paymentAmount = $curSym.format_amount($row['paymentAmount'], 2);
	if ($row['additionalFee'] != '') {
		$additionalFee = $curSym.format_amount($row['additionalFee'], 2);
	} else {
		$additionalFee = 'None';
	}
	$total = $row['paymentAmount'] + $row['additionalFee'];
	$totalPaid = $curSym.format_amount($total, 2);

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
    $alertres = mysqli_query($mysqli, $alert) or die('-2'.mysqli_error());

	// Only allow access to the Assigned Manager or Admins
	$qry = "SELECT
				assignedprojects.assignedTo
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
					<h5 class="invoice-head"><?php echo $paidToText; ?></h5>
					<address>
						<strong><?php echo $set['businessName']; ?></strong><br>
						<?php echo nl2br($set['businessAddress']); ?><br>
						<?php echo $set['businessPhone']; ?><br/>
						<?php echo $set['businessEmail']; ?>
					</address>
				</div>
				<div class="col-md-4 invoice-col">
					<h5 class="invoice-head"><?php echo $receivedFromText; ?></h5>
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
							<td class="no-print" style="width:50%"><?php echo $paymentIdText; ?></td>
							<td class="text-right" data-th="<?php echo $paymentIdText; ?>"><?php echo $row['paymentId']; ?></td>
						</tr>
						<tr>
							<td class="no-print"><?php echo $paymentDateText; ?></td>
							<td class="text-right" data-th="<?php echo $paymentDateText; ?>"><?php echo $row['paymentDate']; ?></td>
						</tr>
					</table>
				</div>
			</div>

			<table class="rwd-table">
				<thead>
					<tr>
						<th><?php echo $descriptionText; ?></th>
						<th><?php echo $projectText; ?></th>
						<th><?php echo $paidByText; ?></th>
						<th><?php echo $amountText; ?></th>
						<th><?php echo $additionalFeeText; ?></th>
						<th><?php echo $subtotalText; ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td data-th="<?php echo $descriptionText; ?>"><?php echo clean($row['paymentFor']); ?></td>
						<td data-th="<?php echo $projectText; ?>"><?php echo clean($row['projectName']); ?></td>
						<td data-th="<?php echo $paidByText; ?>"><?php echo clean($row['paidBy']); ?></td>
						<td data-th="<?php echo $amountText; ?>"><?php echo $paymentAmount; ?></td>
						<td data-th="<?php echo $additionalFeeText; ?>"><?php echo $additionalFee; ?></td>
						<td data-th="<?php echo $subtotalText; ?>"><?php echo $totalPaid; ?></td>
					</tr>
				</tbody>
			</table>

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
					<?php } ?>
				</div>
				<div class="col-xs-4">
					<table class="rwd-table invoice-table">
						<tr>
							<td class="no-print" style="width:50%"><?php echo $subtotalText; ?></td>
							<td class="text-right" data-th="<?php echo $subtotalText; ?>"><?php echo $paymentAmount; ?></td>
						</tr>
						<?php if ($row['additionalFee'] != '') { ?>
							<tr>
								<td class="no-print"><?php echo $additionalFeeText; ?></td>
								<td class="text-right" data-th="<?php echo $additionalFeeText; ?>"><?php echo $additionalFee; ?></td>
							</tr>
						<?php } ?>
						<tr>
							<td class="no-print"><strong><?php echo $totalPaidText; ?></strong></td>
							<td class="text-right" data-th="<?php echo $totalPaidText; ?>"><strong><?php echo $totalPaid; ?></strong></td>
						</tr>
					</table>
				</div>
			</div>

			<?php if ($row['paymentNotes'] != '') { ?>
				<p class="well well-sm no-shadow"><?php echo $paymentNotesField.': '.nl2br(clean($row['paymentNotes'])); ?></p>
			<?php } ?>

			<div class="clearfix"></div>
			<a href="index.php?action=viewPayment&paymentId=<?php echo $paymentId; ?>" class="btn btn-success no-print mt10"><i class="fa fa-edit"></i> <?php echo $editPaymentBtn; ?></a>
			<button class="btn btn-info no-print mt10" onclick="window.print();"><i class="fa fa-print"></i> <?php echo $receiptPrintBtn; ?></button>
		</div>
	</div>
<?php } ?>