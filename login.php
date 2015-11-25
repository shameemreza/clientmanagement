<?php
	// Check if install.php is present
	if(is_dir('install')) {
		header("Location: install/install.php");
	} else {
		// Access DB Info
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'en':		include ('language/en.php');		break;
		}

		// Include Functions
		include('includes/functions.php');

		$msgBox = '';
		$isReset = '';

		// User Log In Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'signIn') {
			if($_POST['clientEmail'] == '') {
				$msgBox = alertBox($emailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password'] == '') {
				$msgBox = alertBox($passworReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Check if the User account has been activated
				$clientEmail = (isset($_POST['clientEmail'])) ? $mysqli->real_escape_string($_POST['clientEmail']) : '';
				$check = $mysqli->query("SELECT isActive FROM clients WHERE clientEmail = '".$clientEmail."'");
				$row = mysqli_fetch_assoc($check);

				// If the account is active - allow the login
				if ($row['isActive'] == '1') {
					$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);
					$password = encryptIt($_POST['password']);

					if($stmt = $mysqli -> prepare("
											SELECT
												clientId,
												clientEmail,
												clientFirstName,
												clientLastName,
												clientCompany
											FROM
												clients
											WHERE
												clientEmail = ? AND password = ?
					"))	{
						$stmt -> bind_param("ss",
											$clientEmail,
											$password
						);
						$stmt -> execute();
						$stmt -> bind_result(
									$clientId,
									$clientEmail,
									$clientFirstName,
									$clientLastName,
									$clientCompany
						);
						$stmt -> fetch();
						$stmt -> close();

						if (!empty($clientId)) {
							session_start();
								$_SESSION["clientId"] 			= $clientId;
								$_SESSION["clientEmail"] 		= $clientEmail;
								$_SESSION["clientFirstName"] 	= $clientFirstName;
								$_SESSION["clientLastName"] 	= $clientLastName;
								$_SESSION["clientCompany"] 		= $clientCompany;
							header('Location: index.php');
						} else {
							$msgBox = alertBox($loginFailedMsg, "<i class='fa fa-times-circle'></i>", "danger");
						}
					}

					// Update Last Visited Date for User
					$lastVisited = date("Y-m-d H:i:s");
					$sqlStmt = $mysqli->prepare("
											UPDATE
												clients
											SET
												lastVisited = ?
											WHERE
												clientId = ?
					");
					$sqlStmt->bind_param('ss',
									   $lastVisited,
									   $clientId
					);
					$sqlStmt->execute();
					$sqlStmt->close();

				} else if ($row['isActive'] == '0') {
					// If the account is not active, show a message
					$msgBox = alertBox($inactiveAccountMsg, "<i class='fa fa-warning'></i>", "warning");
				} else {
					// No account found
					$msgBox = alertBox($accountNotFoundMsg, "<i class='fa fa-times-circle'></i>", "danger");
				}
			}
		}

		// Reset Account Password Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'resetPass') {
			// Set the email address
			$theEmail = (isset($_POST['theEmail'])) ? $mysqli->real_escape_string($_POST['theEmail']) : '';

			// Validation
			if ($_POST['theEmail'] == "") {
				$msgBox = alertBox($emailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				$query = "SELECT clientEmail FROM clients WHERE clientEmail = ?";
				$stmt = $mysqli->prepare($query);
				$stmt->bind_param("s",$theEmail);
				$stmt->execute();
				$stmt->bind_result($clientEmail);
				$stmt->store_result();
				$numrows = $stmt->num_rows();

				if ($numrows == 1){
					// Generate a RANDOM Hash for a password
					$randomPassword = uniqid(rand());

					// Take the first 8 digits and use them as the password we intend to email the user
					$emailPassword = substr($randomPassword, 0, 8);

					// Encrypt $emailPassword for the database
					$newpassword = encryptIt($emailPassword);

					//update password in db
					$updatesql = "UPDATE clients SET password = ? WHERE clientEmail = ?";
					$update = $mysqli->prepare($updatesql);
					$update->bind_param("ss",
											$newpassword,
											$theEmail
										);
					$update->execute();

					// Send out the email in HTML
					$installUrl 	= $set['installUrl'];
					$siteName 		= $set['siteName'];
					$businessEmail = $set['businessEmail'];

					$subject = 'Your '.$siteName.' Password has been Reset';

					// -------------------------------
					// ---- START Edit Email Text ----
					// -------------------------------
					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>Your temporary password is:</p>';
					$message .= '<hr>';
					$message .= '<p>'.$emailPassword.'</p>';
					$message .= '<hr>';
					$message .= '<p>Please take the time to change your password to something you can easily remember. You can change your password on your My Profile page after logging into your account. There you can update your password, as well as your account details.</p>';
					$message .= '<p>You can log into your account with your email address and new password at: '.$installUrl.'</p>';
					$message .= '<p>Thank you,<br>'.$siteName.'</p>';
					$message .= '</body></html>';
					// -----------------------------
					// ---- END Edit Email Text ----
					// -----------------------------

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($theEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($passwordResetMsg, "<i class='fa fa-check-square'></i>", "success");
						$isReset = 'true';
						$stmt->close();
					}
				} else {
					// No account found
					$msgBox = alertBox($accountNotFoundMsg, "<i class='fa fa-warning'></i>", "warning");
				}
			}
		}

		// Create a New Account Form
		if (isset($_POST['submit']) && $_POST['submit'] == 'createAccount') {
			// User Validations
			if($_POST['newEmail'] == '') {
				$msgBox = alertBox($emailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password1'] == '') {
				$msgBox = alertBox($passworReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['password1'] != $_POST['password2']) {
				$msgBox = alertBox($passwordsNotMatchMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['clientFirstName'] == '') {
				$msgBox = alertBox($firstNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['clientLastName'] == '') {
				$msgBox = alertBox($lastNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			// Black Hole Trap to help reduce bot registrations
			} else if($_POST['isEmpty'] != '') {
				$msgBox = alertBox($newAccountCreateError, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				// Set some variables
				$dupEmail = '';
				$newEmail = $mysqli->real_escape_string($_POST['newEmail']);

				// Check for Duplicate email
				$check = $mysqli->query("SELECT 'X' FROM clients WHERE clientEmail = '".$newEmail."'");
				if ($check->num_rows) {
					$dupEmail = 'true';
				}

				// If duplicates are found
				if ($dupEmail != '') {
					$msgBox = alertBox($emailInUseMsg, "<i class='fa fa-times-circle'></i>", "danger");
				} else {
					// Create the new account
					$password = encryptIt($_POST['password1']);
					$clientFirstName = $mysqli->real_escape_string($_POST['clientFirstName']);
					$clientLastName = $mysqli->real_escape_string($_POST['clientLastName']);
					$createDate = date("Y-m-d H:i:s");
					$hash = md5(rand(0,1000));
					$isActive = '0';

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

					$subject = 'Your '.$siteName.' Account has been created';

					// -------------------------------
					// ---- START Edit Email Text ----
					// -------------------------------
					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<p>Your new Account details:</p>';
					$message .= '<hr>';
					$message .= '<p>Username: Your email address<br>Password: '.$newPass.'</p>';
					$message .= '<p>You must activate your account before you will be able to log in. Please click (or copy/paste) the following link to activate your account:<br>'.$installUrl.'activate.php?clientEmail='.$newEmail.'&hash='.$hash.'</p>';
					$message .= '<hr>';
					$message .= '<p>Once you have activated your new account and logged in, please take the time to update your account profile details.</p>';
					$message .= '<p>You can log in to your account at '.$installUrl.'</p>';
					$message .= '<p>Thank you,<br>'.$siteName.'</p>';
					$message .= '</body></html>';
					// -------------------------------
					// ---- END Edit Email Text ----
					// -------------------------------

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($newEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($newAccountCreatedMsg, "<i class='fa fa-check-square'></i>", "success");
						// Clear the Form of values
						$_POST['newEmail'] = $_POST['clientFirstName'] = $_POST['clientLastName'] = '';
					}
					$stmt->close();
				}
			}
		}
?>
	<!DOCTYPE html>
	<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo $set['siteName']; ?> &middot; <?php echo $pageHeadTitle; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

		<link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Raleway:200,300,400,700'>
		<link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,300italic,400italic,600italic'>

		<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/custom.css">
		<link rel="stylesheet" type="text/css" href="css/clientmanagement.css">
		<link rel="stylesheet" type="text/css" href="css/font-awesome.css">

		<!--[if lt IE 9]>
			<script src="js/html5shiv.js"></script>
			<script src="js/respond.js"></script>
		<![endif]-->
    </head>

	<body>
		<div id="wrap">
			<div class="container">
				<div class="row">
					<div class="col-md-3"></div>
					<div class="col-md-6">
						<div class="loginCont">
							<p class="logo"><img alt="clientmanage" src="images/login_logo.png" /></p>
							<div class='login text-center'>
								<h2><?php echo $loginTitle; ?></h2>
								<?php if ($msgBox) { echo $msgBox; } ?>
								<form action="" method="post">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
										<input type="email" class="form-control" required="" placeholder="<?php echo $emailAddressField; ?>" name="clientEmail" />
									</div>
									<br>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-lock"></i></span>
										<input type="password" class="form-control" required="" placeholder="<?php echo $passwordField; ?>" name="password" />
									</div>
									<small class="pull-right"><a data-toggle="modal" href="#resetPassword"><i class="fa fa-unlock"></i> <?php echo $resetPasswordBtn; ?></a></small>
									<button type="input" name="submit" value="signIn" class="btn btn-primary btn-icon"><i class="fa fa-sign-in"></i> <?php echo $signInBtn; ?></button>
								</form>
								<?php if ($set['allowRegistrations'] == '1') { ?>
									<p><?php echo $dontHaveAccountLinkQuip; ?> <a data-toggle="modal" href="#newAccount"><?php echo $createAccountLink; ?></a></p>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="col-md-3"></div>
				</div>
			</div>

			<div class="modal fade" id="resetPassword" tabindex="-1" role="dialog" aria-labelledby="resetPassword" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title"><?php echo $resetPasswordBtn; ?></h4>
						</div>
						<?php if ($isReset == '') { ?>
							<form action="" method="post">
								<div class="modal-body">
									<div class="form-group">
										<label for="theEmail"><?php echo $emailAddressField; ?></label>
										<input type="email" class="form-control" required="" name="theEmail" id="theEmail" value="" />
										<span class="help-block"><?php echo $emailAddressHelp; ?></span>
									</div>
								</div>
								<div class="modal-footer">
									<button type="input" name="submit" value="resetPass" class="btn btn-success btn-icon"><i class="fa fa-unlock"></i> <?php echo $resetPasswordBtn; ?></button>
									<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						<?php } else { ?>
							<div class="modal-body">
								<p class="lead"><?php echo $passwordResetTitle; ?></p>
								<p><?php echo $passwordResetQuip; ?></p>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>

			<?php if ($set['allowRegistrations'] == '1') { ?>
				<div class="modal fade" id="newAccount" tabindex="-1" role="dialog" aria-labelledby="newAccount" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
								<h4 class="modal-title"><?php echo $createAccountTitle; ?></h4>
							</div>
							<form action="" method="post">
								<div class="modal-body">
									<div class="form-group">
										<label for="newEmail"><?php echo $emailAddressField; ?></label>
										<input type="email" class="form-control" required="" name="newEmail" value="<?php echo isset($_POST['newEmail']) ? $_POST['newEmail'] : ''; ?>" />
										<span class="help-block"><?php echo $validEmailAddressHelp; ?></span>
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
												<input type="password" class="form-control" required="" name="password1" />
												<span class="help-block"><?php echo $newAccountPassword; ?></span>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label for="password2"><?php echo $repeatPasswordField; ?></label>
												<input type="password" class="form-control" required="" name="password2" />
												<span class="help-block"><?php echo $repeatpasswordHelp; ?></span>
											</div>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<input name="isEmpty" id="isEmpty" value="" type="hidden" />
									<button type="input" name="submit" value="createAccount" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $createAccountBtn; ?></button>
									<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
								</div>
							</form>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>

		<script type="text/javascript" src="js/jquery.js"></script>
		<script type="text/javascript" src="js/bootstrap.js"></script>
		<script type="text/javascript" src="js/slimscroll.js"></script>
		<script type="text/javascript" src="js/custom.js"></script>

	</body>
	</html>
<?php } ?>