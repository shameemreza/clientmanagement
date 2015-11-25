<?php
	// Get Avatar Image
	$avatarDir = $set['avatarFolder'];
    $a = "SELECT clientAvatar, DATE_FORMAT(createDate,'%b %d %Y') AS createDate FROM clients WHERE clientId = ".$clientId;
    $b = mysqli_query($mysqli, $a) or die('Avatar Dir Error '.mysqli_error());
	$c = mysqli_fetch_assoc($b);

	// Get Unread Message Count
	$unreadsql = "SELECT 'X' FROM privatemessages WHERE toClientId = ".$clientId." AND toRead = 0";
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
						privatemessages.toClientId = ".$clientId." AND
						privatemessages.toRead = 0
					ORDER BY
						orderDate DESC";
    $unreadmsgres = mysqli_query($mysqli, $unreadmsgsql) or die('Unread Msg Error '.mysqli_error());
?>
<body>
	<div id="wrap">

		<div class="navbar navbar-default navbar-fixed-top no-print" role="navigation">
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
						<li><a href="index.php?page=myCalendar"><?php echo $calendarNavLink; ?></a></li>
						<?php if ($set['enablePayments'] == '1') { ?>
							<li><a href="index.php?page=myInvoices"><?php echo $invoicesNavLink; ?></a></li>
						<?php } ?>
						<li><a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $projectsNavLink; ?></a>
							<ul class="dropdown-menu">
								<li><a href="index.php?page=openProjects"> <?php echo $openProjNavLink; ?></a></li>
								<li><a href="index.php?page=closedProjects"><?php echo $closedProjNavLink; ?></a></li>
								<li><a href="index.php?page=myRequests"><?php echo $projReqNavLink; ?></a></li>
							</ul>
						</li>
						<?php if(mysqli_num_rows($unreadmsgres) > 0) { ?>
							<li class="dropdown notify" data-toggle="tooltip" data-placement="left" title="<?php echo $myMsgNavLink; ?>">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-envelope-o"></i>
									<span class="label"><?php echo $unread; ?></span>
								</a>
								<ul class="dropdown-menu notifications">
									<div id="mail">
										<div class="list-group">
											<a href="index.php?page=inbox" class="list-group-item active">
												<?php echo $youHaveNotice.' '.$unread.' '.$unreadMsgTitle; ?>
												<small class="pull-right label label-default"><?php echo $viewAll; ?> <i class="fa fa-long-arrow-right"></i></small>
											</a>
											<?php while ($msg = mysqli_fetch_assoc($unreadmsgres)) { ?>
												<a href="index.php?page=inbox" class="list-group-item no-lr-border">
													<h4 class="text-small">
														<strong><?php echo clean($msg['messageTitle']); ?></strong><br>
														<small><?php echo $from; ?>
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
							<li data-toggle="tooltip" data-placement="left" title="<?php echo $myMsgNavLink; ?>"><a href="index.php?page=inbox"><i class="fa fa-envelope-o"></i></a></li>
						<?php } ?>
					</ul>
					
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown user-menu">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown">
								<span><?php echo $clientFullName; ?> <i class="fa fa-angle-down"></i></span>
							</a>
							<ul class="dropdown-menu">
								<li class="user-header">
									<img src="<?php echo $avatarDir.$c['clientAvatar']; ?>" alt="Avatar" />
									<p>
										<?php echo $clientFullName; ?>
										<small>
											<?php echo $set['siteName'].' '.$clientTitle; ?>
										</small>
										<small><?php echo $memnberSince.' '.$c['createDate']; ?></small>
									</p>
								</li>
								<li class="user-footer">
									<div class="pull-left">
										<a href="index.php?page=myProfile" class="btn btn-default"><i class="fa fa-user"></i> <?php echo $myProfileLink; ?></a>
									</div>
									<div class="pull-right">
										<a data-toggle="modal" href="#signOut" class="btn btn-default"><i class="fa fa-sign-out"></i> <?php echo $signOutLink; ?></a>
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
						<p class="lead"><?php echo $clientFullName.', '.$signOutConfirm; ?></p>
					</div>
					<div class="modal-footer">
						<a href="index.php?action=logout" class="btn btn-success btn-icon-alt">Sign Out <i class="fa fa-sign-out"></i></a>
						<button type="button" class="btn btn-default btn-icon" data-dismiss="modal"><i class="fa fa-times-circle"></i> <?php echo $cancelBtn; ?></button>
					</div>
				</div>
			</div>
		</div>
		
		<div class="userbar no-print">
			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<a href="index.php"><img alt="clientmanage" class="logo" src="images/logo.png" /></a>
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