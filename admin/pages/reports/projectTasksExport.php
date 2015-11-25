<?php
	// Report Options
	$projectId = $_POST['projectId'];

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportProjectTasks.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$taskTitleField,
		$projectNameField,
		$managerText,
		$priorityText,
		$statusField,
		$dateCreatedTableHead,
		$dateDueText,
		$openslashClosedText,
		$dateClosedText,
		$taskDescField
	));

	// Get Data
	$sql = "SELECT
				tasks.taskId,
				tasks.projectId,
				tasks.adminId,
				tasks.taskTitle,
				tasks.taskDesc,
				tasks.taskPriority,
				tasks.taskStatus,
				DATE_FORMAT(tasks.taskStart,'%M %d, %Y') AS taskStart,
				DATE_FORMAT(tasks.taskDue,'%M %d, %Y') AS taskDue,
				UNIX_TIMESTAMP(tasks.taskDue) AS orderDate,
				tasks.isClosed,
				DATE_FORMAT(tasks.dateClosed,'%M %d, %Y') AS dateClosed,
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				tasks
				LEFT JOIN clientprojects ON tasks.projectId = clientprojects.projectId
				LEFT JOIN admins ON tasks.adminId = admins.adminId
			WHERE
				tasks.projectId = ".$projectId."
			ORDER BY tasks.isClosed, orderDate";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		if ($row['isClosed'] == '1') { $closed = 'Closed'; } else { $closed = 'Open'; }

		$items_array[] = clean($row['taskTitle']);
		$items_array[] = clean($row['projectName']);
		$items_array[] = clean($row['theAdmin']);
		$items_array[] = clean($row['taskPriority']);
		$items_array[] = clean($row['taskStatus']);
		$items_array[] = $row['taskStart'];
		$items_array[] = $row['taskDue'];
		$items_array[] = $closed;
		$items_array[] = $row['dateClosed'];
		$items_array[] = clean($row['taskDesc']);

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>