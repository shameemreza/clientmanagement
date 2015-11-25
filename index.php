<?php
	// Check if install.php is present
	if(is_dir('install')) {
		header('Location: install/install.php');
	} else {
		session_start();
		if (!isset($_SESSION['clientId'])) {
			header ('Location: login.php');
			exit;
		}

		// Logout
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
			if ($action == 'logout') {
				session_destroy();
				header('Location: login.php');
			}
		}

		// Access DB Info
		include('config.php');

		// Get Settings Data
		include ('includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'en':		include ('language/en.php');		break;
		}

		// Include Functions
		include('includes/functions.php');

		// Keep some Client data available
		$clientId 			= $_SESSION['clientId'];
		$clientEmail 		= $_SESSION['clientEmail'];
		$clientFullName 	= $_SESSION['clientFirstName'].' '.$_SESSION['clientLastName'];
		$clientCompany		= $_SESSION['clientCompany'];

		// Link to the Page
		if (isset($_GET['page']) && $_GET['page'] == 'myProfile') {
			$page = 'myProfile';
			$pageName = $myProfilePageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'myCalendar') {
			$page = 'myCalendar';
			$pageName = $myCalendarPageName;
			$addCss = '
				<link rel="stylesheet" type="text/css" href="css/fullcalendar.css" />
				<link rel="stylesheet" type="text/css" href="css/datetimepicker.css" />
				<link rel="stylesheet" type="text/css" href="css/colorpicker.css" />
			';
		} else if (isset($_GET['page']) && $_GET['page'] == 'inbox') {
			$page = 'inbox';
			$pageName = $inboxPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'sent') {
			$page = 'sent';
			$pageName = $sentPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'archived') {
			$page = 'archived';
			$pageName = $archivedPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'openProjects') {
			$page = 'openProjects';
			$pageName = $openProjectsPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'closedProjects') {
			$page = 'closedProjects';
			$pageName = $closedProjectsPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewProject') {
			$page = 'viewProject';
			$pageName = $viewProjectPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'myRequests') {
			$page = 'myRequests';
			$pageName = $myRequestsPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewRequest') {
			$page = 'viewRequest';
			$pageName = $viewRequestPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'myPayments') {
			$page = 'myPayments';
			$pageName = $myPaymentsPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'newPayment') {
			$page = 'newPayment';
			$pageName = $newPaymentPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'receipt') {
			$page = 'receipt';
			$pageName = $receiptPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewDiscussion') {
			$page = 'viewDiscussion';
			$pageName = $viewDiscussionPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'projectDiscussions') {
			$page = 'projectDiscussions';
			$pageName = $projectDiscussionsPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewFile') {
			$page = 'viewFile';
			$pageName = $viewFilePageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewFolder') {
			$page = 'viewFolder';
			$pageName = $viewFolderPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'projectFolders') {
			$page = 'projectFolders';
			$pageName = $projectFoldersPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'projectFiles') {
			$page = 'projectFiles';
			$pageName = $projectFilesPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'projectAccount') {
			$page = 'projectAccount';
			$pageName = $projectAccountPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'myInvoices') {
			$page = 'myInvoices';
			$pageName = $myInvoicesPageName;
		} else if (isset($_GET['page']) && $_GET['page'] == 'viewInvoice') {
			$page = 'viewInvoice';
			$pageName = $viewInvoicePageName;
		} else {
			$page = 'dashboard';
			$pageName = $dashboardPageName;
		}

		include('includes/header.php');

		if (file_exists('pages/'.$page.'.php')) {
			// Load the Page
			include('pages/'.$page.'.php');
		} else {
			include 'includes/navigation.php';
			// Else Display an Error
			echo '
					<div class="content last">
						<h3>'.$pageNotFoundHeader.'</h3>
						<div class="alertMsg default">
							<i class="fa fa-warning"></i> '.$pageNotFoundQuip.' "'.$pageName.'"
						</div>
					</div>
				';
		}

		include('includes/footer.php');
	}
?>