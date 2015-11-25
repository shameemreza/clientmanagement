<?php
	// Report Options
	if (!empty($_POST['payFromDate'])) {
		$payFromDate = $mysqli->real_escape_string($_POST['payFromDate']);
	}
	if (!empty($_POST['payToDate'])) {
		$payToDate = $mysqli->real_escape_string($_POST['payToDate']);
	}

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportDatedPayments.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$projectNameField,
		$clientNameText,
		$receivedByText,
		$paymentDateText,
		$capForText,
		$paidByText,
		$paidFromInvoiceText,
		$paymentAmtText,
		$feesPaidText,
		$totalPaidText,
		$paymentNotesField
	));

	// Get Data
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
				projectpayments.paymentNotes,
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
			FROM
				projectpayments
				LEFT JOIN clientprojects ON projectpayments.projectId = clientprojects.projectId
				LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
				LEFT JOIN clients ON projectpayments.clientId = clients.clientId
			WHERE
				projectpayments.paymentDate >= '".$payFromDate."' AND projectpayments.paymentDate <= '".$payToDate."'
			ORDER BY orderDate DESC";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		if ($row['invoiceId'] != '0') { $fromInvoice = $yesBtn; } else { $fromInvoice = $noBtn; }
		if ($row['paymentAmount'] != '') { $paymentAmount = $curSym.format_amount($row['paymentAmount'], 2); } else { $paymentAmount = ''; }
		if ($row['additionalFee'] != '') { $additionalFee = $curSym.format_amount($row['additionalFee'], 2); } else { $additionalFee = ''; }
		$lineTotal = $curSym.format_amount($row['paymentAmount'] + $row['additionalFee'], 2);

		$items_array[] = clean($row['projectName']);
		$items_array[] = clean($row['theClient']);
		$items_array[] = clean($row['theAdmin']);
		$items_array[] = $row['paymentDate'];
		$items_array[] = clean($row['paymentFor']);
		$items_array[] = clean($row['paidBy']);
		$items_array[] = $fromInvoice;
		$items_array[] = $paymentAmount;
		$items_array[] = $additionalFee;
		$items_array[] = $lineTotal;
		$items_array[] = clean($row['paymentNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>