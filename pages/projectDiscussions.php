<?php
	$projectId = $_GET['projectId'];
	$getId = 'projectId='.$projectId;
	$pagPages = '10';

	// Add New Discussion Topic
    if (isset($_POST['submit']) && $_POST['submit'] == 'newDiscussion') {
        // Validation
		if($_POST['discussionTitle'] == "") {
            $msgBox = alertBox($discTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
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
										clientId,
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
								$clientId,
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
		$msgBox = alertBox($discDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
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

	$query = "SELECT clientId, projectName FROM clientprojects WHERE projectId = ".$projectId;
    $result = mysqli_query($mysqli, $query) or die('-2'.mysqli_error());
	$rows = mysqli_fetch_assoc($result);

	include 'includes/navigation.php';

	if ($rows['clientId'] != $clientId) {
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
			<li><a href="index.php?page=viewProject&projectId=<?php echo $projectId; ?>"><i class="fa fa-folder-open"></i> <?php echo clean($rows['projectName']); ?></a></li>
			<li class="pull-right"><a href="#newDiscussion" data-toggle="modal"><i class="fa fa-comments-o"></i> <?php echo $newDiscussionLink; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noDiscussions; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table no-margin">
				<tbody>
					<tr class="primary">
						<th><?php echo $topicText; ?></th>
						<th><?php echo $commentsText; ?></th>
						<th><?php echo $createdByText; ?></th>
						<th><?php echo $dateCreatedText; ?></th>
						<th><?php echo $lastUpdatedText; ?></th>
						<th></th>
					</tr>
					<?php while ($row = mysqli_fetch_assoc($res)) { ?>
						<tr>
							<td data-th="<?php echo $topicText; ?>">
								<a href="index.php?page=viewDiscussion&discussionId=<?php echo $row['discussionId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewDiscText; ?>">
									<?php echo clean($row['discussionTitle']); ?>
								</a>
							</td>
							<td data-th="<?php echo $commentsText; ?>">
								<span data-toggle="tooltip" data-placement="top" title="<?php echo ellipsis($row['discussionText'],200); ?>">
									<?php echo ellipsis($row['discussionText'],50); ?>
								</span>
							</td>
							<td data-th="<?php echo $createdByText; ?>">
								<?php
									if ($row['adminId'] != '0') {
										echo clean($row['theAdmin']);
									} else {
										echo clean($row['theClient']);
									}
								?>
							</td>
							<td data-th="<?php echo $dateCreatedText; ?>"><?php echo $row['discussionDate']; ?></td>
							<td data-th="<?php echo $lastUpdatedText; ?>"><?php echo $row['lastUpdated']; ?></td>
							<td data-th="<?php echo $actionsText; ?>">
								<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewDiscText;?>">
									<a href="index.php?page=viewDiscussion&discussionId=<?php echo $row['discussionId']; ?>"><i class="fa fa-comments edit"></i></a>
								</span>
								<?php if ($row['adminId'] == '0') { ?>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteDiscTooltip; ?>">
										<a href="#deletedisc<?php echo $row['discussionId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
									</span>
								<?php } else { ?>
									<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteDiscDisabled; ?>">
										<i class="fa fa-trash-o disabled"></i>
									</span>
								<?php } ?>
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
					<h4 class="modal-title"><?php echo $newDiscModal; ?></h4>
				</div>
				<form action="" method="post">
					<div class="modal-body">
						<div class="form-group">
							<label for="discussionTitle"><?php echo $discTitleField; ?></label>
							<input type="text" class="form-control" name="discussionTitle" required="" value="<?php echo isset($_POST['discussionTitle']) ? $_POST['discussionTitle'] : ''; ?>">
							<span class="help-block"><?php echo $eventTitleFieldHelp; ?></span>
						</div>
						<div class="form-group">
							<label for="discussionText"><?php echo $discTextField; ?></label>
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