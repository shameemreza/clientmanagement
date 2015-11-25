<?php
	$validReport = '';
	
	// Server Side validation
	if($_POST['client'] == "...") {
		$msgBox = alertBox($reportError1, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else if($_POST['fromDate'] == "") {
		$msgBox = alertBox($reportError2, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else if($_POST['toDate'] == "") {
		$msgBox = alertBox($reportError3, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else {
		// Report Options
		$clientId = $mysqli->real_escape_string($_POST['client']);
		$clientName = $mysqli->real_escape_string($_POST['clientFullName']);
		if (!empty($_POST['fromDate'])) {
			$fromDate = $mysqli->real_escape_string($_POST['fromDate']);
			$fdate = date('F d, Y', strtotime($fromDate));
		}
		if (!empty($_POST['toDate'])) {
			$toDate = $mysqli->real_escape_string($_POST['toDate']);
			$tdate = date('F d, Y', strtotime($toDate));
		}
		
		// Get Data
		$query  = "SELECT
						projectpayments.paymentId,
						projectpayments.clientId,
						projectpayments.projectId,
						projectpayments.enteredBy,
						projectpayments.paymentFor,
						projectpayments.paymentDate,
						DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS datePaid,
						UNIX_TIMESTAMP(projectpayments.paymentDate) AS orderDate,
						projectpayments.paidBy,
						projectpayments.paymentAmount,
						projectpayments.additionalFee,
						clientprojects.projectName,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						projectpayments
						LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
						LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
					WHERE
						projectpayments.clientId = ".$clientId." AND
						projectpayments.invoiceId = 0 AND
						projectpayments.paymentDate >= '".$fromDate."' AND projectpayments.paymentDate <= '".$toDate."'
					ORDER BY
						orderDate DESC,
						projectpayments.paymentId";
		$res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
		
		// Get the Totals
		$totals = "SELECT
					SUM(paymentAmount) AS totalPaid,
					SUM(additionalFee) AS totalFee
				FROM
					projectpayments
				WHERE
					clientId = ".$clientId." AND
					invoiceId = 0 AND
					paymentDate >= '".$fromDate."' AND
					paymentDate <= '".$toDate."'";
		$total = mysqli_query($mysqli, $totals) or die('-2' . mysqli_error());
		$tot = mysqli_fetch_assoc($total);
		$grandTotal = $curSym.format_amount($tot['totalPaid'] + $tot['totalFee'], 2);
	}

	include 'includes/navigation.php';
?>
<div class="content last">
	<h4><?php echo $pageName; ?></h4>
	<?php if ($validReport == '') { ?>
		<p>
			<span class="label label-default preview-label">
				<?php echo $clientText; ?>: <a href="index.php?action=viewClient&clientId=<?php echo $clientId; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>"><?php echo $clientName; ?></a>
			</span>
			<span class="label label-default preview-label ml5"><?php echo $fdate.' &mdash; '.$tdate; ?></span>
			<span class="label label-default preview-label ml5"><?php echo $totalRecordsLabel.': '.$totalRecs; ?></span>
			<span class="label label-default preview-label pull-right"><a href="index.php?action=reports"><i class="fa fa-bar-chart-o"></i> <?php echo $reportsLabel; ?></a></span>
		</p>
	
		<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin mt20">
			<i class="fa fa-warning"></i> <?php echo $noReportResults; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table">
			<tbody>
				<tr>
					<th><?php echo $projectText; ?></th>
					<th><?php echo $paymentDateText; ?></th>
					<th><?php echo $enteredByText; ?></th>
					<th><?php echo $capForText; ?></th>
					<th><?php echo $paidByText; ?></th>
					<th><?php echo $feesPaidText; ?></th>
					<th><?php echo $amountPaidText; ?></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						if ($row['additionalFee'] != '') {
							$additionalFee = $curSym.format_amount($row['additionalFee'], 2);
						} else {
							$additionalFee = '';
						}
						$lineTotal = $curSym.format_amount($row['paymentAmount'] + $row['additionalFee'], 2);
				?>
					<tr>
						<td data-th="<?php echo $projectText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $paymentDateText; ?>"><?php echo $row['datePaid']; ?></td>
						<td data-th="<?php echo $enteredByText; ?>"><?php echo clean($row['theAdmin']); ?></td>
						<td data-th="<?php echo $capForText; ?>"><?php echo clean($row['paymentFor']); ?></td>
						<td data-th="<?php echo $paidByText; ?>"><?php echo clean($row['paidBy']); ?></td>
						<td data-th="<?php echo $feesPaidText; ?>"><?php echo $additionalFee; ?></td>
						<td data-th="<?php echo $amountPaidText; ?>"><?php echo $lineTotal; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=clientPaymentsExport" method="post" class="mt10" target="_blank">
			<input type="hidden" name="clientId" value="<?php echo $clientId; ?>" />
			<input type="hidden" name="fromDate" value="<?php echo $fromDate; ?>" />
			<input type="hidden" name="toDate" value="<?php echo $toDate; ?>" />
			<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
			<span class="label label-default preview-label pull-right"><strong><?php echo $grandTotalText; ?>:</strong> <?php echo $grandTotal; ?></strong></span>
		</form>
	<?php
			}
		} else {
	?>
		<p class="clearfix"><span class="label label-default preview-label pull-right"><a href="index.php?action=reports"><i class="fa fa-bar-chart-o"></i> <?php echo $reportsLabel; ?></a></span></p>
		<div class="mt20">
	<?php
		if ($msgBox) { echo $msgBox; }
	}
	?>
		</div>
</div>