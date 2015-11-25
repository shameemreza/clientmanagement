<?php
	// Report Options
	$projectId = $_POST['projectId'];

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportProjectPayments.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$clientNameText,
		$paymentDateText,
		$enteredByText,
		$paymentForField,
		$paidByText,
		$paidFromInvoiceText,
		$feesPaidText,
		$amountPaidText,
		$totalPaidText,
		$paymentNotesField
	));

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
				projectpayments.projectId = ".$projectId."
			ORDER BY
				projectpayments.projectId,
				projectpayments.paymentId";
	$res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();

		if ($row['invoiceId'] == '0') { $fromInvoice = $noBtn; } else { $fromInvoice = $yesBtn; }
		if ($row['paymentAmount'] != '') { $paymentAmount = $curSym.format_amount($row['paymentAmount'], 2); } else { $paymentAmount = ''; }
		if ($row['additionalFee'] != '') { $additionalFee = $curSym.format_amount($row['additionalFee'], 2); } else { $additionalFee = ''; }
		$lineTotal = $curSym.format_amount($row['paymentAmount'] + $row['additionalFee'], 2);

		$items_array[] = clean($row['theClient']);
		$items_array[] = $row['datePaid'];
		$items_array[] = clean($row['theAdmin']);
		$items_array[] = clean($row['paymentFor']);
		$items_array[] = clean($row['paidBy']);
		$items_array[] = $fromInvoice;
		$items_array[] = $additionalFee;
		$items_array[] = $paymentAmount;
		$items_array[] = $lineTotal;
		$items_array[] = clean($row['paymentNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>