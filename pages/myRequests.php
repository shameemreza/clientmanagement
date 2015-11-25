<?php
	$pagPages = '10';

	// New Project Request
    if (isset($_POST['submit']) && $_POST['submit'] == 'requestQuote') {
        // Validation
        if($_POST['requestTitle'] == "") {
            $msgBox = alertBox($projTitleReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['requestDesc'] == "") {
            $msgBox = alertBox($projDescReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$requestTitle = $mysqli->real_escape_string($_POST['requestTitle']);
			$requestDesc = $_POST['requestDesc'];
			if ($set['enablePayments'] == '1') {
				$requestBudget = $mysqli->real_escape_string($_POST['requestBudget']);
			} else {
				$requestBudget = '';
			}
			$timeFrame = $mysqli->real_escape_string($_POST['timeFrame']);
			$requestDate = date("Y-m-d H:i:s");

            $stmt = $mysqli->prepare("
                                INSERT INTO
                                    projectrequests(
                                        clientId,
                                        requestTitle,
                                        requestDesc,
                                        requestBudget,
                                        timeFrame,
										requestDate
                                    ) VALUES (
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?
                                    )");
            $stmt->bind_param('ssssss',
				$clientId,
                $requestTitle,
                $requestDesc,
				$requestBudget,
				$timeFrame,
				$requestDate
            );
            $stmt->execute();

			// Send out the email in HTML
			$installUrl = $set['installUrl'];
			$siteName = $set['siteName'];
			$businessEmail = $set['businessEmail'];

			$subject = $projRequestEmail1.' '.$siteName.' '.$projRequestEmail2;

			$message = '<html><body>';
			$message .= '<h3>'.$subject.'</h3>';
			$message .= '<hr>';
			$message .= '<p>'.$projectText.': '.$requestTitle.'</p>';
			$message .= '<p>'.$fromText.': '.$clientFullName.'</p>';
			$message .= '<p>'.$requestDesc.'</p>';
			$message .= '<hr>';
			$message .= $emailLink;
			$message .= $emailThankYou;
			$message .= '</body></html>';

			$headers = "From: ".$siteName." <".$businessEmail.">\r\n";
			$headers .= "Reply-To: ".$businessEmail."\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

			if (mail($managers, $subject, $message, $headers)) {
				$msgBox = alertBox($projRequestCreatedMsg, "<i class='fa fa-check-square'></i>", "success");
				// Clear the form of Values
				$_POST['requestTitle'] = $_POST['requestDesc'] = $_POST['requestBudget'] = $_POST['timeFrame'] = '';
			} else {
				$msgBox = alertBox($emailErrorMsg, "<i class='fa fa-times-circle'></i>", "danger");
			}
            $stmt->close();
		}
	}

	// Include Pagination Class
	include('includes/pagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM projectrequests WHERE clientId = ".$clientId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Data
	$sqlStmt = "SELECT
					requestId,
					clientId,
					requestTitle,
					requestDesc,
					requestBudget,
					timeFrame,
					DATE_FORMAT(requestDate,'%M %d, %Y') AS requestDate,
					requestAccepted
				FROM
					projectrequests
				WHERE clientId = ".$clientId."
				ORDER BY requestId ".$pages->get_limit();
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1'.mysqli_error());

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li><a href="index.php?page=openProjects"><i class="fa fa-folder-open-o"></i> <?php echo $openProjectsLink; ?></a></li>
		<li><a href="index.php?page=closedProjects"><i class="fa fa-check-square-o"></i> <?php echo $closedProjectsLink; ?></a></li>
		<li class="active"><a href="#requests" data-toggle="tab"><i class="fa fa-comments-o"></i> <?php echo $projectRequestsLink; ?></a></li>
		<li class="pull-right"><a data-toggle="modal" data-target="#newRequest"><i class="fa fa-folder"></i> <?php echo $requestNewProjLink; ?></a></li>
	</ul>
</div>

<div class="content last">
	<h3><?php echo $pageName; ?></h3>
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="tab-content">
		<div class="tab-pane in active no-padding" id="requests">
			<?php if(mysqli_num_rows($res) < 1) { ?>
				<div class="alertMsg default no-margin">
					<i class="fa fa-minus-square-o"></i> <?php echo $noProjReqMsg; ?>
				</div>
			<?php } else { ?>
				<table class="rwd-table no-margin">
					<tbody>
						<tr class="primary">
							<th><?php echo $projectText; ?></th>
							<th><?php echo $descText; ?></th>
							<?php if ($set['enablePayments'] == '1') { ?>
								<th><?php echo $budgetText; ?></th>
							<?php } ?>
							<th><?php echo $timeFrameText; ?></th>
							<th><?php echo $dateReqText; ?></th>
							<th><?php echo $statusText; ?></th>
							<th></th>
						</tr>
						<?php
							while ($row = mysqli_fetch_assoc($res)) {
								if (!empty($row['requestBudget'])) {
									$requestBudget = $curSym.format_amount($row['requestBudget'], 2);
								} else {
									$requestBudget = '';
								}
								if ($row['requestAccepted'] == '0') {
									$requestAccepted = 'Open';
								} else if ($row['requestAccepted'] == '1') {
									$requestAccepted = '<strong class="text-success">Accepted</strong>';
								} else {
									$requestAccepted = '<strong class="text-danger">Declined</strong>';
								}
						?>
								<tr>
									<td data-th="<?php echo $projectText; ?>">
										<a href="index.php?page=viewRequest&requestId=<?php echo $row['requestId']; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewRequestTooltip; ?>">
											<?php echo clean($row['requestTitle']); ?>
										</a>
									</td>
									<td data-th="<?php echo $descText; ?>"><?php echo ellipsis($row['requestDesc'],65); ?></td>
									<?php if ($set['enablePayments'] == '1') { ?>
										<td data-th="<?php echo $budgetText; ?>"><?php echo $requestBudget; ?></td>
									<?php } ?>
									<td data-th="<?php echo $timeFrameText; ?>"><?php echo clean($row['timeFrame']); ?></td>
									<td data-th="<?php echo $dateReqText; ?>"><?php echo $row['requestDate']; ?></td>
									<td data-th="<?php echo $statusText; ?>"><?php echo $requestAccepted; ?></td>
									<td data-th="<?php echo $actionsText; ?>">
										<a href="index.php?page=viewRequest&requestId=<?php echo $row['requestId']; ?>">
											<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewRequestTooltip; ?>"></i>
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

<div class="modal fade" id="newRequest" tabindex="-1" role="dialog" aria-labelledby="newRequest" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4 class="modal-title"><?php echo $requestNewProjModal; ?></h4>
			</div>
			<form action="" method="post">
				<div class="modal-body">
					<?php if ($set['enablePayments'] == '1') { ?>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="requestTitle"><?php echo $projTitleField; ?></label>
									<input type="text" class="form-control" required="" name="requestTitle" value="<?php echo isset($_POST['requestTitle']) ? $_POST['requestTitle'] : ''; ?>">
									<span class="help-block"><?php echo $projTitleHelp; ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="timeFrame"><?php echo $projTimeFrameField; ?></label>
									<input type="text" class="form-control" name="timeFrame" value="<?php echo isset($_POST['timeFrame']) ? $_POST['timeFrame'] : ''; ?>">
									<span class="help-block"><?php echo $projTimeFrameFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="requestBudget"><?php echo $projBudgetField; ?></label>
									<input type="text" class="form-control" name="requestBudget" value="<?php echo isset($_POST['requestBudget']) ? $_POST['requestBudget'] : ''; ?>">
									<span class="help-block"><?php echo $projBudgetFieldHelp; ?></span>
								</div>
							</div>
						</div>
					<?php } else { ?>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="requestTitle"><?php echo $projTitleField; ?></label>
									<input type="text" class="form-control" name="requestTitle" value="<?php echo isset($_POST['requestTitle']) ? $_POST['requestTitle'] : ''; ?>">
									<span class="help-block"><?php echo $projTitleHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="timeFrame"><?php echo $projTimeFrameField; ?></label>
									<input type="text" class="form-control" name="timeFrame" value="<?php echo isset($_POST['timeFrame']) ? $_POST['timeFrame'] : ''; ?>">
									<span class="help-block"><?php echo $projTimeFrameFieldHelp; ?></span>
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="form-group">
						<label for="requestDesc"><?php echo $projDescText; ?></label>
						<textarea class="form-control" name="requestDesc" required="" rows="6"><?php echo isset($_POST['requestDesc']) ? $_POST['requestDesc'] : ''; ?></textarea>
						<span class="help-block"><?php echo $projDescFieldHelp; ?></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="input" name="submit" value="requestQuote" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $reqQuoteBtn; ?></button>
					<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
				</div>
			</form>
		</div>
	</div>
</div>