<?php
	$datePicker = 'true';
	$jsFile = 'siteAlerts';
	$pagPages = '10';
	$count = 0;

	// Add New Alert
    if (isset($_POST['submit']) && $_POST['submit'] == 'newAlert') {
        // Validation
		if($_POST['alertTitle'] == "") {
            $msgBox = alertBox($alertTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['alertText'] == "") {
            $msgBox = alertBox($alertTextReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$invoicePrint = $mysqli->real_escape_string($_POST['invoicePrint']);
			$alertTitle = $mysqli->real_escape_string($_POST['alertTitle']);
			$alertText = $_POST['alertText'];
			$alertStart = $mysqli->real_escape_string($_POST['alertStart']);
			$alertExpires = $mysqli->real_escape_string($_POST['alertExpires']);
			$alertDate = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									sitealerts(
										adminId,
										isActive,
										invoicePrint,
										alertTitle,
										alertText,
										alertDate,
										alertStart,
										alertExpires
									) VALUES (
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?
									)
			");
			$stmt->bind_param('ssssssss',
								$adminId,
								$isActive,
								$invoicePrint,
								$alertTitle,
								$alertText,
								$alertDate,
								$alertStart,
								$alertExpires
			);
			$stmt->execute();
			$msgBox = alertBox($newAlertCreatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['alertTitle'] = $_POST['alertText'] = $_POST['alertStart'] = $_POST['alertExpires'] = '';
			$stmt->close();
		}
	}

	// Edit Site Alert
    if (isset($_POST['submit']) && $_POST['submit'] == 'editAlert') {
        // Validation
		if($_POST['alertTitle'] == "") {
            $msgBox = alertBox($alertTitleReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['alertText'] == "") {
            $msgBox = alertBox($alertTextReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$alertId = $mysqli->real_escape_string($_POST['alertId']);
			$isActive = $mysqli->real_escape_string($_POST['isActive']);
			$invoicePrint = $mysqli->real_escape_string($_POST['invoicePrint']);
			$alertTitle = $mysqli->real_escape_string($_POST['alertTitle']);
			$alertText = $_POST['alertText'];
			$alertStart = $mysqli->real_escape_string($_POST['alertStart']);
			$alertExpires = $mysqli->real_escape_string($_POST['alertExpires']);

            $stmt = $mysqli->prepare("UPDATE
										sitealerts
									SET
										isActive = ?,
										invoicePrint = ?,
										alertTitle = ?,
										alertText = ?,
										alertStart = ?,
										alertExpires = ?
									WHERE
										alertId = ?"
			);
			$stmt->bind_param('sssssss',
									$isActive,
									$invoicePrint,
									$alertTitle,
									$alertText,
									$alertStart,
									$alertExpires,
									$alertId
			);
			$stmt->execute();
			$msgBox = alertBox($alertUpdatedMsg, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['alertTitle'] = $_POST['alertText'] = $_POST['alertStart'] = $_POST['alertExpires'] = '';
			$stmt->close();
		}
	}

	// Delete Site Alert
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteAlert') {
		$alertId = $mysqli->real_escape_string($_POST['alertId']);
		$stmt = $mysqli->prepare("DELETE FROM sitealerts WHERE alertId = ?");
		$stmt->bind_param('s', $alertId);
		$stmt->execute();
		$msgBox = alertBox($alertDeletedMsg, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Include Pagination Class
	include('includes/pagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records for Private Closed
	$rows = $mysqli->query("SELECT * FROM sitealerts");
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$sqlStmt = "SELECT
					sitealerts.alertId,
					sitealerts.adminId,
					sitealerts.isActive,
					sitealerts.invoicePrint,
					sitealerts.alertTitle,
					sitealerts.alertText,
					sitealerts.alertDate,
					DATE_FORMAT(sitealerts.alertDate,'%M %d, %Y') AS createDate,
					sitealerts.alertStart,
					DATE_FORMAT(sitealerts.alertStart,'%M %d, %Y') AS startDate,
					DATE_FORMAT(sitealerts.alertStart,'%Y-%m-%d') AS showStart,
					sitealerts.alertExpires,
					DATE_FORMAT(sitealerts.alertExpires,'%M %d, %Y') AS endDate,
					DATE_FORMAT(sitealerts.alertExpires,'%Y-%m-%d') AS showEnd,
					CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS createdBy
				FROM
					sitealerts
					LEFT JOIN admins ON sitealerts.adminId = admins.adminId ".$pages->get_limit();
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1' . mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li class="pull-right"><a  data-toggle="modal" href="#newAlert"><i class="fa fa-plus"></i> Add a New Alert</a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<?php if(mysqli_num_rows($res) < 1) { ?>
		<div class="alertMsg default no-margin">
			<i class="fa fa-minus-square-o"></i> No Site Alerts Found.
		</div>
	<?php } else { ?>
		<table class="rwd-table no-margin">
			<tbody>
				<tr class="primary">
					<th><?php echo $titleTableHead; ?></th>
					<th><?php echo $createdByTableHead; ?></th>
					<th><?php echo $dateCreatedTableHead; ?></th>
					<th><?php echo $activeTableHead; ?></th>
					<th><?php echo $invoiceTableHead; ?></th>
					<th><?php echo $startsOnTableHead; ?></th>
					<th><?php echo $endsOnTableHead; ?></th>
					<th></th>
				</tr>
				<?php
					while ($row = mysqli_fetch_assoc($res)) {
					if ($row['isActive'] == '1') { $active = 'Yes'; $isActive = 'selected'; } else { $active = 'No'; $isActive = ''; }
					if ($row['invoicePrint'] == '1') { $invoice = 'Yes'; $invoicePrint = 'selected'; } else { $invoice = 'No'; $invoicePrint = ''; }
					if ($row['showStart'] != '0000-00-00') { $showStart = $row['showStart']; } else { $showStart = ''; }
					if ($row['showEnd'] != '0000-00-00') { $showEnd = $row['showEnd']; } else { $showEnd = ''; }
				?>
					<tr>
						<td class="text-left" data-th="<?php echo $titleTableHead; ?>">
							<span data-toggle="tooltip" data-placement="right" title="<?php echo $editAlertTooltip; ?>">
								<a data-toggle="modal" href="#editAlert<?php echo $row['alertId']; ?>"><?php echo clean($row['alertTitle']); ?></a>
							</span>
						</td>
						<td data-th="<?php echo $createdByTableHead; ?>"><?php echo clean($row['createdBy']); ?></td>
						<td data-th="<?php echo $dateCreatedTableHead; ?>"><?php echo $row['createDate']; ?></td>
						<td data-th="<?php echo $activeTableHead; ?>"><?php echo $active; ?></td>
						<td data-th="<?php echo $invoiceTableHead; ?>"><?php echo $invoice; ?></td>
						<td data-th="<?php echo $startsOnTableHead; ?>"><?php echo $row['startDate']; ?></td>
						<td data-th="<?php echo $endsOnTableHead; ?>"><?php echo $row['endDate']; ?></td>
						<td class="text-right" data-th="Actions">
							<span data-toggle="tooltip" data-placement="left" title="<?php echo $editAlertTooltip; ?>">
								<a data-toggle="modal" href="#editAlert<?php echo $row['alertId']; ?>"><i class="fa fa-edit edit"></i></a>
							</span>
							<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteAlertTooltip; ?>">
								<a data-toggle="modal" href="#deleteAlert<?php echo $row['alertId']; ?>"><i class="fa fa-trash-o remove"></i></a>
							</span>
						</td>
					</tr>

					<div id="editAlert<?php echo $row['alertId']; ?>" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">

								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<h4 class="modal-title"><?php echo $editSiteAlertTitle; ?></h4>
								</div>

								<form action="" method="post">
									<div class="modal-body">
										<p><?php echo $alertDatesQuip; ?></p>
										<div class="alertDates">
											<div class="row">
												<div class="col-md-6">
													<div class="form-group">
														<label for="alertStart"><?php echo $startDateField; ?></label>
														<input type="text" class="form-control" name="alertStart" id="alertStart_<?php echo $count; ?>" value="<?php echo $showStart; ?>" />
														<span class="help-block"><?php echo $startDateFieldHelp; ?></span>
													</div>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<label for="alertExpires"><?php echo $endDateField; ?></label>
														<input type="text" class="form-control" name="alertExpires" id="alertExpires_<?php echo $count; ?>" value="<?php echo $showEnd; ?>" />
														<span class="help-block"><?php echo $endDateFieldHelp; ?></span>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="isActive"><?php echo $activeAlertField; ?></label>
													<select class="form-control" name="isActive">
														<option value="0"><?php echo $noBtn; ?></option>
														<option value="1" <?php echo $isActive; ?>><?php echo $yesBtn; ?></option>
													</select>
													<span class="help-block"><?php echo $activeAlertFieldHelp; ?></span>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="invoicePrint"><?php echo $invoicePrintField; ?></label>
													<select class="form-control" name="invoicePrint">
														<option value="0"><?php echo $noBtn; ?></option>
														<option value="1" <?php echo $invoicePrint; ?>><?php echo $yesBtn; ?></option>
													</select>
													<span class="help-block"><?php echo $invoicePrintFieldHelp; ?></span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="alertTitle"><?php echo $alertTitleField; ?></label>
											<input type="text" class="form-control" name="alertTitle" required="required" value="<?php echo clean($row['alertTitle']); ?>" />
										</div>
										<div class="form-group">
											<label for="alertText"><?php echo $alertTextField; ?></label>
											<textarea class="form-control" required="" name="alertText" rows="4"><?php echo clean($row['alertText']); ?></textarea>
										</div>
									</div>

									<div class="modal-footer">
										<input type="hidden" name="alertId" value="<?php echo $row['alertId']; ?>" />
										<button type="input" name="submit" value="editAlert" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveChangesBtn; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>

							</div>
						</div>
					</div>

					<div class="modal fade" id="deleteAlert<?php echo $row['alertId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<form action="" method="post">
									<div class="modal-body">
										<p class="lead"><?php echo $deleteAlertQuip; ?></p>
									</div>
									<div class="modal-footer">
										<input name="alertId" type="hidden" value="<?php echo $row['alertId']; ?>" />
										<button type="input" name="submit" value="deleteAlert" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				<?php
					$count++;
					}
				?>
			</tbody>
		</table>
	<?php
		}
		if ($total > $pagPages) {
			echo $pages->page_links();
		}
	?>
</div>

<div id="newAlert" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">

			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title">Add a new Site Alert</h4>
			</div>

			<form action="" method="post">
				<div class="modal-body">
					<p><?php echo $alertDatesQuip; ?></p>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="alertStart"><?php echo $startDateField; ?></label>
								<input type="text" class="form-control" name="alertStart" id="newAlertStart" value="<?php echo isset($_POST['alertStart']) ? $_POST['alertStart'] : ''; ?>" />
								<span class="help-block"><?php echo $startDateFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="alertExpires"><?php echo $endDateField; ?></label>
								<input type="text" class="form-control" name="alertExpires" id="newAlertExpires" value="<?php echo isset($_POST['alertExpires']) ? $_POST['alertExpires'] : ''; ?>" />
								<span class="help-block"><?php echo $endDateFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="isActive"><?php echo $activeAlertField; ?></label>
								<select class="form-control" name="isActive">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $activeAlertFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="invoicePrint"><?php echo $invoicePrintField; ?></label>
								<select class="form-control" name="invoicePrint">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $invoicePrintFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="alertTitle"><?php echo $alertTitleField; ?></label>
						<input type="text" class="form-control" name="alertTitle" required="required" value="<?php echo isset($_POST['alertTitle']) ? $_POST['alertTitle'] : ''; ?>" />
					</div>
					<div class="form-group">
						<label for="alertText"><?php echo $alertTextField; ?></label>
						<textarea class="form-control" required="" name="alertText" rows="4"><?php echo isset($_POST['alertText']) ? $_POST['alertText'] : ''; ?></textarea>
					</div>
				</div>

				<div class="modal-footer">
					<button type="input" name="submit" value="newAlert" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>

		</div>
	</div>
</div>