<?php
	// Report Options
	$inactiveManagers = $_POST['inactiveManagers'];
	if (isset($inactiveManagers) && $inactiveManagers == '0') {		// All Active
		$isActive = "'1'";
		$included = 'All Active';
	} else {														// Show All
		$isActive = "'0','1'";
		$included = 'All';
	}

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportManagers.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$managerNameText,
		$managerEmailText,
		$primaryPhoneText,
		$altPhoneText,
		$activeAccountText,
		$accountTypeText,
		$accountRoleText,
		$dateAccCreatedText,
		$lastLoginText,
		$managerNotesText
	));

	// Get Data
	$sql = "SELECT
				adminId,
				adminEmail,
				CONCAT(adminFirstName,' ',adminLastName) AS theAdmin,
				adminPhone,
				adminCell,
				adminNotes,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
				DATE_FORMAT(lastVisited,'%M %e, %Y') AS lastVisited,
				isAdmin,
				adminRole,
				isActive
			FROM
				admins
			WHERE
				isActive IN (".$isActive.")";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		// Decrypt Data
		if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = ''; }
		if ($row['adminCell'] != '') { $adminCell = decryptIt($row['adminCell']); } else { $adminCell = ''; }
		if ($row['isAdmin'] == '1') { $isAdmin = $yesBtn; } else { $isAdmin = $noBtn; }
		if ($row['isActive'] == '1') { $isActive = $yesBtn; } else { $isActive = $noBtn; }

		$items_array[] = clean($row['theAdmin']);
		$items_array[] = clean($row['adminEmail']);
		$items_array[] = $adminPhone;
		$items_array[] = $adminCell;
		$items_array[] = $isActive;
		$items_array[] = $isAdmin;
		$items_array[] = clean($row['adminRole']);
		$items_array[] = $row['createDate'];
		$items_array[] = $row['lastVisited'];
		$items_array[] = clean($row['adminNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>