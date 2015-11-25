<?php
	$pagPages = '10';

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get Data
	if ($isAdmin == '1') {
		// Get the number of total records
		$rows = $mysqli->query("SELECT * FROM clientprojects WHERE archiveProj = 0");
		$total = mysqli_num_rows($rows);

		// Pass the number of total records
		$pages->set_total($total);

		// Get All Projects
		$stmt  = "SELECT
						clientprojects.projectId,
						clientprojects.createdBy,
						clientprojects.clientId,
						clientprojects.projectName,
						clientprojects.percentComplete,
						clientprojects.projectFee,
						DATE_FORMAT(clientprojects.startDate,'%M %e, %Y') AS startDate,
						DATE_FORMAT(clientprojects.dueDate,'%M %e, %Y') AS dueDate,
						UNIX_TIMESTAMP(clientprojects.dueDate) AS orderDate,
						assignedprojects.assignedTo,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
						CONCAT(clientFirstName,' ',clientLastName) AS theClient
					FROM
						clientprojects
						LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
						LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
						LEFT JOIN clients ON clientprojects.clientId = clients.clientId
					WHERE
						clientprojects.archiveProj = 0
					ORDER BY orderDate, clientprojects.clientId ".$pages->get_limit();
		$results = mysqli_query($mysqli, $stmt) or die('-1' . mysqli_error());
	} else {
		// Get the number of total records
		$rows = $mysqli->query("SELECT *
								FROM
									clientprojects
									LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
									LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
								WHERE assignedprojects.assignedTo = ".$adminId." AND archiveProj = 0");
		$total = mysqli_num_rows($rows);

		// Pass the number of total records
		$pages->set_total($total);

		// Get Projects Assigned to the logged in Manager
		$stmt  = "SELECT
						clientprojects.projectId,
						clientprojects.clientId,
						clientprojects.projectName,
						clientprojects.percentComplete,
						clientprojects.projectFee,
						DATE_FORMAT(clientprojects.startDate,'%M %e, %Y') AS startDate,
						DATE_FORMAT(clientprojects.dueDate,'%M %e, %Y') AS dueDate,
						UNIX_TIMESTAMP(clientprojects.dueDate) AS orderDate,
						assignedprojects.assignedTo,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin,
						CONCAT(clientFirstName,' ',clientLastName) AS theClient
					FROM
						clientprojects
						LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
						LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
						LEFT JOIN clients ON clientprojects.clientId = clients.clientId
					WHERE
						clientprojects.archiveProj = 0 AND
						assignedprojects.assignedTo = ".$adminId."
					ORDER BY orderDate, clientprojects.clientId ".$pages->get_limit();
		$results = mysqli_query($mysqli, $stmt) or die('-2' . mysqli_error());
	}

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li class="active"><a href="" data-toggle="tab"><i class="fa fa-folder-open"></i> <?php echo $openProjNavLink; ?></a></li>
		<li><a href="index.php?action=closedProjects"><i class="fa fa-folder"></i> <?php echo $pageNameclosedProjects; ?></a></li>
		<li class="pull-right"><a href="index.php?action=newProject"><i class="fa fa-plus"></i> <?php echo $pageNamenewProject; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($results) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i>
			<?php if ($isAdmin == '0') { ?>
				<?php echo $noOpenAssigned; ?>
			<?php } else { ?>
				<?php echo $noOpenProj; ?>
			<?php } ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table no-margin">
			<tbody>
				<tr class="primary">
					<th><?php echo $projectText; ?></th>
					<th><?php echo $clientText; ?></th>
					<th><?php echo $assignedToText; ?></th>
					<th><?php echo $percentCompleteText; ?></th>
					<?php if ($set['enablePayments'] == '1') { ?>
						<th><?php echo $projectFeeText; ?></th>
					<?php } ?>
					<th><?php echo $dateCreatedTableHead; ?></th>
					<th><?php echo $dateDueText; ?></th>
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
							<td data-th="<?php echo $percentCompleteText; ?>"><?php echo $row['percentComplete']; ?>%</td>
							<?php if ($set['enablePayments'] == '1') { ?>
								<td data-th="<?php echo $projectFeeText; ?>"><?php echo $projectFee; ?></td>
							<?php } ?>
							<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['startDate']; ?></td>
							<td data-th="<?php echo $dateDueText; ?>"><?php echo $row['dueDate']; ?></td>
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