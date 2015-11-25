<?php
	$entryId = $_GET['entryId'];
	$jsFile = 'projectAccount';

	// Edit Request
    if (isset($_POST['submit']) && $_POST['submit'] == 'editEntry') {
        // Validation
		if($_POST['entryTitle'] == "") {
            $msgBox = alertBox($entryNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['entryUsername'] == "") {
            $msgBox = alertBox($entryUsernameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['entryPass'] == "") {
            $msgBox = alertBox($entryPasswordReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$entryTitle = encryptIt($_POST['entryTitle']);
			$entryDesc = encryptIt($_POST['entryDesc']);
			$entryUsername = encryptIt($_POST['entryUsername']);
			$entryPass = encryptIt($_POST['entryPass']);
			$entryUrl = encryptIt($_POST['entryUrl']);
			$entryNotes = encryptIt($_POST['entryNotes']);

            $stmt = $mysqli->prepare("UPDATE
										pwentries
									SET
										entryTitle = ?,
										entryDesc = ?,
										entryUsername = ?,
										entryPass = ?,
										entryUrl = ?,
										entryNotes = ?
									WHERE
										entryId = ?"
			);
			$stmt->bind_param('sssssss',
									$entryTitle,
									$entryDesc,
									$entryUsername,
									$entryPass,
									$entryUrl,
									$entryNotes,
									$entryId
			);
			$stmt->execute();
			$msgBox = alertBox($accountEntryUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Get Data
	$sqlStmt = "SELECT
					pwentries.entryId,
					pwentries.projectId,
					pwentries.adminId,
					pwentries.clientId,
					pwentries.entryTitle,
					pwentries.entryDesc,
					pwentries.entryUsername,
					pwentries.entryPass,
					pwentries.entryUrl,
					pwentries.entryNotes,
					DATE_FORMAT(pwentries.entryDate,'%M %d, %Y') AS entryDate,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
					clientprojects.projectName
				FROM
					pwentries
					LEFT JOIN admins ON pwentries.adminId = admins.adminId
					LEFT JOIN clients ON pwentries.clientId = clients.clientId
					LEFT JOIN clientprojects ON pwentries.projectId = clientprojects.projectId
				WHERE pwentries.entryId = ".$entryId;
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Decrypt Data
	if ($row['entryTitle'] != '') { $entryTitle = decryptIt($row['entryTitle']); } else { $entryTitle = ''; }
	if ($row['entryDesc'] != '') { $entryDesc = decryptIt($row['entryDesc']); } else { $entryDesc = ''; }
	if ($row['entryUsername'] != '') { $entryUsername = decryptIt($row['entryUsername']); } else { $entryUsername = ''; }
	if ($row['entryPass'] != '') { $entryPass = decryptIt($row['entryPass']); } else { $entryPass = ''; }
	if ($row['entryUrl'] != '') { $entryUrl = decryptIt($row['entryUrl']); } else { $entryUrl = ''; }
	if ($row['entryNotes'] != '') { $entryNotes = decryptIt($row['entryNotes']); } else { $entryNotes = ''; }

	// Only allow access to the Assigned Manager or Admins
	$qry = "SELECT
				assignedprojects.assignedTo
			FROM
				assignedprojects
				LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
			WHERE assignedprojects.projectId = ".$row['projectId'];
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
	<div class="content">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-quote-left"></i> <?php echo $titleTableHead; ?>:</td>
						<td class="infoVal"><?php echo $entryTitle; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-link"></i> <?php echo $urlText; ?>:</td>
						<td class="infoVal"><?php echo $entryUrl; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-folder-open-o"></i> <?php echo $projectText; ?>:</td>
						<td class="infoVal">
							<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($row['projectName']); ?>
							</a>
						</td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateCreatedTableHead; ?>:</td>
						<td class="infoVal"><?php echo $row['entryDate']; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-lock"></i> <?php echo $usernameText; ?>:</td>
						<td class="infoVal"><?php echo $entryUsername; ?></td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-user"></i> <?php echo $enteredByText; ?>:</td>
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
			<strong><?php echo $descriptionText; ?>:</strong> <?php echo nl2br($entryDesc); ?>
		</div>

		<?php if ($entryNotes != '') { ?>
			<div class="well well-sm bg-trans no-margin mt20">
				<strong><?php echo $notesText; ?>:</strong> <?php echo nl2br($entryNotes); ?>
			</div>
		<?php } ?>

		<a data-toggle="modal" data-target="#editEntry" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $updateEntryBtn; ?></a>

		<div id="editEntry" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">

					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
						<h4 class="modal-title"><?php echo $updateEntryBtn; ?></h4>
					</div>

					<form action="" method="post">
						<div class="modal-body">
							<div class="form-group">
								<label for="entryTitle"><?php echo $accountText; ?></label>
								<input type="text" class="form-control" required="" name="entryTitle" value="<?php echo $entryTitle; ?>" />
							</div>
							<div class="form-group">
								<label for="entryDesc"><?php echo $descriptionText; ?></label>
								<textarea class="form-control" name="entryDesc" required="" rows="3"><?php echo $entryDesc; ?></textarea>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="entryUsername"><?php echo $usernameText; ?></label>
										<input type="text" class="form-control" required="" name="entryUsername" value="<?php echo $entryUsername; ?>" />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="entryPass"><?php echo $passwordField; ?></label>
										<input type="password" class="form-control" required="" name="entryPass" id="newPass" value="<?php echo $entryPass; ?>" />
										<span class="help-block">
											<a href="" id="show2" class="btn btn-warning btn-xs"><?php echo $showPlainText; ?></a>
											<a href="" id="hide2" class="btn btn-info btn-xs"><?php echo $hidePlainText; ?></a>
										</span>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label for="entryUrl"><?php echo $urlText; ?></label>
								<input type="text" class="form-control" name="entryUrl" value="<?php echo $entryUrl; ?>" />
							</div>
							<div class="form-group">
								<label for="entryNotes"><?php echo $notesText; ?></label>
								<textarea class="form-control" name="entryNotes" rows="3"><?php echo $entryNotes; ?></textarea>
							</div>
						</div>

						<div class="modal-footer">
							<button type="input" name="submit" value="editEntry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateAccEntryBtn; ?></button>
							<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
<?php } ?>