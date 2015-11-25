<?php
	$projectId = $_GET['projectId'];
	$getId = 'projectId='.$projectId;
	$pagPages = '10';

	// Add New Discussion Topic
    if (isset($_POST['submit']) && $_POST['submit'] == 'newDiscussion') {
        // Validation
		if($_POST['discussionTitle'] == "") {
            $msgBox = alertBox($discTopicReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['discussionText'] == "") {
            $msgBox = alertBox($discTextReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$discussionTitle = $mysqli->real_escape_string($_POST['discussionTitle']);
			$discussionText = $_POST['discussionText'];
			$entryDate = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									projectdiscus(
										projectId,
										adminId,
										discussionTitle,
										discussionText,
										discussionDate
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
								$adminId,
								$discussionTitle,
								$discussionText,
								$discussionDate
			);
			$stmt->execute();
			$msgBox = alertBox($newDiscSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['discussionTitle'] = $_POST['discussionText'] = '';
			$stmt->close();
		}
	}

	// Delete Discussion
	if (isset($_POST['submit']) && $_POST['submit'] == 'deletedisc') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);

		// Delete the Discussion Topic
		$stmt = $mysqli->prepare("DELETE FROM projectdiscus WHERE discussionId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();

		// Delete all of the comments associated with the Discussion Topic
		$stmt = $mysqli->prepare("DELETE FROM replies WHERE discussionId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($discThreadDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Include Pagination Class
	include('includes/getpagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM projectdiscus WHERE projectId = ".$projectId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Project Discussions
	$sql = "SELECT
				projectdiscus.discussionId,
				projectdiscus.projectId,
				projectdiscus.adminId,
				projectdiscus.clientId,
				projectdiscus.discussionTitle,
				projectdiscus.discussionText,
				DATE_FORMAT(projectdiscus.discussionDate,'%W, %M %e, %Y') AS discussionDate,
				UNIX_TIMESTAMP(projectdiscus.discussionDate) AS orderDate,
				DATE_FORMAT(projectdiscus.lastUpdated,'%W, %M %e, %Y') AS lastUpdated,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectdiscus
				LEFT JOIN clients ON projectdiscus.clientId = clients.clientId
				LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
			WHERE
				projectdiscus.projectId = ".$projectId."
			ORDER BY orderDate ".$pages->get_limit();
	$res = mysqli_query($mysqli, $sql) or die('-1'.mysqli_error());

	// Only allow access to the Assigned Manager or Admins
	$qry = "SELECT
				assignedprojects.assignedTo,
				clientprojects.projectName
			FROM
				assignedprojects
				LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
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
			<li><a href="index.php?action=viewProject&projectId=<?php echo $projectId; ?>"><i class="fa fa-folder-open"></i> <?php echo clean($rows['projectName']); ?></a></li>
			<li class="pull-right"><a href="#newDiscussion" data-toggle="modal"><i class="fa fa-comments-o"></i> <?php echo $createNewDiscTabLink; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noProjDiscFound; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table no-margin">
				<tbody>
					<tr class="primary">
						<th><?php echo $topicText; ?></th>
						<th><?php echo $commentsText; ?></th>
						<th><?php echo $createdByTableHead; ?></th>
						<th><?php echo $dateCreatedTableHead; ?></th>
						<th><?php echo $lastUpdatedText; ?></th>
						<th></th>
					</tr>
					<?php while ($row = mysqli_fetch_assoc($res)) { ?>
						<tr>
							<td data-th="<?php echo $topicText; ?>">
								<a href="index.php?action=viewDiscussion&discussionId=<?php echo $row['discussionId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewDiscTooltip; ?>">
									<?php echo clean($row['discussionTitle']); ?>
								</a>
							</td>
							<td data-th="<?php echo $commentsText; ?>">
								<span data-toggle="tooltip" data-placement="top" title="<?php echo ellipsis($row['discussionText'],200); ?>">
									<?php echo ellipsis($row['discussionText'],50); ?>
								</span>
							</td>
							<td data-th="<?php echo $createdByTableHead; ?>">
								<?php
									if ($row['adminId'] != '0') {
										echo clean($row['theAdmin']);
									} else {
										echo clean($row['theClient']);
									}
								?>
							</td>
							<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['discussionDate']; ?></td>
							<td data-th="<?php echo $lastUpdatedText; ?>"><?php echo $row['lastUpdated']; ?></td>
							<td data-th="<?php echo $actionsText; ?>">
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewDiscTooltip; ?>">
									<a href="index.php?action=viewDiscussion&discussionId=<?php echo $row['discussionId']; ?>"><i class="fa fa-comments edit"></i></a>
								</span>
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteDiscTooltip; ?>">
									<a href="#deletedisc<?php echo $row['discussionId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
								</span>
							</td>
						</tr>

						<div class="modal fade" id="deletedisc<?php echo $row['discussionId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteDiscConf1.' '.clean($row['discussionTitle']).' '.$deleteDiscConf2; ?></p>
										</div>
										<div class="modal-footer">
											<input name="deleteId" type="hidden" value="<?php echo $row['discussionId']; ?>" />
											<button type="input" name="submit" value="deletedisc" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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

	<div class="modal fade" id="newDiscussion" tabindex="-1" role="dialog" aria-labelledby="newDiscussion" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
					<h4 class="modal-title"><?php echo $createNewDiscTabLink; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="discussionTitle"><?php echo $discTopicText; ?></label>
							<input type="text" class="form-control" name="discussionTitle" required="" value="<?php echo isset($_POST['discussionTitle']) ? $_POST['discussionTitle'] : ''; ?>">
							<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="discussionText"><?php echo $discTextText; ?></label>
							<textarea class="form-control" name="discussionText" required="" rows="6"><?php echo isset($_POST['discussionText']) ? $_POST['discussionText'] : ''; ?></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="input" name="submit" value="newDiscussion" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveNewDiscBtn; ?></button>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>