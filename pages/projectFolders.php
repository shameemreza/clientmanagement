<?php
	$projectId = $_GET['projectId'];
	$getId = 'projectId='.$projectId;
	$pagPages = '10';

	// Get the File Uploads Folder from the Site Settings
	$uploadsDir = $set['uploadPath'];

	// Create a New Folder
    if (isset($_POST['submit']) && $_POST['submit'] == 'newFolder') {
        // Validation
        if($_POST['folderTitle'] == "") {
            $msgBox = alertBox($folderNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['folderDesc'] == "") {
            $msgBox = alertBox($folderDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$projectName = $mysqli->real_escape_string($_POST['projectName']);
			$folderTitle = $mysqli->real_escape_string($_POST['folderTitle']);
			$folderDesc = $_POST['folderDesc'];
			$clientFullName = $mysqli->real_escape_string($_POST['clientFullName']);
			$folderDate = date("Y-m-d H:i:s");

			// Replace any spaces with an underscore
			// And set to all lower-case
			$clientFullName = str_replace(' ', '_', $clientFullName);
			$clientNewName = strtolower($clientFullName);

			$folderName = str_replace(' ', '_', $folderTitle);
			$folderNewName = strtolower($folderName);

			$fullFolderName = $clientNewName.'_pid-'.$projectId.'_'.$folderNewName;

			// Create the new Folder
			if (mkdir($uploadsDir.$fullFolderName, 0755, true)) {
				$newDir = $uploadsDir.$fullFolderName;
			} else {
				$msgBox = alertBox($newFolderError, "<i class='fa fa-times-circle'></i>", "danger");
			}

            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    projectfolders(
                                        projectId,
                                        clientId,
                                        folderTitle,
										folderDesc,
										folderUrl,
										folderDate
                                    ) VALUES (
                                        ?,
                                        ?,
                                        ?,
										?,
										?,
										?
                                    )
			");
            $stmt->bind_param('ssssss',
								$projectId,
								$clientId,
								$folderTitle,
								$folderDesc,
								$fullFolderName,
								$folderDate
            );
            $stmt->execute();

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $folderEmailSubject.' '.$projectName;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$projectTableHead.': '.$projectName.'</p>';
			$message .= '<p>'.$folderNameText.': '.$folderTitle.'</p>';
			$message .= '<p>'.$fromText.': '.$clientFullName.'</p>';
			$message .= '<p>'.$folderDesc.'</p>';
			$message .= '<hr>';
			$message .= $emailLink;
			$message .= $emailThankYou;
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($managers, $subject, $message, $headers)) {
				$msgBox = alertBox($newFolderCreatedMsg, "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			// Clear the Form of values
			$_POST['folderTitle'] = $_POST['folderDesc'] = '';
            $stmt->close();
		}
	}

	// Delete Folder
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteFolder') {
		$isFiles = '';
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$folderUrl = $_POST['folderUrl'];

		// Check for Files in the Folder
		$check = $mysqli->query("SELECT 'X' FROM projectfiles WHERE folderId = '".$deleteId."'");
		if ($check->num_rows) {
			$isFiles = 'true';
		}

		if ($isFiles != 'true') {
			// Delete the DB Record
			$stmt = $mysqli->prepare("DELETE FROM projectfolders WHERE folderId = ?");
			$stmt->bind_param('s', $deleteId);
			$stmt->execute();

			// Delete the Folder on the host
			if (is_dir($uploadsDir.$folderUrl)) {
				rmdir($uploadsDir.$folderUrl);
				$msgBox = alertBox("The Project Folder has been Deleted.", "<i class='fa fa-check-square'></i>", "success");
			} else {
				$msgBox = alertBox($deleteFolderErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
			$stmt->close();
		} else {
			$msgBox = alertBox($foldNotEmptyMsg, "<i class='fa fa-times-circle'></i>", "danger");
		}
    }
	
	// Include Pagination Class
	include('includes/getpagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM projectfolders WHERE projectId = ".$projectId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

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
				UNIX_TIMESTAMP(projectfolders.folderDate) AS orderDate,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectfolders
				LEFT JOIN clients ON projectfolders.clientId = clients.clientId
				LEFT JOIN admins ON projectfolders.adminId = admins.adminId
			WHERE
				projectfolders.projectId = ".$projectId."
			ORDER BY orderDate ".$pages->get_limit();
    $res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());

	$query = "SELECT clientId, projectName FROM clientprojects WHERE projectId = ".$projectId;
    $result = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());
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
			<li class="active"><a href="" data-toggle="tab"><i class="fa fa-folder-o"></i> <?php echo $projectFoldersLink; ?></a></li>
			<li><a href="index.php?page=projectFiles&projectId=<?php echo $projectId; ?>"><i class="fa fa-file-o"></i> <?php echo $projectFilesLink; ?></a></li>
			<li class="pull-right"><a href="#newFolder" data-toggle="modal"><i class="fa fa-folder-open-o"></i> <?php echo $newFolderLink; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<?php if ($msgBox) { echo $msgBox; } ?>
		<h3>
			<a href="index.php?page=viewProject&projectId=<?php echo $projectId; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
				<?php echo clean($col['projectName']); ?>
			</a>
			<?php echo $pageName; ?>
		</h3>
		<p><?php echo $projectFoldersQuip; ?></p>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin mt20">
				<i class="fa fa-minus-square-o"></i> <?php echo $noFoldersMsg; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $folderNameText; ?></th>
						<th><?php echo $descText; ?></th>
						<th><?php echo $dateCreatedText; ?></th>
						<th><?php echo $createdByText; ?></th>
						<th></th>
					</tr>
					<?php while ($row = mysqli_fetch_assoc($res)) { ?>
						<tr>
							<td data-th="<?php echo $folderNameText; ?>">
								<a href="index.php?page=viewFolder&folderId=<?php echo $row['folderId']; ?>" data-toggle="tooltip" data-placement="right" title="View Folder">
									<?php echo clean($row['folderTitle']); ?>
								</a>
							</td>
							<td data-th="<?php echo $descText; ?>">
								<span data-toggle="tooltip" data-placement="top" title="<?php echo ellipsis($row['folderDesc'],200); ?>">
									<?php echo ellipsis($row['folderDesc'],50); ?>
								</span>
							</td>
							<td data-th="<?php echo $dateCreatedText; ?>"><?php echo $row['folderDate']; ?></td>
							<td data-th="<?php echo $createdByText; ?>">
								<?php
									if ($row['adminId'] != '0') {
										echo clean($row['theAdmin']);
									} else {
										echo clean($row['theClient']);
									}
								?>
							</td>
							<td data-th="<?php echo $actionsText; ?>">
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewFolderTooltip; ?>">
									<a href="index.php?page=viewFolder&folderId=<?php echo $row['folderId']; ?>"><i class="fa fa-folder edit"></i></a>
								</span>
								<?php if ($row['adminId'] == '0') { ?>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteFolderTooltip; ?>">
										<a href="#deleteFolder<?php echo $row['folderId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
									</span>
								<?php } else { ?>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteDisabledTooltip; ?>">
										<i class="fa fa-trash-o disabled"></i>
									</span>
								<?php } ?>
							</td>
						</tr>

						<div class="modal fade" id="deleteFolder<?php echo $row['folderId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteFoldConf.' '.clean($row['folderTitle']); ?>?</p>
										</div>
										<div class="modal-footer">
											<input name="deleteId" type="hidden" value="<?php echo $row['folderId']; ?>" />
											<input name="folderUrl" type="hidden" value="<?php echo $row['folderUrl']; ?>" />
											<button type="input" name="submit" value="deleteFolder" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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
			if ($total > $pagPages) {
				echo $pages->page_links();
			}
		}
		?>
	</div>

	<div class="modal fade" id="newFolder" tabindex="-1" role="dialog" aria-labelledby="newFolder" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $newFolderModal; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<p><?php echo $newFolderQuip; ?></p>

						<div class="form-group">
							<label for="folderTitle"><?php echo $folderNameText; ?></label>
							<input type="text" class="form-control" name="folderTitle" required="" value="<?php echo isset($_POST['folderTitle']) ? $_POST['folderTitle'] : ''; ?>">
							<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="folderDesc"><?php echo $descText; ?></label>
							<textarea class="form-control" name="folderDesc" required="" rows="4"><?php echo isset($_POST['folderDesc']) ? $_POST['folderDesc'] : ''; ?></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="clientFullName" value="<?php echo $clientFullName; ?>" />
						<input type="hidden" name="projectName" value="<?php echo clean($col['projectName']); ?>" />
						<button type="input" name="submit" value="newFolder" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $createFolderBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>