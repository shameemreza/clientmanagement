<?php
	$pagPages = '10';

	// Include Pagination Class
	include('includes/pagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM clientprojects WHERE clientId = ".$clientId." AND archiveProj = '1'");
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$sqlStmt = "SELECT
					clientprojects.projectId,
					clientprojects.clientId,
					clientprojects.projectName,
					clientprojects.projectFee,
					clientprojects.projectDeatils,
					DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
					clientprojects.archiveProj,
					DATE_FORMAT(clientprojects.archiveDate,'%M %d, %Y') AS archiveDate,
					assignedprojects.assignedTo,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS manager
				FROM
					clientprojects
					LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
					LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
				WHERE clientprojects.clientId = ".$clientId." AND clientprojects.archiveProj = '1'
				ORDER BY clientprojects.projectId ".$pages->get_limit();
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li><a href="index.php?page=openProjects"><i class="fa fa-folder-open-o"></i> <?php echo $openProjectsLink; ?></a></li>
		<li class="active"><a href="#closed" data-toggle="tab"><i class="fa fa-check-square-o"></i> <?php echo $closedProjectsLink; ?></a></li>
		<li><a href="index.php?page=myRequests"><i class="fa fa-comments-o"></i> <?php echo $projectRequestsLink; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="tab-content">
		<div class="tab-pane in active no-padding" id="closed">
			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noClosedProjMsg; ?>
				</div>
			<?php } else { ?>
				<table class="rwd-table no-margin">
					<tbody>
						<tr class="primary">
							<th><?php echo $projectText; ?></th>
							<th><?php echo $descText; ?></th>
							<?php if ($set['enablePayments'] == '1') { ?>
								<th><?php echo $feeText; ?></th>
							<?php } ?>
							<th><?php echo $dateDueText; ?></th>
							<th><?php echo $dateClosedText; ?></th>
							<th><?php echo $assignedToText; ?></th>
							<th></th>
						</tr>
						<?php
							while ($row = mysqli_fetch_assoc($res)) {
								$projectFee = $curSym.format_amount($row['projectFee'], 2);
						?>
								<tr>
									<td data-th="<?php echo $projectText; ?>">
										<a href="index.php?page=viewProject&projectId=<?php echo $row['projectId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
											<?php echo clean($row['projectName']); ?>
										</a>
									</td>
									<td data-th="<?php echo $descText; ?>"><?php echo ellipsis($row['projectDeatils'],65); ?></td>
									<?php if ($set['enablePayments'] == '1') { ?>
										<td data-th="<?php echo $feeText; ?>"><?php echo $projectFee; ?></td>
									<?php } ?>
									<td data-th="<?php echo $dateDueText; ?>"><?php echo $row['dueDate']; ?></td>
									<td data-th="<?php echo $dateClosedText; ?>"><?php echo $row['archiveDate']; ?></td>
									<td data-th="<?php echo $assignedToText; ?>"><?php echo clean($row['manager']); ?></td>
									<td data-th="<?php echo $actionsText; ?>">
										<a href="index.php?page=viewProject&projectId=<?php echo $row['projectId']; ?>">
											<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewProject; ?>"></i>
										</a>
									</td>
								</tr>
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
	</div>
</div>