<?php
	$pagPages = '10';

	// Include Pagination Class
	include('includes/pagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM projectpayments WHERE clientId = ".$clientId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Payment Data
	$query = "SELECT
				projectpayments.paymentId,
				projectpayments.projectId,
				projectpayments.paymentFor,
				DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
				UNIX_TIMESTAMP(projectpayments.paymentDate) AS orderDate,
				projectpayments.paidBy,
				projectpayments.paymentAmount,
				projectpayments.additionalFee,
				projectpayments.paymentNotes,
				clientprojects.projectName
			FROM
				projectpayments
				LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
			WHERE
				projectpayments.clientId = ".$clientId."
			ORDER BY orderDate DESC ".$pages->get_limit();
	$res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php
		if(mysqli_num_rows($res) < 1) {
	?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-info-circle"></i> <?php echo $noPaymentsMsg; ?>
			</div>
	<?php
		} else {
	?>
			<table class="rwd-table">
				<tbody>
					<tr>
						<th class="text-left"><?php echo $projectTableHead; ?></th>
						<th><?php echo $paymentDateTableHead; ?></th>
						<th><?php echo $forText; ?></th>
						<th><?php echo $paymentAmountTableHead; ?></th>
						<th><?php echo $feeAmountTableHead; ?></th>
						<th><?php echo $totalPaidTableHead; ?></th>
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
										<a href="index.php?page=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
									</span>
								</td>
								<td data-th="<?php echo $paymentDateTableHead; ?>"><?php echo $row['paymentDate']; ?></td>
								<td data-th="<?php echo $forText; ?>"><?php echo $row['paymentFor']; ?></td>
								<td data-th="<?php echo $paymentAmountTableHead; ?>">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo clean($row['paymentNotes']); ?>">
										<?php echo $paymentAmount; ?>
									</span>
								</td>
								<td <?php echo $highlight; ?> data-th="<?php echo $feeAmountTableHead; ?>"><?php echo $additionalFee; ?></td>
								<td data-th="<?php echo $totalPaidTableHead; ?>"><?php echo $totalPaid; ?></td>
								<td data-th="<?php echo $paidByText; ?>"><?php echo clean($row['paidBy']); ?></td>
								<td class="text-right" data-th="Invoice">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $printReceiptTooltip; ?>">
										<a href="index.php?page=receipt&paymentId=<?php echo $row['paymentId']; ?>"><i class="fa fa-print print"></i></a>
									</span>
								</td>
							</tr>
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