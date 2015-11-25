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

		$activeAccount = '';
		$nowActive = '';

		if((isset($_GET['clientEmail']) && !empty($_GET['clientEmail'])) && (isset($_GET['hash']) && !empty($_GET['hash']))) {
			// Set some variables
			$clientEmail = $mysqli->real_escape_string($_GET['clientEmail']);
			$hash = $mysqli->real_escape_string($_GET['hash']);

			// Check to see if there is an account that matches the link
			$check1 = $mysqli->query("SELECT
										clientEmail,
										hash,
										isActive
									FROM
										clients
									WHERE
										clientEmail = '".$clientEmail."' AND
										hash = '".$hash."' AND
										isActive = 0
			");
			$match = mysqli_num_rows($check1);
			
			// Check if account has all ready been activated
			$check2 = $mysqli->query("SELECT 'X' FROM clients WHERE clientEmail = '".$clientEmail."' AND hash = '".$hash."' AND isActive = 1");
			if ($check2->num_rows) {
				$activeAccount = 'true';
			}

			// Match found, update the Client's account to active
			if ($match > 0) {
				$isActive = '1';

				$stmt = $mysqli->prepare("
									UPDATE
										clients
									SET
										isActive = ?
									WHERE
										clientEmail = ?");
				$stmt->bind_param('ss',
								   $isActive,
								   $clientEmail);
				$stmt->execute();
				$nowActive = 'true';
				$stmt->close();
			}
		}
?>
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title><?php echo $set['siteName']; ?> &middot; <?php echo $activatePageTitle; ?></title>
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
						<div class="col-md-2"></div>
						<div class="col-md-8">
							<div class="loginCont">
								<p class="logo"><img alt="clientmanagement" src="images/login_logo.png" /></p>
								<div class='login'>
									<?php
										// The account has been activated - show a Signin button
										if ($nowActive != '') {
									?>
											<h4><?php echo $activateMsg1; ?></h4>
											<div class="alertMsg success no-margin">
												<i class="fa fa-check"></i> <?php echo $activateMsg2; ?>
											</div>
											<p><a href="login.php" class="btn btn-default btn-block btn-icon"><i class="fa fa-sign-in"></i> <?php echo $activateLoginBtn; ?></a></p>
									<?php
										// An account match was found and has all ready been activated
										} else if ($activeAccount != '') {
									?>
											<h4><?php echo $activateMsg3; ?></h4>
											<div class="alertMsg success no-margin">
												<i class="fa fa-check"></i> <?php echo $activateMsg2; ?>
											</div>
											<p><a href="login.php" class="btn btn-default btn-block btn-icon"><i class="fa fa-sign-in"></i> <?php echo $activateLoginBtn1; ?></a></p>
									<?php
										// An account match was not found/or the
										// Client tried to directly access this page
										} else {
									?>
											<h4><?php echo $activateMsg4; ?></h4>
											<div class="alertMsg danger no-margin">
												<i class="fa fa-times-circle"></i> <?php echo $activateMsg5; ?>
											</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="col-md-2"></div>
					</div>
				</div>
			</div>

			<script type="text/javascript" src="js/jquery.js"></script>
			<script type="text/javascript" src="js/bootstrap.js"></script>
			<script type="text/javascript" src="js/slimscroll.js"></script>
			<script type="text/javascript" src="js/custom.js"></script>

	</body>
	</html>
<?php } ?>
