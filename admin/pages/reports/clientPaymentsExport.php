<?php
	// Report Options
	$clientId = $_POST['clientId'];
	$fromDate = $_POST['fromDate'];
	$toDate = $_POST['toDate'];


	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportClientPayments.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$projectNameField,
		$paymentDateText,
		$clientNameText,
		$enteredByText,
		$paymentForField,
		$paidByText,
		$amountPaidText,
		$feesPaidText,
		$totalPaidText,
		$paymentNotesField
	));

	// Get Data
	$query  = "SELECT
				projectpayments.paymentId,
				projectpayments.clientId,
				projectpayments.projectId,
				projectpayments.enteredBy,
				projectpayments.paymentDate,
				projectpayments.paymentFor,
				DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS datePaid,
				UNIX_TIMESTAMP(projectpayments.paymentDate) AS orderDate,
				projectpayments.paidBy,
				projectpayments.paymentAmount,
				projectpayments.additionalFee,
				projectpayments.paymentNotes,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectpayments
				LEFT JOIN clients ON projectpayments.clientId = clients.clientId
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

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();

		if ($row['paymentAmount'] != '') { $paymentAmount = $curSym.format_amount($row['paymentAmount'], 2); } else { $paymentAmount = ''; }
		if ($row['additionalFee'] != '') { $additionalFee = $curSym.format_amount($row['additionalFee'], 2); } else { $additionalFee = ''; }
		$lineTotal = $curSym.format_amount($row['paymentAmount'] + $row['additionalFee'], 2);

		$items_array[] = clean($row['projectName']);
		$items_array[] = $row['datePaid'];
		$items_array[] = clean($row['theClient']);
		$items_array[] = clean($row['theAdmin']);
		$items_array[] = clean($row['paymentFor']);
		$items_array[] = clean($row['paidBy']);
		$items_array[] = $paymentAmount;
		$items_array[] = $additionalFee;
		$items_array[] = $lineTotal;
		$items_array[] = clean($row['paymentNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>