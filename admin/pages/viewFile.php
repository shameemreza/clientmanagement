<?php
	$fileId = $_GET['fileId'];

	// Get the File Uploads Folder from the Site Settings
	$uploadsDir = $set['uploadPath'];

	// Edit File Description
    if (isset($_POST['submit']) && $_POST['submit'] == 'editFile') {
        // Validation
		if($_POST['fileDesc'] == "") {
            $msgBox = alertBox($fileDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$fileDesc = $_POST['fileDesc'];

            $stmt = $mysqli->prepare("UPDATE
										projectfiles
									SET
										fileDesc = ?
									WHERE
										fileId = ?"
			);
			$stmt->bind_param('ss',
									$fileDesc,
									$fileId
			);
			$stmt->execute();
			$msgBox = alertBox($fileDescUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Edit Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'editComment') {
        // Validation
		if($_POST['commentText'] == "") {
            $msgBox = alertBox($commentsReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$commentText = $_POST['commentText'];
			$commentId = $mysqli->real_escape_string($_POST['commentId']);

            $stmt = $mysqli->prepare("UPDATE
										filecomments
									SET
										commentText = ?
									WHERE
										commentId = ?"
			);
			$stmt->bind_param('ss',
									$commentText,
									$commentId
			);
			$stmt->execute();
			$msgBox = alertBox($commentsUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['commentText'] = '';
			$stmt->close();
		}
	}

	// Delete Comment
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteComment') {
		$commentId = $mysqli->real_escape_string($_POST['commentId']);
		$stmt = $mysqli->prepare("DELETE FROM filecomments WHERE commentId = ?");
		$stmt->bind_param('s', $commentId);
		$stmt->execute();
		$msgBox = alertBox($commentDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Add New Comment
    if (isset($_POST['submit']) && $_POST['submit'] == 'newComment') {
        // Validation
		if($_POST['commentText'] == "") {
            $msgBox = alertBox($commentsReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$commentText = $_POST['commentText'];
			$commentDate = date("Y-m-d H:i:s");
			$adminFullName = $mysqli->real_escape_string($_POST['adminFullName']);
			$clientEmail = $mysqli->real_escape_string($_POST['clientEmail']);
			$fileTitle = $mysqli->real_escape_string($_POST['fileTitle']);
			$projectId = $mysqli->real_escape_string($_POST['projectId']);

			$stmt = $mysqli->prepare("
								INSERT INTO
									filecomments(
										projectId,
										fileId,
										adminId,
										commentText,
										commentDate
									) VALUES (
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('sssss',
								$projectId,
								$fileId,
								$adminId,
								$commentText,
								$commentDate
			);
			$stmt->execute();

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $fileCommentEmailSubject1.' '.$adminFullName.' '.$fileCommentEmailSubject2.' '.$fileTitle;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$fileText.': '.$fileTitle.'</p>';
			$message .= '<p>'.$fromText.': '.$adminFullName.'</p>';
			$message .= '<p>'.$commentText.'</p>';
			$message .= '<hr>';
			$message .= '<p>'.$emailLink.'</p>';
			$message .= '<p>'.$emailThankYou.'</p>';
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($clientEmail, $subject, $message, $headers)) {
				$msgBox = alertBox($newCommentSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['commentText'] = '';
            $stmt->close();
		}
	}

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
				projectfiles.fileId = ".$fileId;
    $res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Get Comment Data
	$qry = "SELECT
				filecomments.commentId,
				filecomments.projectId,
				filecomments.fileId,
				filecomments.adminId,
				filecomments.clientId,
				filecomments.commentText,
				DATE_FORMAT(filecomments.commentDate,'%b %d %Y at %h:%i %p') AS commentDate,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clients.clientAvatar,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
				admins.adminAvatar
			FROM
				filecomments
				LEFT JOIN clients ON filecomments.clientId = clients.clientId
				LEFT JOIN admins ON filecomments.adminId = admins.adminId
			WHERE filecomments.fileId = ".$fileId;
	$results = mysqli_query($mysqli, $qry) or die('-2'.mysqli_error());

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
		</ul>
	</div>

	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row mt10">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-file"></i> <?php echo $fileTitleText; ?>:</td>
						<td class="infoVal"><?php echo clean($row['fileTitle']); ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateUploadedText; ?>:</td>
						<td class="infoVal"><?php echo $row['fileDate']; ?></td>
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
						<td class="infoKey"><i class="fa fa-user"></i> <?php echo $uploadedByText; ?>:</td>
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
			<?php echo nl2br(clean($row['fileDesc'])); ?>
		</div>

		<a data-toggle="modal" data-target="#editFile" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editFileDescBtn; ?></a>

		<div class="modal fade" id="editFile" tabindex="-1" role="dialog" aria-labelledby="editFile" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $editFileDescBtn; ?></h4>
					</div>
					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="fileDesc"><?php echo $descriptionText; ?></label>
								<textarea class="form-control" name="fileDesc" required="" rows="6"><?php echo clean($row['fileDesc']); ?></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="input" name="submit" value="editFile" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<div class="content">
		<?php
			//Get File Extension
			$ext = substr(strrchr($row['fileUrl'],'.'), 1);
			$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');

			if (in_array($ext, $imgExts)) {
				echo '<p><img alt="'.clean($row['fileTitle']).'" src="../'.$uploadsDir.$row['folderUrl'].'/'.$row['fileUrl'].'" class="img-responsive" /></p>';
			} else {
				echo '
						<div class="alertMsg info"><i class="fa fa-info-circle"></i> '.$noPreviewMsg.': '.clean($row['fileTitle']).'</div>
						<p>
							<a href="../'.$uploadsDir.$row['folderUrl'].'/'.$row['fileUrl'].'" class="btn btn-info btn-icon" target="_blank">
							<i class="fa fa-download"></i> '.$downloadFileMsg.': '.$row['fileTitle'].'</a>
						</p>
					';
			}
		?>
	</div>

	<div class="content">
		<?php
			if(mysqli_num_rows($results) > 0) {
				while ($cols = mysqli_fetch_assoc($results)) {
					if ($cols['adminId'] == '0') {
		?>
						<div class="well well-xs comments">
							<img src="<?php echo '../'.$avatarDir.$cols['clientAvatar']; ?>" alt="<?php echo clean($cols['theClient']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($cols['theClient']); ?>" />
							<h4>
								<?php echo clean($cols['theClient']).' '.$commentedText; ?>
								<small class="text-muted"><?php echo $onText.' '.$cols['commentDate']; ?></small>
								<small class="pull-right">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentTooltip; ?>"><a class="text-success" data-toggle="modal" href="#editComment<?php echo $cols['commentId']; ?>"><i class="fa fa-edit"></i></a></span>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentTooltip; ?>"><a class="text-danger" data-toggle="modal" href="#deleteComment<?php echo $cols['commentId']; ?>"><i class="fa fa-times"></i></a></span>
								</small>
							</h4>
							<small><?php echo nl2br(clean($cols['commentText'])); ?></small>
						</div>
		<?php
					} else {
		?>
						<div class="well well-xs comments">
							<img src="<?php echo '../'.$avatarDir.$cols['adminAvatar']; ?>" alt="<?php echo clean($cols['theAdmin']); ?>" class="avatar" data-toggle="tooltip" title="<?php echo clean($cols['theAdmin']); ?>" />
							<h4>
								<?php echo clean($cols['theAdmin']).' '.$commentedText; ?>
								<small class="text-muted"><?php echo $onText.' '.$cols['commentDate']; ?></small>
								<small class="pull-right">
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $editCommentTooltip; ?>"><a class="text-success" data-toggle="modal" href="#editComment<?php echo $cols['commentId']; ?>"><i class="fa fa-edit"></i></a></span>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteCommentTooltip; ?>"><a class="text-danger" data-toggle="modal" href="#deleteComment<?php echo $cols['commentId']; ?>"><i class="fa fa-times"></i></a></span>
								</small>
							</h4>
							<small><?php echo nl2br(clean($cols['commentText'])); ?></small>
						</div>
		<?php
					}
		?>
					<div id="editComment<?php echo $cols['commentId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">

									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
										<h4 class="modal-title"><?php echo $editCommentTooltip; ?></h4>
									</div>

									<form action="" method="post">
										<div class="modal-body">
											<div class="form-group">
												<textarea class="form-control" required="" name="commentText" rows="6"><?php echo clean($cols['commentText']); ?></textarea>
											</div>
										</div>

										<div class="modal-footer">
											<input type="hidden" name="commentId" value="<?php echo $cols['commentId']; ?>" />
											<button type="input" name="submit" value="editComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>

								</div>
							</div>
						</div>

						<div class="modal fade" id="deleteComment<?php echo $cols['commentId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteCommentConf; ?></p>
										</div>
										<div class="modal-footer">
											<input type="hidden" name="commentId" value="<?php echo $cols['commentId']; ?>" />
											<button type="input" name="submit" value="deleteComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
											<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
										</div>
									</form>
								</div>
							</div>
						</div>
		<?php
				}
			} else {
		?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noCommentsFound; ?>
				</div>
		<?php } ?>
	</div>

	<div class="content last">
		<h4><?php echo $addCommentTitle; ?></h4>
		<form action="" method="post">
			<div class="form-group">
				<textarea class="form-control" name="commentText" rows="6"><?php echo isset($_POST['commentText']) ? $_POST['commentText'] : ''; ?></textarea>
			</div>
			<input type="hidden" name="adminFullName" value="<?php echo $adminFullName; ?>" />
			<input type="hidden" name="clientEmail" value="<?php echo clean($rows['clientEmail']); ?>" />
			<input type="hidden" name="fileTitle" value="<?php echo clean($row['fileTitle']); ?>" />
			<input type="hidden" name="projectId" value="<?php echo clean($row['projectId']); ?>" />
			<button type="input" name="submit" value="newComment" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $addCommentBtn; ?></button>
		</form>
	</div>
<?php } ?>