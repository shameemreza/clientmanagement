<?php
	// Report Options
	$theManager = $_POST['theManager'];

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportAssignedProjects.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	if ($set['enablePayments'] == '1') {
		fputcsv($output, array(
			$projectNameField,
			$clientNameText,
			$fromRequestText,
			$projectFeeText,
			$percentCompleteText,
			$dateCreatedTableHead,
			$dateDueText,
			$projectNotesField
		));
	} else {
		fputcsv($output, array(
			$projectNameField,
			$clientNameText,
			$fromRequestText,
			$percentCompleteText,
			$dateCreatedTableHead,
			$dateDueText,
			$projectNotesField
		));
	}

	// Get Data
	$sql = "SELECT
				clientprojects.projectId,
				clientprojects.clientId,
				clientprojects.projectName,
				clientprojects.percentComplete,
				clientprojects.projectFee,
				DATE_FORMAT(clientprojects.startDate,'%M %d, %Y') AS startDate,
				DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
				clientprojects.projectNotes,
				clientprojects.fromRequest,
				assignedprojects.assignedTo,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
			FROM
				clientprojects
				LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE
				assignedprojects.assignedTo = ".$theManager." AND
				clientprojects.archiveProj = 0
			ORDER BY clientprojects.projectId";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		if ($row['fromRequest'] == '0') { $fromRequest = $noBtn; } else { $fromRequest = $yesBtn; }

		$items_array[] = clean($row['projectName']);
		$items_array[] = clean($row['theClient']);
		$items_array[] = $fromRequest;
		if ($set['enablePayments'] == '1') {
			if ($row['projectFee'] != '') { $projectFee = $curSym.format_amount($row['projectFee'], 2); } else { $projectFee = ''; }
			$items_array[] = $projectFee;
		}
		$items_array[] = clean($row['percentComplete']).'%';
		$items_array[] = $row['startDate'];
		$items_array[] = $row['dueDate'];
		$items_array[] = clean($row['projectNotes']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>