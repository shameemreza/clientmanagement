<?php
	$datePicker = 'true';
	$jsFile = 'reports';

	include 'includes/navigation.php';
?>
<div class="contentAlt">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#clientReports" data-toggle="tab"><i class="fa fa-user"></i> <?php echo $clientsText; ?></a></li>
		<li><a href="#projectReports" data-toggle="tab"><i class="fa fa-folder-open"></i> <?php echo $projectsText; ?></a></li>
		<li><a href="#taskReports" data-toggle="tab"><i class="fa fa-tasks"></i> <?php echo $tasksText; ?></a></li>
		<?php if ($set['enablePayments'] == '1') { ?>
			<li><a href="#paymentReports" data-toggle="tab"><i class="fa fa-money"></i> <?php echo $paymentsInvTabLink; ?></a></li>
		<?php
			}
			if ($isAdmin == '1') {
		?>
			<li><a href="#timeReports" data-toggle="tab"><i class="fa fa-clock-o"></i> <?php echo $pageNametimeTracking; ?></a></li>
			<li><a href="#managerReports" data-toggle="tab"><i class="fa fa-male"></i> <?php echo $managersNavLink; ?></a></li>
		<?php } ?>
	</ul>
</div>

<div class="contentAlt">
	<?php if ($msgBox) { echo $msgBox; } ?>

	<div class="tab-content">
		<div class="tab-pane in active bg-trans" id="clientReports">
			<div class="row">
				<div class="col-md-6">
					<div class="content no-margin">
					<h4><?php echo $clientReports1; ?></h4>
						<form action="index.php?action=clientReport" method="post">
							<div class="form-group">
								<label for="showClients"><?php echo $includeInactive; ?></label>
								<select class="form-control" id="showClients" name="showClients">
									<option value="0"><?php echo $allActiveText; ?></option>
									<option value="1"><?php echo $allArchived; ?></option>
									<option value="2"><?php echo $allInactiveText; ?></option>
									<option value="3"><?php echo $allClientsText; ?></option>
								</select>
							</div>
							<button type="input" name="submit" value="clientReport1" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
						</form>
					</div>
				</div>
				<?php if ($set['enablePayments'] == '1') { ?>
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $clientReports2; ?></h4>
							<form action="index.php?action=clientPaymentsReport" method="post">
								<div class="form-group">
									<label for="client"><?php echo $selectReportClientText; ?></label>
									<select class="form-control" id="client" name="client">
										<option value="..."><?php echo $selectOption; ?></option>
										<?php
											// Get the Client List
											$qry1 = "SELECT
														clientId,
														CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient,
														isActive,
														isArchived
													FROM
														clients";
											$res1 = mysqli_query($mysqli, $qry1) or die('-1'.mysqli_error());
											while ($a = mysqli_fetch_assoc($res1)) {
												if ($a['isActive'] == '0' || $a['isArchived'] == '1') { $mark = '*'; } else { $mark = ''; }
										?>
												<option value="<?php echo $a['clientId']; ?>"><?php echo clean($a['theClient']).' '.$mark; ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="clientFullName" id="clientFullName" value="" />
									<span class="help-block"><?php echo $selectReportClientHelp; ?></span>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="fromDate"><?php echo $fromDateField; ?></label>
											<input type="text" class="form-control" name="fromDate" id="fromDate" value="">
											<span class="help-block"><?php echo $fromDateFieldHelp; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="toDate"><?php echo $toDateField; ?></label>
											<input type="text" class="form-control" name="toDate" id="toDate" value="">
											<span class="help-block"><?php echo $toDateFieldHelp; ?></span>
										</div>
									</div>
								</div>
								<button type="input" name="submit" value="clientReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="tab-pane bg-trans" id="projectReports">
			<div class="row">
				<div class="col-md-6">
					<div class="content no-margin">
						<h4>P<?php echo $projReports1; ?></h4>
						<form action="index.php?action=projectReport" method="post">
							<div class="form-group">
								<label for="archivedProjects"><?php echo $includeClosedProj; ?></label>
								<select class="form-control" id="archivedProjects" name="archivedProjects">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $includeClosedProjHelp; ?></span>
							</div>
							<button type="input" name="submit" value="projectReport1" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
						</form>
					</div>
				</div>
				<?php if ($set['enablePayments'] == '1') { ?>
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $projReports2; ?></h4>
							<form action="index.php?action=projectPaymentsReport" method="post">
								<div class="form-group">
									<label for="project"><?php echo $selectProjReport; ?></label>
									<select class="form-control" id="project" name="project">
										<option value="..."><?php echo $selectOption; ?></option>
										<?php
											// Get the Project List
											$qry2 = "SELECT
														clientprojects.projectId,
														clientprojects.projectName,
														clientprojects.archiveProj,
														CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
													FROM
														clientprojects
														LEFT JOIN clients ON clientprojects.clientId = clients.clientId
													ORDER BY clientprojects.projectId";
											$res2 = mysqli_query($mysqli, $qry2) or die('-2'.mysqli_error());
											while ($b = mysqli_fetch_assoc($res2)) {
												if ($b['archiveProj'] == '1') { $mark = '*'; } else { $mark = ''; }
										?>
												<option value="<?php echo $b['projectId']; ?>"><?php echo clean($b['projectName']).' '.$mark; ?> &mdash; Client: <?php echo clean($b['theClient']); ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="projectFullName" id="projectFullName" value="" />
									<span class="help-block"><?php echo $selectProjReportHelp; ?></span>
								</div>
								<button type="input" name="submit" value="projectReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
		<div class="tab-pane bg-trans" id="taskReports">
			<div class="row">
				<div class="col-md-6">
					<div class="content no-margin">
						<h4><?php echo $tasksReport1; ?></h4>
						<form action="index.php?action=allTasksReport" method="post">
							<div class="form-group">
								<label for="completedtasks"><?php echo $includeCompTasks; ?></label>
								<select class="form-control" id="completedtasks" name="completedtasks">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1"><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $includeCompTasksHelp; ?></span>
							</div>
							<button type="input" name="submit" value="projectReport1" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
						</form>
					</div>
				</div>
				<div class="col-md-6">
					<div class="content no-margin">
						<h4><?php echo $tasksReport2; ?></h4>
						<form action="index.php?action=projectTasksReport" method="post">
							<div class="form-group">
								<label for="task"><?php echo $selectProjReport; ?></label>
								<select class="form-control" id="task" name="task">
									<option value="..."><?php echo $selectOption; ?></option>
									<?php
										// Get the Project List
										$qry3 = "SELECT
													clientprojects.projectId,
													clientprojects.projectName,
													clientprojects.archiveProj,
													CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
												FROM
													clientprojects
													LEFT JOIN clients ON clientprojects.clientId = clients.clientId
												ORDER BY clientprojects.projectId";
										$res3 = mysqli_query($mysqli, $qry3) or die('-3'.mysqli_error());
										while ($c = mysqli_fetch_assoc($res3)) {
											if ($c['archiveProj'] == '1') { $mark = '*'; } else { $mark = ''; }
									?>
											<option value="<?php echo $c['projectId']; ?>"><?php echo clean($c['projectName']).' '.$mark; ?> &mdash; <?php echo $clientText.': '.clean($c['theClient']); ?></option>
									<?php } ?>
								</select>
								<input type="hidden" name="taskFullName" id="taskFullName" value="" />
								<span class="help-block"><?php echo $selectProjReportHelp; ?></span>
							</div>
							<button type="input" name="submit" value="projectReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php if ($set['enablePayments'] == '1') { ?>
			<div class="tab-pane bg-trans" id="paymentReports">
				<div class="row">
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $paymentsReport1; ?></h4>
							<p><?php echo $noOptionsAvailQuip; ?></p>
							<form action="index.php?action=allPaymentsReport" method="post">
								<button type="input" name="submit" value="accountingReport1" class="btn btn-primary btn-icon mt10"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>

						<div class="content rptBoxLg">
							<h4><?php echo $paymentsReport2; ?></h4>
							<form action="index.php?action=datedPaymentsReport" method="post">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="payFromDate"><?php echo $fromDateField; ?></label>
											<input type="text" class="form-control" name="payFromDate" id="payFromDate" value="">
											<span class="help-block"><?php echo $fromDateFieldHelp; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="payToDate"><?php echo $toDateField; ?></label>
											<input type="text" class="form-control" name="payToDate" id="payToDate" value="">
											<span class="help-block"><?php echo $toDateFieldHelp; ?></span>
										</div>
									</div>
								</div>
								<button type="input" name="submit" value="accountingReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $invoiceReport1; ?></h4>
							<p><?php echo $noOptionsAvailQuip; ?></p>
							<form action="index.php?action=unpaidInvoicesReport" method="post">
								<button type="input" name="submit" value="invoiceReport1" class="btn btn-primary btn-icon mt10"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>

						<div class="content rptBoxLg">
							<h4><?php echo $invoiceReport2; ?></h4>
							<form action="index.php?action=paidInvoicesReport" method="post">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="invFromDate"><?php echo $fromDateField; ?></label>
											<input type="text" class="form-control" name="invFromDate" id="invFromDate" value="">
											<span class="help-block"><?php echo $fromDateFieldHelp; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="invToDate"><?php echo $toDateField; ?></label>
											<input type="text" class="form-control" name="invToDate" id="invToDate" value="">
											<span class="help-block"><?php echo $toDateFieldHelp; ?></span>
										</div>
									</div>
								</div>
								<button type="input" name="submit" value="invoiceReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php
			}
			if ($isAdmin == '1') {
		?>
			<div class="tab-pane bg-trans" id="timeReports">
				<div class="row">
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $timeReports1; ?></h4>
							<form action="index.php?action=managerTimeReport" method="post">
								<div class="form-group">
									<label for="manager"><?php echo $selectManagerReport; ?></label>
									<select class="form-control" id="manager" name="manager">
										<option value="..."><?php echo $selectOption; ?></option>
										<?php
											// Get the Manager List
											$qry4 = "SELECT
														adminId,
														CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
													FROM
														admins
													WHERE isActive = 1";
											$res4 = mysqli_query($mysqli, $qry4) or die('-4'.mysqli_error());
											while ($d = mysqli_fetch_assoc($res4)) {
										?>
												<option value="<?php echo $d['adminId']; ?>"><?php echo clean($d['theAdmin']); ?></option>
										<?php } ?>
									</select>
									<span class="help-block"><?php echo $selectManagerReportHelp; ?></span>
									<input type="hidden" name="fullAdminName" id="fullAdminName" value="" />
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="timeFromDate"><?php echo $fromDateField; ?></label>
											<input type="text" class="form-control" name="timeFromDate" id="timeFromDate" value="">
											<span class="help-block"><?php echo $fromDateFieldHelp; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="timeToDate"><?php echo $toDateField; ?></label>
											<input type="text" class="form-control" name="timeToDate" id="timeToDate" value="">
											<span class="help-block"><?php echo $toDateFieldHelp; ?></span>
										</div>
									</div>
								</div>
								<button type="input" name="submit" value="clientReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $timeReports2; ?></h4>
							<form action="index.php?action=projectTimeReport" method="post">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="timeProj1"><?php echo $activeProjectsText; ?></label>
											<select class="form-control" id="timeProj1" name="timeProj1">
												<option value=""><?php echo $selectOption; ?></option>
												<?php
													// Get the Project List
													$qry5 = "SELECT
																clientprojects.projectId,
																clientprojects.projectName,
																clientprojects.archiveProj,
																CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
															FROM
																clientprojects
																LEFT JOIN clients ON clientprojects.clientId = clients.clientId
															WHERE clientprojects.archiveProj = 0
															ORDER BY archiveProj";
													$res5 = mysqli_query($mysqli, $qry5) or die('-5'.mysqli_error());
													while ($e = mysqli_fetch_assoc($res5)) {
												?>
														<option value="<?php echo $e['projectId']; ?>"><?php echo clean($e['projectName']).' &mdash; Client: '.clean($e['theClient']); ?></option>
												<?php } ?>
											</select>
											<span class="help-block"><?php echo $currActiveProjects; ?></span>
											<input type="hidden" name="projClientName" id="projClientName" value="" />
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="timeProj2"><?php echo $closedProjectsText; ?></label>
											<select class="form-control" id="timeProj2" name="timeProj2">
												<option value=""><?php echo $selectOption; ?></option>
												<?php
													// Get the Project List
													$qry6 = "SELECT
																clientprojects.projectId,
																clientprojects.projectName,
																clientprojects.archiveProj,
																CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS theClient
															FROM
																clientprojects
																LEFT JOIN clients ON clientprojects.clientId = clients.clientId
															WHERE clientprojects.archiveProj = 1
															ORDER BY archiveProj";
													$res6 = mysqli_query($mysqli, $qry6) or die('-6'.mysqli_error());
													while ($f = mysqli_fetch_assoc($res6)) {
												?>
														<option value="<?php echo $f['projectId']; ?>"><?php echo clean($f['projectName']).' &mdash; Client: '.clean($f['theClient']); ?></option>
												<?php } ?>
											</select>
											<span class="help-block"><?php echo $allClosedProjectsText; ?></span>
											<input type="hidden" name="clientProjName" id="clientProjName" value="" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="projTimeFromDate"><?php echo $fromDateField; ?></label>
											<input type="text" class="form-control" name="projTimeFromDate" id="projTimeFromDate" value="">
											<span class="help-block"><?php echo $fromDateFieldHelp; ?></span>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="projTimeToDate"><?php echo $toDateField; ?></label>
											<input type="text" class="form-control" name="projTimeToDate" id="projTimeToDate" value="">
											<span class="help-block"><?php echo $toDateFieldHelp; ?></span>
										</div>
									</div>
								</div>
								<button type="input" name="submit" value="clientReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane bg-trans" id="managerReports">
				<div class="row">
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $managerReports1; ?></h4>
							<form action="index.php?action=managersReport" method="post">
								<div class="form-group">
									<label for="inactiveManagers"><?php echo $includeInactManagers; ?></label>
									<select class="form-control" id="inactiveManagers" name="inactiveManagers">
										<option value="0"><?php echo $noBtn; ?></option>
										<option value="1"><?php echo $yesBtn; ?></option>
									</select>
								</div>
								<button type="input" name="submit" value="managerReport1" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
					<div class="col-md-6">
						<div class="content no-margin">
							<h4><?php echo $managerReports2; ?></h4>
							<form action="index.php?action=assignedProjectsReport" method="post">
								<div class="form-group">
									<label for="theManager"><?php echo $selectManagerReport; ?></label>
									<select class="form-control" id="theManager" name="theManager">
										<option value="..."><?php echo $selectOption; ?></option>
										<?php
											// Get Manager List
											$qry7 = "SELECT
														adminId,
														CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS theAdmin
													FROM
														admins
													WHERE
														isActive = '1'
													ORDER BY adminId";
											$res7 = mysqli_query($mysqli, $qry7) or die('-7'.mysqli_error());
											while ($g = mysqli_fetch_assoc($res7)) {
										?>
												<option value="<?php echo $g['adminId']; ?>"><?php echo clean($g['theAdmin']); ?></option>
										<?php } ?>
									</select>
									<input type="hidden" name="theManagerName" id="theManagerName" value="" />
								</div>
								<button type="input" name="submit" value="managerReport2" class="btn btn-primary btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $runReportBtn; ?></button>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
	</div>
</div>

<div class="content last">
	<p class="text-muted"><i class="fa fa-info-circle mr5"></i> <?php echo $filterOptionsQuip; ?></p>
</div>