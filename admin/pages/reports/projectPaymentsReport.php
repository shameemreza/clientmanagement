<?php
	$validReport = '';
	
	// Server Side validation
	if($_POST['project'] == "...") {
		$msgBox = alertBox($reportError4, "<i class='fa fa-warning'></i>", "default");
		$validReport = 'false';
	} else {
		// Report Options
		$projectId = $mysqli->real_escape_string($_POST['project']);
		$projectName = $mysqli->real_escape_string($_POST['projectFullName']);
		
		// Get Data
		$query = "SELECT
					projectpayments.paymentId,
					projectpayments.clientId,
					projectpayments.projectId,
					projectpayments.invoiceId,
					projectpayments.enteredBy,
					projectpayments.paymentFor,
					projectpayments.paymentDate,
					DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS datePaid,
					projectpayments.paymentAmount,
					projectpayments.additionalFee,
					clientprojects.projectName,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
				FROM
					projectpayments
					LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
					LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
					LEFT JOIN clients ON projectpayments.clientId = clients.clientId
				WHERE
					projectpayments.projectId = ".$projectId."
				ORDER BY
					projectpayments.projectId,
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
					projectId = ".$projectId." AND
					invoiceId = 0";
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
				<a href="index.php?action=viewProject&projectId=<?php echo $projectId; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>"><?php echo $projectName; ?></a>
			</span>
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
					<th><?php echo $clientNameText; ?></th>
					<th><?php echo $paymentDateText; ?></th>
					<th><?php echo $enteredByText; ?></th>
					<th><?php echo $capForText; ?></th>
					<th><?php echo $fromInvoiceText; ?></th>
					<th><?php echo $feesPaidText; ?></th>
					<th><?php echo $amountPaidText; ?></th>
					<th><?php echo $totalPaidText; ?></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						$lineTotal = $curSym.format_amount($row['paymentAmount'] + $row['additionalFee'], 2);
						if ($row['invoiceId'] == '0') { $fromInvoice = 'No'; } else { $fromInvoice = 'Yes'; }
				?>
					<tr>
						<td data-th="<?php echo $clientNameText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
								<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>"><?php echo clean($row['theClient']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $paymentDateText; ?>"><?php echo $row['datePaid']; ?></td>
						<td data-th="<?php echo $enteredByText; ?>"><?php echo $row['theAdmin']; ?></td>
						<td data-th="<?php echo $capForText; ?>"><?php echo clean($row['paymentFor']); ?></td>
						<td data-th="<?php echo $fromInvoiceText; ?>"><?php echo $fromInvoice; ?></td>
						<td data-th="<?php echo $feesPaidText; ?>"><?php echo $row['additionalFee']; ?></td>
						<td data-th="<?php echo $amountPaidText; ?>"><?php echo $row['paymentAmount']; ?></td>
						<td data-th="<?php echo $totalPaidText; ?>"><?php echo $lineTotal; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=projectPaymentsExport" method="post" class="mt10" target="_blank">
			<input type="hidden" name="projectId" value="<?php echo $projectId; ?>" />
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