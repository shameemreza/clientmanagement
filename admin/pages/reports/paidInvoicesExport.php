<?php
	// Report Options
	if (!empty($_POST['invFromDate'])) {
		$invFromDate = $mysqli->real_escape_string($_POST['invFromDate']);
	}
	if (!empty($_POST['invToDate'])) {
		$invToDate = $mysqli->real_escape_string($_POST['invToDate']);
	}

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportDatedInvoices.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$invoiceTableHead,
		$createdByTableHead,
		$projectNameField,
		$clientNameText,
		$invoiceDateText,
		$lastUpdatedDateText,
		$invoiceAmtText,
		$feesPaidText,
		$totalPaidText,
		$invoiceNotesField
	));

	// Get Data
	$sql = "SELECT
				invoices.invoiceId,
				invoices.projectId,
				invoices.adminId,
				invoices.clientId,
				invoices.invoiceTitle,
				invoices.invoiceNotes,
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

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
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

		$items_array[] = clean($row['invoiceTitle']);
		$items_array[] = clean($row['theAdmin']);
		$items_array[] = clean($row['projectName']);
		$items_array[] = clean($row['theClient']);
		$items_array[] = $row['invoiceDate'];
		$items_array[] = $row['lastUpdated'];
		$items_array[] = $lineTotal;
		$items_array[] = $additionalFee;
		$items_array[] = $invAmountPaid;
		$items_array[] = clean($row['invoiceNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>