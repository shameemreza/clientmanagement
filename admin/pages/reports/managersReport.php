<?php
	// Report Options
	$inactiveManagers = $_POST['inactiveManagers'];
	if (isset($inactiveManagers) && $inactiveManagers == '0') {		// All Active
		$isActive = "'1'";
		$included = $allActiveText;
	} else {														// Show All
		$isActive = "'0','1'";
		$included = $viewAllText;
	}
	
    $sql = "SELECT
				adminId,
				adminEmail,
				CONCAT(adminFirstName,' ',adminLastName) AS theAdmin,
				adminPhone,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
				DATE_FORMAT(lastVisited,'%M %e, %Y') AS lastVisited,
				isAdmin,
				adminRole,
				isActive
			FROM
				admins
			WHERE
				isActive IN (".$isActive.")";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);

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
	<div class="content last">
		<h4><?php echo $pageName; ?></h4>
		<p>
			<span class="label label-default preview-label"><?php echo $reportOptionsLabel.': '.$included.' '.$managersNavLink; ?></span>
			<span class="label label-default preview-label ml5"><?php echo $totalRecordsLabel.': '.$totalRecs; ?></span>
			<span class="label label-default preview-label pull-right"><a href="index.php?action=reports"><i class="fa fa-bar-chart-o"></i> <?php echo $reportsLabel; ?></a></span>
		</p>
		
		<?php if(mysqli_num_rows($res) < 1) { ?>
			<div class="alertMsg default no-margin mt20">
				<i class="fa fa-warning"></i> <?php echo $noReportResults; ?>
			</div>
		<?php } else { ?>
			<table class="rwd-table">
				<tbody>
					<tr class="primary">
						<th><?php echo $managerText; ?></th>
						<th><?php echo $emailText; ?></th>
						<th><?php echo $phoneText; ?></th>
						<th><?php echo $activeTableHead; ?></th>
						<th><?php echo $accountTypeText; ?></th>
						<th><?php echo $accountRoleText; ?></th>
						<th><?php echo $dateCreatedTableHead; ?></th>
						<th><?php echo $lastLoginText; ?></th>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							if ($row['adminPhone'] != '') { $adminPhone = decryptIt($row['adminPhone']); } else { $adminPhone = ''; }
							if ($row['isAdmin'] == '0') { $anAdmin = $managerText; } else { $anAdmin = $adminNavLink; }
							if ($row['isActive'] == '0') { $isActive = '<strong class="text-danger">'.$noBtn.'</strong>'; } else { $isActive = $yesBtn; }
					?>
						<tr>
							<td data-th="<?php echo $managerText; ?>">
								<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewManagerTooltip; ?>">
									<a href="index.php?action=viewManager&adminId=<?php echo $row['adminId']; ?>"><?php echo clean($row['theAdmin']); ?></a>
								</span>
							</td>
							<td data-th="<?php echo $emailText; ?>"><?php echo clean($row['adminEmail']); ?></td>
							<td data-th="<?php echo $phoneText; ?>"><?php echo $adminPhone; ?></td>
							<td data-th="<?php echo activeTableHead; ?>"><?php echo $isActive; ?></td>
							<td data-th="<?php echo $accountTypeText; ?>"><?php echo $anAdmin; ?></td>
							<td data-th="<?php echo $accountRoleText; ?>"><?php echo clean($row['adminRole']); ?></td>
							<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['createDate']; ?></td>
							<td data-th="<?php echo $lastLoginText; ?>"><?php echo $row['lastVisited']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<form action="index.php?action=managersExport" method="post" class="mt10" target="_blank">
				<input type="hidden" name="inactiveManagers" value="<?php echo $inactiveManagers; ?>" />
				<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
			</form>
			<div class="clearfix"></div>
		<?php } ?>
	</div>
<?php } ?>