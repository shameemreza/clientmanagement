<?php
	// Get the file types allowed from Site Settings
	$avatarTypes = $set['avatarTypes'];
	// Replace the commas with a comma space
	$avatarTypesAllowed = preg_replace('/,/', ', ', $avatarTypes);

	$avatarDir = $set['avatarFolder'];

	// Remove Avatar Image
    if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAvatar') {
		// Get the Admin's avatar url
		$sql = "SELECT adminAvatar FROM admins WHERE adminId = ".$adminId;
		$result = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
		$r = mysqli_fetch_assoc($result);
		$avatarName = $r['adminAvatar'];

		$filePath = '../'.$avatarDir.$avatarName;
		// Delete the Admin's image from the server
		if (file_exists($filePath)) {
			unlink($filePath);

			// Update the Admin record
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
							   $adminId);
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
			$msgBox = alertBox($invalidAvatarType, "<i class='fa fa-times-circle'></i>", "danger");
		} else {
			// Rename the Admin's Avatar
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
			$movePath = '../'.$avatarDir.$newAvatarName;

			$stmt = $mysqli->prepare("
								UPDATE
									admins
								SET
									adminAvatar = ?
								WHERE
									adminId = ?");
			$stmt->bind_param('ss',
							   $newAvatarName,
							   $adminId);

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
		$adminBio = $_POST['adminBio'];

		$stmt = $mysqli->prepare("UPDATE
									admins
								SET
									adminBio = ?
								WHERE
									adminId = ?"
		);
		$stmt->bind_param('ss', $adminBio, $adminId);
		$stmt->execute();
		$msgBox = alertBox($profileUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Update Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'updateAccount') {
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
									$adminId
			);
			$stmt->execute();
			$msgBox = alertBox($accountInfoUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
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
			$stmt->bind_param('ss', $adminEmail, $adminId);
			$stmt->execute();
			$msgBox = alertBox($emailUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Update Account Password
	if (isset($_POST['submit']) && $_POST['submit'] == 'changePassword') {
		$currentPass = encryptIt($_POST['currentpass']);
		// Validation
		if($_POST['currentpass'] == '') {
			$msgBox = alertBox($currentAccountPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if ($currentPass != $_POST['passwordOld']) {
			$msgBox = alertBox($currentpasswordErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] == '') {
			$msgBox = alertBox($newAccountPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password_r'] == '') {
			$msgBox = alertBox($repeatNewAccountPassReq, "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['password'] != $_POST['password_r']) {
            $msgBox = alertBox($newPassDoNotMatch, "<i class='fa fa-times-circle'></i>", "danger");
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
			$stmt->bind_param('ss', $password, $adminId);
			$stmt->execute();
			$msgBox = alertBox($accountPassUpdated, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
    }

	// Get Data
	$query = "SELECT
				adminId,
				adminEmail,
				password,
				adminFirstName,
				adminLastName,
				CONCAT(adminFirstName,' ',adminLastName) AS adminName,
				adminBio,
				adminAddress,
				adminPhone,
				adminCell,
				adminAvatar,
				DATE_FORMAT(lastVisited,'%M %e, %Y at %l:%i %p') AS lastVisited,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate
			FROM
				admins
			WHERE adminId = ".$adminId;
    $res = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt data
	if ($row['adminAddress'] != '') { $adminAddress = decryptIt($row['adminAddress']); } else { $adminAddress = '';  }
	if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = '';  }
	if ($row['adminCell'] != '') { $adminCell = decryptIt($row['adminCell']); } else { $adminCell = '';  }

	include 'includes/navigation.php';
?>
<div class="content last">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="row">
		<div class="col-md-6">
			<div class="card-wrap">
				<div class="profile_pic-wrap">
					<img src="<?php echo '../'.$avatarDir.$row['adminAvatar']; ?>" alt="" />
				</div>
				<div class="info-wrap">
					<h2 class="user-name"><?php echo clean($row['adminName']); ?></h2>
					<h4 class="user-info"><?php echo clean($row['adminEmail']); ?></h4>
				</div>
				<div class="actions-wrap text-center">
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changeAvatarLink; ?>">
						<a data-toggle="modal" href="#profileAvatar" class="btn btn-default"><i class="fa fa-picture-o noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changeBioLink; ?>">
						<a data-toggle="modal" href="#profileBio" class="btn btn-default"><i class="fa fa-quote-left noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $updateAccountLink; ?>">
						<a data-toggle="modal" href="#updateAccount" class="btn btn-default"><i class="fa fa-user noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changeEmailLink; ?>">
						<a data-toggle="modal" href="#updateEmail" class="btn btn-default"><i class="fa fa-envelope noMgn"></i></a>
					</span>
					<span data-toggle="tooltip" data-placement="top" title="<?php echo $changePasswordLink; ?>">
						<a data-toggle="modal" href="#changePassword" class="btn btn-default"><i class="fa fa-unlock noMgn"></i></a>
					</span>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<table class="infoTable mt30 mb20">
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
					<td class="infoVal"><?php echo nl2br($adminAddress); ?></td>
				</tr>
			</table>

			<?php if ($row['adminBio'] != '') { ?>
				<blockquote>
					<p><i class="fa fa-quote-left icon-quote"></i> <?php echo nl2br(clean($row['adminBio'])); ?> <i class="fa fa-quote-right icon-quote"></i></p>
				</blockquote>
			<?php } else { ?>
				<blockquote>
					<p><?php echo $noProfileBioQuip; ?></p>
				</blockquote>
			<?php } ?>
		</div>
	</div>

	<div class="clearfix"></div>

	<h4 class="bg-success"><?php echo $personalInfoTitle; ?></h4>
	<p><?php echo $personalInfoQuip; ?></p>
</div>

<div id="profileAvatar" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $myAvatarModal; ?></h4>
			</div>
			<?php if ($row['adminAvatar'] != 'adminDefault.png') { ?>
				<div class="modal-body">
					<img alt="" src="<?php echo '../'.$avatarDir.$row['adminAvatar']; ?>" class="avatar" />
					<p><?php echo $myAvatarQuip; ?></p>
				</div>
				<div class="clearfix"></div>
				<div class="modal-footer">
					<a data-toggle="modal" href="#deleteAvatar" class="btn btn-warning btn-icon" data-dismiss="modal"><i class="fa fa-ban"></i> <?php echo $removeClientAvatarBtn; ?></a>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			<?php } ?>

			<?php if ($row['adminAvatar'] == 'adminDefault.png') { ?>
				<form enctype="multipart/form-data" action="" method="post">
					<div class="modal-body">
						<p class="lead"><?php echo $uploadNewAvatarModal; ?></p>
						<p><?php echo $allowedFileTypesQuip.': '.$avatarTypesAllowed; ?></p>

						<div class="form-group">
							<label for="file"><?php echo $selectAvatarField; ?></label>
							<input type="file" id="file" name="file">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="avatarName" value="<?php echo $row['adminFirstName'].'_'.$row['adminLastName']; ?>" />
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
				<h4 class="modal-title"><?php echo $changeBioLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="adminBio"><?php echo $profileBioField; ?></label>
						<textarea class="form-control" name="adminBio" rows="4"><?php echo clean($row['adminBio']); ?></textarea>
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
				<h4 class="modal-title"><?php echo $updateAccountLink; ?></h4>
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
				<h4 class="modal-title"><?php echo $changeEmailLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="adminEmail"><?php echo $emailAddressField; ?></label>
						<input type="text" class="form-control" name="adminEmail" required="" value="<?php echo $row['adminEmail']; ?>" />
						<span class="help-block"><?php echo $validEmailAddyHelp; ?></span>
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
				<h4 class="modal-title"><?php echo $changePasswordLink; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
                        <label for="currentpass"><?php echo $currentPassField; ?></label>
                        <input type="text" class="form-control" name="currentpass" required="" value="" />
						<span class="help-block"><?php echo $currentPassFieldHelp; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password"><?php echo $newPasswordField; ?></label>
                        <input type="text" class="form-control" name="password" required="" value="" />
						<span class="help-block"><?php echo $newPasswordFieldHelp; ?></span>
                    </div>
					<div class="form-group">
                        <label for="password_r"><?php echo $confNewPasswordField; ?></label>
                        <input type="text" class="form-control" name="password_r" required="" value="" />
						<span class="help-block"><?php echo $confNewPasswordFieldHelp; ?></span>
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