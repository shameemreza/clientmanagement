<?php
	// Report Options
	$archivedProjects = $_POST['archivedProjects'];
	if (isset($_POST['archivedProjects']) && $_POST['archivedProjects'] == '0') {
		$isArchived = "'0'";
		$includeArchived = 'Current';
	} else {
		$isArchived = "'0','1'";
		$includeArchived = 'Current &amp; Archived';
	}
	
    $sql = "SELECT
				clientprojects.projectId,
				clientprojects.createdBy,
				clientprojects.clientId,
				clientprojects.projectName,
				clientprojects.percentComplete,
				clientprojects.projectFee,
				DATE_FORMAT(clientprojects.startDate,'%M %d, %Y') AS startDate,
				DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
				UNIX_TIMESTAMP(clientprojects.dueDate) AS orderDate,
				clientprojects.archiveProj,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
			FROM
				clientprojects
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
			WHERE
				archiveProj IN (".$isArchived.")
			ORDER BY archiveProj, orderDate";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);
	
	include 'includes/navigation.php';
?>
<div class="content last">
	<h4><?php echo $pageName; ?></h4>
	<p>
		<span class="label label-default preview-label"><?php echo $reportOptionsLabel.': '.$includeArchived.' '.$projectsText; ?></span>
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
					<th><?php echo $clientNameText; ?></th>
					<?php if ($set['enablePayments'] == '1') { ?>
						<th><?php echo $projectFeeText; ?></th>
					<?php } ?>
					<th><?php echo $percentCompleteText; ?></th>
					<th><?php echo $dateCreatedTableHead; ?></th>
					<th><?php echo $dateDueText; ?></th>
					<th><?php echo $statusText; ?></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						$projectFee = $curSym.format_amount($row['projectFee'], 2);
						if ($row['archiveProj'] == '1') { $archived = '<strong class="text-danger">'.$closedArchivedText.'</strong>'; } else { $archived = $activeProjectText; }
				?>
					<tr>
						<td data-th="<?php echo $projectText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<a href="index.php?action=viewProject&projectId=<?php echo $row['projectId']; ?>"><?php echo clean($row['projectName']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $clientNameText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
								<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>"><?php echo clean($row['theClient']); ?></a>
							</span>
						</td>
						<?php if ($set['enablePayments'] == '1') { ?>
							<td data-th="<?php echo $projectFeeText; ?>"><?php echo $projectFee; ?></td>
						<?php } ?>
						<td data-th="<?php echo $percentCompleteText; ?>"><?php echo $row['percentComplete']; ?>%</td>
						<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['startDate']; ?></td>
						<td data-th="<?php echo $dateDueText; ?>"><?php echo $row['dueDate']; ?></td>
						<td data-th="<?php echo $statusText; ?>"><?php echo $archived; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=projectsExport" method="post" class="mt10" target="_blank">
			<input type="hidden" name="archivedProjects" value="<?php echo $archivedProjects; ?>" />
			<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
		</form>
		<div class="clearfix"></div>
	<?php } ?>
</div>