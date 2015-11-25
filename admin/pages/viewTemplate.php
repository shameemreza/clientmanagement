<?php
	$templateId = $_GET['templateId'];

	// Get the Template Uploads Folder from the Site Settings
	$uploadsDir = $set['templatesPath'];

	// Edit Template Description
    if (isset($_POST['submit']) && $_POST['submit'] == 'editTemplate') {
        // Validation
		if($_POST['templateDesc'] == "") {
            $msgBox = alertBox($tempDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$templateDesc = $_POST['templateDesc'];

            $stmt = $mysqli->prepare("UPDATE
										templates
									SET
										templateDesc = ?
									WHERE
										templateId = ?"
			);
			$stmt->bind_param('ss',
									$templateDesc,
									$templateId
			);
			$stmt->execute();
			$msgBox = alertBox($tempDescUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
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
				WHERE templates.templateId = ".$templateId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li class="pull-right"><a href="index.php?action=templates"><i class="fa fa-file-o"></i> <?php echo $templatesText; ?></a></li>
	</ul>
</div>

<div class="content">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="row mt10">
		<div class="col-md-6">
			<table class="infoTable">
				<tr>
					<td class="infoKey"><i class="fa fa-file"></i> <?php echo $templateNameText; ?>:</td>
					<td class="infoVal"><?php echo clean($row['templateName']); ?></td>
				</tr>
				<tr>
					<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateUploadedText; ?>:</td>
					<td class="infoVal"><?php echo $row['templateDate']; ?></td>
				</tr>
			</table>
		</div>
		<div class="col-md-6">
			<table class="infoTable">
				<tr>
					<td class="infoKey"><i class="fa fa-user"></i> <?php echo $uploadedByText; ?>:</td>
					<td class="infoVal"><?php echo clean($row['theAdmin']); ?></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="well well-sm bg-trans no-margin mt20">
		<?php echo nl2br(clean($row['templateDesc'])); ?>
	</div>

	<a data-toggle="modal" data-target="#editTemplate" class="btn btn-success btn-icon mt20"><i class="fa fa-edit"></i> <?php echo $editTempDescBtn; ?></a>
</div>

<div class="content last">
	<?php
		//Get Template Extension
		$ext = substr(strrchr($row['templateUrl'],'.'), 1);
		$imgExts = array('gif', 'GIF', 'jpg', 'JPG', 'jpeg', 'JPEG', 'png', 'PNG', 'tiff', 'TIFF', 'tif', 'TIF', 'bmp', 'BMP');

		if (in_array($ext, $imgExts)) {
			echo '<p><img alt="'.clean($row['templateName']).'" src="'.$uploadsDir.$row['templateUrl'].'" class="img-responsive" /></p>';
		} else {
			echo '
					<div class="alertMsg info"><i class="fa fa-info-circle"></i> '.$notempPreviewMsg.' '.clean($row['templateName']).'</div>
					<p>
						<a href="'.$uploadsDir.$row['templateUrl'].'" class="btn btn-info btn-icon" target="_blank">
						<i class="fa fa-download"></i> '.$downloadTempMsg.' '.$row['templateName'].'</a>
					</p>
				';
		}
	?>
</div>

<div class="modal fade" id="editTemplate" tabindex="-1" role="dialog" aria-labelledby="editTemplate" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $editTempDescBtn; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<div class="form-group">
						<label for="templateDesc"><?php echo $descriptionText; ?></label>
						<textarea class="form-control" name="templateDesc" required="" rows="6"><?php echo clean($row['templateDesc']); ?></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="editTemplate" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>