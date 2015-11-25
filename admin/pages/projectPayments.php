<?php
	$projectId = $_GET['projectId'];
	$getId = 'projectId='.$projectId;
	$pagPages = '10';

	// Add New Project Payment
    if (isset($_POST['submit']) && $_POST['submit'] == 'newPayment') {
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
			$projectPayments = $mysqli->real_escape_string($_POST['projectPayments']);
			$paymentNotes = $_POST['paymentNotes'];

			// Update the Project Record
			$paymentNumber = $projectPayments + 1;
			$stmt = $mysqli->prepare("UPDATE clientprojects SET projectPayments = ? WHERE projectId = ?");
			$stmt->bind_param('ss', $paymentNumber, $projectId);
			$stmt->execute();
			$stmt->close();

			// Save the Payment Data
			$stmt = $mysqli->prepare("
								INSERT INTO
									projectpayments(
										projectId,
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
										?
									)
			");
			$stmt->bind_param('ssssssss',
								$projectId,
								$adminId,
								$paymentFor,
								$paymentDate,
								$paidBy,
								$paymentAmount,
								$additionalFee,
								$paymentNotes
			);
			$stmt->execute();
			$msgBox = alertBox($paymentSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['paymentFor'] = $_POST['paymentDate'] = $_POST['paidBy'] = $_POST['paymentAmount'] = $_POST['additionalFee'] = $_POST['paymentNotes'] = '';
			$stmt->close();
		}
	}

	// Delete Payment
	if (isset($_POST['submit']) && $_POST['submit'] == 'deletePayment') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$projectPayments = $mysqli->real_escape_string($_POST['projectPayments']);

		// Update the Project Record
		$paymentNumber = $projectPayments - 1;
		$stmt = $mysqli->prepare("UPDATE clientprojects SET projectPayments = ? WHERE projectId = ?");
		$stmt->bind_param('ss', $paymentNumber, $projectId);
		$stmt->execute();
		$stmt->close();

		// Delete the Payment
		$stmt = $mysqli->prepare("DELETE FROM projectpayments WHERE paymentId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$msgBox = alertBox($paymentDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Include Pagination Class
	include('includes/getpagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM projectpayments WHERE projectId = ".$projectId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Payment Data
	$query = "SELECT
				projectpayments.paymentId,
				projectpayments.projectId,
				projectpayments.invoiceId,
				projectpayments.paymentFor,
				DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
				UNIX_TIMESTAMP(projectpayments.paymentDate) AS orderDate,
				projectpayments.paidBy,
				projectpayments.paymentAmount,
				projectpayments.additionalFee,
				projectpayments.paymentNotes,
				clientprojects.projectName,
				clientprojects.projectPayments
			FROM
				projectpayments
				LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
			WHERE
				projectpayments.projectId = ".$projectId."
			ORDER BY orderDate DESC ".$pages->get_limit();
	$res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	// Only allow access to the Assigned Manager or Admins
	$qry = "SELECT
				assignedprojects.assignedTo
			FROM
				assignedprojects
				LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
			WHERE assignedprojects.projectId = ".$projectId;
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
	<div class="contentAlt">
		<ul class="nav nav-tabs">
			<li class="pull-right"><a href="#newPayment" data-toggle="modal"><i class="fa fa-credit-card"></i> <?php echo $recordNewPaymentBtn; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<?php
			if(mysqli_num_rows($res) < 1) {
		?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-info-circle"></i> <?php echo $noProjPaymentsFound; ?>
				</div>
		<?php
			} else {
		?>
				<table class="rwd-table">
					<tbody>
						<tr>
							<th class="text-left"><?php echo $projectTableHead; ?></th>
							<th><?php echo $paymentDateText; ?></th>
							<th><?php echo $capForText; ?></th>
							<th><?php echo $paymentAmtText; ?></th>
							<th><?php echo $feeAmtText; ?></th>
							<th><?php echo $totalPaidText; ?></th>
							<th><?php echo $paidByText; ?></th>
							<th></th>
						</tr>
						<?php
							while ($row = mysqli_fetch_assoc($res)) {
								// Format the Amounts
								$paymentAmount = $curSym.format_amount($row['paymentAmount'], 2);
								if ($row['additionalFee'] != '') { $additionalFee = $curSym.format_amount($row['additionalFee'], 2); $highlight = 'class="text-danger"'; } else { $additionalFee = ''; $highlight = ''; }
								$totreceived = $row['paymentAmount'] + $row['additionalFee'];
								$totalPaid = $curSym.format_amount($totreceived, 2);

						?>
								<tr>
									<td class="text-left" data-th="<?php echo $projectTableHead; ?>">
										<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
											<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
										</span>
									</td>
									<td data-th="<?php echo $paymentDateText; ?>"><?php echo $row['paymentDate']; ?></td>
									<td data-th="<?php echo $capForText; ?>">
										<span data-toggle="tooltip" data-placement="right" title="<?php echo $editPaymentTooltip; ?>">
											<a href="index.php?action=viewPayment&paymentId=<?php echo $row['paymentId']; ?>"><?php echo clean($row['paymentFor']); ?></a>
										</span>
									</td>
									<td data-th="<?php echo $paymentAmtText; ?>">
										<span data-toggle="tooltip" data-placement="left" title="<?php echo clean($row['paymentNotes']); ?>">
											<?php echo $paymentAmount; ?>
										</span>
									</td>
									<td <?php echo $highlight; ?> data-th="<?php echo $feeAmtText; ?>"><?php echo $additionalFee; ?></td>
									<td data-th="<?php echo $totalPaidText; ?>"><?php echo $totalPaid; ?></td>
									<td data-th="<?php echo $paidByText; ?>"><?php echo clean($row['paidBy']); ?></td>
									<td class="text-right" data-th="options">
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $editPaymentTooltip; ?>">
											<a href="index.php?action=viewPayment&paymentId=<?php echo $row['paymentId']; ?>"><i class="fa fa-money edit"></i></a>
										</span>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $printReceiptTooltip; ?>">
											<a href="index.php?action=receipt&paymentId=<?php echo $row['paymentId']; ?>"><i class="fa fa-print print"></i></a>
										</span>
										<span data-toggle="tooltip" data-placement="left" title="<?php echo $deletePaymentTooltip; ?>">
											<a href="#deletePayment<?php echo $row['paymentId']; ?>" data-toggle="modal"><i class="fa fa-times remove"></i></a>
										</span>
									</td>
								</tr>

								<div class="modal fade" id="deletePayment<?php echo $row['paymentId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
									<div class="modal-dialog">
										<div class="modal-content">
											<form action="" method="post">
												<div class="modal-body">
													<p class="lead"><?php echo $deletePaymentConf.' '.$row['paymentDate'].' '.$forText.' '.$totalPaid; ?>?</p>
												</div>
												<div class="modal-footer">
													<input name="deleteId" type="hidden" value="<?php echo $row['paymentId']; ?>" />
													<input name="projectPayments" type="hidden" value="<?php echo $row['projectPayments']; ?>" />
													<button type="input" name="submit" value="deletePayment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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

	<div id="newPayment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $recordNewPaymentModal; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="paymentFor"><?php echo $paymentForField; ?></label>
							<input type="text" class="form-control" required="" name="paymentFor" value="<?php echo isset($_POST['paymentFor']) ? $_POST['paymentFor'] : ''; ?>" />
							<span class="help-block"><?php echo $paymentForFieldHelp; ?></span>
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
						<input name="projectPayments" type="hidden" value="<?php echo $row['projectPayments']; ?>" />
						<button type="input" name="submit" value="newPayment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>