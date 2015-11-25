<?php
	$projectId = $_GET['projectId'];
	$getId = 'projectId='.$projectId;
	$pagPages = '10';

	// Get the Max Upload Size allowed
    $maxUpload = (int)(ini_get('upload_max_filesize'));

	// Get the File Uploads Folder from the Site Settings
	$uploadsDir = $set['uploadPath'];

	// Get the File Types allowed
	$fileExt = $set['fileTypesAllowed'];
	$allowed = preg_replace('/,/', ', ', $fileExt); // Replace the commas with a comma space
	$ftypes = array($fileExt);
	$ftypes_data = explode( ',', $fileExt );

	// Upload a New File
    if (isset($_POST['submit']) && $_POST['submit'] == 'uploadFile') {
		// Validation
        if($_POST['folderId'] == "...") {
            $msgBox = alertBox($selectFolderReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['fileTitle'] == "") {
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
				$folderId = $mysqli->real_escape_string($_POST['folderId']);
				$fileTitle = $mysqli->real_escape_string($_POST['fileTitle']);
				$fileDesc = $_POST['fileDesc'];
				$adminFullName = $mysqli->real_escape_string($_POST['adminFullName']);
				$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);
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
	
	// Include Pagination Class
	include('includes/getpagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records for Private Closed
	$rows = $mysqli->query("SELECT * FROM projectfiles WHERE projectfiles.projectId = ".$projectId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get File Data
    $sql  = "SELECT
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
				projectfiles.projectId = ".$projectId."
			ORDER BY orderDate, projectfiles.folderId ".$pages->get_limit();
    $res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());

	// Get the Assigned Project's Data
	$qry = "SELECT
				assignedprojects.assignedTo,
				clientprojects.projectId,
				clientprojects.projectName,
				clients.clientEmail
			FROM
				assignedprojects
				LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE assignedprojects.projectId = ".$projectId;
	$result = mysqli_query($mysqli, $qry) or die('-2' . mysqli_error());
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
			<li><a href="index.php?action=projectFolders&projectId=<?php echo $projectId; ?>"><i class="fa fa-folder-open-o"></i> <?php echo $projectFoldersTabLink; ?></a></li>
			<li class="active"><a href="" data-toggle="tab"><i class="fa fa-folder-o"></i> <?php echo $upldProjFilesTabLink; ?></a></li>
			<li class="pull-right"><a href="#newUpload" data-toggle="modal"><i class="fa fa-upload"></i> <?php echo $uploadFileTabLink; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<?php if ($msgBox) { echo $msgBox; } ?>
		<h3>
			<a href="index.php?action=viewProject&projectId=<?php echo $projectId; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
				<?php echo clean($rows['projectName']); ?>
			</a>
			<?php echo $pageName; ?>
		</h3>

		<?php if(mysqli_num_rows($res) < 1) { ?>
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
					<?php while ($row = mysqli_fetch_assoc($res)) { ?>
						<tr>
							<td data-th="<?php echo $fileTitleText; ?>">
								<a href="index.php?action=viewFile&fileId=<?php echo $row['fileId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewFileTooltip; ?>">
									<?php echo clean($row['fileTitle']); ?>
								</a>
							</td>
							<td data-th="<?php echo $descriptionText; ?>">
								<span data-toggle="tooltip" data-placement="top" title="<?php echo ellipsis($row['fileDesc'],200); ?>">
									<?php echo ellipsis($row['fileDesc'],50); ?>
								</span>
							</td>
							<td data-th="<?php echo $folderText; ?>">
								<a href="index.php?action=viewFolder&folderId=<?php echo $row['folderId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewFolderText; ?>">
									<?php echo clean($row['folderTitle']); ?>
								</a>
							</td>
							<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['fileDate']; ?></td>
							<td data-th="<?php echo $createdByTableHead; ?>">
								<?php
									if ($row['adminId'] != '0') {
										echo clean($row['theAdmin']);
									} else {
										echo clean($row['theClient']);
									}
								?>
							</td>
							<td data-th="<?php echo $actionsText; ?>">
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewFileTooltip; ?>">
									<a href="index.php?action=viewFile&fileId=<?php echo $row['fileId']; ?>"><i class="fa fa-file-text edit"></i></a>
								</span>
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteFileTooltip; ?>">
									<a href="#deleteFile<?php echo $row['fileId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
								</span>
							</td>
						</tr>

						<div class="modal fade" id="deleteFile<?php echo $row['fileId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteFileConf.' '.clean($row['fileTitle']); ?>"?</p>
										</div>
										<div class="modal-footer">
											<input name="fileId" type="hidden" value="<?php echo $row['fileId']; ?>" />
											<input name="fileUrl" type="hidden" value="<?php echo $row['fileUrl']; ?>" />
											<input name="folderUrl" type="hidden" value="<?php echo $row['folderUrl']; ?>" />
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
		<?php
		}
		if ($total > $pagPages) {
			echo $pages->page_links();
		}
		?>
	</div>

	<div class="modal fade" id="newUpload" tabindex="-1" role="dialog" aria-labelledby="newUpload" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $uploadNewProjFileModal; ?></h4>
				</div>
				<?php
					// Check if the Project is Assigned to a Manager/Admin
					$x = $mysqli->query("SELECT 'X' FROM assignedprojects WHERE projectId = ".$projectId);
					if ($x->num_rows) {
				?>
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
								<label for="folderId"><?php echo $selectFolderField; ?></label>
								<select class="form-control" name="folderId">
									<?php
										$qry = "SELECT folderId, folderTitle FROM projectfolders WHERE projectId = ".$projectId;
										$r = mysqli_query($mysqli, $qry) or die('-3'.mysqli_error());
									?>
									<option value="..."><?php echo $selectOption; ?></option>
									<?php while ($c = mysqli_fetch_assoc($r)) { ?>
										<option value="<?php echo $c['folderId']; ?>"><?php echo clean($c['folderTitle']); ?></option>
									<?php } ?>
								</select>
								<span class="help-block"><?php echo $selectFolderFieldHelp; ?></span>
							</div>
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
							<input type="hidden" name="projectId" value="<?php echo clean($rows['projectId']); ?>" />
							<input type="hidden" name="projectName" value="<?php echo clean($rows['projectName']); ?>" />
							<input type="hidden" name="clientEmail" value="<?php echo clean($rows['clientEmail']); ?>" />
							<button type="input" name="submit" value="uploadFile" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadFileBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				<?php } else { ?>
					<div class="modal-body">
						<p class="lead"><?php echo $assignFirstMsg2; ?></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>