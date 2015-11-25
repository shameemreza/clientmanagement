<?php
	$clientId = $_GET['clientId'];
	$getId = 'clientId='.$clientId;
	$pagPages = '5';

	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);

	$avatarDir = $set['avatarFolder'];

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAvatar') {
		$clientAvatar = $mysqli->real_escape_string($_POST['clientAvatar']);

		$filePath = '../'.$avatarDir.$clientAvatar;
		// Delete the client's image from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Update the client record
			$clientAvatar = 'clientDefault.png';
			$stmt = $mysqli->prepare("
								UPDATE
									clients
								SET
									clientAvatar = ?
								WHERE
									clientId = ?");
			$stmt->bind_param('ss',
							   $clientAvatar,
							   $clientId);
			$stmt->execute();
			$msgBox = alertBox($clientAvatarRemovedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($clientAvatarRemoveError, "<i class='fa fa-warning'></i>", "warning");
		}
	}

	// Edit Bio
    if (isset($_POST['submit']) && $_POST['submit'] == 'profileBio') {
		$clientBio = $_POST['clientBio'];

		$stmt = $mysqli->prepare("UPDATE
									clients
								SET
									clientBio = ?
								WHERE
									clientId = ?"
		);
		$stmt->bind_param('ss',
								$clientBio,
								$clientId
		);
		$stmt->execute();
		$msgBox = alertBox($clientBioUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	// Update Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'personalInfo') {
		// Validation
		if($_POST['clientFirstName'] == "") {
            $msgBox = alertBox($firstNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['clientLastName'] == "") {
            $msgBox = alertBox($lastNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['clientPhone'] == "") {
            $msgBox = alertBox($phoneReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['clientAddress'] == "") {
            $msgBox = alertBox($mailingAddyReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$clientFirstName = $mysqli->real_escape_string($_POST['clientFirstName']);
			$clientLastName = $mysqli->real_escape_string($_POST['clientLastName']);
			$clientCompany = $mysqli->real_escape_string($_POST['clientCompany']);
			$clientPhone = encryptIt($_POST['clientPhone']);
			$clientCell = encryptIt($_POST['clientCell']);
			$clientAddress = encryptIt($_POST['clientAddress']);

			$stmt = $mysqli->prepare("UPDATE
										clients
									SET
										clientFirstName = ?,
										clientLastName = ?,
										clientCompany = ?,
										clientPhone = ?,
										clientCell = ?,
										clientAddress = ?
									WHERE
										clientId = ?"
			);
			$stmt->bind_param('sssssss',
									$clientFirstName,
									$clientLastName,
									$clientCompany,
									$clientPhone,
									$clientCell,
									$clientAddress,
									$clientId
			);
			$stmt->execute();
			$msgBox = alertBox($clientInfoUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Email
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		// Validation
		if($_POST['clientEmail'] == "") {
            $msgBox = alertBox($emailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);

			$stmt = $mysqli->prepare("UPDATE
										clients
									SET
										clientEmail = ?
									WHERE
										clientId = ?"
			);
			$stmt->bind_param('ss', $clientEmail, $clientId);
			$stmt->execute();
			$msgBox = alertBox($clientEmailUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'clientPassword') {
		// Validation
		if($_POST['password'] == '') {
			$msgBox = alertBox($passworReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($repeatpasswordHelp, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($passwordsNotMatchMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			if(isset($_POST['password']) && $_POST['password'] != "") {
				$password = encryptIt($_POST['password']);
			} else {
				$password = $_POST['passwordOld'];
			}

			$stmt = $mysqli->prepare("UPDATE
										clients
									SET
										password = ?
									WHERE
										clientId = ?"
			);
			$stmt->bind_param('ss', $password, $clientId);
			$stmt->execute();
			$msgBox = alertBox($clientPassUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Status
	if (isset($_POST['submit']) && $_POST['submit'] == 'accountStatus') {
		$openProjects = '';
		$openInvoices = '';
		$isActive = $mysqli->real_escape_string($_POST['isActive']);
		$isArchived = $mysqli->real_escape_string($_POST['isArchived']);

		if ($isArchived == '1') {
			$archiveDate = date("Y-m-d H:i:s");
		} else {
			$archiveDate = '0000-00-00 00:00:00';
		}

		// Check for Open Projects
		$check = $mysqli->query("SELECT 'X' FROM clientprojects WHERE clientId = '".$clientId."' AND archiveProj = 0");
		if ($check->num_rows) { $openProjects = 'true'; }

		// Check for Open Invoices
		$check1 = $mysqli->query("SELECT 'X' FROM invoices WHERE clientId = '".$clientId."' AND isPaid = 0");
		if ($check1->num_rows) { $openInvoices = 'true'; }

		if ($openProjects == '') {
			if ($openInvoices == '') {
				$stmt = $mysqli->prepare("UPDATE
											clients
										SET
											isActive = ?,
											isArchived = ?,
											archiveDate = ?
										WHERE
											clientId = ?"
				);
				$stmt->bind_param('ssss',
									$isActive,
									$isArchived,
									$archiveDate,
									$clientId
				);
				$stmt->execute();
				$msgBox = alertBox($clientStatusMsg1, "<i class='fa fa-check-square'></i>", "success");
				$stmt->close();
			} else {
				$msgBox = alertBox($clientStatusMsg2, "<i class='fa fa-warning'></i>", "warning");
			}
		} else {
			$msgBox = alertBox($clientStatusMsg3, "<i class='fa fa-warning'></i>", "warning");
		}
    }

	// Get Data
    $query = "SELECT
				clientId,
				clientEmail,
				password,
				clientFirstName,
				clientLastName,
				CONCAT(clientFirstName,' ',clientLastName) AS theClient,
				clientCompany,
				clientBio,
				clientAddress,
				clientPhone,
				clientCell,
				clientAvatar,
				clientNotes,
				DATE_FORMAT(lastVisited,'%M %e, %Y at %l:%i %p') AS lastVisited,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
				isActive,
				isArchived,
				DATE_FORMAT(archiveDate,'%M %d, %Y') AS archiveDate
			FROM
				clients
			WHERE clientId = ".$clientId;
    $res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data
	if ($row['clientAddress'] != '') { $clientAddress = decryptIt($row['clientAddress']); } else { $clientAddress = '';  }
	if ($row['clientPhone'] != '') { $clientPhone = decryptIt($row['clientPhone']); } else { $clientPhone = '';  }
	if ($row['clientCell'] != '') { $clientCell = decryptIt($row['clientCell']); } else { $clientCell = '';  }

	// Set some variables
	if ($row['isActive'] == '1') { $isActive = $activeText; $highlight = 'text-success'; $selActive = ''; } else { $isActive = $inactiveText; $highlight = 'text-danger'; $selActive = 'selected'; }
	if ($row['isArchived'] == '1') { $isArchived = $archivedText; $selArchived = 'selected'; } else { $isArchived = ''; $selArchived = ''; }

	// Get Project Data
	if ($isAdmin == '1') {
		// Include Pagination Class
		include('includes/getpagination.php');

		$pages = new paginator($pagPages,'p');
		// Get the number of total records
		$rows = $mysqli->query("SELECT * FROM clientprojects WHERE clientprojects.clientId = ".$clientId." AND clientprojects.archiveProj = 0");
		$total = mysqli_num_rows($rows);
		// Pass the number of total records
		$pages->set_total($total);
	
		// Get All Projects for this Client
		$stmt  = "SELECT
						clientprojects.projectId,
						clientprojects.createdBy,
						clientprojects.clientId,
						clientprojects.projectName,
						clientprojects.percentComplete,
						DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
						assignedprojects.assignedTo,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						clientprojects
						LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
						LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
					WHERE
						clientprojects.clientId = ".$clientId." AND
						clientprojects.archiveProj = 0
					ORDER BY clientprojects.projectId
		".$pages->get_limit();
		$results = mysqli_query($mysqli, $stmt) or die('-2' . mysqli_error());
	} else {
		// Include Pagination Class
		include('includes/getpagination.php');

		$pages = new paginator($pagPages,'p');
		// Get the number of total records
		$rows = $mysqli->query("SELECT
									*
								FROM
									clientprojects
									LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
									LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
								WHERE
									clientprojects.clientId = ".$clientId." AND
									clientprojects.archiveProj = 0 AND
									assignedprojects.assignedTo = ".$adminId);
		$total = mysqli_num_rows($rows);
		// Pass the number of total records
		$pages->set_total($total);

		// Get Projects Assigned to the logged in Manager
		$stmt  = "SELECT
						clientprojects.projectId,
						clientprojects.clientId,
						clientprojects.projectName,
						clientprojects.percentComplete,
						DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
						assignedprojects.assignedTo,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						clientprojects
						LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
						LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
					WHERE
						clientprojects.clientId = ".$clientId." AND
						clientprojects.archiveProj = 0 AND
						assignedprojects.assignedTo = ".$adminId."
					ORDER BY clientprojects.projectId
		".$pages->get_limit();
		$results = mysqli_query($mysqli, $stmt) or die('-3' . mysqli_error());
	}

	include 'includes/navigation.php';
?>
<div class="contentAlt no-margin">
	<div class="row">
		<div class="col-md-8">
			<div class="content">
				<?php if ($msgBox) { echo $msgBox; } ?>
				<div class="well well-xs comments">
					<img src="<?php echo '../'.$avatarDir.$row['clientAvatar']; ?>" alt="<?php echo clean($row['theClient']); ?>" class="avatarProfile" data-toggle="tooltip" title="<?php echo clean($row['theClient']); ?>" />
					<h4><strong><?php echo clean($row['theClient']); ?></strong></h4>
					<p class="clearfix">
						<?php echo clean($row['clientEmail']); ?><br />
						<?php echo clean($row['clientCompany']); ?>
					</p>
				</div>
			</div>

			<?php if ($row['clientBio'] != '') { ?>
				<div class="content">
					<blockquote>
						<p><i class="fa fa-quote-left icon-quote"></i> <?php echo nl2br(clean($row['clientBio'])); ?> <i class="fa fa-quote-right icon-quote"></i></p>
					</blockquote>
				</div>
			<?php } ?>

			<div class="content">
				<table class="infoTable">
					<tbody>
						<tr>
							<td class="infoKey"><i class="fa fa-info-circle"></i> <?php echo $currentStatusText; ?>:</td>
							<td class="infoVal"><strong class="<?php echo $highlight; ?>"><?php echo $isArchived.' '.$isActive; ?></strong></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $joinDateText; ?>:</td>
							<td class="infoVal"><?php echo $row['createDate']; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $lastLoginText; ?>:</td>
							<td class="infoVal"><?php echo $row['lastVisited']; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-phone"></i> <?php echo $phoneText; ?>:</td>
							<td class="infoVal"><?php echo $clientPhone; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-mobile"></i> <?php echo $altPhoneText; ?>:</td>
							<td class="infoVal"><?php echo $clientCell; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-building"></i> <?php echo $mailingAddressText; ?>:</td>
							<td class="infoVal"><?php echo nl2br(clean($clientAddress)); ?></td>
						</tr>
					</tbody>
				</table>
			</div>

			<div class="content last">
				<h3><?php echo $openProjNavLink; ?></h3>
				<?php if(mysqli_num_rows($results) < 1) { ?>
					<div class="alertMsg default">
						<i class="fa fa-minus-square-o"></i>
						<?php
							if ($isAdmin == '0') {
								echo $noOpenProj1;
							} else {
								echo $noOpenProj2;
							}
						?>
					</div>
				<?php } else { ?>
					<table class="rwd-table">
						<tbody>
							<tr class="primary">
								<th><?php echo $projectText; ?></th>
								<th><?php echo $assignedToText; ?></th>
								<th><?php echo $percentCompleteText; ?></th>
								<th><?php echo $dateDueText; ?></th>
							</tr>
							<?php while ($rows = mysqli_fetch_assoc($results)) { ?>
								<tr>
									<td data-th="<?php echo $projectText; ?>">
										<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProjectText; ?>">
											<a href="index.php?action=viewProject&projectId=<?php echo $rows['projectId']; ?>"><?php echo clean($rows['projectName']); ?></a>
										</span>
									</td>
									<td data-th="<?php echo $assignedToText; ?>"><?php echo clean($rows['theAdmin']); ?></td>
									<td data-th="<?php echo $percentCompleteText; ?>"><?php echo $rows['percentComplete']; ?>%</td>
									<td data-th="<?php echo $dateDueText; ?>"><?php echo $rows['dueDate']; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php
				}
				if ($total > $pagPages) {
					echo $pages->page_links();
				}
				?>
			</div>
		</div>
		<div class="col-md-4">
			<div class="contentAlt">
				<div class="list-group">
					<li class="list-group-item default"><?php echo $updClientAcctLink; ?></li>
					<a data-toggle="modal" href="#profileAvatar" class="list-group-item"><?php echo $clientAvatarLink; ?></a>
					<a data-toggle="modal" href="#profileBio" class="list-group-item"><?php echo $editClientBioLink; ?></a>
					<a data-toggle="modal" href="#personalInfo" class="list-group-item"><?php echo $updateClientInfoLink; ?></a>
					<a data-toggle="modal" href="#updateEmail" class="list-group-item"><?php echo $editClientEmailLink; ?></a>
					<a data-toggle="modal" href="#clientPassword" class="list-group-item"><?php echo $changeClientPassLink; ?></a>
					<a data-toggle="modal" href="#accountStatus" class="list-group-item"><?php echo $changeClientStatusLink; ?></a>
				</div>
			</div>

			<div class="content last">
				<p><?php echo $clientAccountQuip; ?></p>
			</div>

			<div class="alertMsg success mt20">
				<?php echo $clientAccountQuip2; ?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="profileAvatar" tabindex="-1" role="dialog" aria-labelledby="profileAvatar" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header modal-primary">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $removeClientAvatarModal; ?></h4>
			</div>

			<?php if ($row['clientAvatar'] != 'clientDefault.png') { ?>
				<div class="modal-body">
					<img alt="" src="<?php echo '../'.$avatarDir.$row['clientAvatar']; ?>" class="avatar" />
					<p><?php echo $removeClientAvatarQuip1; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-ban"></i> <?php echo $removeClientAvatarBtn; ?></a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } else { ?>
				<div class="modal-body">
					<p class="lead"><?php echo $removeClientAvatarQuip2; ?></p>
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<div class="modal fade" id="deleteAvatar" tabindex="-1" role="dialog" aria-labelledby="deleteAvatar" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $removeClientAvatarConf.' '.clean($row['theClient']); ?>?</p>
				</div>
				<div class="modal-footer">
					<input name="clientAvatar" type="hidden" value="<?php echo $row['clientAvatar']; ?>" />
					<button type="input" name="submit" value="deleteAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="profileBio" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $editClientBioLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="clientBio"><?php echo $clientBioField; ?></label>
						<textarea class="form-control" required="" name="clientBio" rows="4"><?php echo clean($row['clientBio']); ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="profileBio" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="personalInfo" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updateClientInfoLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="clientFirstName"><?php echo $newAccountFirstName; ?></label>
								<input type="text" class="form-control" required="" name="clientFirstName" value="<?php echo clean($row['clientFirstName']); ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="clientLastName"><?php echo $newAccountLastName; ?></label>
								<input type="text" class="form-control" required="" name="clientLastName" value="<?php echo clean($row['clientLastName']); ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="clientCompany"><?php echo $companyText; ?></label>
						<input type="text" class="form-control" name="clientCompany" value="<?php echo clean($row['clientCompany']); ?>" />
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="clientPhone"><?php echo $phoneText; ?></label>
								<input type="text" class="form-control" required="" name="clientPhone" value="<?php echo $clientPhone; ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="clientCell"><?php echo $altPhoneText; ?></label>
								<input type="text" class="form-control" name="clientCell" value="<?php echo $clientCell; ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="clientAddress"><?php echo $mailingAddressText; ?></label>
						<textarea class="form-control" name="clientAddress" required="" rows="3"><?php echo $clientAddress; ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="personalInfo" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="updateEmail" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $editClientEmailLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="clientEmail"><?php echo $emailAddressField; ?></label>
						<input type="text" class="form-control" name="clientEmail" required="" value="<?php echo $row['clientEmail']; ?>" />
						<span class="help-block"><?php echo $validEmailAddyHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="clientPassword" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $changeClientPassLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="password"><?php echo $newPasswordField; ?></label>
								<input type="text" class="form-control" name="password" required="" value="" />
								<span class="help-block"><?php echo $newPasswordFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="password_r"><?php echo $confNewPasswordField; ?></label>
								<input type="text" class="form-control" name="password_r" required="" value="" />
								<span class="help-block"><?php echo $confNewPasswordFieldHelp; ?></span>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="passwordOld" value="<?php echo $row['password']; ?>" />
					<button type="input" name="submit" value="clientPassword" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="accountStatus" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $changeClientStatusLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<p><?php echo $changeClientStatusQuip; ?></p>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="isActive"><?php echo $clientStatusField; ?></label>
								<select class="form-control" name="isActive">
									<option value="1"><?php echo $activeText; ?></option>
									<option value="0" <?php echo $selActive; ?>><?php echo $inactiveText; ?></option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="isArchived"><?php echo $archiveClientField; ?></label>
								<select class="form-control" name="isArchived">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1" <?php echo $selArchived; ?>><?php echo $yesBtn; ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="accountStatus" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>