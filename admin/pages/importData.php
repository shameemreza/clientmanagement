<?php
	$delimiter = ',';

	if (isset($_POST['submit']) && $_POST['submit'] == 'importClients') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									clients(
										clientId,
										clientEmail,
										password,
										clientFirstName,
										clientLastName,
										clientCompany,
										clientBio,
										clientAddress,
										clientPhone,
										clientCell,
										clientNotes,
										createDate,
										hash,
										isActive,
										isArchived,
										archiveDate
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]',
										'$data[5]',
										'$data[6]',
										'$data[7]',
										'$data[8]',
										'$data[9]',
										'$data[10]',
										'$data[11]',
										'$data[12]',
										'$data[13]',
										'$data[14]',
										'$data[15]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($importClientsMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importFileError, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}

	if (isset($_POST['submit']) && $_POST['submit'] == 'importProjects') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				$stmt = $mysqli->prepare("
									INSERT INTO
									clientprojects(
										projectId,
										createdBy,
										clientId,
										projectName,
										percentComplete,
										projectFee,
										projectPayments,
										projectDeatils,
										startDate,
										dueDate,
										projectNotes,
										fromRequest,
										archiveProj,
										archiveDate
									) VALUES (
										'$data[0]',
										'$data[1]',
										'$data[2]',
										'$data[3]',
										'$data[4]',
										'$data[5]',
										'$data[6]',
										'$data[7]',
										'$data[8]',
										'$data[9]',
										'$data[10]',
										'$data[11]',
										'$data[12]',
										'$data[13]'
									)
				");
				$stmt->execute();
			}

			fclose($handle);
			$msgBox = alertBox($importClientprojMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importFileError, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}

	if (isset($_POST['submit']) && $_POST['submit'] == 'importAdmins') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");
			$i = 0;

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				if($i > 0) {
					$stmt = $mysqli->prepare("
										INSERT INTO
										admins(
											adminId,
											isAdmin,
											adminEmail,
											password,
											adminFirstName,
											adminLastName,
											adminPhone,
											adminCell,
											adminAddress,
											createDate,
											isActive
										) VALUES (
											'$data[0]',
											'$data[1]',
											'$data[2]',
											'$data[3]',
											'$data[4]',
											'$data[5]',
											'$data[6]',
											'$data[7]',
											'$data[8]',
											'$data[9]',
											'$data[10]'
										)
					");
					$stmt->execute();
				}
				$i++;
			}

			fclose($handle);
			$msgBox = alertBox($importAdminDataMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importFileError, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}

	if (isset($_POST['submit']) && $_POST['submit'] == 'importPayments') {
		$fname = $_FILES['importfile']['name'];
		$chk_ext = explode(".",$fname);

		if(strtolower($chk_ext[1]) == "csv") {
			$filename = $_FILES['importfile']['tmp_name'];
			$handle = fopen($filename, "r");
			$i = 0;

			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				if($i > 0) {
					$stmt = $mysqli->prepare("
										INSERT INTO
										projectpayments(
											paymentId,
											clientId,
											enteredBy,
											projectId,
											paymentFor,
											paymentDate,
											paidBy,
											paymentAmount,
											additionalFee,
											paymentNotes
										) VALUES (
											'$data[0]',
											'$data[1]',
											'$data[2]',
											'$data[3]',
											'$data[4]',
											'$data[5]',
											'$data[6]',
											'$data[7]',
											'$data[8]',
											'$data[9]'
										)
					");
					$stmt->execute();
				}
				$i++;
			}

			fclose($handle);
			$msgBox = alertBox($importPaymentDataMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($importFileError, "<i class='fa fa-times-circle'></i>", "danger");
		}
	}

	// Check for Clients
	$clientCk = $mysqli->query("SELECT 'X' FROM clients");
	$totalClients = mysqli_num_rows($clientCk);

	// Check for Projects
	$projectCk = $mysqli->query("SELECT 'X' FROM clientprojects");
	$totalProjects = mysqli_num_rows($projectCk);

	// Check for Admins
	$adminCk = $mysqli->query("SELECT 'X' FROM admins");
	$totalAdmins = mysqli_num_rows($adminCk);

	// Check for Payments
	$paymentsCk = $mysqli->query("SELECT 'X' FROM projectpayments");
	$totalPayments = mysqli_num_rows($paymentsCk);

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
			<li><a href="index.php?action=siteSettings"><i class="fa fa-cogs"></i> <?php echo $pageNamesiteSettings; ?></a></li>
			<li class="active pull-right"><a href="index.php?action=importData"><i class="fa fa-hdd-o"></i> <?php echo $pageNameimportData; ?></a></li>
		</ul>
	</div>

	<div class="content">
		<h3><?php echo $importDataInsTitle; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<p><?php echo $importDataQuip1; ?></p>

		<div class="alertMsg default no-margin mt10">
			<i class="fa fa-info-circle"></i> <?php echo $importDataAlertMsg; ?>
		</div>

		<p class="mt10"><?php echo $importDataQuip2; ?></p>
		<p class="mt10"><?php echo $importDataQuip3; ?></p>
	</div>

	<div class="contentAlt no-margin">
		<div class="row">
			<div class="col-lg-6">
				<div class="content">
					<h4 class="bg-primary"><?php echo $importClientsText; ?></h4>
					<?php if ($totalClients < 1) { ?>
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for="file"><?php echo $selectFileField; ?></label>
								<input type="file" id="importfile" name="importfile" required="">
							</div>
							<button type="input" name="submit" value="importClients" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $importClientsText; ?></button>
						</form>
					<?php } else { ?>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> <?php echo $importClientsError; ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="content">
					<h4 class="bg-info"><?php echo $importClientProjText; ?></h4>
					<?php if ($totalProjects < 1) { ?>
						<p></p>
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for="file"><?php echo $selectFileField; ?></label>
								<input type="file" id="importfile" name="importfile" required="">
							</div>
							<button type="input" name="submit" value="importProjects" class="btn btn-info btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $importClientProjText; ?></button>
						</form>
					<?php } else { ?>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> <?php echo $importClientProjError; ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="contentAlt no-margin">
		<div class="row">
			<div class="col-lg-6">
				<div class="content last">
					<h4 class="bg-warning"><?php echo $importAdminsText; ?></h4>
					<?php if ($totalAdmins == 1) { ?>
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for="file"><?php echo $selectFileField; ?></label>
								<input type="file" id="importfile" name="importfile" required="">
							</div>
							<button type="input" name="submit" value="importAdmins" class="btn btn-warning btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $importAdminsText; ?></button>
						</form>
					<?php } else { ?>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> <?php echo $importAdminsError; ?>
						</div>
					<?php } ?>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="content last">
					<h4 class="bg-success"><?php echo $importPaymentsText; ?></h4>
					<?php if ($totalPayments < 1) { ?>
						<form action="" method="post" enctype="multipart/form-data">
							<div class="form-group">
								<label for="file"><?php echo $selectFileField; ?></label>
								<input type="file" id="importfile" name="importfile" required="">
							</div>
							<button type="input" name="submit" value="importPayments" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $importPaymentsText; ?></button>
						</form>
					<?php } else { ?>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> <?php echo $importPaymentsError; ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>