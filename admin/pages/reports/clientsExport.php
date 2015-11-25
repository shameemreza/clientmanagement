<?php
	// Report Options
	$showClients = $_POST['showClients'];
	if ($showClients == '0') {			// All Active
		$isActive = "'1'";
		$isArchived = "'0'";
		$included = 'All Active';
	} else if ($showClients == '1') {	// All Archived
		$isActive = "'0'";
		$isArchived = "'1'";
		$included = 'All Archived';
	} else if ($showClients == '2') {	// All Inactive
		$isActive = "'0'";
		$isArchived = "'0'";
		$included = 'All Inactive';
	} else {							// Show All
		$isActive = "'0','1'";
		$isArchived = "'0','1'";
		$included = 'All';
	}

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=clientsExport.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$clientNameText,
		$clientEmailText,
		$clientCompanyText,
		$primaryPhoneText,
		$altPhoneText,
		$activeAccountText,
		$archivedAccountText,
		$dateAccCreatedText,
		$clientLastLoginText
	));

	// Get Data
	$sql = "SELECT
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clientEmail,
				clientCompany,
				clientPhone,
				clientCell,
				isActive,
				isArchived,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
				DATE_FORMAT(lastVisited,'%M %e, %Y at %l:%i %p') AS lastVisited
			FROM
				clients
			WHERE
				isActive IN (".$isActive.") AND
				isArchived IN (".$isArchived.")";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		// Decrypt Data
		if ($row['clientPhone'] != '') { $clientPhone = decryptIt($row['clientPhone']); } else { $clientPhone = ''; }
		if ($row['clientCell'] != '') { $clientCell = decryptIt($row['clientCell']); } else { $clientCell = ''; }
		if ($row['isActive'] == '0') { $isActive = $noBtn; } else { $isActive = $yesBtn; }
		if ($row['isArchived'] == '0') { $isArchived = $noBtn; } else { $isArchived = $yesBtn; }

		$items_array[] = clean($row['theClient']);
		$items_array[] = clean($row['clientEmail']);
		$items_array[] = clean($row['clientCompany']);
		$items_array[] = $clientPhone;
		$items_array[] = $clientCell;
		$items_array[] = $isActive;
		$items_array[] = $isArchived;
		$items_array[] = $row['createDate'];
		$items_array[] = $row['lastVisited'];

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>