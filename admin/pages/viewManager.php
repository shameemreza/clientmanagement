<?php
	$aId = $_GET['adminId'];

	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);

	$avatarDir = $set['avatarFolder'];

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAvatar') {
		$adminAvatar = $mysqli->real_escape_string($_POST['adminAvatar']);

		$filePath = '../'.$avatarDir.$adminAvatar;
		// Delete the Manager's image from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Update the Manager record
			$adminAvatar = 'adminDefault.png';
			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminAvatar = ?
								WHERE
									adminId = ?");
			$stmt->bind_param('ss',
							   $adminAvatar,
							   $aId);
			$stmt->execute();
			$msgBox = alertBox($mngrAvatarRemovedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($mngrAvatarRemoveError, "<i class='fa fa-warning'></i>", "warning");
		}
	}

	// Edit Bio
    if (isset($_POST['submit']) && $_POST['submit'] == 'profileBio') {
		$adminBio = $_POST['adminBio'];

		$stmt = $mysqli->prepare("UPDATE
									admins
								SET
									adminBio = ?
								WHERE
									adminId = ?"
		);
		$stmt->bind_param('ss',
								$adminBio,
								$aId
		);
		$stmt->execute();
		$msgBox = alertBox($managerBioUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	// Update Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'personalInfo') {
		// Validation
		if($_POST['adminFirstName'] == "") {
            $msgBox = alertBox($firstNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adminLastName'] == "") {
            $msgBox = alertBox($lastNameReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adminPhone'] == "") {
            $msgBox = alertBox($phoneReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['adminAddress'] == "") {
            $msgBox = alertBox($mailingAddyReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$adminFirstName = $mysqli->real_escape_string($_POST['adminFirstName']);
			$adminLastName = $mysqli->real_escape_string($_POST['adminLastName']);
			$adminPhone = encryptIt($_POST['adminPhone']);
			$adminCell = encryptIt($_POST['adminCell']);
			$adminAddress = encryptIt($_POST['adminAddress']);

			$stmt = $mysqli->prepare("UPDATE
										admins
									SET
										adminFirstName = ?,
										adminLastName = ?,
										adminPhone = ?,
										adminCell = ?,
										adminAddress = ?
									WHERE
										adminId = ?"
			);
			$stmt->bind_param('ssssss',
									$adminFirstName,
									$adminLastName,
									$adminPhone,
									$adminCell,
									$adminAddress,
									$aId
			);
			$stmt->execute();
			$msgBox = alertBox($mngrInfoUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Email
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateEmail') {
		// Validation
		if($_POST['adminEmail'] == "") {
            $msgBox = alertBox($emailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$adminEmail = $mysqli->real_escape_string($_POST['adminEmail']);

			$stmt = $mysqli->prepare("UPDATE
										admins
									SET
										adminEmail = ?
									WHERE
										adminId = ?"
			);
			$stmt->bind_param('ss', $adminEmail, $aId);
			$stmt->execute();
			$msgBox = alertBox($mngrEmailUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'accountPassword') {
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
										admins
									SET
										password = ?
									WHERE
										adminId = ?"
			);
			$stmt->bind_param('ss', $password, $aId);
			$stmt->execute();
			$msgBox = alertBox($mngrPasswordUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Status
	if (isset($_POST['submit']) && $_POST['submit'] == 'accountStatus') {
		if ($adminId != $aId) {
			$projectsAssigned = '';
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$isArchived = $mysqli->real_escape_string($_POST['isArchived']);

			if ($isArchived == '1') {
				$archiveDate = date("Y-m-d H:i:s");
			} else {
				$archiveDate = '0000-00-00 00:00:00';
			}

			// Check for Open Projects
			$check = $mysqli->query("SELECT
										'X'
									FROM
										clientprojects
										LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
										LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
									WHERE
										assignedprojects.assignedTo = '".$aId."' AND clientprojects.archiveProj = 0");
			if ($check->num_rows) { $projectsAssigned = 'true'; }

			if ($projectsAssigned == '') {
				$stmt = $mysqli->prepare("UPDATE
											admins
										SET
											isActive = ?,
											isArchived = ?,
											archiveDate = ?
										WHERE
											adminId = ?"
				);
				$stmt->bind_param('ssss',
									$isActive,
									$isArchived,
									$archiveDate,
									$aId
				);
				$stmt->execute();
				$msgBox = alertBox($mngrStatusUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
				$stmt->close();
			} else {
				$msgBox = alertBox($mngrStatusError1, "<i class='fa fa-warning'></i>", "warning");
			}
		} else {
			$msgBox = alertBox($mngrStatusError2, "<i class='fa fa-warning'></i>", "warning");
		}
    }

	// Update Manager Type
	if (isset($_POST['submit']) && $_POST['submit'] == 'managerType') {
		$superuser = $mysqli->real_escape_string($_POST['superuser']);
		$adminRole = $mysqli->real_escape_string($_POST['adminRole']);

		$stmt = $mysqli->prepare("UPDATE
									admins
								SET
									isAdmin = ?,
									adminRole = ?
								WHERE
									adminId = ?"
		);
		$stmt->bind_param('sss',
							$superuser,
							$adminRole,
							$aId
		);
		$stmt->execute();
		$msgBox = alertBox($mngrTypeUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
	}

	// Get Data
    $query = "SELECT
				adminId,
				adminEmail,
				password,
				adminFirstName,
				adminLastName,
				CONCAT(adminFirstName,' ',adminLastName) AS theAdmin,
				adminBio,
				adminAddress,
				adminPhone,
				adminCell,
				adminAvatar,
				adminNotes,
				DATE_FORMAT(lastVisited,'%M %e, %Y at %l:%i %p') AS lastVisited,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
				isAdmin,
				adminRole,
				isActive,
				isArchived,
				DATE_FORMAT(archiveDate,'%M %d, %Y') AS archiveDate
			FROM
				admins
			WHERE adminId = ".$aId;
    $res = mysqli_query($mysqli, $query) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data
	if ($row['adminAddress'] != '') { $adminAddress = decryptIt($row['adminAddress']); } else { $adminAddress = '';  }
	if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = '';  }
	if ($row['adminCell'] != '') { $adminCell = decryptIt($row['adminCell']); } else { $adminCell = '';  }

	// Set some variables
	if ($row['isActive'] == '1') { $isActive = $activeText; $highlight = 'text-success'; $selActive = ''; } else { $isActive = $inactiveText; $highlight = 'text-danger'; $selActive = 'selected'; }
	if ($row['isArchived'] == '1') { $isArchived = $archivedText; $selArchived = 'selected'; } else { $isArchived = ''; $selArchived = ''; }
	if ($row['isAdmin'] == '1') { $isaAdmin = 'selected'; } else { $isaAdmin = ''; }

	// Get All Assigned Projects for this Manager
	$stmt = "SELECT
				clientprojects.projectId,
				clientprojects.createdBy,
				clientprojects.clientId,
				clientprojects.projectName,
				clientprojects.percentComplete,
				DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
				assignedprojects.assignedTo,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
			FROM
				clientprojects
				LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE
				assignedprojects.assignedTo = ".$aId." AND
				clientprojects.archiveProj = 0
			ORDER BY clientprojects.projectId
	";
	$results = mysqli_query($mysqli, $stmt) or die('-2' . mysqli_error());

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
	<div class="contentAlt no-margin">
		<div class="row">
			<div class="col-md-8">
				<div class="content">
					<?php if ($msgBox) { echo $msgBox; } ?>

					<div class="well well-xs comments">
						<img src="<?php echo '../'.$avatarDir.$row['adminAvatar']; ?>" alt="<?php echo clean($row['theAdmin']); ?>" class="avatarProfile" data-toggle="tooltip" title="<?php echo clean($row['theAdmin']); ?>" />
						<h4><strong><?php echo clean($row['theAdmin']); ?></strong></h4>
						<p class="clearfix">
							<?php echo clean($row['adminEmail']); ?><br />
							<?php echo clean($row['adminRole']); ?>

						</p>
					</div>
				</div>

				<?php if ($row['adminBio'] != '') { ?>
					<div class="content">
						<blockquote>
							<p><i class="fa fa-quote-left icon-quote"></i> <?php echo nl2br(clean($row['adminBio'])); ?> <i class="fa fa-quote-right icon-quote"></i></p>
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
								<td class="infoVal"><?php echo $adminPhone; ?></td>
							</tr>
							<tr>
								<td class="infoKey"><i class="fa fa-mobile"></i> <?php echo $altPhoneText; ?>:</td>
								<td class="infoVal"><?php echo $adminCell; ?></td>
							</tr>
							<tr>
								<td class="infoKey"><i class="fa fa-building"></i> <?php echo $mailingAddressText; ?>:</td>
								<td class="infoVal"><?php echo nl2br(clean($adminAddress)); ?></td>
							</tr>
						</tbody>
					</table>
				</div>

				<div class="content last">
					<h3><?php echo $assignedProjText; ?></h3>
					<?php if(mysqli_num_rows($results) < 1) { ?>
						<div class="alertMsg default">
							<i class="fa fa-minus-square-o"></i> <?php echo $noAssignedProj; ?>
						</div>
					<?php } else { ?>
						<table class="rwd-table">
							<tbody>
								<tr class="primary">
									<th><?php echo $projectText; ?></th>
									<th><?php echo $clientText; ?></th>
									<th><?php echo $percentCompleteText; ?></th>
									<th><?php echo $dateDueText; ?></th>
								</tr>
								<?php while ($rows = mysqli_fetch_assoc($results)) { ?>
									<tr>
										<td data-th="<?php echo $projectText; ?>">
											<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
												<a href="index.php?action=viewProject&projectId=<?php echo $rows['projectId']; ?>"><?php echo clean($rows['projectName']); ?></a>
											</span>
										</td>
										<td data-th="<?php echo $assignedToText; ?>"><?php echo clean($rows['theClient']); ?></td>
										<td data-th="<?php echo $percentCompleteText; ?>"><?php echo $rows['percentComplete']; ?>%</td>
										<td data-th="<?php echo $dateDueText; ?>"><?php echo $rows['dueDate']; ?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="contentAlt">
					<div class="list-group">
						<li class="list-group-item default"><?php echo $updateMngrAccountLink; ?></li>
						<a data-toggle="modal" href="#profileAvatar" class="list-group-item"><?php echo $mngrAvatarLink; ?></a>
						<a data-toggle="modal" href="#profileBio" class="list-group-item"><?php echo $mngrBioLink; ?></a>
						<a data-toggle="modal" href="#personalInfo" class="list-group-item"><?php echo $mngrPersonalInfoLink; ?></a>
						<a data-toggle="modal" href="#updateEmail" class="list-group-item"><?php echo $mngrEmailLink; ?></a>
						<a data-toggle="modal" href="#accountPassword" class="list-group-item"><?php echo $mngrPasswordLink; ?></a>
						<a data-toggle="modal" href="#accountStatus" class="list-group-item"><?php echo $mngrStatusLink; ?></a>
						<a data-toggle="modal" href="#managerType" class="list-group-item"><?php echo $mngrAccountTypeLink; ?></a>
					</div>
				</div>

				<div class="content last">
					<p><?php echo $viewMngrQuip; ?></p>
				</div>

				<div class="alertMsg success mt20"><?php echo $clientAccountQuip2; ?></div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="profileAvatar" tabindex="-1" role="dialog" aria-labelledby="profileAvatar" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-primary">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $removeMngrAvatar; ?></h4>
				</div>

				<?php if ($row['adminAvatar'] != 'adminDefault.png') { ?>
					<div class="modal-body">
						<img alt="" src="<?php echo '../'.$avatarDir.$row['adminAvatar']; ?>" class="avatar" />
						<p><?php echo $removeMngrAvatarQuip; ?></p>
					</div>
					<div class="clearfix"></div>
					<div class="modal-footer">
						<a data-toggle="modal" href="#deleteAvatar" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-ban"></i> <?php echo $removeClientAvatarBtn; ?></a>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				<?php } else { ?>
					<div class="modal-body">
						<p class="lead"><?php echo $noMngrAvatar; ?></p>
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
						<p class="lead"><?php echo $removeClientAvatarConf.' '.clean($row['theAdmin']); ?>?</p>
					</div>
					<div class="modal-footer">
						<input name="adminAvatar" type="hidden" value="<?php echo $row['adminAvatar']; ?>" />
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
					<h4 class="modal-title"><?php echo $mngrBioLink; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="adminBio"><?php echo $clientBioField; ?></label>
							<textarea class="form-control" required="" name="adminBio" rows="4"><?php echo clean($row['adminBio']); ?></textarea>
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
					<h4 class="modal-title"><?php echo $mngrPersonalInfoLink; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="adminFirstName"><?php echo $newAccountFirstName; ?></label>
									<input type="text" class="form-control" required="" name="adminFirstName" value="<?php echo clean($row['adminFirstName']); ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="adminLastName"><?php echo $newAccountLastName; ?></label>
									<input type="text" class="form-control" required="" name="adminLastName" value="<?php echo clean($row['adminLastName']); ?>" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="adminPhone"><?php echo $phoneText; ?></label>
									<input type="text" class="form-control" required="" name="adminPhone" value="<?php echo $adminPhone; ?>" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="adminCell"><?php echo $altPhoneText; ?></label>
									<input type="text" class="form-control" name="adminCell" value="<?php echo $adminCell; ?>" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="adminAddress"><?php echo $mailingAddressText; ?></label>
							<textarea class="form-control" name="adminAddress" required="" rows="3"><?php echo $adminAddress; ?></textarea>
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
					<h4 class="modal-title"><?php echo $mngrEmailLink; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="adminEmail"><?php echo $emailAddressField; ?></label>
							<input type="text" class="form-control" name="adminEmail" required="" value="<?php echo $row['adminEmail']; ?>" />
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

	<div id="accountPassword" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $mngrPasswordLink; ?></h4>
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
						<button type="input" name="submit" value="accountPassword" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
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
					<h4 class="modal-title"><?php echo $mngrStatusLink; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<p><?php echo $mngrStatusQuip; ?></p>
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
									<label for="isArchived"><?php echo $archiveMngrAccountField; ?></label>
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

	<div id="managerType" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $mngrAccountTypeLink; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="superuser"><?php echo $accountLevelField; ?></label>
							<select class="form-control" id="superuser" name="superuser">
								<option value="0"><?php echo $managerText; ?></option>
								<option value="1" <?php echo $isaAdmin; ?>><?php echo $administratorText; ?></option>
							</select>
							<span class="help-block"><?php echo $accountLevelFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="adminRole"><?php echo $roleTitleHead; ?></label>
							<input type="text" class="form-control" name="adminRole" value="<?php echo $row['adminRole']; ?>" />
							<span class="help-block"><?php echo $mngrRoleFiledHelp; ?></span>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="managerType" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>