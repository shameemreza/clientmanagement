<?php
	// Report Options
	$archivedProjects = $_POST['archivedProjects'];
	if (isset($archivedProjects) && $archivedProjects == '0') {
		$isArchived = "'0'";
	} else {
		$isArchived = "'0','1'";
	}

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportProjects.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	if ($set['enablePayments'] == '1') {
		fputcsv($output, array(
			$projectNameField,
			$clientNameText,
			$createdByTableHead,
			$projectFeeText,
			$percentCompleteText,
			$assignedToText,
			$dateCreatedTableHead,
			$dateDueText,
			$statusField,
			$dateArchivedText,
			$projectNotesField
		));
	} else {
		fputcsv($output, array(
			$projectNameField,
			$clientNameText,
			$createdByTableHead,
			$percentCompleteText,
			$assignedToText,
			$dateCreatedTableHead,
			$dateDueText,
			$statusField,
			$dateArchivedText,
			$projectNotesField
		));
	}

	// Get Data
	$sql = "SELECT
				clientprojects.projectId,
				clientprojects.createdBy,
				clientprojects.clientId,
				clientprojects.projectName,
				clientprojects.percentComplete,
				clientprojects.projectFee,
				DATE_FORMAT(clientprojects.startDate,'%M %d, %Y') AS startDate,
				DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
				UNIX_TIMESTAMP(clientprojects.dueDate) AS orderDate,
				clientprojects.projectNotes,
				clientprojects.archiveProj,
				DATE_FORMAT(clientprojects.archiveDate,'%M %d, %Y') AS archiveDate,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
			FROM
				clientprojects
				LEFT JOIN admins ON clientprojects.createdBy = admins.adminId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE
				archiveProj IN (".$isArchived.")
			ORDER BY archiveProj, orderDate";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		if ($row['archiveProj'] == '0') { $archiveProj = $activeText; } else { $archiveProj = $closedArchivedText; }
		
		// Get the Manager Assigned
		$a = "SELECT
				assignedprojects.assignedTo,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS adminAssigned
			FROM
				assignedprojects
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
			WHERE assignedprojects.projectId = ".$row['projectId'];
		$b = mysqli_query($mysqli, $a) or die('-2'.mysqli_error());
		$rows = mysqli_fetch_assoc($b);

		$items_array[] = clean($row['projectName']);
		$items_array[] = clean($row['theClient']);
		$items_array[] = clean($row['theAdmin']);
		if ($set['enablePayments'] == '1') {
			if ($row['projectFee'] != '') { $projectFee = $curSym.format_amount($row['projectFee'], 2); } else { $projectFee = ''; }
			$items_array[] = $projectFee;
		}
		$items_array[] = clean($row['percentComplete']);
		$items_array[] = clean($rows['adminAssigned']);
		$items_array[] = $row['startDate'];
		$items_array[] = $row['dueDate'];
		$items_array[] = $archiveProj;
		$items_array[] = $row['archiveDate'];
		$items_array[] = clean($row['projectNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>