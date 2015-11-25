<?php
	$pagPages = '10';

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$cols = $mysqli->query("SELECT * FROM invoices WHERE clientId = ".$clientId);
	$total = mysqli_num_rows($cols);

	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$query = "SELECT
				invoices.invoiceId,
				invoices.projectId,
				invoices.adminId,
				invoices.clientId,
				invoices.invoiceTitle,
				DATE_FORMAT(invoices.invoiceDate,'%M %d, %Y') AS invoiceDate,
				DATE_FORMAT(invoices.invoiceDue,'%M %d, %Y') AS invoiceDue,
				UNIX_TIMESTAMP(invoices.invoiceDue) AS orderDate,
				invoices.isPaid,
				clientprojects.projectName
			FROM
				invoices
				LEFT JOIN clientprojects ON invoices.projectId = clientprojects.projectId
			WHERE invoices.clientId = ".$clientId."
			ORDER BY invoices.isPaid, orderDate ".$pages->get_limit();
	$res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-info-circle"></i> <?php echo $noInvMsg; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table no-margin">
			<tbody>
				<tr class="primary">
					<th><?php echo $invoiceText; ?></th>
					<th><?php echo $projectText; ?></th>
					<th><?php echo $invDateText; ?></th>
					<th><?php echo $paymentDueText; ?></th>
					<th><?php echo $invAmountText; ?></th>
					<th><?php echo $statusText; ?></th>
					<th></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						// Get the Invoice Total
						$qry = "SELECT
									itemAmount,
									itemqty
								FROM
									invitems
								WHERE invoiceId = ".$row['invoiceId'];
						$result = mysqli_query($mysqli, $qry) or die('-2'.mysqli_error());

						$lineTotal = 0;
						while ($col = mysqli_fetch_assoc($result)) {
							$lineItem = $col['itemAmount'] * $col['itemqty'];
							$lineTotal += $lineItem;
						}
						$lineTotal = $curSym.format_amount($lineTotal, 2);

						if ($row['isPaid'] == '1') { $status = $paidText; $highlight = 'class="text-success"'; } else { $status = $unpaidText; $highlight = 'class="text-danger"'; }
				?>
						<tr>
							<td data-th="<?php echo $invoiceText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewInvoiceText; ?>">
									<a href="index.php?page=viewInvoice&invoiceId=<?php echo $row['invoiceId']; ?>"><?php echo clean($row['invoiceTitle']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $projectText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
									<a href="index.php?page=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $invDateText; ?>"><?php echo $row['invoiceDate']; ?></td>
							<td data-th="<?php echo $paymentDueText; ?>"><?php echo $row['invoiceDue']; ?></td>
							<td data-th="<?php echo $invAmountText; ?>"><?php echo $lineTotal; ?></td>
							<td data-th="<?php echo $statusText; ?>"><strong <?php echo $highlight; ?>><?php echo $status; ?></strong></td>
							<td data-th="<?php echo $actionsText; ?>">
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewInvoiceText; ?>">
									<a href="index.php?page=viewInvoice&invoiceId=<?php echo $row['invoiceId']; ?>"><i class="fa fa-print print"></i></a>
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