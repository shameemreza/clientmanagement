<?php
	// Get Avatar Image
	$avatarDir = $set['avatarFolder'];
    $a = "SELECT adminAvatar, DATE_FORMAT(createDate,'%b %d %Y') AS createDate, adminRole FROM admins WHERE adminId = ".$adminId;
    $b = mysqli_query($mysqli, $a) or die('Avatar Dir Error '.mysqli_error());
	$c = mysqli_fetch_assoc($b);
	
	// Get Unread Message Count
	$unreadsql = "SELECT 'X' FROM privatemessages WHERE toAdminId = ".$adminId." AND toRead = 0";
	$unreadtotal = mysqli_query($mysqli, $unreadsql) or die('Unread Msg Count Error '.mysqli_error());
	$unread = mysqli_num_rows($unreadtotal);
	
    $unreadmsgsql = "SELECT
						privatemessages.messageId,
						privatemessages.adminId,
						privatemessages.clientId,
						privatemessages.messageTitle,
						privatemessages.messageText,
						DATE_FORMAT(privatemessages.messageDate,'%b %d %Y') AS messageDate,
						UNIX_TIMESTAMP(privatemessages.messageDate) AS orderDate,
						CONCAT(clients.clientFirstName,' ',clients.clientLastName) AS clientSent,
						CONCAT(admins.adminFirstName,' ',admins.adminLastName) AS adminSent
					FROM
						privatemessages
						LEFT JOIN clients ON privatemessages.clientId = clients.clientId
						LEFT JOIN admins ON privatemessages.adminId = admins.adminId
					WHERE
						privatemessages.toAdminId = ".$adminId." AND
						privatemessages.toRead = 0
					ORDER BY
						orderDate DESC";
    $unreadmsgres = mysqli_query($mysqli, $unreadmsgsql) or die('Unread Msg Error '.mysqli_error());
	
	// Get Task Count
	$taskcountsql = "SELECT 'X' FROM tasks WHERE adminId = ".$adminId." AND isClosed = 0";
	$taskcounttotal = mysqli_query($mysqli, $taskcountsql) or die('Unread Task Count Error '.mysqli_error());
	$taskcount = mysqli_num_rows($taskcounttotal);
	
	$opentasksql = "SELECT
						taskId,
						taskTitle,
						taskDesc,
						taskStatus,
						DATE_FORMAT(taskDue,'%b %d %Y') AS dueDate
					FROM
						tasks
					WHERE
						adminId = ".$adminId." AND
						isClosed = 0
					ORDER BY
						taskId";
    $opentaskres = mysqli_query($mysqli, $opentasksql) or die('Open Tasks Error '.mysqli_error());
?>
<body>
	<div id="wrap">

		<div class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						<span class="sr-only"><?php echo $toggleNav; ?></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				
				<div class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<li><a href="index.php"><?php echo $dashboardNavLink; ?></a></li>
						<li><a href="index.php?action=myCalendar"><?php echo $calendarNavLink; ?></a></li>
						<?php if(mysqli_num_rows($unreadmsgres) > 0) { ?>
							<li class="dropdown notify" data-toggle="tooltip" data-placement="left" title="<?php echo $myMsgNavLink; ?>">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-envelope-o"></i>
									<span class="label"><?php echo $unread; ?></span>
								</a>
								<ul class="dropdown-menu notifications">
									<div id="mail">
										<div class="list-group">
											<a href="index.php?action=inbox" class="list-group-item active">
												<?php echo $unreadText1.' '.$unread.' '.$unreadText2; ?>
												<small class="pull-right label label-default"><?php echo $viewAllText; ?> <i class="fa fa-long-arrow-right"></i></small>
											</a>
											<?php while ($msg = mysqli_fetch_assoc($unreadmsgres)) { ?>
												<a href="index.php?action=inbox" class="list-group-item no-lr-border">
													<h4 class="text-small">
														<strong><?php echo clean($msg['messageTitle']); ?></strong><br>
														<small>From:
															<?php
																if ($msg['adminId'] == '0') {
																	echo clean($msg['clientSent']).' on '.$msg['messageDate'];
																} else {
																	echo clean($msg['adminSent']).' on '.$msg['messageDate'];
																}
															?>
														</small>
													</h4>
												</a>
											<?php } ?>
										</div>
									</div>
								</ul>
							</li>
						<?php } else { ?>
							<li data-toggle="tooltip" data-placement="left" title="<?php echo $myMsgNavLink; ?>">
								<a href="index.php?action=inbox"><i class="fa fa-envelope-o"></i></a>
							</li>
						<?php } ?>
						
						<?php if(mysqli_num_rows($opentaskres) > 0) { ?>
							<li data-toggle="tooltip" data-placement="left" title="<?php echo $myTasksNavLink; ?>">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-tasks"></i>
									<span class="label"><?php echo $taskcount; ?></span>
								</a>
								<ul class="dropdown-menu notifications">
									<div id="tasks">
										<div class="list-group">
											<a href="index.php?action=personalTasks" class="list-group-item active">
												<?php echo $unreadText1.' '.$taskcount.' '.$openTasksText; ?>
												<small class="pull-right label label-default"><?php echo $viewAllText; ?> <i class="fa fa-long-arrow-right"></i></small>
											</a>
											<?php while ($tsk = mysqli_fetch_assoc($opentaskres)) { ?>
												<a href="index.php?action=viewTask&taskId=<?php echo $tsk['taskId']; ?>" class="list-group-item no-lr-border">
													<h4 class="text-small">
														<strong><?php echo clean($tsk['taskTitle']); ?></strong><br>
														<small>
															<?php echo $statusText.': '.clean($tsk['taskStatus']); ?>
															<span class="pull-right"><?php echo $dueOnText.': '.$tsk['dueDate']; ?></span>
														</small>
													</h4>
												</a>
											<?php } ?>
										</div>
									</div>
								</ul>
							</li>
						<?php } else { ?>
							<li data-toggle="tooltip" data-placement="left" title="<?php echo $myTasksNavLink; ?>">
								<a href="index.php?action=personalTasks"><i class="fa fa-tasks"></i></a>
							</li>
						<?php } ?>
					</ul>
					
					<ul class="nav navbar-nav navbar-right">
						<li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $clientsNavLink; ?></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?action=activeClients"><?php echo $activeClientsNavLink; ?></a></li>
								<li><a href="index.php?action=inactiveClients"><?php echo $inactiveClientsNavLink; ?></a></li>
								<li><a href="index.php?action=newClient"><?php echo $newClientNavLink; ?></a></li>
								<?php if ($isAdmin == '1') { ?>
									<li><a href="index.php?action=emailClients"><?php echo $emailClientsNavLink; ?></a></li>
								<?php } ?>
							</ul>
						</li>
						<li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $projectsNavLink; ?></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?action=openProjects"><?php echo $openProjNavLink ?></a></li>
								<li><a href="index.php?action=closedProjects"><?php echo $closedProjNavLink; ?></a></li>
								<li><a href="index.php?action=newProject"><?php echo $newProjNavLink; ?></a></li>
							</ul>
						</li>
						<?php if ($isAdmin == '1') { ?>
							<li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $managersNavLink; ?></a>
								<ul class="dropdown-menu">
									<li><a href="index.php?action=activeManagers"><?php echo $activeManagersNavLink; ?></a></li>
									<li><a href="index.php?action=inactiveManagers"><?php echo $inactiveManagersNavLink; ?></a></li>
									<li><a href="index.php?action=newManager"><?php echo $newManagerNavLink; ?></a></li>
								</ul>
							</li>
						<?php } ?>
						<li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $adminNavLink; ?></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?action=projectRequests"><?php echo $projRequestsNavLank; ?></a></li>
								<li><a href="index.php?action=siteAlerts"><?php echo $siteAlertsNavLink; ?></a></li>
								<li><a href="index.php?action=templates"><?php echo $tempaltesNavLink; ?></a></li>
								<?php if ($set['enablePayments'] == '1') { ?>
									<li><a href="index.php?action=invoices"><?php echo $invoicesNavLink; ?></a></li>
								<?php } ?>
								<li><a href="index.php?action=reports"><?php echo $reportsNavLink; ?></a></li>
								<?php if ($isAdmin == '1') { ?>
									<li><a href="index.php?action=timeTracking"><?php echo $timeTrackNavLink; ?></a></li>
									<li><a href="index.php?action=siteSettings"><?php echo $siteSettingsNavLink; ?></a></li>
								<?php } ?>
							</ul>
						</li>
						<li class="dropdown" data-toggle="tooltip" data-placement="left" title="<?php echo $searchTootip; ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search"></i></a>
							<div class="dropdown-menu dropdown-form">
								<form action="index.php?action=searchResults" method="post">
									<div class="input-group custom-search-form">
										<input type="text" class="form-control" required="" name="searchTerm" placeholder="<?php echo $searchPlaceholder; ?>">
										<span class="input-group-btn">
											<button type="input" name="submit" value="search" class="btn btn-search"><span class="fa fa-search"></span></button>
										</span>
									</div>
								</form>
							</div>
						</li>
						<li class="dropdown user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<span><?php echo $adminFullName; ?> <i class="fa fa-angle-down"></i></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-header">
									<img src="../<?php echo $avatarDir.$c['adminAvatar']; ?>" alt="Avatar" />
									<p>
										<?php echo $adminFullName; ?>
										<small><?php echo clean($c['adminRole']); ?></small>
										<small><?php echo $memberSinceText.' '.$c['createDate']; ?></small>
									</p>
								</li>
								<li class="user-footer">
									<div class="row">
										<div class="col-md-4">
											<a href="index.php?action=myProfile" class="btn btn-default btn-sm"><?php echo $myProfileNavLink; ?></a>
										</div>
										<div class="col-md-4">
											<a href="index.php?action=timeLogs" class="btn btn-default btn-sm"><?php echo $timeLogsNavLink; ?></a>
										</div>
										<div class="col-md-4">
											<a data-toggle="modal" href="#signOut" class="btn btn-default btn-sm"><?php echo $signOutNavLink; ?></a>
										</div>
									</div>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</div>
		</div>
		
		<div class="modal fade" id="signOut" tabindex="-1" role="dialog" aria-labelledby="signOutLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-body">
						<p class="lead"><?php echo $adminFullName.', '.$signOutConf; ?></p>
					</div>
					<div class="modal-footer">
						<a href="index.php?action=logout" class="btn btn-success btn-icon-alt"><?php echo $signOutNavLink; ?> <i class="fa fa-sign-out"></i></a>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="userbar">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<a href="index.php"><img alt="clientmanage" class="logo" src="../images/logo.png" /></a>
					</div>
					<div class="col-md-6">
						<p>
							<?php echo date('l').' the '.date('jS \of F, Y'); ?><br />
							<span class="clock">0:00:00 AM</span>
						</p>
					</div>
				</div>
			</div>
		</div>

		<div class="container">