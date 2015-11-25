<?php
	$datePicker = 'true';
	$jsFile = 'newProject';

	// Add New Project
    if (isset($_POST['submit']) && $_POST['submit'] == 'addProject') {
        // Validation
        if($_POST['clientId'] == "...") {
            $msgBox = alertBox($selectClientReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['projectName'] == "") {
            $msgBox = alertBox($projTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if(($set['enablePayments'] == "1") && ($_POST['projectFee'] == "")) {
			$msgBox = alertBox($projFeeReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['projectDeatils'] == "") {
            $msgBox = alertBox($projDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dueDate'] == "") {
            $msgBox = alertBox($projDueByDateReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$sendEmail = $mysqli->real_escape_string($_POST['sendEmail']);
			$clientId = $mysqli->real_escape_string($_POST['clientId']);
			$clientName = $mysqli->real_escape_string($_POST['clientName']);
			$projectName = $mysqli->real_escape_string($_POST['projectName']);
			if ($set['enablePayments'] == '1') {
				$projectFee = $mysqli->real_escape_string($_POST['projectFee']);
			} else {
				$projectFee = '0';
			}
			$dueDate = $mysqli->real_escape_string($_POST['dueDate']);
			$projectDeatils = $_POST['projectDeatils'];
			$projectNotes = $_POST['projectNotes'];
			$startDate = date("Y-m-d H:i:s");

            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    clientprojects(
                                        createdBy,
                                        clientId,
                                        projectName,
										projectFee,
										startDate,
										dueDate,
										projectDeatils,
										projectNotes
                                    ) VALUES (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
										?,
										?
                                    )");
            $stmt->bind_param('ssssssss',
				$adminId,
                $clientId,
                $projectName,
                $projectFee,
                $startDate,
                $dueDate,
				$projectDeatils,
				$projectNotes
            );
            $stmt->execute();

			if ($sendEmail == '1') {
				// Get the Client's email
				$email = "SELECT clientEmail FROM clients WHERE clientId = ".$clientId;
				$emailres = mysqli_query($mysqli, $email) or die('-1'.mysqli_error());
				$col = mysqli_fetch_assoc($emailres);
				$clientEmail = $col['clientEmail'];

				// Send out the email in HTML
				$installUrl = $set['installUrl'];
				$siteName = $set['siteName'];
				$businessEmail = $set['businessEmail'];

				$subject = $newProjEmailSubject;

				$message = '<html><body>';
				$message .= '<h3>'.$subject.'</h3>';
				$message .= '<hr>';
				$message .= '<p>'.$projectText.': '.$projectName.'</p>';
				$message .= '<p>'.$projectDeatils.'</p>';
				$message .= '<hr>';
				$message .= '<p>'.$emailLink.'</p>';
				$message .= '<p>'.$emailThankYou.'</p>';
				$message .= '</body></html>';

				$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
				$headers .= "Reply-To: ".$businessEmail."\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				if (mail($clientEmail, $subject, $message, $headers)) {
					$msgBox = alertBox($newProjEmailSent, "<i class='fa fa-check-square'></i>", "success");
				} else {
					$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-warning'></i>", "warning");
				}
			} else {
				$msgBox = alertBox($newProjCreated, "<i class='fa fa-check-square'></i>", "success");
			}
			// Clear the form of Values
			$_POST['clientName'] = $_POST['projectName'] = $_POST['dueDate'] = $_POST['projectDeatils'] = $_POST['projectNotes'] = '';
            $stmt->close();
		}
	}
	
	if ($set['enablePayments'] == '1') { $colNum = '4'; } else { $colNum = '6'; }

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li><a href="index.php?action=openProjects"><i class="fa fa-folder-open"></i> <?php echo $openProjNavLink; ?></a></li>
		<li><a href="index.php?action=closedProjects"><i class="fa fa-folder"></i> <?php echo $pageNameclosedProjects; ?></a></li>
		<li class="active pull-right"><a href="index.php?action=newProject"><i class="fa fa-plus"></i> <?php echo $pageNamenewProject; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<form action="" method="post">
		<div class="row">
			<div class="col-md-<?php echo $colNum; ?>">
				<div class="form-group">
					<label for="sendEmail"><?php echo $sendEmailToClient; ?></label>
					<select class="form-control" name="sendEmail">
						<option value="0" selected><?php echo $noBtn; ?></option>
						<option value="1"><?php echo $yesBtn; ?></option>
					</select>
					<span class="help-block"><?php echo $sendEmailToClientHelp; ?></span>
				</div>
			</div>
			<div class="col-md-<?php echo $colNum; ?>">
				<div class="form-group">
					<label for="clientId"><?php echo $selectClientField; ?></label>
					<select class="form-control" name="clientId" id="clientId">
					<?php
						// Get the Client List
						$sqlStmt = "SELECT clientId, clientFirstName, clientLastName FROM clients WHERE isActive = 1 AND isArchived = 0";
						$results = mysqli_query($mysqli, $sqlStmt) or die('-2'.mysqli_error());
					?>
						<option value="..."><?php echo $selectOption; ?></option>
						<?php while ($row = mysqli_fetch_assoc($results)) { ?>
							<option value="<?php echo $row['clientId']; ?>"><?php echo clean($row['clientFirstName']).' '.clean($row['clientLastName']); ?></option>
						<?php } ?>
					</select>
					<span class="help-block"><?php echo $selectClientFieldHelp; ?></span>
					<input type="hidden" name="clientName" id="clientName" />
				</div>
			</div>
			<?php if ($set['enablePayments'] == '1') { ?>
				<div class="col-md-4">
					<div class="form-group">
						<label for="projectFee"><?php echo $projectFeeText; ?></label>
						<input type="text" class="form-control" required="" name="projectFee" value="<?php echo isset($_POST['projectFee']) ? $_POST['projectFee'] : ''; ?>" />
						<span class="help-block"><?php echo $numbersOnlyHelp; ?></span>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="projectName"><?php echo $projectNameField; ?></label>
					<input type="text" class="form-control" required="" name="projectName" value="<?php echo isset($_POST['projectName']) ? $_POST['projectName'] : ''; ?>" />
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="dueDate"><?php echo $dateDueByField; ?></label>
					<input type="text" class="form-control" required="" name="dueDate" id="dueDate" value="<?php echo isset($_POST['dueDate']) ? $_POST['dueDate'] : ''; ?>" />
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="projectDeatils"><?php echo $projectDescField; ?></label>
			<textarea class="form-control" name="projectDeatils" rows="3"><?php echo isset($_POST['projectDeatils']) ? $_POST['projectDeatils'] : ''; ?></textarea>
			<span class="help-block"><?php echo $projectDescFieldHelp; ?></span>
		</div>
		<div class="form-group">
			<label for="projectNotes"><?php echo $projectNotesField; ?></label>
			<textarea class="form-control" name="projectNotes" rows="3"><?php echo isset($_POST['projectNotes']) ? $_POST['projectNotes'] : ''; ?></textarea>
			<span class="help-block"><?php echo $projectNotesFieldHelp; ?></span>
		</div>
		<button type="input" name="submit" value="addProject" class="btn btn-success btn-icon mt20"><i class="fa fa-check-square-o"></i> <?php echo $saveNewProjBtn; ?></button>
	</form>
</div>