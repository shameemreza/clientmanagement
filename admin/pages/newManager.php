<?php
	$jsFile = 'newClient';

	// Add New Client Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'addManager') {
        // Validation
        if($_POST['adminEmail'] == "") {
            $msgBox = alertBox($emailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['password1'] == "") {
            $msgBox = alertBox($passworReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password1'] != $_POST['password2']) {
			$msgBox = alertBox($passwordsNotMatchMsg, "<i class='fa fa-warning'></i>", "warning");
        } else if($_POST['adminFirstName'] == "") {
            $msgBox = alertBox($firstNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adminLastName'] == "") {
            $msgBox = alertBox($lastNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Set some variables
			$setActive = $mysqli->real_escape_string($_POST['setActive']);
			$dupEmail = '';
			$newEmail = $mysqli->real_escape_string($_POST['adminEmail']);
			$adminFirstName = $mysqli->real_escape_string($_POST['adminFirstName']);
			$adminLastName = $mysqli->real_escape_string($_POST['adminLastName']);

			// Check for Duplicate email
			$check = $mysqli->query("SELECT 'X' FROM admins WHERE adminEmail = '".$newEmail."'");
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
											admins(
												adminEmail,
												password,
												adminFirstName,
												adminLastName,
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
						$adminFirstName,
						$adminLastName,
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

					$subject = $newMngrEmailSubject1.' '.$siteName.' '.$newMngrEmailSubject2;

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>'.$newMngrEmailText1.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$newMngrEmailText2.' '.$newPass.'</p>';
					$message .= '<p>'.$newMngrEmailText3.$installUrl.$newMngrEmailText4.$newEmail.$newMngrEmailText5.$hash.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$newMngrEmailText6.'</p>';
					$message .= '<p>'.$loginURL1.'</p>';
					$message .= '<p>'.$emailThankYou.'</p>';
					$message .= '</body></html>';

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($adminEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($newMngrEmailSent, "<i class='fa fa-check-square'></i>", "success");
						// Clear the form of Values
						$_POST['adminEmail'] = $_POST['password1'] = $_POST['password2'] = $_POST['adminFirstName'] = $_POST['adminLastName'] = '';
					} else {
						$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
					}
					$stmt->close();
				} else {
					// Create the new account and set it to Active
					$hash = md5(rand(0,1000));
					$isActive = '1';
					$createDate = date("Y-m-d H:i:s");
					$password = encryptIt($_POST['password1']);

					$stmt = $mysqli->prepare("
										INSERT INTO
											admins(
												adminEmail,
												password,
												adminFirstName,
												adminLastName,
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
						$adminFirstName,
						$adminLastName,
						$createDate,
						$hash,
						$isActive
					);
					$stmt->execute();
					$msgBox = alertBox($newMngrActive, "<i class='fa fa-check-square'></i>", "success");
					// Clear the form of Values
					$_POST['adminEmail'] = $_POST['password1'] = $_POST['password2'] = $_POST['adminFirstName'] = $_POST['adminLastName'] = '';
					$stmt->close();
				}
			}
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
	<div class="contentAlt">
		<ul class="nav nav-tabs">
			<li><a href="index.php?action=activeManagers"><i class="fa fa-user"></i> <?php echo $activeManagersTabLink; ?></a></li>
			<li><a href="index.php?action=inactiveManagers"><i class="fa fa-archive"></i> <?php echo $inactiveManagersTabLink; ?></a></li>
			<li class="active pull-right"><a href="" data-toggle="tab"><i class="fa fa-plus"></i> <?php echo $newManagerTabLink; ?></a></li>
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
						<span class="help-block"><?php echo $setMngrActiveQuip; ?></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="adminEmail"><?php echo $emailAddressField; ?></label>
						<input type="text" class="form-control" required="" name="adminEmail" value="<?php echo isset($_POST['adminEmail']) ? $_POST['adminEmail'] : ''; ?>" />
						<span class="help-block"><?php echo $validEmailAddressHelp; ?></span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="adminFirstName"><?php echo $newAccountFirstName; ?></label>
						<input type="text" class="form-control" required="" name="adminFirstName" value="<?php echo isset($_POST['adminFirstName']) ? $_POST['adminFirstName'] : ''; ?>" />
					</div>
				</div>
				<div class="col-md-6">
				<div class="form-group">
						<label for="adminLastName"><?php echo $newAccountLastName; ?></label>
						<input type="text" class="form-control" required="" name="adminLastName" value="<?php echo isset($_POST['adminLastName']) ? $_POST['adminLastName'] : ''; ?>" />
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
			<button type="input" name="submit" value="addManager" class="btn btn-success btn-icon mt20"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
		</form>
	</div>
<?php } ?>