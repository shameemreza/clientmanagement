<?php
	// Get the Max Upload Size allowed
    $maxUpload = (int)(ini_get('upload_max_filesize'));

	// Get the Template Uploads Folder from the Site Settings
	$uploadsDir = $set['templatesPath'];

	// Get the File Types allowed
	$fileExt = $set['fileTypesAllowed'];
	$allowed = preg_replace('/,/', ', ', $fileExt); // Replace the commas with a comma space
	$ftypes = array($fileExt);
	$ftypes_data = explode( ',', $fileExt );

	// Upload a New Template
    if (isset($_POST['submit']) && $_POST['submit'] == 'newTemplate') {
		// Validation
        if($_POST['templateName'] == "") {
            $msgBox = alertBox($tempNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['templateDesc'] == "") {
            $msgBox = alertBox($tempDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if(empty($_FILES['file']['name'])) {
            $msgBox = alertBox($selectTemplateFileReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Check file type
            $ext = substr(strrchr(basename($_FILES['file']['name']), '.'), 1);
            if (!in_array($ext, $ftypes_data)) {
                $msgBox = alertBox($templateUploadError, "<i class='fa fa-times-circle'></i>", "danger");
            } else {
				$templateName = $mysqli->real_escape_string($_POST['templateName']);
				$templateDesc = $_POST['templateDesc'];
				$templateDate = date("Y-m-d H:i:s");

				// Replace any spaces with an underscore
				// And set to all lower-case
				$newName = str_replace(' ', '_', $templateName);
				$fileNewName = strtolower($newName);

				// Set the upload path
				$uploadTo = $uploadsDir;
				$fileUrl = basename($_FILES['file']['name']);

				// Get the files original Ext
				$extension = end(explode(".", $fileUrl));

				// Generate a random string to append to the file's name
				$randomString=md5(uniqid(rand()));
				$appendName=substr($randomString, 0, 8);

				// Set the files name to the name set in the form
				// And add the original Ext
				$newfilename = $fileNewName.'-'.$appendName.'.'.$extension;
				$movePath = $uploadTo.'/'.$newfilename;

				$stmt = $mysqli->prepare("
                                    INSERT INTO
                                        templates(
                                            adminId,
                                            templateName,
                                            templateDesc,
                                            templateUrl,
                                            templateDate
                                        ) VALUES (
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?
                                        )");
                $stmt->bind_param('sssss',
                    $adminId,
                    $templateName,
                    $templateDesc,
                    $newfilename,
                    $templateDate
                );

                if (move_uploaded_file($_FILES['file']['tmp_name'], $movePath)) {
                    $stmt->execute();
					$msgBox = alertBox($templateUploadedMsg, "<i class='fa fa-check-square'></i>", "success");
					// Clear the Form of values
					$_POST['templateName'] = $_POST['templateDesc'] = '';
					$stmt->close();
				}
			}
		}
	}

	// Delete Template
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteTemplate') {
		$templateId = $mysqli->real_escape_string($_POST['templateId']);
		$templateUrl = $mysqli->real_escape_string($_POST['templateUrl']);

		// Delete the Template from the server
		$filePath = $uploadsDir.'/'.$templateUrl;

		if (file_exists($filePath)) {
			// Delete the Template
			unlink($filePath);

			// Delete the Record
			$stmt = $mysqli->prepare("DELETE FROM templates WHERE templateId = ?");
			$stmt->bind_param('s', $_POST['templateId']);
			$stmt->execute();
			$stmt->close();

			$msgBox = alertBox($templateDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		} else {
			$msgBox = alertBox($templateDeleteError, "<i class='fa fa-times-circle'></i>", "danger");
		}
    }

	// Get Template Data
    $query  = "SELECT
                    templates.templateId,
                    templates.adminId,
                    templates.templateName,
					templates.templateDesc,
					templates.templateUrl,
                    DATE_FORMAT(templates.templateDate,'%M %d, %Y') AS templateDate,
                    CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
                FROM
                    templates
                    LEFT JOIN admins ON templates.adminId = admins.adminId
                ";
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li class="pull-right"><a href="#newTemplate" data-toggle="modal"><i class="fa fa-upload"></i> <?php echo $uplNewTemplateTabLink; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> <?php echo $noTemplatesFound; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table">
			<tbody>
				<tr class="primary">
					<th><?php echo $templateText; ?></th>
					<th><?php echo $descriptionText; ?></th>
					<th><?php echo $uploadedByText; ?></th>
					<th><?php echo $dateUploadedText; ?></th>
					<th></th>
				</tr>
				<?php while ($row = mysqli_fetch_assoc($res)) { ?>
					<tr>
						<td data-th="<?php echo $descriptionText; ?>">
							<a href="index.php?action=viewTemplate&templateId=<?php echo $row['templateId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewTemplateTooltip; ?>">
								<?php echo clean($row['templateName']); ?>
							</a>
						</td>
						<td data-th="<?php echo $descriptionText; ?>"><?php echo ellipsis($row['templateDesc'],75); ?></td>
						<td data-th="<?php echo $uploadedByText; ?>"><?php echo clean($row['theAdmin']); ?></td>
						<td data-th="<?php echo $dateUploadedText; ?>"><?php echo $row['templateDate']; ?></td>
						<td data-th="<?php echo $actionsText; ?>">
							<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewTemplateTooltip; ?>">
								<a href="index.php?action=viewTemplate&templateId=<?php echo $row['templateId']; ?>"><i class="fa fa-file-text edit"></i></a>
							</span>
							<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteTemplateTooltip; ?>">
								<a href="#deleteTemplate<?php echo $row['templateId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
							</span>
						</td>
					</tr>

					<div class="modal fade" id="deleteTemplate<?php echo $row['templateId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<form action="" method="post">
									<div class="modal-body">
										<p class="lead"><?php echo $deleteTemplateConf.' '.clean($row['templateName']); ?>?</p>
									</div>
									<div class="modal-footer">
										<input name="templateId" type="hidden" value="<?php echo $row['templateId']; ?>" />
										<input name="templateUrl" type="hidden" value="<?php echo $row['templateUrl']; ?>" />
										<button type="input" name="submit" value="deleteTemplate" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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

<div class="modal fade" id="newTemplate" tabindex="-1" role="dialog" aria-labelledby="newTemplate" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $uplNewTemplateTabLink; ?></h4>
			</div>
			<form action="" method="post" enctype="multipart/form-data">
				<div class="modal-body">
					<p>
						<small>
							<strong><?php echo $allowedFileTypesQuip; ?></strong> <?php echo $allowed; ?><br />
							<strong><?php echo $maxFileSizeQuip; ?></strong> <?php echo $maxUpload.' '.$mbText; ?>.
						</small>
					</p>
					<div class="form-group">
						<label for="templateName"><?php echo $templateNameText; ?></label>
						<input type="text" class="form-control" name="templateName" required="" value="<?php echo isset($_POST['templateName']) ? $_POST['templateName'] : ''; ?>">
						<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
					</div>
					<div class="form-group">
						<label for="templateDesc"><?php echo $descriptionText; ?></label>
						<textarea class="form-control" name="templateDesc" required="" rows="4"><?php echo isset($_POST['templateDesc']) ? $_POST['templateDesc'] : ''; ?></textarea>
					</div>
					<div class="form-group">
						<label for="file"><?php echo $selectFileField; ?></label>
						<input type="file" id="file" name="file" required="">
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="newTemplate" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadTemplateBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>