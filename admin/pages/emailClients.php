<?php
	// Get the list of active Client emails
	$clientsql = "SELECT clientEmail FROM clients WHERE isActive = 1";
	$clientresult = mysqli_query($mysqli, $clientsql) or die('-1'.mysqli_error());

	// Set each email into a csv
	$emailClients = array();
	while ($c = mysqli_fetch_assoc($clientresult)) {
		$emailClients[] = $c['clientEmail'];
	}
	$activeClients = implode(',',$emailClients);

	// Send the Email
	if (isset($_POST['submit']) && $_POST['submit'] == 'sendEmail') {
		if($_POST['subject'] == "") {
            $msgBox = alertBox($msgSubjectReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['emailContent'] == "") {
            $msgBox = alertBox($msgTextReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$subject = $mysqli->real_escape_string($_POST['subject']);
			$adminFullName = $mysqli->real_escape_string($_POST['adminFullName']);
			$emailContent = $_POST['emailContent'];

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			// -------------------------------
			// ---- START Edit Email Text ----
			// -------------------------------
			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$emailContent.'</p>';
			$message .= '<p>'.$sendCntEmailThanks.$adminFullName.'</p>';
			$message .= '</body></html>';
			// -------------------------------
			// ---- END Edit Email Text ----
			// -------------------------------

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($activeClients, $subject, $message, $headers)) {
				$msgBox = alertBox($sendCntEmailSentMsg, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the form of Values
			$_POST['subject'] = $_POST['emailContent'] = '';
		}
	}

	include 'includes/navigation.php';

	if ($isAdmin != '1') {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<form action="" method="post">
			<div class="form-group">
				<label for="subject"><?php echo $subjectText; ?></label>
				<input type="text" class="form-control" required="" name="subject" value="<?php echo isset($_POST['subject']) ? $_POST['subject'] : ''; ?>" />
			</div>
			<div class="form-group">
				<label for="emailContent"><?php echo $emailContentField; ?></label>
				<textarea class="form-control" name="emailContent" required="" rows="10"><?php echo isset($_POST['emailContent']) ? $_POST['emailContent'] : ''; ?></textarea>
			</div>
			<input type="hidden" name="adminFullName" value="<?php echo $adminFullName; ?>" />
			<button type="input" name="submit" value="sendEmail" class="btn btn-success btn-icon mt20"><i class="fa fa-check-square-o"></i> <?php echo $sendClientsEmailBtn; ?></button>
		</form>

	</div>
<?php } ?>