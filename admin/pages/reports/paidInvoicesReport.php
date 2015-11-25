<?php
	$validReport = '';
	
	// Server Side validation
	if($_POST['invFromDate'] == "") {
		$msgBox = alertBox($reportError2, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else if($_POST['invToDate'] == "") {
		$msgBox = alertBox($reportError3, "<i class='fa fa-warning'></i>", "warning");
		$validReport = 'false';
	} else {
		// Report Options
		if (!empty($_POST['invFromDate'])) {
			$invFromDate = $mysqli->real_escape_string($_POST['invFromDate']);
			$fdate = date('F d, Y', strtotime($invFromDate));
		}
		if (!empty($_POST['invToDate'])) {
			$invToDate = $mysqli->real_escape_string($_POST['invToDate']);
			$tdate = date('F d, Y', strtotime($invToDate));
		}

		$sql = "SELECT
					invoices.invoiceId,
					invoices.projectId,
					invoices.adminId,
					invoices.clientId,
					invoices.invoiceTitle,
					DATE_FORMAT(invoices.invoiceDate,'%M %d, %Y') AS invoiceDate,
					UNIX_TIMESTAMP(invoices.invoiceDue) AS orderDate,
					invoices.isPaid,
					DATE_FORMAT(invoices.lastUpdated,'%M %d, %Y') AS lastUpdated,
					clientprojects.projectName,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
				FROM
					invoices
					LEFT JOIN clientprojects ON invoices.projectId = clientprojects.projectId
					LEFT JOIN clients ON invoices.clientId = clients.clientId
					LEFT JOIN admins ON invoices.adminId = admins.adminId
				WHERE
					invoices.isPaid = 1 AND
					invoices.invoiceDate >= '".$invFromDate."' AND invoices.invoiceDate <= '".$invToDate."'
				ORDER BY orderDate";
		$res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());
		$totalRecs = mysqli_num_rows($res);
	}
	
	include 'includes/navigation.php';
?>
<div class="content last">
	<h4><?php echo $pageName; ?></h4>
	<p>
		<span class="label label-default preview-label"><?php echo $fdate.' &mdash; '.$tdate; ?></span>
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
					<th><?php echo $invoiceTableHead; ?></th>
					<th><?php echo $createdByTableHead; ?></th>
					<th><?php echo $projectText; ?></th>
					<th><?php echo $clientText; ?></th>
					<th><?php echo $invoiceDateText; ?></th>
					<th><?php echo $lastUpdatedDateText; ?></th>
					<th><?php echo $invoiceAmtText; ?></th>
					<th><?php echo $feesPaidText; ?></th>
					<th><?php echo $totalPaidText; ?></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						// Get the Invoice Total
						$x = "SELECT
									itemAmount,
									itemqty
								FROM
									invitems
								WHERE invoiceId = ".$row['invoiceId'];
						$y = mysqli_query($mysqli, $x) or die('-3'.mysqli_error());
						
						$lineTotal = 0;
						while ($z = mysqli_fetch_assoc($y)) {
							$lineItem = $z['itemAmount'] * $z['itemqty'];
							$lineTotal += $lineItem;
						}
						$lineTotal = $curSym.format_amount($lineTotal, 2);

						// Get the Payment data
						$paid = "SELECT
									invoiceId,
									paymentAmount,
									additionalFee
								FROM
									projectpayments
								WHERE invoiceId = ".$row['invoiceId'];
						$results = mysqli_query($mysqli, $paid) or die('-2'.mysqli_error());
						$cols = mysqli_fetch_assoc($results);
						
						$invAmountPaid = $curSym.format_amount($cols['paymentAmount'] + $cols['additionalFee'], 2);
						if ($cols['additionalFee'] != '') {
							$additionalFee = $curSym.format_amount($cols['additionalFee'], 2);
						} else {
							$additionalFee = '';
						}
				?>
					<tr>
						<td data-th="<?php echo $invoiceTableHead; ?>">
							<span data-toggle="tooltip" data-placement="right" title="View Invoice">
								<a href="index.php?action=viewInvoice&invoiceId=<?php echo $row['invoiceId']; ?>"><?php echo clean($row['invoiceTitle']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $createdByTableHead; ?>"><?php echo clean($row['theAdmin']); ?></td>
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
						<td data-th="<?php echo $invoiceDateText; ?>"><?php echo $row['invoiceDate']; ?></td>
						<td data-th="<?php echo $lastUpdatedDateText; ?>"><?php echo $row['lastUpdated']; ?></td>
						<td data-th="<?php echo $invoiceAmtText; ?>"><?php echo $lineTotal; ?></td>
						<td data-th="<?php echo $feesPaidText; ?>"><?php echo $additionalFee; ?></td>
						<td data-th="<?php echo $totalPaidText; ?>"><?php echo $invAmountPaid; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=paidInvoicesExport" method="post" class="mt10" target="_blank">
			<input type="hidden" name="invFromDate" value="<?php echo $invFromDate; ?>" />
			<input type="hidden" name="invToDate" value="<?php echo $invToDate; ?>" />
			<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
		</form>
		<div class="clearfix"></div>
	<?php } ?>
</div>