<?php
	// Report Options
	if (!empty($_POST['projTimeFromDate'])) {
		$projTimeFromDate = $mysqli->real_escape_string($_POST['projTimeFromDate']);
	}
	if (!empty($_POST['projTimeToDate'])) {
		$projTimeToDate = $mysqli->real_escape_string($_POST['projTimeToDate']);
	}
	$projId = $mysqli->real_escape_string($_POST['projId']);

	// Output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=exportProjectTime.csv');

	// Create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// Output the column headings
	fputcsv($output, array(
		$projectNameField,
		$managerText,
		$yearText,
		$weekNoText,
		$dateInText,
		$timeInText,
		$dateOutText,
		$timeOutText,
		$totalHoursText
	));

	// Get Data
	$sql = "SELECT
				timeclock.clockId,
				timeclock.projectId,
				timeclock.adminId,
				timeclock.weekNo,
				timeclock.clockYear,
				timeentry.entryId,
				timeentry.startTime,
				DATE_FORMAT(timeentry.startTime,'%M %d, %Y') AS dateStarted,
				DATE_FORMAT(timeentry.startTime,'%h:%i %p') AS hourStarted,
				timeentry.endTime,
				DATE_FORMAT(timeentry.endTime,'%M %d, %Y') AS dateEnded,
				DATE_FORMAT(timeentry.endTime,'%h:%i %p') AS hourEnded,
				UNIX_TIMESTAMP(timeentry.startTime) AS orderDate,
				clientprojects.projectName,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				timeclock
				LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
				LEFT JOIN clientprojects ON timeclock.projectId = clientprojects.projectId
				LEFT JOIN admins ON timeclock.adminId = admins.adminId
			WHERE
				timeclock.projectId = ".$projId." AND
				timeentry.endTime != '0000-00-00 00:00:00' AND
				timeentry.startTime >= '".$projTimeFromDate."' AND timeentry.startTime <= '".$projTimeToDate."'
			ORDER BY timeclock.adminId, orderDate";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());

	// Loop through the rows
	while ($row = mysqli_fetch_assoc($res)) {
		$items_array = array();
		
		// Get the Time Total for each Time Entry
		$tot = "SELECT timeentry.startTime, timeentry.endTime FROM timeentry WHERE entryId = ".$row['entryId'];
		$results = mysqli_query($mysqli, $tot) or die('-3'.mysqli_error());
		$rows = mysqli_fetch_assoc($results);
		
		// Convert it to HH:MM
		$from = new DateTime($rows['startTime']);
		$to = new DateTime($rows['endTime']);
		$lineTotal = $from->diff($to)->format('%h:%i');

		$items_array[] = clean($row['projectName']);
		$items_array[] = clean($row['theAdmin']);
		$items_array[] = $row['clockYear'];
		$items_array[] = $row['weekNo'];
		$items_array[] = $row['dateStarted'];
		$items_array[] = $row['hourStarted'];
		$items_array[] = $row['dateEnded'];
		$items_array[] = $row['hourEnded'];
		$items_array[] = $lineTotal;

		// Output the Data to the CSV
		fputcsv($output, $items_array);
	}
?>