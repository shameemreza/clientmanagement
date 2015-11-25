<?php
	$jsFile = 'newClient';

	// Add New Client Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'addClient') {
        // Validation
        if($_POST['clientEmail'] == "") {
            $msgBox = alertBox($emailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['password1'] == "") {
            $msgBox = alertBox($passworReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password1'] != $_POST['password2']) {
			$msgBox = alertBox($passwordsNotMatchMsg, "<i class='fa fa-warning'></i>", "warning");
        } else if($_POST['clientFirstName'] == "") {
            $msgBox = alertBox($firstNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['clientLastName'] == "") {
            $msgBox = alertBox($lastNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$setActive = $mysqli->real_escape_string($_POST['setActive']);
			$dupEmail = '';
			$newEmail = $mysqli->real_escape_string($_POST['clientEmail']);
			$clientFirstName = $mysqli->real_escape_string($_POST['clientFirstName']);
			$clientLastName = $mysqli->real_escape_string($_POST['clientLastName']);

			// Check for Duplicate email
			$check = $mysqli->query("SELECT 'X' FROM clients WHERE clientEmail = '".$newEmail."'");
			if ($check->num_rows) {
				$dupEmail = 'true';
			}

			// If duplicates are found
			if ($dupEmail != '') {
				$msgBox = alertBox($emailInUseMsg, "<i class='fa fa-warning'></i>", "warning");
			} else {
				if ($setActive == '0') {
					// Create the new account & send Activation Email to Client
					$hash = md5(rand(0,1000));
					$isActive = '0';
					$createDate = date("Y-m-d H:i:s");
					$password = encryptIt($_POST['password1']);

					$stmt = $mysqli->prepare("
										INSERT INTO
											clients(
												clientEmail,
												password,
												clientFirstName,
												clientLastName,
												createDate,
												hash,
												isActive
											) VALUES (
												?,
												?,
												?,
												?,
												?,
												?,
												?
											)");
					$stmt->bind_param('sssssss',
						$newEmail,
						$password,
						$clientFirstName,
						$clientLastName,
						$createDate,
						$hash,
						$isActive
					);
					$stmt->execute();

					// Send out the email in HTML
					$installUrl = $set['installUrl'];
					$siteName = $set['siteName'];
					$businessEmail = $set['businessEmail'];
					$newPass = $mysqli->real_escape_string($_POST['password1']);

					$subject = $newClientEmailSubject;

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>'.$newClientEmail1.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$newClientEmail2.' '.$newPass.'</p>';
					$message .= '<p>'.$newClientEmail3.' '.$installUrl.'activate.php?clientEmail='.$newEmail.'&hash='.$hash.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$newClientEmail4.'</p>';
					$message .= '<p>'.$emailThankYou.'</p>';
					$message .= '</body></html>';

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($newEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($newClientAcctEmailSent, "<i class='fa fa-check-square'></i>", "success");
						// Clear the form of Values
						$_POST['clientEmail'] = $_POST['password1'] = $_POST['password2'] = $_POST['clientFirstName'] = $_POST['clientLastName'] = '';
					} else {
						$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
					}
					$stmt->close();
				} else {
					// Create the new account and set it to Active
					$hash = md5(rand(0,1000));
					$isActive = '1';
					$today = date("Y-m-d H:i:s");
					$password = encryptIt($_POST['password1']);

					$stmt = $mysqli->prepare("
										INSERT INTO
											clients(
												clientEmail,
												password,
												clientFirstName,
												clientLastName,
												createDate,
												hash,
												isActive
											) VALUES (
												?,
												?,
												?,
												?,
												?,
												?,
												?
											)");
					$stmt->bind_param('sssssss',
						$newEmail,
						$password,
						$clientFirstName,
						$clientLastName,
						$today,
						$hash,
						$isActive
					);
					$stmt->execute();
					$msgBox = alertBox($newClientAccountActive, "<i class='fa fa-check-square'></i>", "success");
					// Clear the form of Values
					$_POST['clientEmail'] = $_POST['password1'] = $_POST['password2'] = $_POST['clientFirstName'] = $_POST['clientLastName'] = '';
					$stmt->close();
				}
			}
		}
	}

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li><a href="index.php?action=activeClients"><i class="fa fa-user"></i> <?php echo $activeClientsTabLink; ?></a></li>
		<li><a href="index.php?action=inactiveClients"><i class="fa fa-archive"></i> <?php echo $inactiveClientsTabLink; ?></a></li>
		<li class="active pull-right"><a href="" data-toggle="tab"><i class="fa fa-plus"></i> <?php echo $newClientTabLink; ?></a></li>
	</ul>
</div>

<div class="content last">
		<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<form action="" method="post">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="setActive"><?php echo $setAccountActiveField; ?></label>
					<select class="form-control" name="setActive">
						<option value="0" selected><?php echo $noBtn; ?></option>
						<option value="1"><?php echo $yesBtn; ?></option>
					</select>
					<span class="help-block"><?php echo $setAccountActiveHelp; ?></span>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="clientEmail"><?php echo $emailAddressField; ?></label>
					<input type="text" class="form-control" required="" name="clientEmail" value="<?php echo isset($_POST['clientEmail']) ? $_POST['clientEmail'] : ''; ?>" />
					<span class="help-block"><?php echo $validEmailAddressHelp; ?></span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="clientFirstName"><?php echo $newAccountFirstName; ?></label>
					<input type="text" class="form-control" required="" name="clientFirstName" value="<?php echo isset($_POST['clientFirstName']) ? $_POST['clientFirstName'] : ''; ?>" />
				</div>
			</div>
			<div class="col-md-6">
			<div class="form-group">
					<label for="clientLastName"><?php echo $newAccountLastName; ?></label>
					<input type="text" class="form-control" required="" name="clientLastName" value="<?php echo isset($_POST['clientLastName']) ? $_POST['clientLastName'] : ''; ?>" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label for="password1"><?php echo $passwordField; ?></label>
					<div class="input-group">
						<input type="password" class="form-control" required="" name="password1" id="password1" value="<?php echo isset($_POST['password1']) ? $_POST['password1'] : ''; ?>" />
						<span class="input-group-addon"><a href="" id="generate" data-toggle="tooltip" data-placement="top" title="Generate Password"><i class="fa fa-key"></i></a></span>
					</div>
					<span class="help-block">
						<a href="" id="show1" class="btn btn-warning btn-xs"><?php echo $showPlainText; ?></a>
						<a href="" id="hide1" class="btn btn-info btn-xs"><?php echo $hidePlainText; ?></a>
					</span>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label for="password2"><?php echo $repeatPasswordField; ?></label>
					<input type="password" class="form-control" required="" name="password2" id="password2" value="<?php echo isset($_POST['password2']) ? $_POST['password2'] : ''; ?>" />
					<span class="help-block">
						<a href="" id="show2" class="btn btn-warning btn-xs"><?php echo $showPlainText; ?></a>
						<a href="" id="hide2" class="btn btn-info btn-xs"><?php echo $hidePlainText; ?></a>
					</span>
				</div>
			</div>
		</div>
		<button type="input" name="submit" value="addClient" class="btn btn-success btn-icon mt20"><i class="fa fa-check-square-o"></i> <?php echo $addNewClientBtn; ?></button>
	</form>
</div>