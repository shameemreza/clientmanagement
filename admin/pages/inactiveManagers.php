<?php
	$pagPages = '10';

	// Delete Manager Account
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAdmin') {
		$adminId = $mysqli->real_escape_string($_POST['adminId']);
		$stmt = $mysqli->prepare("DELETE FROM admins WHERE adminId = ?");
		$stmt->bind_param('s', $adminId);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($managerDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
    }

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
				isAdmin,
				adminRole,
				isArchived,
				DATE_FORMAT(archiveDate,'%M %e, %Y') AS archiveDate
			FROM
				admins
			WHERE
				isActive = 0 AND
				isAdmin = 0
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
			<li><a href="index.php?action=activeManagers"><i class="fa fa-user"></i> <?php echo $activeManagersTabLink; ?></a></li>
			<li class="active"><a href="" data-toggle="tab"><i class="fa fa-archive"></i> <?php echo $inactiveManagersTabLink; ?></a></li>
			<li class="pull-right"><a href="index.php?action=newManager"><i class="fa fa-plus"></i> <?php echo $newManagerTabLink; ?></a></li>
		</ul>
	</div>

	<div class="content last">
		<h3><?php echo $pageName; ?></h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin">
				<i class="fa fa-minus-square-o"></i> <?php echo $noInactiveMngrsFound; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table no-margin">
				<tbody>
					<tr class="primary">
						<th><?php echo $managerText; ?></th>
						<th><?php echo $emailText; ?></th>
						<th><?php echo $phoneText; ?></th>
						<th><?php echo $archivedText; ?></th>
						<th><?php echo $dateArchivedText; ?></th>
						<th></th>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							// Decrypt data
							if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = '';  }
							if ($row['isArchived'] == '0') { $isArchived = $noBtn; $dateArchived = ''; } else { $isArchived = $yesBtn; $dateArchived = $row['archiveDate']; }
					?>
							<tr>
								<td data-th="<?php echo $managerText; ?>">
									<a href="index.php?action=viewManager&adminId=<?php echo $row['adminId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewManagerTooltip; ?>">
										<?php echo clean($row['theAdmin']); ?>
									</a>
								</td>
								<td data-th="<?php echo $emailText; ?>"><?php echo clean($row['adminEmail']); ?></td>
								<td data-th="<?php echo $phoneText; ?>"><?php echo $adminPhone; ?></td>
								<td data-th="<?php echo $archivedText; ?>"><?php echo $isArchived; ?></td>
								<td data-th="<?php echo $dateArchivedText; ?>"><?php echo $dateArchived; ?></td>
								<td data-th="<?php echo $actionsText; ?>">
									<a href="index.php?action=viewManager&adminId=<?php echo $row['adminId']; ?>">
										<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewManagerTooltip; ?>"></i>
									</a>
									<a data-toggle="modal" href="#deleteAdmin<?php echo $row['adminId']; ?>">
										<i class="fa fa-trash-o text-danger" data-toggle="tooltip" data-placement="left" title="<?php echo $deleteMngrTooltip; ?>"></i>
									</a>
								</td>
							</tr>

							<div class="modal fade" id="deleteAdmin<?php echo $row['adminId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
								<div class="modal-dialog">
									<div class="modal-content">
										<form action="" method="post">
											<div class="modal-body">
												<p class="lead"><?php echo $deleteManagerConf.' '.clean($row['theAdmin']); ?>?</p>
											</div>
											<div class="modal-footer">
												<input name="adminId" type="hidden" value="<?php echo $row['adminId']; ?>" />
												<button type="input" name="submit" value="deleteAdmin" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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
<?php } ?>