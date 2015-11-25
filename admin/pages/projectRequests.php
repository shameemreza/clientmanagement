<?php
	$datePicker = 'true';
	$jsFile = 'siteAlerts';
	$pagPages = '10';

	// Delete Request
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteRequest') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM projectrequests WHERE requestId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$stmt->close();
		$msgBox = alertBox($deleteRequestMsg, "<i class='fa fa-check-square'></i>", "success");
    }

	// Include Pagination Class
	include('includes/pagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM projectrequests");
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$sqlStmt = "SELECT
					projectrequests.requestId,
					projectrequests.clientId,
					projectrequests.requestTitle,
					projectrequests.requestBudget,
					projectrequests.timeFrame,
					DATE_FORMAT(projectrequests.requestDate,'%M %d, %Y') AS requestDate,
					UNIX_TIMESTAMP(projectrequests.requestDate) AS orderDate,
					projectrequests.requestAccepted,
					CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
					clients.clientCompany
				FROM
					projectrequests
					LEFT JOIN clients ON projectrequests.clientId = clients.clientId
				ORDER BY projectrequests.requestAccepted, orderDate, clients.clientId ".$pages->get_limit();
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1' . mysqli_error());

	include 'includes/navigation.php';
?>
<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> <?php echo $noRequestsFound; ?>
		</div>
	<?php } else { ?>
		<table class="rwd-table no-margin">
			<tbody>
				<tr class="primary">
					<th><?php echo $requestText; ?></th>
					<th><?php echo $clientText; ?></th>
					<th><?php echo $companyText; ?></th>
					<?php if ($set['enablePayments'] == '1') { ?>
						<th><?php echo $busdgetText; ?></th>
					<?php } ?>
					<th><?php echo $dateRequestedText; ?></th>
					<th><?php echo $timeFrameText; ?></th>
					<th><?php echo $statusText; ?></th>
					<th></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
						$requestBudget = $curSym.format_amount($row['requestBudget'], 2);
						if ($row['requestAccepted'] == '1') {
							$accepted = 'Accepted';
							$accptd = 'class="text-success"';
						} else if ($row['requestAccepted'] == '2') {
							$accepted = 'Declined';
							$accptd = 'class="text-danger"';
						} else {
							$accepted = 'New';
							$accptd = '';
						}
				?>
					<tr>
						<td data-th="<?php echo $requestText; ?>">
							<a href="index.php?action=viewRequest&requestId=<?php echo $row['requestId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewRequestTooltip; ?>">
								<?php echo clean($row['requestTitle']); ?>
							</a>
						</td>
						<td data-th="<?php echo $clientText; ?>">
							<a href="index.php?action=viewClient&clientId=<?php echo $row['clientId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $pageNameviewClient; ?>">
								<?php echo clean($row['theClient']); ?>
							</a>
						</td>
						<td data-th="<?php echo $companyText; ?>"><?php echo clean($row['clientCompany']); ?></td>
						<?php if ($set['enablePayments'] == '1') { ?>
							<td data-th="<?php echo $busdgetText; ?>"><?php echo $requestBudget; ?></td>
						<?php } ?>
						<td data-th="<?php echo $dateRequestedText; ?>"><?php echo $row['requestDate']; ?></td>
						<td data-th="<?php echo $timeFrameText; ?>"><?php echo clean($row['timeFrame']); ?></td>
						<td <?php echo $accptd; ?> data-th="Status"><?php echo $accepted; ?></td>
						<td data-th="<?php echo $actionsText; ?>">
							<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewRequestTooltip; ?>">
								<a href="index.php?action=viewRequest&requestId=<?php echo $row['requestId']; ?>"><i class="fa fa-edit edit"></i></a>
							</span>
							<span data-toggle="tooltip" data-placement="left" title="Delete Request">
								<a data-toggle="modal" href="#deleteRequest<?php echo $row['requestId']; ?>"><i class="fa fa-trash-o remove"></i></a>
							</span>
						</td>
					</tr>

					<div class="modal fade" id="deleteRequest<?php echo $row['requestId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<form action="" method="post">
									<div class="modal-body">
										<p class="lead"><?php echo $deleteRequestConf.' '.clean($row['requestTitle']); ?>?</p>
									</div>
									<div class="modal-footer">
										<input name="deleteId" type="hidden" value="<?php echo $row['requestId']; ?>" />
										<button type="input" name="submit" value="deleteRequest" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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
		}
		if ($total > $pagPages) {
			echo $pages->page_links();
		}
	?>
</div>