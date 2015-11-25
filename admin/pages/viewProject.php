<?php
	$projectId = $_GET['projectId'];
	$datePicker = 'true';
	$jsFile = 'viewProject';
	$getId = 'projectId='.$projectId;
	$pagPages = '10';

	// Start/Stop the Time Clock
	if (isset($_POST['submit']) && $_POST['submit'] == 'toggleTime') {
		$isRecord = $mysqli->real_escape_string($_POST['isRecord']);

		if ($isRecord != '0') {
			// Record All Ready Exists
			$clockId = $mysqli->real_escape_string($_POST['clockId']);
			$entryId = $mysqli->real_escape_string($_POST['entryId']);
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$clockYear = $mysqli->real_escape_string($_POST['clockYear']);
			$running = $mysqli->real_escape_string($_POST['running']);
			$entryDate = $endTime = date("Y-m-d");
			$startTime = $endTime = date("Y-m-d H:i:s");

			if ($running == '0') {
				// Start Clock - Update the timeclock Record
				$sqlstmt = $mysqli->prepare("
									UPDATE
										timeclock
									SET
										running = 1
									WHERE
										clockId = ?
				");
				$sqlstmt->bind_param('s',$clockId);
				$sqlstmt->execute();
				$sqlstmt->close();

				// Start Clock - Add a new time entry
				$stmt = $mysqli->prepare("
									INSERT INTO
										timeentry(
											clockId,
											projectId,
											adminId,
											entryDate,
											startTime
										) VALUES (
											?,
											?,
											?,
											?,
											?
										)
				");
				$stmt->bind_param('sssss',
									$clockId,
									$projectId,
									$adminId,
									$entryDate,
									$startTime
				);
				$stmt->execute();
				$stmt->close();
			} else {
				// Stop Clock - Update the timeclock Record
				$sqlstmt = $mysqli->prepare("
									UPDATE
										timeclock
									SET
										running = 0
									WHERE
										clockId = ?
				");
				$sqlstmt->bind_param('s',$clockId);
				$sqlstmt->execute();
				$sqlstmt->close();

				// Stop Clock - Update the time entry
				$stmt = $mysqli->prepare("
									UPDATE
										timeentry
									SET
										endTime = ?
									WHERE
										entryId = ?
				");
				$stmt->bind_param('ss',
									$endTime,
									$entryId
				);
				$stmt->execute();
				$stmt->close();
			}
		} else {
			// Record Does Not Exist
			// Start Clock - Create a timeclock Record
			$weekNo = $mysqli->real_escape_string($_POST['weekNo']);
			$clockYear = $mysqli->real_escape_string($_POST['clockYear']);
			$running = '1';
			$startTime = date("Y-m-d H:i:s");

			$sqlstmt = $mysqli->prepare("
								INSERT INTO
									timeclock(
										projectId,
										adminId,
										weekNo,
										clockYear,
										running
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)
			");
			$sqlstmt->bind_param('sssss',
									$projectId,
									$adminId,
									$weekNo,
									$clockYear,
									$running
			);
			$sqlstmt->execute();
			$sqlstmt->close();

			// Get the new Tracking ID
			$track_id = $mysqli->query("SELECT clockId FROM timeclock WHERE projectId = ".$projectId." AND adminId = ".$adminId." AND weekNo = '".$weekNo."' AND clockYear = ".$currentYear);
			$id = mysqli_fetch_assoc($track_id);
			$clockId = $id['clockId'];
			$entryDate = $endTime = date("Y-m-d");

			// Start Clock - Add a new time entry
			$stmt = $mysqli->prepare("
								INSERT INTO
									timeentry(
										clockId,
										projectId,
										adminId,
										entryDate,
										startTime
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssss',
								$clockId,
								$projectId,
								$adminId,
								$entryDate,
								$startTime
			);
			$stmt->execute();
			$stmt->close();
		}
	}

	// Update Project
    if (isset($_POST['submit']) && $_POST['submit'] == 'updateProject') {
        // Validation
		if($_POST['projectName'] == "") {
            $msgBox = alertBox($projTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['projectDeatils'] == "") {
            $msgBox = alertBox($projDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['startDate'] == "") {
            $msgBox = alertBox($projStartDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dueDate'] == "") {
            $msgBox = alertBox($projDueByDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['percentComplete'] == "") {
            $msgBox = alertBox($projPercentCompReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$projectName = $mysqli->real_escape_string($_POST['projectName']);
			$startDate = $mysqli->real_escape_string($_POST['startDate']);
			$dueDate = $mysqli->real_escape_string($_POST['dueDate']);
			$percentComplete = $mysqli->real_escape_string($_POST['percentComplete']);
			$projectDeatils = $_POST['projectDeatils'];
			$projectNotes = $_POST['projectNotes'];

			if (isset($_POST['projectFee'])) {
                $projectFee = $mysqli->real_escape_string($_POST['projectFee']);
            } else {
                $projectFee = '0';
            }

            $stmt = $mysqli->prepare("UPDATE
										clientprojects
									SET
										projectName = ?,
										percentComplete = ?,
										projectFee = ?,
										projectDeatils = ?,
										startDate = ?,
										dueDate = ?,
										projectNotes = ?
									WHERE
										projectId = ?"
			);
			$stmt->bind_param('ssssssss',
									$projectName,
									$percentComplete,
									$projectFee,
									$projectDeatils,
									$startDate,
									$dueDate,
									$projectNotes,
									$projectId
			);
			$stmt->execute();
			$msgBox = alertBox($projUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Assign Project
	if (isset($_POST['submit']) && $_POST['submit'] == 'assignProject') {
		$admin = $mysqli->real_escape_string($_POST['adminId']);

		if($_POST['isManager'] != '0') {
			// There is all ready a Project Manager for this Project, so Update it
			$stmt = $mysqli->prepare("
								UPDATE
									assignedprojects
								SET
									assignedTo = ?
								WHERE
									projectId = ?");
			$stmt->bind_param('ss',
							   $admin,
							   $projectId
			);
			$stmt->execute();
			$msgBox = alertBox($projManagerUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			// There is NOT a Project Manager record for this Project, so Create it
            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    assignedprojects(
                                        projectId,
                                        assignedTo
                                    ) VALUES (
										?,
										?
                                    )");
            $stmt->bind_param('ss',
								$projectId,
								$admin
            );
            $stmt->execute();
			$msgBox = alertBox($projManagerAssignedMsg, "<i class='fa fa-check-square'></i>", "success");
            $stmt->close();
		}
	}

	// Archive Project
	if (isset($_POST['submit']) && $_POST['submit'] == 'archiveProject') {
		$archiveProj = '1';
		$archiveDate = date("Y-m-d H:i:s");

		$stmt = $mysqli->prepare("
							UPDATE
								clientprojects
							SET
								archiveProj = ?,
								archiveDate = ?
							WHERE
								projectId = ?");
		$stmt->bind_param('sss',
						   $archiveProj,
						   $archiveDate,
						   $projectId
		);
		$stmt->execute();
		$msgBox = alertBox($projArchivedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	// Reopen Project
	if (isset($_POST['submit']) && $_POST['submit'] == 'reopenProject') {
		$archiveProj = '0';
		$archiveDate = '0000-00-00 00:00:00';

		$stmt = $mysqli->prepare("
							UPDATE
								clientprojects
							SET
								archiveProj = ?,
								archiveDate = ?
							WHERE
								projectId = ?");
		$stmt->bind_param('sss',
						   $archiveProj,
						   $archiveDate,
						   $projectId
		);
		$stmt->execute();
		$msgBox = alertBox($projReopenedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	// Add New Account Entry
    if (isset($_POST['submit']) && $_POST['submit'] == 'newentry') {
        // Validation
		if($_POST['entryTitle'] == "") {
            $msgBox = alertBox($entryNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['entryUsername'] == "") {
            $msgBox = alertBox($entryUsernameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['entryPass'] == "") {
            $msgBox = alertBox($entryPasswordReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$entryTitle = encryptIt($_POST['entryTitle']);
			$entryDesc = encryptIt($_POST['entryDesc']);
			$entryUsername = encryptIt($_POST['entryUsername']);
			$entryPass = encryptIt($_POST['entryPass']);
			$entryUrl = encryptIt($_POST['entryUrl']);
			$entryNotes = encryptIt($_POST['entryNotes']);
			$entryDate = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									pwentries(
										projectId,
										adminId,
										entryTitle,
										entryDesc,
										entryUsername,
										entryPass,
										entryUrl,
										entryNotes,
										entryDate
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssssssss',
								$projectId,
								$adminId,
								$entryTitle,
								$entryDesc,
								$entryUsername,
								$entryPass,
								$entryUrl,
								$entryNotes,
								$entryDate
			);
			$stmt->execute();
			$msgBox = alertBox($newEntrySavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['entryTitle'] = $_POST['entryDesc'] = $_POST['entryUsername'] = $_POST['entryPass'] = $_POST['entryUrl'] = $_POST['entryNotes'] = '';
			$stmt->close();
		}
	}

	// Delete Account Entry
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteentry') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM pwentries WHERE entryId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$msgBox = alertBox($entryDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Add New Project Payment
    if (isset($_POST['submit']) && $_POST['submit'] == 'newPayment') {
        // Validation
		if($_POST['paymentFor'] == "") {
            $msgBox = alertBox($paymentForReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['paymentDate'] == "") {
            $msgBox = alertBox($paymentDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['paidBy'] == "") {
            $msgBox = alertBox($paidByReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['paymentAmount'] == "") {
            $msgBox = alertBox($paymentAmountReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$cId = $mysqli->real_escape_string($_POST['cId']);
			$paymentFor = $mysqli->real_escape_string($_POST['paymentFor']);
			$paymentDate = $mysqli->real_escape_string($_POST['paymentDate']);
			$paidBy = $mysqli->real_escape_string($_POST['paidBy']);
			$paymentAmount = $mysqli->real_escape_string($_POST['paymentAmount']);
			$additionalFee = $mysqli->real_escape_string($_POST['additionalFee']);
			$projectPayments = $mysqli->real_escape_string($_POST['projectPayments']);
			$paymentNotes = $_POST['paymentNotes'];

			// Update the Project Record
			$paymentNumber = $projectPayments + 1;
			$stmt = $mysqli->prepare("UPDATE clientprojects SET projectPayments = ? WHERE projectId = ?");
			$stmt->bind_param('ss', $paymentNumber, $projectId);
			$stmt->execute();
			$stmt->close();

			// Save the Payment Data
			$stmt = $mysqli->prepare("
								INSERT INTO
									projectpayments(
										clientId,
										projectId,
										enteredBy,
										paymentFor,
										paymentDate,
										paidBy,
										paymentAmount,
										additionalFee,
										paymentNotes
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssssssss',
								$cId,
								$projectId,
								$adminId,
								$paymentFor,
								$paymentDate,
								$paidBy,
								$paymentAmount,
								$additionalFee,
								$paymentNotes
			);
			$stmt->execute();
			$msgBox = alertBox($paymentSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['paymentFor'] = $_POST['paymentDate'] = $_POST['paidBy'] = $_POST['paymentAmount'] = $_POST['additionalFee'] = $_POST['paymentNotes'] = '';
			$stmt->close();
		}
	}

	// Delete Payment
	if (isset($_POST['submit']) && $_POST['submit'] == 'deletePayment') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$projectPayments = $mysqli->real_escape_string($_POST['projectPayments']);

		// Check if this is an Invoice Payment
		$isInv = $mysqli->query("SELECT invoiceId FROM projectpayments WHERE paymentId = ".$deleteId);
		$inv = mysqli_fetch_assoc($isInv);
		$invoiceId = $inv['invoiceId'];

		if ($invoiceId != '0') {
			// Update the Invoice Record
			$isPaid = '0';
			$stmt = $mysqli->prepare("UPDATE invoices SET isPaid = ? WHERE invoiceId = ?");
			$stmt->bind_param('ss', $isPaid, $invoiceId);
			$stmt->execute();
			$stmt->close();
		}

		// Update the Project Record
		$paymentNumber = $projectPayments - 1;
		$stmt = $mysqli->prepare("UPDATE clientprojects SET projectPayments = ? WHERE projectId = ?");
		$stmt->bind_param('ss', $paymentNumber, $projectId);
		$stmt->execute();
		$stmt->close();

		// Delete the Payment
		$stmt = $mysqli->prepare("DELETE FROM projectpayments WHERE paymentId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$msgBox = alertBox($paymentDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Add New Invoice
    if (isset($_POST['submit']) && $_POST['submit'] == 'newInvoice') {
        // Validation
		if($_POST['invoiceDue'] == "") {
            $msgBox = alertBox($invoiceDueDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['invoiceTitle'] == "") {
            $msgBox = alertBox($invoiceTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$projName = $mysqli->real_escape_string($_POST['projName']);
			$invoiceDue = $mysqli->real_escape_string($_POST['invoiceDue']);
			$invoiceTitle = $mysqli->real_escape_string($_POST['invoiceTitle']);
			$invNotes = $mysqli->real_escape_string($_POST['invoiceNotes']);
			$invoiceNotes = $_POST['invoiceNotes'];
			$invoiceDate = date("Y-m-d H:i:s");

			// Get some Client data
			$cd = 	"SELECT
						clients.clientId,
						clients.clientEmail,
						clientprojects.projectId
					FROM
						clients
						LEFT JOIN clientprojects ON clients.clientId = clientprojects.clientId
					WHERE
						clientprojects.projectId = ".$projectId;
			$cdres = mysqli_query($mysqli, $cd) or die('-1' . mysqli_error());
			$col = mysqli_fetch_assoc($cdres);
			$clientId = $col['clientId'];
			$clientEmail = $col['clientEmail'];

			$stmt = $mysqli->prepare("
								INSERT INTO
									invoices(
										projectId,
										adminId,
										clientId,
										invoiceTitle,
										invoiceNotes,
										invoiceDate,
										invoiceDue
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssssss',
								$projectId,
								$adminId,
								$clientId,
								$invoiceTitle,
								$invoiceNotes,
								$invoiceDate,
								$invoiceDue
			);
			$stmt->execute();
			$stmt->close();

			if (isset($_POST['notifyClient']) && $_POST['notifyClient'] == '1') {
				// Send out the email in HTML
				$installUrl = $set['installUrl'];
				$siteName = $set['siteName'];
				$businessEmail = $set['businessEmail'];

				$subject = $newInvEmailSubject;

				$message = '<html><body>';
				$message .= '<h3>'.$subject.'</h3>';
				$message .= '<hr>';
				$message .= '<p>'.$projectText.': '.$projName.'</p>';
				$message .= '<p>'.$invoiceTitleField.': '.$invoiceTitle.'</p>';
				$message .= '<p>'.$invNotes.'</p>';
				$message .= '<hr>';
				$message .= '<p>'.$emailLink.'</p>';
				$message .= '<p>'.$emailThankYou.'</p>';
				$message .= '</body></html>';

				$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
				$headers .= "Reply-To: ".$businessEmail."\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				if (mail($clientEmail, $subject, $message, $headers)) {
					$msgBox = alertBox($invoiceCreatedEmailSent, "<i class='fa fa-check-square'></i>", "success");
				} else {
					$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-warning'></i>", "warning");
				}
			} else {
				$msgBox = alertBox($invoiceCreatedMsg, "<i class='fa fa-check-square'></i>", "success");
			}
			// Clear the Form of values
			$_POST['projName'] = $_POST['invoiceDue'] = $_POST['invoiceTitle'] = $_POST['invoiceNotes'] = '';
		}
	}

	if ($isAdmin == '1') {
		// Get all Time Worked for the Project
		$checktime = $mysqli->query("SELECT 'X' FROM timeclock WHERE projectId = ".$projectId);
		if ($checktime->num_rows) {
			$sqlstmt1 = "SELECT
							TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
						FROM
							timeclock
							LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
						WHERE
							timeclock.projectId = ".$projectId." AND
							timeentry.endTime != '0000-00-00 00:00:00'";
			$sqlres1 = mysqli_query($mysqli, $sqlstmt1) or die('-2'.mysqli_error());
			$times = array();
			while ($u = mysqli_fetch_assoc($sqlres1)) {
				$times[] = $u['diff'];
			}
			$totalTime = sumHours($times);
		} else {
			$totalTime = '00:00:00';
		}
		$totalTimeQuip = $projHoursWorked1;
	} else {
		// Get Total Time for the logged in Manager
		$checktime = $mysqli->query("SELECT 'X' FROM timeclock WHERE adminId = ".$adminId." AND projectId = ".$projectId);
		if ($checktime->num_rows) {
			$sqlstmt1 = "SELECT
							TIMEDIFF(timeentry.endTime,timeentry.startTime) AS diff
						FROM
							timeclock
							LEFT JOIN timeentry ON timeclock.clockId = timeentry.clockId
						WHERE
							timeclock.adminId = ".$adminId." AND
							timeclock.projectId = ".$projectId." AND
							timeentry.endTime != '0000-00-00 00:00:00'";
			$sqlres1 = mysqli_query($mysqli, $sqlstmt1) or die('-3'.mysqli_error());
			$times = array();
			while ($u = mysqli_fetch_assoc($sqlres1)) {
				$times[] = $u['diff'];
			}
			$totalTime = sumHours($times);
		} else {
			$totalTime = '00:00:00';
		}
		$totalTimeQuip = $projHoursWorked2;
	}

	// Check for an Time Clock Existing Record
	$check = $mysqli->query("SELECT 'X' FROM timeclock WHERE adminId = ".$adminId." AND projectId = ".$projectId." AND weekNo = '".$weekNum."' AND clockYear = '".$currentYear."'");
	if ($check->num_rows) {
		$checked = "SELECT
						clockId,
						projectId,
						adminId,
						weekNo,
						clockYear,
						running
					FROM
						timeclock
					WHERE
						adminId = ".$adminId." AND
						projectId = ".$projectId." AND
						weekNo = '".$weekNum."' AND
						clockYear = '".$currentYear."'";
		$checkres = mysqli_query($mysqli, $checked) or die('-4'.mysqli_error());
		$col = mysqli_fetch_assoc($checkres);
		$clockId = $col['clockId'];
		$running = $col['running'];

		$sel = "SELECT
					entryId,
					clockId
				FROM
					timeentry
				WHERE
					clockId = ".$clockId." AND
					adminId = ".$adminId." AND
					projectId = ".$projectId." AND
					endTime = '0000-00-00'";
		$selresult = mysqli_query($mysqli, $sel) or die('-5'.mysqli_error());
		$rows = mysqli_fetch_assoc($selresult);
		$entryId = (is_null($rows['entryId'])) ? '' : $rows['entryId'];
		$isRecord = '1';
	} else {
		$clockId = $entryId = '';
		$running = $isRecord = '0';
	}

	// Get Project Data
    $query = "SELECT
                clientprojects.projectId,
                clientprojects.clientId,
                clientprojects.projectName,
                clientprojects.percentComplete,
                clientprojects.projectFee,
				clientprojects.projectPayments,
				clientprojects.projectDeatils,
				DATE_FORMAT(clientprojects.startDate,'%Y-%m-%d') AS dateStart,
				DATE_FORMAT(clientprojects.startDate,'%M %d, %Y') AS startDate,
				DATE_FORMAT(clientprojects.dueDate,'%Y-%m-%d') AS dateDue,
                DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
                clientprojects.projectNotes,
                clientprojects.archiveProj,
				DATE_FORMAT(clientprojects.archiveDate,'%M %d, %Y') AS archiveDate,
                CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
                SUM(projectpayments.paymentAmount) AS totalAmount,
				SUM(projectpayments.additionalFee) AS totalFee,
				assignedprojects.assignedTo,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
            FROM
                clientprojects
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
                LEFT JOIN projectpayments ON clientprojects.projectId = projectpayments.projectId
				LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
            WHERE
                clientprojects.projectId = ".$projectId;
    $res = mysqli_query($mysqli, $query) or die('-6'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Calculate & Format the Totals
	$a = "SELECT SUM(paymentAmount) AS totalAmount FROM projectpayments WHERE projectId = ".$projectId;
	$b = mysqli_query($mysqli, $a) or die('-7'.mysqli_error());
	$c = mysqli_fetch_assoc($b);
	$totalPayments = $c['totalAmount'];

	$d = "SELECT SUM(additionalFee) AS totalFee FROM projectpayments WHERE projectId = ".$projectId;
	$e = mysqli_query($mysqli, $d) or die('-8'.mysqli_error());
	$f = mysqli_fetch_assoc($e);
	$totalFees = $f['totalFee'];

	$projectFee = $curSym.format_amount($row['projectFee'], 2);
	$hasPaid = $totalPayments + $totalFees;
	$totalPaid = $curSym.format_amount($hasPaid, 2);
	$amtDue = $row['projectFee'] - $totalPayments;
	$totalDue = $curSym.format_amount($amtDue, 2);

	if ($amtDue == '0.00') { $due = $paidInFullText; $highlight = 'text-success'; } else { $due = $totalDue; $highlight = 'text-danger'; }
	if ($row['assignedTo'] != '') { $isManager = '1'; } else { $isManager = '0'; }

	// Get the Current Status of the Project
	if ($row['archiveProj'] == '0') {
		$curStatus = '<strong class="text-success">'.$openProjText.'</strong>';
	} else {
		$curStatus = '<strong class="text-danger">'.$closedProjText.' '.$row['archiveDate'].'</strong>';
	}
	
	// Include Pagination Class
	include('includes/getpagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records for Private Closed
	$rows = $mysqli->query("SELECT * FROM pwentries WHERE projectId = ".$projectId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Project Accounts & Passwords
    $sql = "SELECT
				entryId,
				projectId,
				adminId,
				clientId,
				entryTitle,
				entryDesc,
				entryUsername,
				entryPass,
				entryUrl,
				entryNotes
            FROM
                pwentries
            WHERE
                projectId = ".$projectId." ".$pages->get_limit();
    $results = mysqli_query($mysqli, $sql) or die('-9'.mysqli_error());

	// Get Project Tasks
	if ($isAdmin == '1') {
		$tasks = "SELECT
						tasks.taskId,
						tasks.projectId,
						tasks.adminId,
						tasks.taskTitle,
						tasks.taskDesc,
						tasks.taskPriority,
						tasks.taskStatus,
						DATE_FORMAT(tasks.taskDue,'%M %d, %Y') AS taskDue,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						tasks
						LEFT JOIN admins ON tasks.adminId = admins.adminId
					WHERE
						tasks.projectId = ".$projectId." AND
						tasks.isClosed = 0
					ORDER BY tasks.taskId";
		$taskres = mysqli_query($mysqli, $tasks) or die('-10' . mysqli_error());
	} else {
		$tasks = "SELECT
						tasks.taskId,
						tasks.projectId,
						tasks.adminId,
						tasks.taskTitle,
						tasks.taskDesc,
						tasks.taskPriority,
						tasks.taskStatus,
						DATE_FORMAT(tasks.taskDue,'%M %d, %Y') AS taskDue,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						tasks
						LEFT JOIN admins ON tasks.adminId = admins.adminId
					WHERE
						tasks.projectId = ".$projectId." AND
						tasks.adminId = ".$adminId." AND
						tasks.isClosed = 0
					ORDER BY tasks.taskId";
		$taskres = mysqli_query($mysqli, $tasks) or die('-11' . mysqli_error());
	}

	if ($set['enablePayments'] == '1') {
		// Get the project payments
		$payments = "SELECT
                        projectpayments.paymentId,
						projectpayments.projectId,
						projectpayments.enteredBy,
						projectpayments.paymentFor,
						DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
						projectpayments.paidBy,
						projectpayments.paymentAmount,
						projectpayments.additionalFee,
						projectpayments.paymentNotes,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						projectpayments
						LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
					WHERE
						projectpayments.projectId = ".$projectId."
                    ORDER BY projectpayments.paymentId DESC";
		$payres = mysqli_query($mysqli, $payments) or die('-12'.mysqli_error());

		$invQry = "SELECT
						invoices.invoiceId,
						invoices.projectId,
						invoices.adminId,
						invoices.clientId,
						invoices.invoiceTitle,
						invoices.invoiceNotes,
						DATE_FORMAT(invoices.invoiceDue,'%M %d, %Y') AS invoiceDue,
						UNIX_TIMESTAMP(invoices.invoiceDue) AS orderDate,
						invoices.isPaid,
						clientprojects.projectName,
						CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						invoices
						LEFT JOIN clientprojects ON invoices.projectId = clientprojects.projectId
						LEFT JOIN clients ON invoices.clientId = clients.clientId
						LEFT JOIN admins ON invoices.adminId = admins.adminId
					WHERE invoices.projectId = ".$projectId." AND invoices.isPaid = 0
					ORDER BY invoices.isPaid, orderDate";
		$invres = mysqli_query($mysqli, $invQry) or die('-13'.mysqli_error());
	}

	// Get Project Discussions
	$disc = "SELECT
				projectdiscus.discussionId,
				projectdiscus.projectId,
				projectdiscus.adminId,
				projectdiscus.clientId,
				projectdiscus.discussionTitle,
				projectdiscus.discussionText,
				DATE_FORMAT(projectdiscus.discussionDate,'%W, %M %e, %Y at %l:%i %p') AS discussionDate,
				DATE_FORMAT(projectdiscus.lastUpdated,'%W, %M %e, %Y at %l:%i %p') AS lastUpdated,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectdiscus
				LEFT JOIN clients ON projectdiscus.clientId = clients.clientId
				LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
			WHERE
				projectdiscus.projectId = ".$projectId."
			ORDER BY discussionId DESC
			LIMIT 5";
	$discres = mysqli_query($mysqli, $disc) or die('-14'.mysqli_error());

    // Get File Data
    $file = "SELECT
                projectfiles.fileId,
                projectfiles.folderId,
                projectfiles.projectId,
				projectfiles.adminId,
				projectfiles.clientId,
                projectfiles.fileTitle,
				projectfiles.fileDesc,
				DATE_FORMAT(projectfiles.fileDate,'%M %d, %Y') AS fileDate,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
            FROM
                projectfiles
				LEFT JOIN clients ON projectfiles.clientId = clients.clientId
				LEFT JOIN admins ON projectfiles.adminId = admins.adminId
            WHERE
                projectfiles.projectId = ".$projectId."
            ORDER BY projectId DESC
			LIMIT 5";
    $fileres = mysqli_query($mysqli, $file) or die('-15'.mysqli_error());

	include 'includes/navigation.php';

	if (($isAdmin != '1') && ($row['assignedTo'] != $adminId)) {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="contentAlt">
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row">
			<div class="col-md-8">
				<div class="content no-margin">
					<h3><?php echo $projectInfoTitle; ?></h3>

					<table class="infoTable">
						<tr>
							<td class="infoKey"><i class="fa fa-folder-open"></i> <?php echo $projectText; ?>:</td>
							<td class="infoVal"><?php echo clean($row['projectName']); ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-user"></i> <?php echo $clientText; ?>:</td>
							<td class="infoVal">
								<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
									<?php echo clean($row['theClient']); ?>
								</a>
							</td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-info-circle"></i> <?php echo $currentStatusText; ?>:</td>
							<td class="infoVal"><?php echo $curStatus; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-male"></i> <?php echo $projManagerText; ?>:</td>
							<td class="infoVal">
								<?php
									if ($row['assignedTo'] != '') {
										echo clean($row['theAdmin']);
									} else {
										echo '<strong class="text-warning">'.$unassignedText.'</strong>';
									}
								?>
							</td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateStartedText; ?>:</td>
							<td class="infoVal"><?php echo $row['startDate']; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateDueText; ?>:</td>
							<td class="infoVal"><strong><?php echo $row['dueDate']; ?></strong></td>
						</tr>
						<?php if ($set['enablePayments'] == '1') { ?>
							<tr>
								<td class="infoKey"><i class="fa fa-usd"></i> <?php echo $projectFeeText; ?>:</td>
								<td class="infoVal"><?php echo $projectFee; ?></td>
							</tr>
							<tr>
								<td class="infoKey"><i class="fa fa-credit-card"></i> <?php echo $totalPaidText; ?>:</td>
								<td class="infoVal"><?php echo $totalPaid; ?>*</td>
							</tr>
							<tr>
								<td class="infoKey"><i class="fa fa-money"></i> <?php echo $amountOwedText; ?>:</td>
								<td class="infoVal"><strong class="<?php echo $highlight; ?>"><?php echo $due; ?></strong></td>
							</tr>
						<?php } ?>
					</table>
					<div class="well well-sm bg-trans no-margin mt20">
						<strong><?php echo $projectDescField; ?>:</strong> <?php echo nl2br(clean($row['projectDeatils'])); ?>
					</div>
					<?php if ($row['projectNotes'] != '') { ?>
						<div class="well well-sm bg-trans no-margin mt20">
							<strong><?php echo $projectNotesField; ?>:</strong> <?php echo nl2br(clean($row['projectNotes'])); ?>
						</div>
					<?php } ?>
					<p class="text-muted mt10"><small>* <?php echo $totalPaidQuip; ?></small></p>

					<div class="clearfix mt20"></div>

					<a href="#updateProject" data-toggle="modal" class="btn btn-primary btn-icon"><i class="fa fa-folder-open"></i> <?php echo $updateProjBtn; ?></a>
					<?php if ($isAdmin == '1') { ?>
						<a href="#assignProject" data-toggle="modal" class="btn btn-info btn-icon"><i class="fa fa-male"></i> <?php echo $assignProjBtn; ?></a>
					<?php } ?>

					<?php if ($row['archiveProj'] == '0') { ?>
						<a href="#archiveProject" data-toggle="modal" class="btn btn-warning btn-icon"><i class="fa fa-archive"></i> <?php echo $closeProjBtn; ?></a>

						<div id="archiveProject" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $closeProjQuip; ?></p>
										</div>
										<div class="modal-footer">
											<button type="input" name="submit" value="archiveProject" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $closeProjBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
					<?php } else { ?>
						<a href="#reopenProject" data-toggle="modal"  class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $reopenProjBtn; ?></a>

						<div id="reopenProject" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $reopenProjQuip; ?></p>
										</div>
										<div class="modal-footer">
											<button type="input" name="submit" value="reopenProject" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $reopenProjBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>

				<div class="content">
					<h3><?php echo $projCurrProgress; ?></h3>
					<div class="barGraph clearfix" data-percent="<?php echo $row['percentComplete']; ?>%">
						<div class="barGraph-title">
							<span><?php echo $row['percentComplete']; ?><?php echo $percentCompleteText; ?></span>
						</div>
						<div class="barGraph-bar"></div>
					</div>
				</div>

				<?php
					if ($set['enablePayments'] == '1') {
						if(mysqli_num_rows($invres) > 0) {
				?>
						<div class="content">
							<h3><?php echo $outstnadingProjInvoices; ?></h3>
							<table class="rwd-table">
								<tbody>
									<tr class="primary">
										<th><?php echo $invoiceTableHead; ?></th>
										<th><?php echo $createdByTableHead; ?></th>
										<th><?php echo $paymentDueText; ?></th>
										<th><?php echo $invoiceAmtText; ?></th>
										<th><?php echo $statusText; ?></th>
										<th></th>
									</tr>
									<?php
										while ($r = mysqli_fetch_assoc($invres)) {
											// Get the Invoice Total
											$x = "SELECT
														itemAmount,
														itemqty
													FROM
														invitems
													WHERE invoiceId = ".$r['invoiceId'];
											$y = mysqli_query($mysqli, $x) or die('-16'.mysqli_error());

											$lineTotal = 0;
											while ($z = mysqli_fetch_assoc($y)) {
												$lineItem = $z['itemAmount'] * $z['itemqty'];
												$lineTotal += $lineItem;
											}
											$lineTotal = $curSym.format_amount($lineTotal, 2);

											if ($r['isPaid'] == '1') { $status = $paidText; $highlight = 'class="text-success"'; } else { $status = $unpaidText; $highlight = 'class="text-danger"'; }
									?>
											<tr>
												<td data-th="<?php echo $invoiceTableHead; ?>">
													<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewInvoiceText; ?>">
														<a href="index.php?action=viewInvoice&invoiceId=<?php echo $r['invoiceId']; ?>"><?php echo clean($r['invoiceTitle']); ?></a>
													</span>
												</td>
												<td data-th="<?php echo $createdByTableHead; ?>"><?php echo clean($r['theAdmin']); ?></td>
												<td data-th="<?php echo $paymentDueText; ?>"><?php echo $r['invoiceDue']; ?></td>
												<td data-th="<?php echo $invoiceAmtText; ?>"><?php echo $lineTotal; ?></td>
												<td data-th="<?php echo $statusText; ?>"><strong <?php echo $highlight; ?>><?php echo $status; ?></strong></td>
												<td data-th="<?php echo $actionsText; ?>">
													<a href="index.php?action=viewInvoice&invoiceId=<?php echo $r['invoiceId']; ?>">
														<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewInvoiceText; ?>"></i>
													</a>
												</td>
											</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
				<?php
						}
					}
				?>

				<div class="content last">
					<h3><?php echo $accountEntryTitle; ?></h3>
					<p>
						<?php echo $accountEntryTitleQuip; ?>
						<span class="pull-right">
							<a href="#newentry" data-toggle="modal" class="btn btn-default btn-sm btn-icon"><i class="fa fa-plus"></i> <?php echo $newEntryBtn; ?></a>
						</span>
					</p>

					<?php if(mysqli_num_rows($results) > 0) { ?>
						<table class="rwd-table">
							<tbody>
								<tr>
									<th class="text-left"><?php echo $accountText; ?></th>
									<th><?php echo $usernameText; ?></th>
									<th><?php echo $urlText; ?></th>
									<th></th>
								</tr>
								<?php
									while ($pw = mysqli_fetch_assoc($results)) {
										// Decrypt Data
										if ($pw['entryTitle'] != '') { $entryTitle = decryptIt($pw['entryTitle']); } else { $entryTitle = ''; }
										if ($pw['entryDesc'] != '') { $entryDesc = decryptIt($pw['entryDesc']); } else { $entryDesc = ''; }
										if ($pw['entryUsername'] != '') { $entryUsername = decryptIt($pw['entryUsername']); } else { $entryUsername = ''; }
										if ($pw['entryPass'] != '') { $entryPass = decryptIt($pw['entryPass']); } else { $entryPass = ''; }
										if ($pw['entryUrl'] != '') { $entryUrl = decryptIt($pw['entryUrl']); } else { $entryUrl = ''; }
										if ($pw['entryNotes'] != '') { $entryNotes = decryptIt($pw['entryNotes']); } else { $entryNotes = ''; }
								?>
										<tr>
											<td data-th="<?php echo $accountText; ?>">
												<span data-toggle="tooltip" data-placement="right" title="<?php echo ellipsis($entryDesc,125); ?>">
													<?php echo $entryTitle; ?>
												</span>
											</td>
											<td data-th="<?php echo $usernameText; ?>"><?php echo $entryUsername; ?></td>
											<td data-th="<?php echo $urlText; ?>"><a href="<?php echo $entryUrl; ?>" target="_blank"><?php echo $entryUrl; ?></a></td>
											<td class="text-right" data-th="<?php echo $actionsText; ?>">
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewPassTooltip; ?>">
													<a href="#viewentry<?php echo $pw['entryId']; ?>" data-toggle="modal"><i class="fa fa-external-link-square print"></i></a>
												</span>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $editEntryTooltip; ?>">
													<a href="index.php?action=projectAccount&entryId=<?php echo $pw['entryId']; ?>"><i class="fa fa-edit edit"></i></a>
												</span>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteEntryTooltip; ?>">
													<a href="#deleteentry<?php echo $pw['entryId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
												</span>
											</td>
										</tr>

										<div class="modal fade" id="viewentry<?php echo $pw['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $entryPass; ?></p>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $closeBtn; ?></button>
														</div>
													</form>
												</div>
											</div>
										</div>

										<div class="modal fade" id="deleteentry<?php echo $pw['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $deleteEntryConf.' '.clean($entryTitle); ?>?</p>
														</div>
														<div class="modal-footer">
															<input name="deleteId" type="hidden" value="<?php echo $pw['entryId']; ?>" />
															<button type="input" name="submit" value="deleteentry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
															<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
														</div>
													</form>
												</div>
											</div>
										</div>
								<?php } ?>
							</tbody>
						</table>
					<?php
					}
					if ($total > $pagPages) {
						echo $pages->page_links();
					}
					?>

					<div id="newentry" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<h4 class="modal-title"><?php echo $newEntryBtn; ?></h4>
								</div>
								<form action="" method="post">
									<div class="modal-body">
										<div class="form-group">
											<label for="entryTitle"><?php echo $accountText; ?></label>
											<input type="text" class="form-control" required="" name="entryTitle" value="<?php echo isset($_POST['entryTitle']) ? $_POST['entryTitle'] : ''; ?>" />
										</div>
										<div class="form-group">
											<label for="entryDesc"><?php echo $descriptionText; ?></label>
											<textarea class="form-control" name="entryDesc" required="" rows="3"><?php echo isset($_POST['entryDesc']) ? $_POST['entryDesc'] : ''; ?></textarea>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="entryUsername"><?php echo $usernameText; ?></label>
													<input type="text" class="form-control" required="" name="entryUsername" value="<?php echo isset($_POST['entryUsername']) ? $_POST['entryUsername'] : ''; ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="entryPass"><?php echo $passwordField; ?></label>
													<input type="password" class="form-control" required="" name="entryPass" id="newPass" value="<?php echo isset($_POST['entryPass']) ? $_POST['entryPass'] : ''; ?>" />
													<span class="help-block">
														<a href="" id="show2" class="btn btn-warning btn-xs"><?php echo $showPlainText; ?></a>
														<a href="" id="hide2" class="btn btn-info btn-xs"><?php echo $hidePlainText; ?></a>
													</span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="entryUrl"><?php echo $urlText; ?></label>
											<input type="text" class="form-control" name="entryUrl" value="<?php echo isset($_POST['entryUrl']) ? $_POST['entryUrl'] : ''; ?>" />
										</div>
										<div class="form-group">
											<label for="entryNotes"><?php echo $notesText; ?></label>
											<textarea class="form-control" name="entryNotes" rows="3"><?php echo isset($_POST['entryNotes']) ? $_POST['entryNotes'] : ''; ?></textarea>
										</div>
									</div>
									<div class="modal-footer">
										<button type="input" name="submit" value="newentry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="col-md-4">
				<div class="small-box bg-white">
					<div class="inner">
						<h3><?php echo $totalTime; ?></h3>
						<p><?php echo $totalTimeQuip; ?></p>
					</div>
					<div class="icon icon-lg"><i class="fa fa-clock-o"></i></div>
				</div>

				<?php if ($row['archiveProj'] == '0') { ?>
					<div class="small-box bg-white">
						<div class="inner">
							<div class="row">
								<div class="col-md-6">
									<p>
										<?php echo $youAreCurrentlyText; ?><br />
										<span class="clock-status"></span>
									</p>
								</div>
								<div class="col-md-6">
									<form action="" method="post" class="clockBtn">
										<input type="hidden" name="clockId" value="<?php echo $clockId; ?>" />
										<input type="hidden" name="entryId" value="<?php echo $entryId; ?>" />
										<input type="hidden" name="weekNo" value="<?php echo $weekNum; ?>" />
										<input type="hidden" name="clockYear" value="<?php echo $currentYear; ?>" />
										<input type="hidden" name="running" id="running" value="<?php echo $running; ?>" />
										<input type="hidden" name="isRecord" id="isRecord" value="<?php echo $isRecord; ?>" />
										<button type="input" name="submit" id="timetrack" value="toggleTime" class="btn btn-block btn-icon" value="toggleTime"><i class=""></i> <span><?php echo $clockInText; ?></span></button>
									</form>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<div class="content">
					<h4 class="bg-primary"><?php echo $openProjTasksText; ?></h4>
					<?php if(mysqli_num_rows($taskres) < 1) { ?>
						<dl class="accordion">
							<dt class="noneFound"><a><i class="fa fa-minus-square"></i> <?php echo $noOpenProjTasks; ?></a></dt>
						</dl>
					<?php
						} else {
							echo '<dl class="accordion">';
							while ($t = mysqli_fetch_assoc($taskres)) {
					?>
								<dt><a><?php echo ellipsis($t['taskTitle'],35); ?><span><i class="fa fa-angle-right"></i></span></a></dt>
								<dd class="hideIt">
									<p><?php echo ellipsis($t['taskDesc'],150); ?></p>
									<p class="updatedOn">
										<?php echo $dueByText; ?>: <?php echo $t['taskDue']; ?><br />
										<?php echo $priorityText; ?>: <?php echo $t['taskPriority']; ?>
									</p>
									<p>
										<a href="index.php?action=viewTask&taskId=<?php echo $t['taskId']; ?>" class="btn btn-success btn-xs btn-icon"><i class="fa fa-tasks"></i> <?php echo $pageNameviewTask; ?></a>
									</p>
								</dd>
					<?php
							}
							echo '</dl>';
						}
					?>
					<div class="clearfix"></div>
					<a href="index.php?action=projectTasks" class="btn btn-default btn-block btn-icon"><i class="fa fa-tasks"></i> <?php echo $viewAllProjTasks; ?></a>
				</div>

				<?php if ($set['enablePayments'] == '1') { ?>
					<div class="content">
						<h4 class="bg-success"><?php echo $projPaymentsText; ?></h4>
						<?php if(mysqli_num_rows($payres) < 1) { ?>
							<dl class="accordion">
								<dt class="noneFound"><a><i class="fa fa-minus-square"></i> <?php echo $noProjPayments; ?></a></dt>
							</dl>
						<?php
							} else {
								echo '<dl class="accordion">';
								while ($a = mysqli_fetch_assoc($payres)) {
									$paymentAmount = $a['paymentAmount'];
									$additionalFee = $a['additionalFee'];
									$payTotal = $paymentAmount+$additionalFee;
									$paymentTotal = $curSym.format_amount($payTotal, 2);
						?>
									<dt><a><?php echo ellipsis($a['paymentFor'],35); ?><span><i class="fa fa-angle-right"></i></span></a></dt>
									<dd class="hideIt">
										<p>
											<?php echo $paymentTotalText; ?>: <?php echo $paymentTotal; ?><br />
											<?php echo $dateRecvdText; ?>: <?php echo $a['paymentDate']; ?>
										</p>
										<p class="updatedOn">
											<?php echo $receivedByText; ?>: <?php echo clean($a['theAdmin']); ?><br />
											<?php echo $paidByText; ?>: <?php echo clean($a['paidBy']); ?>
										</p>
										<p class="mt10">
											<a href="index.php?action=viewPayment&paymentId=<?php echo $a['paymentId']; ?>" class="btn btn-success btn-xs btn-icon"><i class="fa fa-money"></i> <?php echo $viewPaymentBtn; ?></a>
											<a href="index.php?action=receipt&paymentId=<?php echo $a['paymentId']; ?>" class="btn btn-info btn-xs btn-icon"><i class="fa fa-print"></i> <?php echo $receiptBtn; ?></a>
											<a href="#deletePayment<?php echo $a['paymentId']; ?>" data-toggle="modal" class="btn btn-danger btn-xs btn-icon"><i class="fa fa-times"></i> <?php echo $deleteBtn; ?></a>
										</p>
									</dd>

									<div class="modal fade" id="deletePayment<?php echo $a['paymentId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<form action="" method="post">
													<div class="modal-body">
														<p class="lead"><?php echo $deletePaymentConf.' '.$a['paymentDate'].' '.$forText.' '.$paymentTotal; ?>?</p>
													</div>
													<div class="modal-footer">
														<input name="deleteId" type="hidden" value="<?php echo $a['paymentId']; ?>" />
														<input name="projectPayments" type="hidden" value="<?php echo $row['projectPayments']; ?>" />
														<button type="input" name="submit" value="deletePayment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
														<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
													</div>
												</form>
											</div>
										</div>
									</div>
						<?php
								}
								echo '</dl>';
							}
						?>
						<div class="clearfix"></div>
						<?php if ($due != 'Paid in Full') { ?>
							<a href="#newPayment" data-toggle="modal" class="btn btn-default btn-block btn-icon"><i class="fa fa-money"></i> <?php echo $recordPaymentBtn; ?></a>
						<?php } ?>
						<a href="index.php?action=projectPayments&projectId=<?php echo $projectId; ?>" class="btn btn-default btn-block btn-icon"><i class="fa fa-credit-card"></i> <?php echo $viewProjPaymentsBtn; ?></a>
						<a href="#newInvoice" data-toggle="modal" class="btn btn-default btn-block btn-icon"><i class="fa fa-file-text-o"></i> <?php echo $createInvoiceBtn; ?></a>
					</div>

					<div id="newInvoice" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">

								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<h4 class="modal-title"><?php echo $createInvoiceBtn; ?></h4>
								</div>

								<form action="" method="post">
									<div class="modal-body">
										<p><?php echo $createInvoiceQuip; ?></p>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="invoiceTitle"><?php echo $invoiceTitleField; ?></label>
													<input type="text" class="form-control" required="" name="invoiceTitle" value="<?php echo isset($_POST['invoiceTitle']) ? $_POST['invoiceTitle'] : ''; ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="invoiceDue"><?php echo $invoiceDateDueField; ?></label>
													<input type="text" class="form-control" required="" name="invoiceDue" id="invoiceDue" value="<?php echo isset($_POST['invoiceDue']) ? $_POST['invoiceDue'] : ''; ?>" />
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="invoiceNotes"><?php echo $invoiceNotesField; ?></label>
											<textarea class="form-control" name="invoiceNotes" rows="2"><?php echo isset($_POST['invoiceNotes']) ? $_POST['invoiceNotes'] : ''; ?></textarea>
											<span class="help-block"><?php echo $invoiceNotesFieldHelp; ?></span>
										</div>
										<div class="checkbox">
											<label>
												<input type="checkbox" name="notifyClient" value="1">
												<?php echo $notifyClientCheckbox; ?>
											</label>
										</div>
										<span class="help-block"><?php echo $notifyClientCheckboxHelp; ?></span>
									</div>

									<div class="modal-footer">
										<input name="projName" type="hidden" value="<?php echo clean($row['projectName']); ?>" />
										<button type="input" name="submit" value="newInvoice" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>

							</div>
						</div>
					</div>
				<?php } ?>

				<div class="content">
					<h4 class="bg-info"><?php echo $recentDiscText; ?></h4>
					<?php if(mysqli_num_rows($discres) < 1) { ?>
						<dl class="accordion">
							<dt class="noneFound"><a><i class="fa fa-minus-square"></i> <?php echo $noDiscMsg; ?></a></dt>
						</dl>
					<?php
						} else {
							echo '<dl class="accordion">';
							while ($b = mysqli_fetch_assoc($discres)) {
					?>
								<dt><a><?php echo ellipsis($b['discussionTitle'],35); ?><span><i class="fa fa-angle-right"></i></span></a></dt>
								<dd class="hideIt">
									<p><?php echo ellipsis($b['discussionText'],150); ?></p>
									<p class="updatedOn">
										<?php echo $postedOnText; ?>: <?php echo $b['discussionDate']; ?>
										<?php if($b['lastUpdated'] != '') { ?>
											<br /><?php echo $lastUpdatedText; ?>: <?php echo $b['lastUpdated']; ?>
										<?php } ?>
									</p>
									<p>
										<a href="index.php?action=viewDiscussion&discussionId=<?php echo $b['discussionId']; ?>" class="btn btn-success btn-xs btn-icon"><i class="fa fa-comment"></i> <?php echo $pageNameviewDiscussion; ?></a>
									</p>
								</dd>
					<?php
							}
							echo '</dl>';
						}
					?>
					<div class="clearfix"></div>
					<a href="index.php?action=projectDiscussions&projectId=<?php echo $projectId; ?>" class="btn btn-default btn-block btn-icon"><i class="fa fa-comments"></i> <?php echo $viewAllProjDisc; ?></a>
				</div>

				<div class="content last">
					<h4 class="bg-warning"><?php echo $recentUploadsText; ?></h4>
					<?php if(mysqli_num_rows($fileres) < 1) { ?>
						<dl class="accordion">
							<dt class="noneFound"><a><i class="fa fa-minus-square"></i> <?php echo $noUploadsMsg; ?></a></dt>
						</dl>
					<?php
						} else {
							echo '<dl class="accordion">';
							while ($c = mysqli_fetch_assoc($fileres)) {
					?>
								<dt><a><?php echo ellipsis($c['fileTitle'],35); ?><span><i class="fa fa-angle-right"></i></span></a></dt>
								<dd class="hideIt">
									<p><?php echo ellipsis($c['fileDesc'],125); ?></p>
									<p class="updatedOn">
										<?php echo $uploadedOnText; ?>: <?php echo $c['fileDate']; ?>
									</p>
									<p>
										<a href="index.php?action=viewFile&fileId=<?php echo $c['fileId']; ?>" class="btn btn-primary btn-xs btn-icon"><i class="fa fa-file-o"></i> <?php echo $viewFileText; ?></a>
									</p>
								</dd>
					<?php
							}
							echo '</dl>';
						}
					?>
					<div class="clearfix"></div>
					<a href="index.php?action=projectFolders&projectId=<?php echo $projectId; ?>" class="btn btn-default btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $viewAllProjFiles; ?></a>
				</div>
			</div>
		</div>
	</div>

	<div id="updateProject" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $updateProjBtn; ?>: <?php echo clean($row['projectName']); ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<?php if ($set['enablePayments'] == '1') { ?>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="projectName"><?php echo $projectNameField; ?></label>
										<input type="text" class="form-control" required="" name="projectName" value="<?php echo clean($row['projectName']); ?>" />
										<span class="help-block"><?php echo $projectNameFieldHelp; ?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="projectFee"><?php echo $projFeeField; ?></label>
										<input type="text" class="form-control" required="" name="projectFee" value="<?php echo $row['projectFee']; ?>" />
										<span class="help-block"><?php echo $numbersOnlyHelp; ?></span>
									</div>
								</div>
							</div>
						<?php } else { ?>
							<div class="form-group">
								<label for="projectName"><?php echo $projectNameField; ?></label>
								<input type="text" class="form-control" required="" name="projectName" value="<?php echo clean($row['projectName']); ?>" />
								<span class="help-block"><?php echo $projectNameFieldHelp; ?></span>
							</div>
						<?php } ?>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="startDate"><?php echo $ProjStartDateField; ?></label>
									<input type="text" class="form-control" required="" name="startDate" id="startDate" value="<?php echo $row['dateStart']; ?>" />
									<span class="help-block"><?php echo $dateFormatHelp; ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="dueDate"><?php echo $ProjDueDateField; ?></label>
									<input type="text" class="form-control" required="" name="dueDate" id="dueDate" value="<?php echo $row['dateDue']; ?>" />
									<span class="help-block"><?php echo $dateFormatHelp; ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="percentComplete"><?php echo $percentCompleteText; ?></label>
									<input type="text" class="form-control" required="" name="percentComplete" value="<?php echo clean($row['percentComplete']); ?>" />
									<span class="help-block"><?php echo $percentCompleteHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="projectDeatils"><?php echo $projectDescField; ?></label>
							<textarea class="form-control" required="" name="projectDeatils" rows="5"><?php echo clean($row['projectDeatils']); ?></textarea>
							<span class="help-block"><?php echo $projectDescFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="projectNotes"><?php echo $projectNotesField; ?></label>
							<textarea class="form-control" name="projectNotes" rows="3"><?php echo clean($row['projectNotes']); ?></textarea>
							<span class="help-block"><?php echo $projectNotesFieldHelp; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="updateProject" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php if ($isAdmin == '1') { ?>
		<div id="assignProject" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $assignProjBtn; ?>: <?php echo clean($row['projectName']); ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="adminId"><?php echo $selectProjManagerField; ?></label>
								<select class="form-control" name="adminId">
								<?php
									// Get the Manager List
									$sqlStmt = "SELECT adminId, CONCAT(adminFirstName,' ',adminLastName) AS theManager FROM admins WHERE isActive = 1";
									$results = mysqli_query($mysqli, $sqlStmt) or die('-17'.mysqli_error());
								?>
									<option value="..."><?php echo $selectOption; ?></option>
									<?php while ($a = mysqli_fetch_assoc($results)) { ?>
										<option value="<?php echo $a['adminId']; ?>"><?php echo clean($a['theManager']); ?></option>
									<?php } ?>
								</select>
								<span class="help-block"><?php echo $selectProjManagerFieldHelp; ?></span>
							</div>
						</div>
						<div class="modal-footer">
							<input name="isManager" type="hidden" value="<?php echo $isManager; ?>" />
							<button type="input" name="submit" value="assignProject" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if ($set['enablePayments'] == '1') { ?>
		<div id="newPayment" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $recordPaymentModal; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<p><?php echo $recordPaymentModalQuip; ?></p>
							<div class="form-group">
								<label for="paymentFor"><?php echo $paymentForField; ?></label>
								<input type="text" class="form-control" required="" name="paymentFor" value="<?php echo isset($_POST['paymentFor']) ? $_POST['paymentFor'] : ''; ?>" />
								<span class="help-block"><?php echo $paymentForFieldHelp; ?></span>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="paymentDate"><?php echo $datePayReceivedField; ?></label>
										<input type="text" class="form-control" required="" name="paymentDate" id="paymentDate" value="<?php echo isset($_POST['paymentDate']) ? $_POST['paymentDate'] : ''; ?>" />
										<span class="help-block"><?php echo $dateFormatHelp; ?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="paidBy"><?php echo $paidByText; ?></label>
										<input type="text" class="form-control" required="" name="paidBy" value="<?php echo isset($_POST['paidBy']) ? $_POST['paidBy'] : ''; ?>" />
										<span class="help-block"><?php echo $paidByFieldHelp; ?></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="paymentAmount"><?php echo $baseAmountField; ?></label>
										<input type="text" class="form-control" required="" name="paymentAmount" value="<?php echo isset($_POST['paymentAmount']) ? $_POST['paymentAmount'] : ''; ?>" />
										<span class="help-block"><?php echo $baseAmountFieldHelp; ?></span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="additionalFee"><?php echo $feesAmountField; ?></label>
										<input type="text" class="form-control" name="additionalFee" value="<?php echo isset($_POST['additionalFee']) ? $_POST['additionalFee'] : ''; ?>" />
										<span class="help-block"><?php echo $feesAmountFieldHelp; ?></span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="paymentNotes"><?php echo $paymentNotesField; ?></label>
								<textarea class="form-control" name="paymentNotes" rows="2"><?php echo isset($_POST['paymentNotes']) ? $_POST['paymentNotes'] : ''; ?></textarea>
								<span class="help-block"><?php echo $paymentNotesFieldHelp; ?></span>
							</div>
						</div>
						<div class="modal-footer">
							<input name="projectPayments" type="hidden" value="<?php echo $row['projectPayments']; ?>" />
							<input name="cId" type="hidden" value="<?php echo $row['clientId']; ?>" />
							<button type="input" name="submit" value="newPayment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php
		}
	}
?>