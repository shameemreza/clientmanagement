<?php
	$folderId = $_GET['folderId'];

	// Get the Max Upload Size allowed
    $maxUpload = (int)(ini_get('upload_max_filesize'));

	// Get the File Uploads Folder from the Site Settings
	$uploadsDir = $set['uploadPath'];

	// Get the File Types allowed
	$fileExt = $set['fileTypesAllowed'];
	$allowed = preg_replace('/,/', ', ', $fileExt); // Replace the commas with a comma space
	$ftypes = array($fileExt);
	$ftypes_data = explode( ',', $fileExt );

	// Edit Folder Description
    if (isset($_POST['submit']) && $_POST['submit'] == 'editFolder') {
        // Validation
		if($_POST['folderDesc'] == "") {
            $msgBox = alertBox($folderDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$folderDesc = $_POST['folderDesc'];

            $stmt = $mysqli->prepare("UPDATE
										projectfolders
									SET
										folderDesc = ?
									WHERE
										folderId = ?"
			);
			$stmt->bind_param('ss',
									$folderDesc,
									$folderId
			);
			$stmt->execute();
			$msgBox = alertBox($folderDescUpdtMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Upload a New File
    if (isset($_POST['submit']) && $_POST['submit'] == 'uploadFile') {
		// Validation
        if($_POST['fileTitle'] == "") {
            $msgBox = alertBox($fileNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['fileDesc'] == "") {
            $msgBox = alertBox($fileDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if(empty($_FILES['file']['name'])) {
            $msgBox = alertBox($selectFileReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Check file type
            $ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
            if (!in_array($ext, $ftypes_data)) {
                $msgBox = alertBox($fileUploadErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
            } else {
				$fileTitle = $mysqli->real_escape_string($_POST['fileTitle']);
				$fileDesc = $_POST['fileDesc'];
				$adminFullName = $mysqli->real_escape_string($_POST['adminFullName']);
				$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);
				$projectId = $mysqli->real_escape_string($_POST['projectId']);
				$projectName = $mysqli->real_escape_string($_POST['projectName']);
				$fileDate = date("Y-m-d H:i:s");

				// Get the Client's Folder URL from the Selected Folder
				$folderurl = "SELECT folderUrl FROM projectfolders WHERE folderId = ".$folderId;
				$foldurl = mysqli_query($mysqli, $folderurl);
				$getfolder = mysqli_fetch_assoc($foldurl);
				$folderUrl = $getfolder['folderUrl'];

				// Replace any spaces with an underscore
				// And set to all lower-case
				$newName = str_replace(' ', '_', $fileTitle);
				$fileNewName = strtolower($newName);

				// Set the upload path
				$uploadTo = $folderUrl;
				$fileUrl = basename($_FILES['file']['name']);

				// Get the files original Ext
				$extension = end(explode(".", $fileUrl));

				// Generate a random string to append to the file's name
				$randomString=md5(uniqid(rand()));
				$appendName=substr($randomString, 0, 8);

				// Set the files name to the name set in the form
				// And add the original Ext
				$newfilename = $fileNewName.'-'.$appendName.'.'.$extension;
				$movePath = '../'.$uploadsDir.$uploadTo.'/'.$newfilename;

				$stmt = $mysqli->prepare("
                                    INSERT INTO
                                        projectfiles(
                                            folderId,
                                            projectId,
                                            adminId,
                                            fileTitle,
                                            fileDesc,
                                            fileUrl,
                                            fileDate
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
                    $folderId,
                    $projectId,
                    $adminId,
                    $fileTitle,
                    $fileDesc,
                    $newfilename,
                    $fileDate
                );

                if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
                    $stmt->execute();

					// Send out the email in HTML
					$installUrl = $set['installUrl'];
					$siteName = $set['siteName'];
					$businessEmail = $set['businessEmail'];

					$subject = $newFileEmailSubject.' '.$projectName;

					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<hr>';
					$message .= '<p>'.$projectText.': '.$projectName.'</p>';
					$message .= '<p>'.$fileText.': '.$fileTitle.'</p>';
					$message .= '<p>'.$fromText.': '.$adminFullName.'</p>';
					$message .= '<p>'.$fileDesc.'</p>';
					$message .= '<hr>';
					$message .= '<p>'.$emailLink.'</p>';
					$message .= '<p>'.$emailThankYou.'</p>';
					$message .= '</body></html>';

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($clientEmail, $subject, $message, $headers)) {
						$msgBox = alertBox($newFileUpldMsg, "<i class='fa fa-check-square'></i>", "success");
					} else {
						$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
					}
					// Clear the Form of values
					$_POST['fileTitle'] = $_POST['fileDesc'] = '';
					$stmt->close();
				}
			}
		}
	}

	// Delete File
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteFile') {
		$fileId = $mysqli->real_escape_string($_POST['fileId']);
		$fileUrl = $_POST['fileUrl'];
		$folderUrl = $_POST['folderUrl'];

		// Delete the file from the server
		$filePath = '../'.$uploadsDir.$folderUrl.'/'.$fileUrl;

		if (file_exists($filePath)) {
			// Delete the File
			unlink($filePath);

			// Delete the Record
			$stmt = $mysqli->prepare("DELETE FROM projectfiles WHERE fileId = ?");
			$stmt->bind_param('s', $_POST['fileId']);
			$stmt->execute();
			$stmt->close();

			$msgBox = alertBox($fileDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		} else {
			$msgBox = alertBox($fileDeleteErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
    }

	// Get Folder Data
    $sql  = "SELECT
				projectfolders.folderId,
				projectfolders.projectId,
				projectfolders.adminId,
				projectfolders.clientId,
				projectfolders.folderTitle,
				projectfolders.folderDesc,
				projectfolders.folderUrl,
				DATE_FORMAT(projectfolders.folderDate,'%M %d, %Y') AS folderDate,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectfolders
				LEFT JOIN clients ON projectfolders.clientId = clients.clientId
				LEFT JOIN admins ON projectfolders.adminId = admins.adminId
			WHERE
				projectfolders.folderId = ".$folderId;
    $res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Get File Data
    $stmt  = "SELECT
				projectfiles.fileId,
				projectfiles.folderId,
				projectfiles.projectId,
				projectfiles.adminId,
				projectfiles.clientId,
				projectfiles.fileTitle,
				projectfiles.fileDesc,
				projectfiles.fileUrl,
				DATE_FORMAT(projectfiles.fileDate,'%M %d, %Y') AS fileDate,
				UNIX_TIMESTAMP(projectfiles.fileDate) AS orderDate,
				projectfolders.folderTitle,
				projectfolders.folderUrl,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectfiles
				LEFT JOIN projectfolders ON projectfiles.folderId = projectfolders.folderId
				LEFT JOIN clients ON projectfiles.clientId = clients.clientId
				LEFT JOIN admins ON projectfiles.adminId = admins.adminId
			WHERE
				projectfiles.folderId = ".$folderId."
			ORDER BY orderDate, projectfiles.folderId";
    $results = mysqli_query($mysqli, $stmt) or die('-2'.mysqli_error());

	// Only allow access to the Assigned Manager or Admins
	$qry = "SELECT
				assignedprojects.assignedTo,
				clientprojects.projectName,
				clients.clientEmail
			FROM
				assignedprojects
				LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE assignedprojects.projectId = ".$row['projectId'];
	$result = mysqli_query($mysqli, $qry) or die('-3' . mysqli_error());
	$rows = mysqli_fetch_assoc($result);
	$assignedTo = $rows['assignedTo'];

	include 'includes/navigation.php';

	if (($isAdmin != '1') && ($rows['assignedTo'] != $adminId)) {
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
			<li><a href="index.php?action=projectFolders&projectId=<?php echo $row['projectId']; ?>"><i class="fa fa-folder-o"></i> <?php echo $projectFoldersTabLink; ?></a></li>
			<li><a href="index.php?action=projectFiles&projectId=<?php echo $row['projectId']; ?>"><i class="fa fa-file-o"></i> <?php echo $upldProjFilesTabLink; ?></a></li>
			<li class="pull-right"><a href="#newUpload" data-toggle="modal"><i class="fa fa-upload"></i> <?php echo $uploadFileTabLink; ?></a></li>
		</ul>
	</div>

	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row mt10">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-folder-o"></i> <?php echo $folderNameText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['folderTitle']); ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateCreatedTableHead; ?>:</td>
						<td class="infoVal"><?php echo $row['folderDate']; ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-folder-open"></i> <?php echo $projectText; ?>:</td>
						<td class="infoVal">
							<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($rows['projectName']); ?>
							</a>
						</td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-user"></i> <?php echo $createdByTableHead; ?>:</td>
						<td class="infoVal">
							<?php
								if ($row['adminId'] != '0') {
									echo clean($row['theAdmin']);
								} else {
									echo clean($row['theClient']);
								}
							?>
						</td>
					</tr>
				</table>
			</div>
		</div>

		<div class="well well-sm bg-trans no-margin mt20">
			<?php echo nl2br(clean($row['folderDesc'])); ?>
		</div>


		<a data-toggle="modal" data-target="#editFolder" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editFolderDescpBtn; ?></a>

		<div class="modal fade" id="editFolder" tabindex="-1" role="dialog" aria-labelledby="editFolder" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $editFolderDescpBtn; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="folderDesc"><?php echo $descriptionText; ?></label>
								<textarea class="form-control" name="folderDesc" required="" rows="6"><?php echo clean($row['folderDesc']); ?></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="editFolder" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="content last">
		<h3><?php echo clean($row['folderTitle']); ?> <?php echo $projectFilesTitle; ?></h3>

		<?php if(mysqli_num_rows($results) < 1) { ?>
			<div class="alertMsg default no-margin mt20">
				<i class="fa fa-minus-square-o"></i> <?php echo $noUploadedFilesFound; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $fileTitleText; ?></th>
						<th><?php echo $descriptionText; ?></th>
						<th><?php echo $folderText; ?></th>
						<th><?php echo $dateCreatedTableHead; ?></th>
						<th><?php echo $createdByTableHead; ?></th>
						<th></th>
					</tr>
					<?php while ($cols = mysqli_fetch_assoc($results)) { ?>
						<tr>
							<td data-th="<?php echo $fileTitleText; ?>">
								<a href="index.php?action=viewFile&fileId=<?php echo $cols['fileId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewFileTooltip; ?>">
									<?php echo clean($cols['fileTitle']); ?>
								</a>
							</td>
							<td data-th="<?php echo $descriptionText; ?>">
								<span data-toggle="tooltip" data-placement="top" title="<?php echo ellipsis($cols['fileDesc'],200); ?>">
									<?php echo ellipsis($cols['fileDesc'],50); ?>
								</span>
							</td>
							<td data-th="<?php echo $folderText; ?>">
								<a href="index.php?action=viewFolder&folderId=<?php echo $cols['folderId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewFolderText; ?>">
									<?php echo clean($cols['folderTitle']); ?>
								</a>
							</td>
							<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $cols['fileDate']; ?></td>
							<td data-th="<?php echo $createdByTableHead; ?>">
								<?php
									if ($cols['adminId'] != '0') {
										echo clean($cols['theAdmin']);
									} else {
										echo clean($cols['theClient']);
									}
								?>
							</td>
							<td data-th="<?php echo $actionsText; ?>">
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewFileTooltip; ?>">
									<a href="index.php?action=viewFile&fileId=<?php echo $cols['fileId']; ?>"><i class="fa fa-file-text edit"></i></a>
								</span>
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteFileTooltip; ?>">
									<a href="#deleteFile<?php echo $cols['fileId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
								</span>
							</td>
						</tr>

						<div class="modal fade" id="deleteFile<?php echo $cols['fileId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteFileConf.' '.clean($cols['fileTitle']); ?>?</p>
										</div>
										<div class="modal-footer">
											<input name="fileId" type="hidden" value="<?php echo $cols['fileId']; ?>" />
											<input name="fileUrl" type="hidden" value="<?php echo $cols['fileUrl']; ?>" />
											<input name="folderUrl" type="hidden" value="<?php echo $cols['folderUrl']; ?>" />
											<button type="input" name="submit" value="deleteFile" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
					<?php } ?>
				</tbody>
			</table>
		<?php } ?>
	</div>

	<div class="modal fade" id="newUpload" tabindex="-1" role="dialog" aria-labelledby="newUpload" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $uploadNewProjFileModal; ?></h4>
				</div>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="modal-body">
						<p><?php echo $uploadNewProjFileQuip; ?></p>
						<p>
							<small>
								<strong><?php echo $allowedFileTypesQuip; ?></strong> <?php echo $allowed; ?><br />
								<strong><?php echo $maxFileSizeQuip; ?></strong> <?php echo $maxUpload.' '.$mbText; ?>.
							</small>
						</p>
						<hr />
						<div class="form-group">
							<label for="fileTitle"><?php echo $fileTitleText; ?></label>
							<input type="text" class="form-control" name="fileTitle" required="" value="<?php echo isset($_POST['fileTitle']) ? $_POST['fileTitle'] : ''; ?>">
							<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="fileDesc"><?php echo $descriptionText; ?></label>
							<textarea class="form-control" name="fileDesc" required="" rows="4"><?php echo isset($_POST['fileDesc']) ? $_POST['fileDesc'] : ''; ?></textarea>
						</div>
						<div class="form-group">
							<label for="file"><?php echo $selectFileField; ?></label>
							<input type="file" id="file" name="file" required="">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="adminFullName" value="<?php echo $adminFullName; ?>" />
						<input type="hidden" name="projectId" value="<?php echo clean($row['projectId']); ?>" />
						<input type="hidden" name="projectName" value="<?php echo clean($rows['projectName']); ?>" />
						<input type="hidden" name="clientEmail" value="<?php echo clean($rows['clientEmail']); ?>" />
						<button type="input" name="submit" value="uploadFile" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadFileBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>