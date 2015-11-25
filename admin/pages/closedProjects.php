<?php
	$pagPages = '10';
	
	// Delete Project
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteClient') {
		$projectId = $mysqli->real_escape_string($_POST['projectId']);
		$stmt = $mysqli->prepare("DELETE FROM clientprojects WHERE projectId = ?");
		$stmt->bind_param('s', $projectId);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($projectDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM clientprojects WHERE archiveProj = 1");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

	// Get All Projects
	$stmt  = "SELECT
				clientprojects.projectId,
				clientprojects.createdBy,
				clientprojects.clientId,
				clientprojects.projectName,
				clientprojects.projectFee,
				clientprojects.projectPayments,
				DATE_FORMAT(clientprojects.dueDate,'%M %e, %Y') AS dueDate,
				UNIX_TIMESTAMP(clientprojects.dueDate) AS orderDate,
				DATE_FORMAT(clientprojects.archiveDate,'%M %e, %Y') AS archiveDate,
				assignedprojects.assignedTo,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
				CONCAT(clientFirstName,' ',clientLastName) AS theClient
			FROM
				clientprojects
				LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE
				clientprojects.archiveProj = 1
			ORDER BY orderDate, clientprojects.clientId ".$pages->get_limit();
	$results = mysqli_query($mysqli, $stmt) or die('-1' . mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li><a href="index.php?action=openProjects"><i class="fa fa-folder-open"></i> <?php echo $openProjNavLink; ?></a></li>
		<li class="active"><a href="" data-toggle="tab"><i class="fa fa-folder"></i> <?php echo $pageNameclosedProjects; ?></a></li>
		<li class="pull-right"><a href="index.php?action=newProject"><i class="fa fa-plus"></i> <?php echo $pageNamenewProject; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($results) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> <?php echo $noClosedProj; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table no-margin">
			<tbody>
				<tr class="primary">
					<th><?php echo $projectText; ?></th>
					<th><?php echo $clientText; ?></th>
					<th><?php echo $assignedToText; ?></th>
					<?php if ($set['enablePayments'] == '1') { ?>
						<th><?php echo $projectFeeText; ?></th>
						<th><?php echo $paymentsMadeText; ?></th>
					<?php } ?>
					<th><?php echo $dateDueText; ?></th>
					<th><?php echo $dateClosedText; ?></th>
					<th></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($results)) {
						if ($row['projectFee'] != '') { $projectFee = $curSym.format_amount($row['projectFee'], 2); } else { $projectFee = ''; }
				?>
						<tr>
							<td data-th="<?php echo $projectText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
									<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $clientText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
									<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>"><?php echo clean($row['theClient']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $assignedToText; ?>"><?php echo clean($row['theAdmin']); ?></td>
							<?php if ($set['enablePayments'] == '1') { ?>
								<td data-th="<?php echo $percentCompleteText; ?>"><?php echo $projectFee; ?></td>
								<td data-th="<?php echo $percentCompleteText; ?>"><?php echo $row['projectPayments']; ?></td>
							<?php } ?>
							<td data-th="<?php echo $dateDueText; ?>"><?php echo $row['dueDate']; ?></td>
							<td data-th="<?php echo $dateClosedText; ?>"><?php echo $row['archiveDate']; ?></td>
							<td data-th="<?php echo $actionsText; ?>">
								<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>">
									<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewProject; ?>"></i>
								</a>
								<a data-toggle="modal" href="#deleteProject<?php echo $row['projectId']; ?>">
									<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteProjectTooltip; ?>"></i>
								</a>
							</td>
						</tr>
						
						<div class="modal fade" id="deleteProject<?php echo $row['projectId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<form action="" method="post">
										<div class="modal-body">
											<p class="lead"><?php echo $deleteProjConf.' '.clean($row['projectName']); ?>?</p>
										</div>
										<div class="modal-footer">
											<input name="projectId" type="hidden" value="<?php echo $row['projectId']; ?>" />
											<button type="input" name="submit" value="deleteClient" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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