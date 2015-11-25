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
            $msgBox = alertBox("The Folder's Description is required.", "<i class='fa fa-times-circle'></i>", "danger");
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
			$msgBox = alertBox("The Folder Description has been updated.", "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Upload a New File
    if (isset($_POST['submit']) && $_POST['submit'] == 'uploadFile') {
		// Validation
        if($_POST['fileTitle'] == "") {
            $msgBox = alertBox("Please enter a Name for the File.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['fileDesc'] == "") {
            $msgBox = alertBox("Please enter a Description for the File.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if(empty($_FILES['file']['name'])) {
            $msgBox = alertBox("Please select a File to upload.", "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Check file type
            $ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
            if (!in_array($ext, $ftypes_data)) {
                $msgBox = alertBox("The File was not an accepted file type or was too large in file size.", "<i class='fa fa-times-circle'></i>", "danger");
            } else {
				$fileTitle = $mysqli->real_escape_string($_POST['fileTitle']);
				$fileDesc = $_POST['fileDesc'];
				$clientFullName = $mysqli->real_escape_string($_POST['clientFullName']);
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
				$movePath = $uploadsDir.$uploadTo.'/'.$newfilename;

				$stmt = $mysqli->prepare("
                                    INSERT INTO
                                        projectfiles(
                                            folderId,
                                            projectId,
                                            clientId,
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
                    $clientId,
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

					$subject = 'A new Project File has been uploaded for the Project '.$projectName;

					// -------------------------------
					// ---- START Edit Email Text ----
					// -------------------------------
					$message = '<html><body>';
					$message .= '<h3>'.$subject.'</h3>';
					$message .= '<hr>';
					$message .= '<p>Project: '.$projectName.'</p>';
					$message .= '<p>File: '.$fileTitle.'</p>';
					$message .= '<p>From: '.$clientFullName.'</p>';
					$message .= '<p>'.$fileDesc.'</p>';
					$message .= '<hr>';
					$message .= '<p>You can view this new File by logging in to your account at '.$installUrl.'admin</p>';
					$message .= '<p>Thank you,<br>'.$siteName.'</p>';
					$message .= '</body></html>';
					// -------------------------------
					// ---- END Edit Email Text ----
					// -------------------------------

					$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
					$headers .= "Reply-To: ".$businessEmail."\r\n";
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

					if (mail($managers, $subject, $message, $headers)) {
						$msgBox = alertBox("The New File has been uploaded.", "<i class='fa fa-check-square'></i>", "success");
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
		$filePath = $uploadsDir.$folderUrl.'/'.$fileUrl;

		if (file_exists($filePath)) {
			// Delete the File
			unlink($filePath);

			// Delete the Record
			$stmt = $mysqli->prepare("DELETE FROM projectfiles WHERE fileId = ?");
			$stmt->bind_param('s', $_POST['fileId']);
			$stmt->execute();
			$stmt->close();

			$msgBox = alertBox("The File has been Deleted.", "<i class='fa fa-check-square'></i>", "success");
		} else {
			$msgBox = alertBox("An error was encountered and the File could not be deleted at this time.", "<i class='fa fa-times-circle'></i>", "danger");
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

	$query = "SELECT clientId, projectName FROM clientprojects WHERE projectId = ".$row['projectId'];
    $result = mysqli_query($mysqli, $query) or die('-3'.mysqli_error());
	$col = mysqli_fetch_assoc($result);

	include 'includes/navigation.php';

	if ($col['clientId'] != $clientId) {
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
			<li><a href="index.php?page=projectFolders&projectId=<?php echo $row['projectId']; ?>"><i class="fa fa-folder-o"></i> Project Folders</a></li>
			<li><a href="index.php?page=projectFiles&projectId=<?php echo $row['projectId']; ?>"><i class="fa fa-file-o"></i> Uploaded Project Files</a></li>
			<li class="pull-right"><a href="#newUpload" data-toggle="modal"><i class="fa fa-upload"></i> Upload a New File</a></li>
		</ul>
	</div>

	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row mt10">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-folder-o"></i> Folder Title:</td>
						<td class="infoVal"><?php echo clean($row['folderTitle']); ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> Date Created:</td>
						<td class="infoVal"><?php echo $row['folderDate']; ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-folder-open"></i> Project:</td>
						<td class="infoVal">
							<a href="index.php?page=viewProject&projectId=<?php echo $row['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($col['projectName']); ?>
							</a>
						</td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-user"></i> Created By:</td>
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

		<?php if ($row['adminId'] == '0') { ?>
			<a data-toggle="modal" data-target="#editFolder" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> Edit Folder Description</a>

			<div class="modal fade" id="editFolder" tabindex="-1" role="dialog" aria-labelledby="editFolder" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4 class="modal-title">Edit Folder Description</h4>
						</div>
						<form action="" method="post">
							<div class="modal-body">
								<div class="form-group">
									<label for="folderDesc">Description</label>
									<textarea class="form-control" name="folderDesc" required="" rows="6"><?php echo clean($row['folderDesc']); ?></textarea>
								</div>
							</div>
							<div class="modal-footer">
								<button type="input" name="submit" value="editFolder" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Save Changes</button>
								<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>

	<div class="content last">
		<h3><?php echo clean($row['folderTitle']); ?> Project Files</h3>
		<p>Uploaded Files can only be Deleted if you are the owner.</p>

		<?php if(mysqli_num_rows($results) < 1) { ?>
			<div class="alertMsg default no-margin mt20">
				<i class="fa fa-minus-square-o"></i> No Uploaded Files found.
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th>File Title</th>
						<th>Description</th>
						<th>Folder</th>
						<th>Date Created</th>
						<th>Created By</th>
						<th></th>
					</tr>
					<?php while ($rows = mysqli_fetch_assoc($results)) { ?>
						<tr>
							<td data-th="File Title">
								<a href="index.php?page=viewFile&fileId=<?php echo $rows['fileId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewFileTooltip; ?>">
									<?php echo clean($rows['fileTitle']); ?>
								</a>
							</td>
							<td data-th="Description">
								<span data-toggle="tooltip" data-placement="top" title="<?php echo ellipsis($rows['fileDesc'],200); ?>">
									<?php echo ellipsis($rows['fileDesc'],50); ?>
								</span>
							</td>
							<td data-th="Folder">
								<a href="index.php?page=viewFolder&folderId=<?php echo $rows['folderId']; ?>" data-toggle="tooltip" data-placement="right" title="View Folder">
									<?php echo clean($rows['folderTitle']); ?>
								</a>
							</td>
							<td data-th="Date Created"><?php echo $rows['fileDate']; ?></td>
							<td data-th="Created By">
								<?php
									if ($rows['adminId'] != '0') {
										echo clean($rows['theAdmin']);
									} else {
										echo clean($rows['theClient']);
									}
								?>
							</td>
							<td data-th="<?php echo $actionsText; ?>">
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewFileTooltip; ?>">
									<a href="index.php?page=viewFile&fileId=<?php echo $rows['fileId']; ?>"><i class="fa fa-file-text edit"></i></a>
								</span>
								<?php if ($rows['adminId'] == '0') { ?>
									<span data-toggle="tooltip" data-placement="left" title="Delete File">
										<a href="#deleteFile<?php echo $rows['fileId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
									</span>
								<?php } else { ?>
									<span data-toggle="tooltip" data-placement="left" title="You can not delete this File">
										<i class="fa fa-trash-o disabled"></i>
									</span>
								<?php } ?>
							</td>
						</tr>

						<div class="modal fade" id="deleteFile<?php echo $rows['fileId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead">Are you sure you want to DELETE the Project File "<?php echo clean($rows['fileTitle']); ?>?</p>
										</div>
										<div class="modal-footer">
											<input name="fileId" type="hidden" value="<?php echo $rows['fileId']; ?>" />
											<input name="fileUrl" type="hidden" value="<?php echo $rows['fileUrl']; ?>" />
											<input name="folderUrl" type="hidden" value="<?php echo $rows['folderUrl']; ?>" />
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
					<h4 class="modal-title">Upload a New Project File</h4>
				</div>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="modal-body">
						<p>A Folder must first be created before a file can be uploaded to it.</p>
						<p>
							<small>
								<strong>Allowed File Types:</strong> <?php echo $allowed; ?><br />
								<strong>Max Upload File Size:</strong> <?php echo $maxUpload; ?> mb.
							</small>
						</p>
						<hr />
						<div class="form-group">
							<label for="fileTitle">File Title</label>
							<input type="text" class="form-control" name="fileTitle" required="" value="<?php echo isset($_POST['fileTitle']) ? $_POST['fileTitle'] : ''; ?>">
							<span class="help-block">Max 50 Characters.</span>
						</div>
						<div class="form-group">
							<label for="fileDesc">Description</label>
							<textarea class="form-control" name="fileDesc" required="" rows="4"><?php echo isset($_POST['fileDesc']) ? $_POST['fileDesc'] : ''; ?></textarea>
						</div>
						<div class="form-group">
							<label for="file"><?php echo $selectFileField; ?></label>
							<input type="file" id="file" name="file" required="">
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="clientFullName" value="<?php echo $clientFullName; ?>" />
						<input type="hidden" name="projectId" value="<?php echo clean($row['projectId']); ?>" />
						<input type="hidden" name="projectName" value="<?php echo clean($col['projectName']); ?>" />
						<button type="input" name="submit" value="uploadFile" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> Upload File</button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>