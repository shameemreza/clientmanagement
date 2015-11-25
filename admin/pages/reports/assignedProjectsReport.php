<?php
	// Report Options
	$theManager = $_POST['theManager'];
	$theManagerName = $_POST['theManagerName'];
	
    $sql = "SELECT
				clientprojects.projectId,
				clientprojects.clientId,
				clientprojects.projectName,
				clientprojects.percentComplete,
				clientprojects.projectFee,
				DATE_FORMAT(clientprojects.startDate,'%M %d, %Y') AS startDate,
				DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
				assignedprojects.assignedTo,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
			FROM
				clientprojects
				LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE
				assignedprojects.assignedTo = ".$theManager." AND
				clientprojects.archiveProj = 0
			ORDER BY clientprojects.projectId";
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
			<span class="label label-default preview-label"><?php echo $assignedProjForText.': '.$theManagerName; ?></span>
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
						<th><?php echo $projectText; ?></th>
						<th><?php echo $clientText; ?></th>
						<?php if ($set['enablePayments'] == '1') { ?>
							<th><?php echo $projectFeeText; ?></th>
						<?php } ?>
						<th><?php echo $percentCompleteText; ?></th>
						<th><?php echo $dateCreatedTableHead; ?></th>
						<th><?php echo $dateDueText; ?></th>
					</tr>
					<?php
						while ($row = mysqli_fetch_assoc($res)) {
							$projectFee = $curSym.format_amount($row['projectFee'], 2);
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
							<?php if ($set['enablePayments'] == '1') { ?>
								<td data-th="<?php echo $projectFeeText; ?>"><?php echo $projectFee; ?></td>
							<?php } ?>
							<td data-th="<?php echo $percentCompleteText; ?>"><?php echo clean($row['percentComplete']); ?>%</td>
							<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['startDate']; ?></td>
							<td data-th="<?php echo $dateDueText; ?>"><?php echo $row['dueDate']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<form action="index.php?action=assignedProjectsExport" method="post" class="mt10" target="_blank">
				<input type="hidden" name="theManager" value="<?php echo $theManager; ?>" />
				<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
			</form>
			<div class="clearfix"></div>
		<?php } ?>
	</div>
<?php } ?>