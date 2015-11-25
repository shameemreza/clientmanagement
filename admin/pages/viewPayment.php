<?php
	$paymentId = $_GET['paymentId'];
	$datePicker = 'true';
	$jsFile = 'viewPayment';

	// Edit Payment
    if (isset($_POST['submit']) && $_POST['submit'] == 'editPayment') {
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
			$paymentFor = $mysqli->real_escape_string($_POST['paymentFor']);
			$paymentDate = $mysqli->real_escape_string($_POST['paymentDate']);
			$paidBy = $mysqli->real_escape_string($_POST['paidBy']);
			$paymentAmount = $mysqli->real_escape_string($_POST['paymentAmount']);
			$additionalFee = $mysqli->real_escape_string($_POST['additionalFee']);
			$paymentNotes = $_POST['paymentNotes'];
			$invoicepayNotes = $_POST['invoicepayNotes'];

            $stmt = $mysqli->prepare("UPDATE
										projectpayments
									SET
										paymentFor = ?,
										paymentDate = ?,
										paidBy = ?,
										paymentAmount = ?,
										additionalFee = ?,
										paymentNotes = ?,
										invoicepayNotes = ?
									WHERE
										paymentId = ?"
			);
			$stmt->bind_param('ssssssss',
									$paymentFor,
									$paymentDate,
									$paidBy,
									$paymentAmount,
									$additionalFee,
									$paymentNotes,
									$invoicepayNotes,
									$paymentId
			);
			$stmt->execute();
			$msgBox = alertBox($projPaymentUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Get Payment Data
	$query = "SELECT
				projectpayments.paymentId,
				projectpayments.projectId,
				projectpayments.invoiceId,
				projectpayments.enteredBy,
				projectpayments.paymentFor,
				DATE_FORMAT(projectpayments.paymentDate,'%Y-%m-%d') AS datePaid,
				DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
				UNIX_TIMESTAMP(projectpayments.paymentDate) AS orderDate,
				projectpayments.paidBy,
				projectpayments.paymentAmount,
				projectpayments.additionalFee,
				projectpayments.paymentNotes,
				projectpayments.invoicepayNotes,
				clientprojects.projectName,
				clientprojects.projectPayments,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectpayments
				LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
				LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
			WHERE
				projectpayments.paymentId = ".$paymentId."
			ORDER BY orderDate DESC";
	$res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Format the Amounts
	$paymentAmount = $curSym.format_amount($row['paymentAmount'], 2);
	if ($row['additionalFee'] != '') { $additionalFee = $curSym.format_amount($row['additionalFee'], 2); $highlight = 'text-danger'; } else { $additionalFee = ''; $highlight = ''; }
	$totreceived = $row['paymentAmount'] + $row['additionalFee'];
	$totalPaid = $curSym.format_amount($totreceived, 2);

	// Only allow access to the Assigned Manager or Admins
	$qry = "SELECT
				assignedprojects.assignedTo
			FROM
				assignedprojects
				LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
			WHERE assignedprojects.projectId = ".$row['projectId'];
	$result = mysqli_query($mysqli, $qry) or die('-2' . mysqli_error());
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
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-folder-open-o"></i> <?php echo $projectText; ?>:</td>
						<td class="infoVal">
							<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($row['projectName']); ?>
							</a>
						</td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-quote-left"></i> <?php echo $capForText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['paymentFor']); ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-usd"></i> <?php echo $amountPaidText; ?>:</td>
						<td class="infoVal"><?php echo $paymentAmount; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-male"></i> <?php echo $receivedByText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['theAdmin']); ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $paymentDateText; ?>:</td>
						<td class="infoVal"><?php echo $row['paymentDate']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-info-circle"></i> <?php echo $paidByText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['paidBy']); ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-usd"></i> <?php echo $feesPaidText; ?>:</td>
						<td class="infoVal"><strong class="<?php echo $highlight; ?>"><?php echo $additionalFee; ?></strong></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-money"></i> <?php echo $totalRcvdText; ?>:</td>
						<td class="infoVal"><?php echo $totalPaid; ?></td>
					</tr>
				</table>
			</div>
		</div>

		<div class="well well-sm bg-trans mt20">
			<strong><?php echo $paymentNotesField; ?>:</strong> <?php echo nl2br(clean($row['paymentNotes'])); ?>
		</div>

		<?php if (!empty($row['invoicepayNotes'])) { ?>
			<div class="well well-sm bg-trans mt20">
				<strong><?php echo $invoiceNotesField; ?>:</strong> <?php echo nl2br(clean($row['invoicepayNotes'])); ?>
			</div>
		<?php } ?>

		<a data-toggle="modal" data-target="#editPayment" class="btn btn-success btn-icon"><i class="fa fa-edit"></i> <?php echo $updatePaymentBtn; ?></a>
		<a href="index.php?action=receipt&paymentId=<?php echo $paymentId; ?>" class="btn btn-info btn-icon"><i class="fa fa-print"></i> <?php echo $printReceiptTooltip; ?></a>
	</div>

	<div id="editPayment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $updatePaymentBtn; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="paymentFor"><?php echo $paymentForField; ?></label>
							<input type="text" class="form-control" required="" name="paymentFor" value="<?php echo clean($row['paymentFor']); ?>" />
							<span class="help-block"><?php echo $paymentForFieldHelp; ?></span>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="paymentDate"><?php echo $datePayReceivedField; ?></label>
									<input type="text" class="form-control" required="" name="paymentDate" id="paymentDate" value="<?php echo $row['datePaid']; ?>" />
									<span class="help-block"><?php echo $dateFormatHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="paidBy"><?php echo $paidByText; ?></label>
									<input type="text" class="form-control" required="" name="paidBy" value="<?php echo clean($row['paidBy']); ?>" />
									<span class="help-block"><?php echo $paidByFieldHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="paymentAmount"><?php echo $baseAmountField; ?></label>
									<input type="text" class="form-control" required="" name="paymentAmount" value="<?php echo clean($row['paymentAmount']); ?>" />
									<span class="help-block"><?php echo $baseAmountFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="additionalFee"><?php echo $feesAmountField; ?></label>
									<input type="text" class="form-control" name="additionalFee" value="<?php echo clean($row['additionalFee']); ?>" />
									<span class="help-block"><?php echo $feesAmountFieldHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="paymentNotes"><?php echo $paymentNotesField; ?></label>
							<textarea class="form-control" name="paymentNotes" rows="2"><?php echo clean($row['paymentNotes']); ?></textarea>
							<span class="help-block"><?php echo $paymentNotesFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="invoicepayNotes"><?php echo $invoiceNotesField; ?></label>
							<textarea class="form-control" name="invoicepayNotes" rows="2"><?php echo clean($row['invoicepayNotes']); ?></textarea>
							<span class="help-block"><?php echo $invoiceNotesFieldHelp; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="editPayment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>