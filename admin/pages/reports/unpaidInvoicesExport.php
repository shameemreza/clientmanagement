<?php
	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportUnpaidInvoices.csv');

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
		$dueByDateText,
		$amountDueText,
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
				DATE_FORMAT(invoices.invoiceDue,'%M %d, %Y') AS invoiceDue,
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
			WHERE invoices.isPaid = 0
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

		$items_array[] = clean($row['invoiceTitle']);
		$items_array[] = clean($row['theAdmin']);
		$items_array[] = clean($row['projectName']);
		$items_array[] = clean($row['theClient']);
		$items_array[] = $row['invoiceDate'];
		$items_array[] = $row['lastUpdated'];
		$items_array[] = $row['invoiceDue'];
		$items_array[] = $lineTotal;
		$items_array[] = clean($row['invoiceNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>