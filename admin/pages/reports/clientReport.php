<?php
	// Report Options
	$showClients = $_POST['showClients'];
	if (isset($showClients) && $showClients == '0') {	// All Active
		$isActive = "'1'";
		$isArchived = "'0'";
		$included = 'All Active';
	} else if ($showClients == '1') {					// All Archived
		$isActive = "'0'";
		$isArchived = "'1'";
		$included = 'All Archived';
	} else if ($showClients == '2') {					// All Inactive
		$isActive = "'0'";
		$isArchived = "'0'";
		$included = 'All Inactive';
	} else {											// Show All
		$isActive = "'0','1'";
		$isArchived = "'0','1'";
		$included = 'All';
	}
	
    $sql = "SELECT
				clientId,
				clientEmail,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				clientCompany,
				clientPhone,
				DATE_FORMAT(createDate,'%M %d, %Y') AS createDate,
				DATE_FORMAT(lastVisited,'%M %e, %Y') AS lastVisited,
				isActive,
				isArchived
			FROM
				clients
			WHERE
				isActive IN (".$isActive.") AND
				isArchived IN (".$isArchived.")";
    $res = mysqli_query($mysqli, $sql) or die('-1' . mysqli_error());
	$totalRecs = mysqli_num_rows($res);

	include 'includes/navigation.php';
?>
<div class="content last">
	<h4><?php echo $pageName; ?></h4>
	<p>
		<span class="label label-default preview-label">Report Options: <?php echo $included; ?> Clients</span>
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
					<th><?php echo $clientText; ?></th>
					<th><?php echo $emailText; ?></th>
					<th>Company</th>
					<th><?php echo $phoneText; ?></th>
					<th>Active</th>
					<th><?php echo $archivedText; ?></th>
					<th><?php echo $dateCreatedTableHead; ?></th>
					<th><?php echo $lastLoginText; ?></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						if ($row['clientPhone'] != '') { $clientPhone = decryptIt($row['clientPhone']); } else { $clientPhone = ''; }
						if ($row['isActive'] == '0') { $inactive = '<strong class="text-danger">'.$noBtn.'</strong>'; } else { $inactive = $yesBtn; }
						if ($row['isArchived'] == '0') { $archived = '<strong class="text-danger">'.$noBtn.'</strong>'; } else { $archived = $yesBtn; }
				?>
					<tr>
						<td data-th="<?php echo $clientText; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
								<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>"><?php echo clean($row['theClient']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $emailText; ?>"><?php echo clean($row['clientEmail']); ?></td>
						<td data-th="Company"><?php echo clean($row['clientCompany']); ?></td>
						<td data-th="<?php echo $phoneText; ?>"><?php echo $clientPhone; ?></td>
						<td data-th="Active"><?php echo $inactive; ?></td>
						<td data-th="<?php echo $archivedText; ?>"><?php echo $archived; ?></td>
						<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['createDate']; ?></td>
						<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['lastVisited']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<form action="index.php?action=clientsExport" method="post" class="mt10" target="_blank">
			<input type="hidden" name="showClients" value="<?php echo $showClients; ?>" />
			<button type="input" name="submit" value="export" class="btn btn-success btn-icon"><i class="fa fa-file-excel-o"></i> <?php echo $exportDataBtn; ?></button>
		</form>
		<div class="clearfix"></div>
	<?php } ?>
</div>