<?php
	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);

	$avatarDir = $set['avatarFolder'];

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAvatar') {
		// Get the client's avatar url
		$sql = "SELECT clientAvatar FROM clients WHERE clientId = ".$clientId;
		$result = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$avatarName = $r['clientAvatar'];

		$filePath = $avatarDir.$avatarName;
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
			$msgBox = alertBox($avatarRemovedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		} else {
			$msgBox = alertBox($avatarRemoveError, "<i class='fa fa-warning'></i>", "warning");
		}
	}

	// Upload Avatar Image
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateAvatar') {
		// Get the File Types allowed
		$fileExt = $set['avatarTypes'];
		$allowed = preg_replace('/,/', ', ', $fileExt); // Replace the commas with a comma space (, )
		$ftypes = array($fileExt);
		$ftypes_data = explode( ',', $fileExt );

		// Check file type
		$ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
		if (!in_array($ext, $ftypes_data)) {
			$msgBox = alertBox($avatarErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Rename the client's Avatar
			$avatarName = htmlentities($_POST['avatarName']);

			// Replace any spaces with an underscore
			// And set to all lowercase
			$newName = str_replace(' ', '_', $avatarName);
			$fileName = strtolower($newName);
			$fullName = $fileName;

			// set the upload path
			$avatarUrl = basename($_FILES['file']['name']);

			// Get the files original Ext
			$extension = end(explode(".", $avatarUrl));

			// Set the files name to the name set in the form
			// And add the original Ext
			$newAvatarName = $fullName.'.'.$extension;
			$movePath = $avatarDir.$newAvatarName;

			$stmt = $mysqli->prepare("
								UPDATE
									clients
								SET
									clientAvatar = ?
								WHERE
									clientId = ?");
			$stmt->bind_param('ss',
							   $newAvatarName,
							   $clientId);

			if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
				$stmt->execute();
				$msgBox = alertBox($avatarUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
				$completed = 'true';
				$stmt->close();
			} else {
				$msgBox = alertBox($avatarUploadError, "<i class='fa fa-times-circle'></i>", "danger");
			}
		}
	}

	// Update Bio text
	if (isset($_POST['submit']) && $_POST['submit'] == 'profileBio') {
		$clientBio = $_POST['clientBio'];

		$stmt = $mysqli->prepare("UPDATE
									clients
								SET
									clientBio = ?
								WHERE
									clientId = ?"
		);
		$stmt->bind_param('ss', $clientBio, $clientId);
		$stmt->execute();
		$msgBox = alertBox($profileUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Update Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateAccount') {
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
			$msgBox = alertBox($accountInfoUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
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
			$msgBox = alertBox($accountEmailUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'changePassword') {
		$currentPass = encryptIt($_POST['currentpass']);
		// Validation
		if($_POST['currentpass'] == '') {
			$msgBox = alertBox($accountPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($currentPass != $_POST['passwordOld']) {
			$msgBox = alertBox($accountPassError, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] == '') {
			$msgBox = alertBox($accountNewPass, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($retypeAccountPass, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($accountPassNotMatch, "<i class='fa fa-times-circle'></i>", "danger");
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
			$msgBox = alertBox($accountPassUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Get Data
	$query = "SELECT
				clientId,
				clientEmail,
				password,
				clientFirstName,
				clientLastName,
				CONCAT(clientFirstName,' ',clientLastName) AS clientName,
				clientCompany,
				clientBio,
				clientAddress,
				clientPhone,
				clientCell,
				clientAvatar,
				DATE_FORMAT(lastVisited,'%M %e, %Y at %l:%i %p') AS lastVisited,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate
			FROM
				clients
			WHERE clientId = ".$clientId;
    $res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data
	if ($row['clientAddress'] != '') { $clientAddress = decryptIt($row['clientAddress']); } else { $clientAddress = '';  }
	if ($row['clientPhone'] != '') { $clientPhone = decryptIt($row['clientPhone']); } else { $clientPhone = '';  }
	if ($row['clientCell'] != '') { $clientCell = decryptIt($row['clientCell']); } else { $clientCell = '';  }

	// Get Project Data
	$stmt = "SELECT
				clientprojects.projectId,
				clientprojects.createdBy,
				clientprojects.clientId,
				clientprojects.projectName,
				clientprojects.percentComplete,
				clientprojects.projectDeatils,
				DATE_FORMAT(clientprojects.startDate,'%M %d, %Y') AS startDate,
				DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
				assignedprojects.assignedTo,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS managerAssigned
			FROM
				clientprojects
				LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
			WHERE
				clientprojects.clientId = ".$clientId." AND
				clientprojects.archiveProj = 0
			ORDER BY clientprojects.projectId";
	$results = mysqli_query($mysqli, $stmt) or die('-3'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="content">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="row">
		<div class="col-md-6">
			<div class="card-wrap">
				<div class="profile_pic-wrap">
					<img src="<?php echo $avatarDir.$row['clientAvatar']; ?>" alt="" />
				</div>
				<div class="info-wrap">
					<h2 class="user-name"><?php echo clean($row['clientName']); ?></h2>
					<h4 class="user-info"><?php echo clean($row['clientEmail']); ?></h4>
					<?php if ($row['clientCompany'] != '') { ?>
						<h4 class="user-info"><?php echo clean($row['clientCompany']); ?></h4>
					<?php } ?>
				</div>
				<div class="actions-wrap text-center">
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changeAvatarText; ?>">
						<a data-toggle="modal" href="#profileAvatar" class="btn btn-default"><i class="fa fa-picture-o noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changeBioText; ?>">
						<a data-toggle="modal" href="#profileBio" class="btn btn-default"><i class="fa fa-quote-left noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $updateAccountText; ?>">
						<a data-toggle="modal" href="#updateAccount" class="btn btn-default"><i class="fa fa-user noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changeEmailText; ?>">
						<a data-toggle="modal" href="#updateEmail" class="btn btn-default"><i class="fa fa-envelope noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changePasswordText; ?>">
						<a data-toggle="modal" href="#changePassword" class="btn btn-default"><i class="fa fa-unlock noMgn"></i></a>
					</span>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<table class="infoTable mt30 mb20">
				<tr>
					<td class="infoKey"><i class="fa fa-phone"></i> <?php echo $primaryPhoneText; ?>:</td>
					<td class="infoVal"><?php echo $clientPhone; ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-mobile"></i> <?php echo $altPhoneText; ?>:</td>
					<td class="infoVal"><?php echo $clientCell; ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-building"></i> <?php echo $mailingAddressText; ?>:</td>
					<td class="infoVal"><?php echo nl2br($clientAddress); ?></td>
				</tr>
			</table>
			
			<?php if ($row['clientBio'] != '') { ?>
				<blockquote>
					<p><i class="fa fa-quote-left icon-quote"></i> <?php echo nl2br(clean($row['clientBio'])); ?> <i class="fa fa-quote-right icon-quote"></i></p>
				</blockquote>
			<?php } else { ?>
				<blockquote>
					<p><?php echo $noBioText1; ?> <a data-toggle="modal" href="#profileBio"><?php echo $noBioText2; ?> <i class="fa fa-long-arrow-right icon-quote"></i></a></p>
				</blockquote>
			<?php } ?>
		</div>
	</div>

	<div class="clearfix"></div>

	<h4 class="bg-success"><?php echo $securePersonalInfo; ?></h4>
	<p><?php echo $securePersonalInfoQuip; ?></p>
</div>

<div class="content last">
	<h3><?php echo $myOpenProjText; ?></h3>
	<?php if(mysqli_num_rows($results) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square"></i> <?php echo $noOpenProjMsg; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table">
			<tbody>
				<tr>
					<th class="text-left"><?php echo $projectText; ?></th>
					<th><?php echo $descText; ?></th>
					<th><?php echo $assignedToText; ?></th>
					<th><?php echo $percentCompleteText; ?></th>
					<th><?php echo $dateStartedText; ?></th>
					<th><?php echo $dateDueText; ?></th>
				</tr>
				<?php while ($rows = mysqli_fetch_assoc($results)) { ?>
					<tr>
						<td class="text-left" data-th="<?php echo $projectText; ?>">
							<a href="index.php?page=viewProject&projectId=<?php echo $rows['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($rows['projectName']); ?>
							</a>
						</td>
						<td data-th="<?php echo $descText; ?>"><?php echo ellipsis($rows['projectDeatils'],65); ?></td>
						<td data-th="<?php echo $assignedToText; ?>"><?php echo clean($rows['managerAssigned']); ?></td>
						<td data-th="<?php echo $percentCompleteText; ?>"><?php echo clean($rows['percentComplete']); ?>%</td>
						<td data-th="<?php echo $dateStartedText; ?>"><?php echo $rows['startDate']; ?></td>
						<td data-th="<?php echo $dateDueText; ?>"><?php echo $rows['dueDate']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } ?>
</div>

<div id="profileAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $profileAvatarModal; ?></h4>
			</div>
			<?php if ($row['clientAvatar'] != 'clientDefault.png') { ?>
				<div class="modal-body">
					<img alt="" src="<?php echo $avatarDir.$row['clientAvatar']; ?>" class="avatar" />
					<p><?php echo $profileAvatarQuip; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-ban"></i> <?php echo $removeAvatarBtn; ?></a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } ?>

			<?php if ($row['clientAvatar'] == 'clientDefault.png') { ?>
				<form enctype="multipart/form-data" action="" method="post">
					<div class="modal-body">
						<p class="lead"><?php echo $uploadNewAvatarText; ?></p>
						<p><?php echo $allowedFileTypesText.': '.$avatarTypesAllowed; ?></p>

						<div class="form-group">
							<label for="file"><?php echo $newAvatarField; ?></label>
							<input type="file" id="file" name="file">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="avatarName" id="avatarName" value="<?php echo $row['clientFirstName'].'_'.$row['clientLastName']; ?>" />
						<button type="input" name="submit" value="updateAvatar" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			<?php } ?>
		</div>
	</div>
</div>

<div id="deleteAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="" method="post">
				<div class="modal-body">
					<p class="lead"><?php echo $deleteAvatarConf; ?></p>
				</div>
				<div class="modal-footer">
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
				<h4 class="modal-title"><?php echo $changeBioText; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="clientBio"><?php echo $profileBioField; ?></label>
						<textarea class="form-control" name="clientBio" rows="4"><?php echo clean($row['clientBio']); ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="profileBio" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="updateAccount" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $updateAccountInfoModal; ?></h4>
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
						<label for="clientCompany"><?php echo $comapnyField; ?></label>
						<input type="text" class="form-control" name="clientCompany" value="<?php echo clean($row['clientCompany']); ?>" />
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="clientPhone"><?php echo $primaryPhoneText ?></label>
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
					<button type="input" name="submit" value="updateAccount" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
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
				<h4 class="modal-title"><?php echo $updateEmailModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="clientEmail"><?php echo $emailAddressField; ?></label>
						<input type="text" class="form-control" name="clientEmail" required="" value="<?php echo $row['clientEmail']; ?>" />
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="updateEmail" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="changePassword" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $changePasswordModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="currentpass"><?php echo $currPassField; ?></label>
                        <input type="text" class="form-control" name="currentpass" required="" value="" />
						<span class="help-block"><?php echo $currPasswordFieldHelp; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password"><?php echo $newPasswordField; ?></label>
                        <input type="text" class="form-control" name="password" required="" value="" />
						<span class="help-block"><?php echo $newPasswordFieldHelp; ?></span>
                    </div>
					<div class="form-group">
                        <label for="password_r"><?php echo $newPassConfField; ?></label>
                        <input type="text" class="form-control" name="password_r" required="" value="" />
						<span class="help-block"><?php echo $newPassConfFieldHelp; ?></span>
                    </div>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="passwordOld" value="<?php echo $row['password']; ?>" />
					<button type="input" name="submit" value="changePassword" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>