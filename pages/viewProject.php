<?php
	$projectId = $_GET['projectId'];
	$jsFile = 'viewProject';
	$getId = 'projectId='.$projectId;
	$pagPages = '10';

	// Add New Account Entry
    if (isset($_POST['submit']) && $_POST['submit'] == 'newentry') {
        // Validation
		if($_POST['entryTitle'] == "") {
            $msgBox = alertBox($accountNameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['entryUsername'] == "") {
            $msgBox = alertBox($accountUsernameReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['entryPass'] == "") {
            $msgBox = alertBox($accountPasswordReq, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$entryTitle = encryptIt($_POST['entryTitle']);
			$entryDesc = encryptIt($_POST['entryDesc']);
			$entryUsername = encryptIt($_POST['entryUsername']);
			$entryPass = encryptIt($_POST['entryPass']);
			$entryUrl = encryptIt($_POST['entryUrl']);
			$entryNotes = encryptIt($_POST['entryNotes']);
			$entryDate = date("Y-m-d H:i:s");

			$stmt = $mysqli->prepare("
								INSERT INTO
									pwentries(
										projectId,
										clientId,
										entryTitle,
										entryDesc,
										entryUsername,
										entryPass,
										entryUrl,
										entryNotes,
										entryDate
									) VALUES (
										?,
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
			$stmt->bind_param('sssssssss',
								$projectId,
								$clientId,
								$entryTitle,
								$entryDesc,
								$entryUsername,
								$entryPass,
								$entryUrl,
								$entryNotes,
								$entryDate
			);
			$stmt->execute();
			$msgBox = alertBox($newAccountEntrySaved, "<i class='fa fa-check-square'></i>", "success");
			// Clear the Form of values
			$_POST['entryTitle'] = $_POST['entryDesc'] = $_POST['entryUsername'] = $_POST['entryPass'] = $_POST['entryUrl'] = $_POST['entryNotes'] = '';
			$stmt->close();
		}
	}

	// Delete Account Entry
	if (isset($_POST['submit']) && $_POST['submit'] == 'deleteentry') {
		$deleteId = $mysqli->real_escape_string($_POST['deleteId']);
		$stmt = $mysqli->prepare("DELETE FROM pwentries WHERE entryId = ?");
		$stmt->bind_param('s', $_POST['deleteId']);
		$stmt->execute();
		$msgBox = alertBox($accountEntryDeleted, "<i class='fa fa-check-square'></i>", "success");
		$stmt->close();
    }

	// Get Project Data
    $query = "SELECT
                clientprojects.projectId,
                clientprojects.clientId,
                clientprojects.projectName,
                clientprojects.percentComplete,
                clientprojects.projectFee,
				clientprojects.projectPayments,
				clientprojects.projectDeatils,
				DATE_FORMAT(clientprojects.startDate,'%M %d, %Y') AS startDate,
                DATE_FORMAT(clientprojects.dueDate,'%M %d, %Y') AS dueDate,
                clientprojects.archiveProj,
				DATE_FORMAT(clientprojects.archiveDate,'%M %d, %Y') AS archiveDate,
                CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
                SUM(projectpayments.paymentAmount) AS totalAmount,
				SUM(projectpayments.additionalFee) AS totalFee,
				assignedprojects.assignedTo,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
            FROM
                clientprojects
				LEFT JOIN clients ON clientprojects.clientId = clients.clientId
                LEFT JOIN projectpayments ON clientprojects.projectId = projectpayments.projectId
				LEFT JOIN assignedprojects ON clientprojects.projectId = assignedprojects.projectId
				LEFT JOIN admins ON assignedprojects.assignedTo = admins.adminId
            WHERE
                clientprojects.projectId = ".$projectId;
    $res = mysqli_query($mysqli, $query) or die('-1'.mysqli_error());
	$row = mysqli_fetch_assoc($res);

	// Calculate & Format the Totals
	$a = "SELECT SUM(paymentAmount) AS totalAmount FROM projectpayments WHERE projectId = ".$projectId;
	$b = mysqli_query($mysqli, $a) or die('-2'.mysqli_error());
	$c = mysqli_fetch_assoc($b);
	$totalPayments = $c['totalAmount'];

	$d = "SELECT SUM(additionalFee) AS totalFee FROM projectpayments WHERE projectId = ".$projectId;
	$e = mysqli_query($mysqli, $d) or die('-3'.mysqli_error());
	$f = mysqli_fetch_assoc($e);
	$totalFees = $f['totalFee'];

	$projectFee = $curSym.format_amount($row['projectFee'], 2);
	$hasPaid = $totalPayments + $totalFees;
	$totalPaid = $curSym.format_amount($hasPaid, 2);
	$amtDue = $row['projectFee'] - $totalPayments;
	$totalDue = $curSym.format_amount($amtDue, 2);

	if ($amtDue == '0.00') { $due = $paidInFullText; $highlight = 'text-success'; } else { $due = $totalDue; $highlight = 'text-danger'; }

	// Get the Current Status of the Project
	if ($row['archiveProj'] == '0') {
		$curStatus = '<strong class="text-success">'.$openProjText.'</strong>';
	} else {
		$curStatus = '<strong class="text-danger">'.$closedProjText.' '.$row['archiveDate'].'</strong>';
	}
	
	// Include Pagination Class
	include('includes/getpagination.php');

	$pages = new paginator($pagPages,'p');
	// Get the number of total records
	$rows = $mysqli->query("SELECT * FROM pwentries WHERE projectId = ".$projectId);
	$total = mysqli_num_rows($rows);
	// Pass the number of total records
	$pages->set_total($total);

	// Get Project Accounts & Passwords
    $sql = "SELECT
				entryId,
				projectId,
				adminId,
				clientId,
				entryTitle,
				entryDesc,
				entryUsername,
				entryPass,
				entryUrl,
				entryNotes
            FROM
                pwentries
            WHERE
                projectId = ".$projectId." ".$pages->get_limit();
    $results = mysqli_query($mysqli, $sql) or die('-4'.mysqli_error());

	if ($set['enablePayments'] == '1') {
		// Get the project payments
		$payments = "SELECT
                        projectpayments.paymentId,
						projectpayments.projectId,
						projectpayments.enteredBy,
						projectpayments.paymentFor,
						DATE_FORMAT(projectpayments.paymentDate,'%M %d, %Y') AS paymentDate,
						projectpayments.paidBy,
						projectpayments.paymentAmount,
						projectpayments.additionalFee,
						projectpayments.paymentNotes,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						projectpayments
						LEFT JOIN admins ON projectpayments.enteredBy = admins.adminId
					WHERE
						projectpayments.projectId = ".$projectId."
                    ORDER BY projectpayments.paymentId DESC";
		$payres = mysqli_query($mysqli, $payments) or die('-5'.mysqli_error());
		
		$invQry = "SELECT
						invoices.invoiceId,
						invoices.projectId,
						invoices.adminId,
						invoices.clientId,
						invoices.invoiceTitle,
						invoices.invoiceNotes,
						DATE_FORMAT(invoices.invoiceDue,'%M %d, %Y') AS invoiceDue,
						UNIX_TIMESTAMP(invoices.invoiceDue) AS orderDate,
						invoices.isPaid,
						clientprojects.projectName,
						CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
					FROM
						invoices
						LEFT JOIN clientprojects ON invoices.projectId = clientprojects.projectId
						LEFT JOIN clients ON invoices.clientId = clients.clientId
						LEFT JOIN admins ON invoices.adminId = admins.adminId
					WHERE invoices.projectId = ".$projectId." AND invoices.isPaid = 0
					ORDER BY invoices.isPaid, orderDate";
		$invres = mysqli_query($mysqli, $invQry) or die('-13'.mysqli_error());
	}

	// Get Project Discussions
	$disc = "SELECT
				projectdiscus.discussionId,
				projectdiscus.projectId,
				projectdiscus.adminId,
				projectdiscus.clientId,
				projectdiscus.discussionTitle,
				projectdiscus.discussionText,
				DATE_FORMAT(projectdiscus.discussionDate,'%W, %M %e, %Y at %l:%i %p') AS discussionDate,
				DATE_FORMAT(projectdiscus.lastUpdated,'%W, %M %e, %Y at %l:%i %p') AS lastUpdated,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
			FROM
				projectdiscus
				LEFT JOIN clients ON projectdiscus.clientId = clients.clientId
				LEFT JOIN admins ON projectdiscus.adminId = admins.adminId
			WHERE
				projectdiscus.projectId = ".$projectId."
			ORDER BY discussionId DESC
			LIMIT 5";
	$discres = mysqli_query($mysqli, $disc) or die('-6'.mysqli_error());

    // Get File Data
    $file = "SELECT
                projectfiles.fileId,
                projectfiles.folderId,
                projectfiles.projectId,
				projectfiles.adminId,
				projectfiles.clientId,
                projectfiles.fileTitle,
				projectfiles.fileDesc,
				DATE_FORMAT(projectfiles.fileDate,'%M %d, %Y') AS fileDate,
				CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
				CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
            FROM
                projectfiles
				LEFT JOIN clients ON projectfiles.clientId = clients.clientId
				LEFT JOIN admins ON projectfiles.adminId = admins.adminId
            WHERE
                projectfiles.projectId = ".$projectId."
            ORDER BY projectId DESC
			LIMIT 5";
    $fileres = mysqli_query($mysqli, $file) or die('-7'.mysqli_error());

	include 'includes/navigation.php';

	if ($row['clientId'] != $clientId) {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="contentAlt">
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row">
			<div class="col-md-8">
				<div class="content no-margin">
					<h3><?php echo $pageName; ?></h3>

					<table class="infoTable">
						<tr>
							<td class="infoKey"><i class="fa fa-folder-open"></i> <?php echo $projectTableHead; ?>:</td>
							<td class="infoVal"><?php echo clean($row['projectName']); ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-info-circle"></i> <?php echo $currStatusText; ?>:</td>
							<td class="infoVal"><?php echo $curStatus; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-male"></i> <?php echo $projManagerText; ?>:</td>
							<td class="infoVal">
								<?php
									if ($row['assignedTo'] != '') {
										echo clean($row['theAdmin']);
									} else {
										echo '<strong class="text-warning">'.$unassignedText.'</strong>';
									}
								?>
							</td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateStartedText; ?>:</td>
							<td class="infoVal"><?php echo $row['startDate']; ?></td>
						</tr>
						<tr>
							<td class="infoKey"><i class="fa fa-calendar"></i> <?php echo $dateDueText; ?>:</td>
							<td class="infoVal"><strong><?php echo $row['dueDate']; ?></strong></td>
						</tr>
						<?php if ($set['enablePayments'] == '1') { ?>
							<tr>
								<td class="infoKey"><i class="fa fa-usd"></i> <?php echo $projFeeText; ?>:</td>
								<td class="infoVal"><?php echo $projectFee; ?></td>
							</tr>
							<tr>
								<td class="infoKey"><i class="fa fa-credit-card"></i> <?php echo $totalPaidText; ?>:</td>
								<td class="infoVal"><?php echo $totalPaid; ?>*</td>
							</tr>
							<tr>
								<td class="infoKey"><i class="fa fa-money"></i> <?php echo $amtOwedText; ?>:</td>
								<td class="infoVal"><strong class="<?php echo $highlight; ?>"><?php echo $due; ?></strong></td>
							</tr>
						<?php } ?>
					</table>
					<div class="well well-sm bg-trans no-margin mt20">
						<strong><?php echo $projDescText; ?>:</strong> <?php echo nl2br(clean($row['projectDeatils'])); ?>
					</div>
					<p class="text-muted mt10"><small>* <?php echo $totalPaidQuip; ?></small></p>
				</div>

				<div class="content">
					<h3><?php echo $currProgress; ?></h3>
					<div class="barGraph clearfix" data-percent="<?php echo $row['percentComplete']; ?>%">
						<div class="barGraph-title">
							<span><?php echo $row['percentComplete']; ?><?php echo $percentCompleteText; ?></span>
						</div>
						<div class="barGraph-bar"></div>
					</div>
				</div>
				
				<?php
					if ($set['enablePayments'] == '1') {
						if(mysqli_num_rows($invres) > 0) {
				?>
						<div class="content">
							<h3><?php echo $outstndInvoiceText; ?></h3>
							<table class="rwd-table">
								<tbody>
									<tr class="primary">
										<th><?php echo $invoiceText; ?></th>
										<th><?php echo $createdByText; ?></th>
										<th><?php echo $paymentDueText; ?></th>
										<th><?php echo $invAmountText; ?></th>
										<th><?php echo $statusText; ?></th>
										<th></th>
									</tr>
									<?php
										while ($r = mysqli_fetch_assoc($invres)) {
											// Get the Invoice Total
											$x = "SELECT
														itemAmount,
														itemqty
													FROM
														invitems
													WHERE invoiceId = ".$r['invoiceId'];
											$y = mysqli_query($mysqli, $x) or die('-16'.mysqli_error());

											$lineTotal = 0;
											while ($z = mysqli_fetch_assoc($y)) {
												$lineItem = $z['itemAmount'] * $z['itemqty'];
												$lineTotal += $lineItem;
											}
											$lineTotal = $curSym.format_amount($lineTotal, 2);

											if ($r['isPaid'] == '1') { $status = 'Paid'; $highlight = 'class="text-success"'; } else { $status = 'Unpaid'; $highlight = 'class="text-danger"'; }
									?>
											<tr>
												<td data-th="<?php echo $invoiceText; ?>">
													<span data-toggle="tooltip" data-placement="right" title="<?php echo $viewInvoiceTooltip; ?>">
														<a href="index.php?page=viewInvoice&invoiceId=<?php echo $r['invoiceId']; ?>"><?php echo clean($r['invoiceTitle']); ?></a>
													</span>
												</td>
												<td data-th="<?php echo $createdByText; ?>"><?php echo clean($r['theAdmin']); ?></td>
												<td data-th="<?php echo $paymentDueText; ?>"><?php echo $r['invoiceDue']; ?></td>
												<td data-th="<?php echo $invAmountText; ?>"><?php echo $lineTotal; ?></td>
												<td data-th="<?php echo $statusText; ?>"><strong <?php echo $highlight; ?>><?php echo $status; ?></strong></td>
												<td data-th="<?php echo $actionsText; ?>">
													<a href="index.php?page=viewInvoice&invoiceId=<?php echo $r['invoiceId']; ?>">
														<i class="fa fa-edit text-info" data-toggle="tooltip" data-placement="left" title="<?php echo $viewInvoiceTooltip; ?>"></i>
													</a>
												</td>
											</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
				<?php
						}
					}
				?>

				<div class="content last">
					<h3><?php echo $projAccountsPass; ?></h3>
					<p>
						<?php echo $projAccountQuip; ?>
						<span class="pull-right">
							<a href="#newentry" data-toggle="modal" class="btn btn-success btn-sm btn-icon"><i class="fa fa-plus"></i> <?php echo $newEntryBtn; ?></a>
						</span>
					</p>

					<?php if(mysqli_num_rows($results) > 0) { ?>
						<table class="rwd-table">
							<tbody>
								<tr>
									<th class="text-left"><?php echo $accountText; ?></th>
									<th><?php echo $usernameText; ?></th>
									<th><?php echo $urlText; ?></th>
									<th></th>
								</tr>
								<?php
									while ($pw = mysqli_fetch_assoc($results)) {
										// Decrypt Data
										if ($pw['entryTitle'] != '') { $entryTitle = decryptIt($pw['entryTitle']); } else { $entryTitle = ''; }
										if ($pw['entryDesc'] != '') { $entryDesc = decryptIt($pw['entryDesc']); } else { $entryDesc = ''; }
										if ($pw['entryUsername'] != '') { $entryUsername = decryptIt($pw['entryUsername']); } else { $entryUsername = ''; }
										if ($pw['entryPass'] != '') { $entryPass = decryptIt($pw['entryPass']); } else { $entryPass = ''; }
										if ($pw['entryUrl'] != '') { $entryUrl = decryptIt($pw['entryUrl']); } else { $entryUrl = ''; }
										if ($pw['entryNotes'] != '') { $entryNotes = decryptIt($pw['entryNotes']); } else { $entryNotes = ''; }
								?>
										<tr>
											<td data-th="<?php echo $accountText; ?>">
												<span data-toggle="tooltip" data-placement="right" title="<?php echo ellipsis($entryDesc,125); ?>">
													<?php echo $entryTitle; ?>
												</span>
											</td>
											<td data-th="<?php echo $usernameText; ?>"><?php echo $entryUsername; ?></td>
											<td data-th="<?php echo $urlText; ?>"><a href="<?php echo $entryUrl; ?>" target="_blank"><?php echo $entryUrl; ?></a></td>
											<td class="text-right" data-th="<?php echo $actionsText; ?>">
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $viewPassTooltip; ?>">
													<a href="#viewentry<?php echo $pw['entryId']; ?>" data-toggle="modal"><i class="fa fa-external-link-square print"></i></a>
												</span>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $editEntryTooltip; ?>">
													<a href="index.php?page=projectAccount&entryId=<?php echo $pw['entryId']; ?>"><i class="fa fa-edit edit"></i></a>
												</span>
												<span data-toggle="tooltip" data-placement="left" title="<?php echo $deleteEntryTooltip; ?>">
													<a href="#deleteentry<?php echo $pw['entryId']; ?>" data-toggle="modal"><i class="fa fa-trash-o remove"></i></a>
												</span>
											</td>
										</tr>

										<div class="modal fade" id="viewentry<?php echo $pw['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $entryPass; ?></p>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $closeBtn; ?></button>
														</div>
													</form>
												</div>
											</div>
										</div>

										<div class="modal fade" id="deleteentry<?php echo $pw['entryId']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
											<div class="modal-dialog">
												<div class="modal-content">
													<form action="" method="post">
														<div class="modal-body">
															<p class="lead"><?php echo $deleteEntryConf.' '.$entryTitle; ?>?</p>
														</div>
														<div class="modal-footer">
															<input name="deleteId" type="hidden" value="<?php echo $pw['entryId']; ?>" />
															<button type="input" name="submit" value="deleteentry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $yesBtn; ?></button>
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

					<div id="newentry" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
									<h4 class="modal-title"><?php echo $addNewEntryModal; ?></h4>
								</div>
								<form action="" method="post">
									<div class="modal-body">
										<div class="form-group">
											<label for="entryTitle"><?php echo $accountField; ?></label>
											<input type="text" class="form-control" required="" name="entryTitle" value="<?php echo isset($_POST['entryTitle']) ? $_POST['entryTitle'] : ''; ?>" />
										</div>
										<div class="form-group">
											<label for="entryDesc"><?php echo $descText; ?></label>
											<textarea class="form-control" name="entryDesc" required="" rows="3"><?php echo isset($_POST['entryDesc']) ? $_POST['entryDesc'] : ''; ?></textarea>
										</div>
										<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label for="entryUsername"><?php echo $usernameText; ?></label>
													<input type="text" class="form-control" required="" name="entryUsername" value="<?php echo isset($_POST['entryUsername']) ? $_POST['entryUsername'] : ''; ?>" />
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label for="entryPass"><?php echo $passwordField; ?></label>
													<input type="password" class="form-control" required="" name="entryPass" id="newPass" value="<?php echo isset($_POST['entryPass']) ? $_POST['entryPass'] : ''; ?>" />
													<span class="help-block">
														<a href="" id="show2" class="btn btn-warning btn-xs"><?php echo $showPlainText; ?></a>
														<a href="" id="hide2" class="btn btn-info btn-xs"><?php echo $hidePlainText; ?></a>
													</span>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label for="entryUrl"><?php echo $urlText; ?></label>
											<input type="text" class="form-control" name="entryUrl" value="<?php echo isset($_POST['entryUrl']) ? $_POST['entryUrl'] : ''; ?>" />
										</div>
										<div class="form-group">
											<label for="entryNotes"><?php echo $notesField; ?></label>
											<textarea class="form-control" name="entryNotes" rows="3"><?php echo isset($_POST['entryNotes']) ? $_POST['entryNotes'] : ''; ?></textarea>
										</div>
									</div>
									<div class="modal-footer">
										<button type="input" name="submit" value="newentry" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $saveBtn; ?></button>
										<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle-o"></i> <?php echo $cancelBtn; ?></button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="col-md-4">
				<?php if ($set['enablePayments'] == '1') { ?>
					<div class="content no-margin">
						<h4 class="bg-success"><?php echo $projPaymentsText; ?></h4>
						<?php if(mysqli_num_rows($payres) < 1) { ?>
							<dl class="accordion">
								<dt class="noneFound"><a><i class="fa fa-minus-square"></i> <?php echo $noPymtsText; ?></a></dt>
							</dl>
						<?php
							} else {
								echo '<dl class="accordion">';
								while ($a = mysqli_fetch_assoc($payres)) {
									$paymentAmount = $a['paymentAmount'];
									$additionalFee = $a['additionalFee'];
									$payTotal = $paymentAmount+$additionalFee;
									$paymentTotal = $curSym.format_amount($payTotal, 2);
						?>
									<dt><a><?php echo ellipsis($a['paymentFor'],35); ?><span><i class="fa fa-angle-right"></i></span></a></dt>
									<dd class="hideIt">
										<p><strong>
											<?php echo $pymtTotalText; ?>: <?php echo $paymentTotal; ?><br />
											<?php echo $dateReceivedText; ?>: <?php echo $a['paymentDate']; ?><br />
											<?php echo $receivedByTableHead; ?>: <?php echo clean($a['theAdmin']); ?><br />
											<?php echo $paidByText; ?>: <?php echo clean($a['paidBy']); ?>
										</strong></p>
										<p class="mt10"><a href="index.php?page=receipt&paymentId=<?php echo $a['paymentId']; ?>" class="btn btn-info btn-sm btn-icon"><i class="fa fa-print"></i> <?php echo $viewPrintRecptBtn; ?></a></p>
									</dd>
						<?php
								}
								echo '</dl>';
							}
						?>
						<div class="clearfix"></div>
						<?php if ($due != 'Paid in Full') { ?>
							<a href="index.php?page=newPayment&projectId=<?php echo $projectId; ?>" class="btn btn-default btn-block btn-icon"><i class="fa fa-money"></i> <?php echo $makeProjPymntBtn; ?></a>
						<?php } ?>
						<a href="index.php?page=myPayments" class="btn btn-default btn-block btn-icon"><i class="fa fa-credit-card"></i> <?php echo $viewAllPymntsBtn; ?></a>
					</div>
				<?php } ?>

				<div class="content">
					<h4 class="bg-info"><?php echo $dbRecentDisc; ?></h4>
					<?php if(mysqli_num_rows($discres) < 1) { ?>
						<dl class="accordion">
							<dt class="noneFound"><a><i class="fa fa-minus-square"></i> <?php echo $noDiscFound; ?>.</a></dt>
						</dl>
					<?php
						} else {
							echo '<dl class="accordion">';
							while ($b = mysqli_fetch_assoc($discres)) {
					?>
								<dt><a><?php echo ellipsis($b['discussionTitle'],35); ?><span><i class="fa fa-angle-right"></i></span></a></dt>
								<dd class="hideIt">
									<p><?php echo ellipsis($b['discussionText'],150); ?></p>
									<p class="updatedOn">
										<?php echo $postedOnText; ?>: <?php echo $b['discussionDate']; ?>
										<?php if($b['lastUpdated'] != '') { ?>
											<br /><?php echo $lastUpdatedText; ?>: <?php echo $b['lastUpdated']; ?>
										<?php } ?>
									</p>
									<p>
										<a href="index.php?page=viewDiscussion&discussionId=<?php echo $b['discussionId']; ?>" class="btn btn-success btn-sm btn-icon"><i class="fa fa-comment"></i> <?php echo $viewDiscText; ?></a>
									</p>
								</dd>
					<?php
							}
							echo '</dl>';
						}
					?>
					<div class="clearfix"></div>
					<a href="index.php?page=projectDiscussions&projectId=<?php echo $projectId; ?>" class="btn btn-default btn-block btn-icon"><i class="fa fa-comments"></i> <?php echo $viewAllDiscBtn; ?></a>
				</div>

				<div class="content last">
					<h4 class="bg-warning"><?php echo $dbRecentUplds; ?></h4>
					<?php if(mysqli_num_rows($fileres) < 1) { ?>
						<dl class="accordion">
							<dt class="noneFound"><a><i class="fa fa-minus-square"></i> <?php echo $noRecentUplds; ?></a></dt>
						</dl>
					<?php
						} else {
							echo '<dl class="accordion">';
							while ($c = mysqli_fetch_assoc($fileres)) {
					?>
								<dt><a><?php echo ellipsis($c['fileTitle'],35); ?><span><i class="fa fa-angle-right"></i></span></a></dt>
								<dd class="hideIt">
									<p><?php echo ellipsis($c['fileDesc'],125); ?></p>
									<p class="updatedOn">
										<?php echo $uploadedOnText; ?>: <?php echo $c['fileDate']; ?>
									</p>
									<p>
										<a href="index.php?page=viewFile&fileId=<?php echo $c['fileId']; ?>" class="btn btn-primary btn-sm btn-icon"><i class="fa fa-file-o"></i> <?php echo $viewFileBtn; ?></a>
									</p>
								</dd>
					<?php
							}
							echo '</dl>';
						}
					?>
					<div class="clearfix"></div>
					<a href="index.php?page=projectFolders&projectId=<?php echo $projectId; ?>" class="btn btn-default btn-block btn-icon"><i class="fa fa-upload"></i> <?php echo $viewAllFoldersBtn; ?></a>
				</div>
			</div>
		</div>
	</div>
<?php } ?>