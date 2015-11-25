<?php
    $sql = "SELECT
				projectpayments.paymentId,
				projectpayments.clientId,
				projectpayments.projectId,
				projectpayments.invoiceId,
				projectpayments.enteredBy,
				projectpayments.paymentFor,
				DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
				UNIX_TIMESTAMP(projectpayments.paymentDate) AS orderDate,
				projectpayments.paidBy,
				projectpayments.paymentAmount,
				projectpayments.additionalFee,
				clientprojects.projectName,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
			FROM
				projectpayments
				LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
				LEFT JOIN clients ON projectpayments.clientId = clients.clientId
			ORDER BY orderDate";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
	
	// Get the Totals
	$totals = "SELECT
					SUM(paymentAmount) AS totalPaid,
					SUM(additionalFee) AS totalFee
				FROM
					projectpayments";
	$total = mysqli_query($mysqli, $totals) or die('-2' . mysqli_error());
	$tot = mysqli_fetch_assoc($total);
	$grandTotal = $curSym.format_amount($tot['totalPaid'] + $tot['totalFee'], 2);
	
	include 'includes/navigation.php';
?>
<div class="content last">
	<h4><?php echo $pageName; ?></h4>
	<p>
		<span class="label label-default preview-label"><?php echo $reportLabelAllPayments; ?></span>
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
				<tr class="primary">
					<th><?php echo $projectText; ?></th>
					<th><?php echo $clientText; ?></th>
					<th><?php echo $paymentDateText; ?></th>
					<th><?php echo $capForText; ?></th>
					<th><?php echo $paidByText; ?></th>
					<th><?php echo $totalPaidText; ?></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						$lineTotal = $curSym.format_amount($row['paymentAmount'] + $row['additionalFee'], 2);
				?>
					<tr>
						<td data-th="<?php echo $projectText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $clientText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
								<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>"><?php echo clean($row['theClient']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $paymentDateText; ?>"><?php echo $row['paymentDate']; ?></td>
						<td data-th="<?php echo $capForText; ?>"><?php echo clean($row['paymentFor']); ?></td>
						<td data-th="<?php echo $paidByText; ?>"><?php echo clean($row['paidBy']); ?></td>
						<td data-th="<?php echo $totalPaidText; ?>"><?php echo $lineTotal; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=allPaymentsExport" method="post" class="mt10" target="_blank">
			<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
			<span class="label label-default preview-label pull-right"><strong><?php echo $grandTotalText; ?>:</strong> <?php echo $grandTotal; ?></strong></span>
		</form>
		<div class="clearfix"></div>
	<?php } ?>
</div>