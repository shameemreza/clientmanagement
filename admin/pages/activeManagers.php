<?php
	$pagPages = '10';

	// Include Pagination Class
	include('includes/pagination.php');

	// Create new object & pass in the number of pages and an identifier
	$pages = new paginator($pagPages,'p');

	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM clients WHERE isActive = 1");
	$total = mysqli_num_rows($rows);

	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$query = "SELECT
				adminId,
				adminEmail,
				CONCAT(adminFirstName,' ',adminLastName) AS theAdmin,
				adminPhone,
				DATE_FORMAT(lastVisited,'%M %e, %Y') AS lastVisited,
				isAdmin,
				adminRole
			FROM
				admins
			WHERE
				isActive = 1
			ORDER BY
				adminId ".$pages->get_limit();
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());

	include 'includes/navigation.php';

	if ($isAdmin != '1') {
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
			<li class="active"><a href="" data-toggle="tab"><i class="fa fa-user"></i> <?php echo $activeManagersTabLink; ?></a></li>
			<li><a href="index.php?action=inactiveManagers"><i class="fa fa-archive"></i> <?php echo $inactiveManagersTabLink; ?></a></li>
			<li class="pull-right"><a href="index.php?action=newManager"><i class="fa fa-plus"></i> <?php echo $newManagerTabLink; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noActiveMngrsFound; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table no-margin">
				<tbody>
					<tr class="primary">
						<th><?php echo $managerText; ?></th>
						<th><?php echo $emailText; ?></th>
						<th><?php echo $phoneText; ?></th>
						<th><?php echo $adminNavLink; ?></th>
						<th><?php echo $projectsNavLink; ?></th>
						<th><?php echo $lastLoginText; ?></th>
						<th></th>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = '';  }
							if ($row['isAdmin'] == '0') { $isAdmin = $noBtn; } else { $isAdmin = $yesBtn; }

							// Get Total Project Count
							$projcountsql = "SELECT
												assignedprojects.projectId
											FROM
												assignedprojects
												LEFT JOIN clientprojects ON assignedprojects.projectId = clientprojects.projectId
											WHERE
												assignedprojects.assignedTo = ".$row['adminId']." AND
												clientprojects.archiveProj = 0";
							$projcounttotal = mysqli_query($mysqli, $projcountsql) or die('-2'.mysqli_error());
							$projcount = mysqli_num_rows($projcounttotal);
					?>
							<tr>
								<td data-th="<?php echo $managerText; ?>">
									<a href="index.php?action=viewManager&adminId=<?php echo $row['adminId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewManagerTooltip; ?>">
										<?php echo clean($row['theAdmin']); ?>
									</a>
								</td>
								<td data-th="<?php echo $emailText; ?>"><?php echo clean($row['adminEmail']); ?></td>
								<td data-th="<?php echo $phoneText; ?>"><?php echo $adminPhone; ?></td>
								<td data-th="<?php echo $adminNavLink; ?>"><?php echo $isAdmin; ?></td>
								<td data-th="<?php echo $projectsNavLink; ?>"><?php echo $projcount; ?></td>
								<td data-th="<?php echo $lastLoginText; ?>"><?php echo $row['lastVisited']; ?></td>
								<td data-th="<?php echo $actionsText; ?>">
									<a href="index.php?action=viewManager&adminId=<?php echo $row['adminId']; ?>">
										<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewManagerTooltip; ?>"></i>
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
<?php } ?>