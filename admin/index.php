<?php
	// Check if install.php is present
	if(is_dir('../install')) {
		header('Location: ../install/install.php');
	} else {
		session_start();
		if (!isset($_SESSION['adminId'])) {
			header ('Location: login.php');
			exit;
		}

		// Logout
		if (isset($_GET['action']) && $_GET['action'] == 'logout') {
			session_destroy();
			header('Location: login.php');
		}

		// Access DB Info
		include('../config.php');

		// Get Settings Data
		include ('../includes/settings.php');
		$set = mysqli_fetch_assoc($setRes);

		// Set Localization
		$local = $set['localization'];
		switch ($local) {
			case 'en':		include ('language/en.php');		break;
		}

		// Include Functions
		include('../includes/functions.php');

		// Keep some data available
		$adminId 		= $_SESSION['adminId'];
		$adminEmail 	= $_SESSION['adminEmail'];
		$adminFullName 	= $_SESSION['adminFirstName'].' '.$_SESSION['adminLastName'];
		$isAdmin 		= $_SESSION['isAdmin'];
		$adminRole 		= $_SESSION['adminRole'];

		// Load the Requested Page
		if (isset($_GET['action']) && $_GET['action'] == 'searchResults') {
			$page = 'searchResults';
			$pageName = $pageNamesearchResults;
		} else if (isset($_GET['action']) && $_GET['action'] == 'myProfile') {
			$page = "myProfile";
			$pageName = $pageNamemyProfile;
		} else if (isset($_GET['action']) && $_GET['action'] == 'timeLogs') {
			$page = "timeLogs";
			$pageName = $pageNametimeLogs;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'myCalendar') {
			$page = 'myCalendar';
			$pageName = $pageNamemyCalendar;
			$addCss = '
				<link rel="stylesheet" type="text/css" href="../css/fullcalendar.css" />
				<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />
				<link rel="stylesheet" type="text/css" href="../css/colorpicker.css" />
			';
		} else if (isset($_GET['action']) && $_GET['action'] == 'inbox') {
			$page = 'inbox';
			$pageName = $pageNameinbox;
		} else if (isset($_GET['action']) && $_GET['action'] == 'sent') {
			$page = 'sent';
			$pageName = $pageNamesent;
		} else if (isset($_GET['action']) && $_GET['action'] == 'archived') {
			$page = 'archived';
			$pageName = $pageNamearchived;
		} else if (isset($_GET['action']) && $_GET['action'] == 'personalTasks') {
			$page = 'personalTasks';
			$pageName = $pageNamepersonalTasks;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectTasks') {
			$page = 'projectTasks';
			$pageName = $pageNameprojectTasks;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'closedTasks') {
			$page = 'closedTasks';
			$pageName = $pageNameclosedTasks;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewTask') {
			$page = 'viewTask';
			$pageName = $pageNameviewTask;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'activeClients') {
			$page = 'activeClients';
			$pageName = $pageNameactiveClients;
		} else if (isset($_GET['action']) && $_GET['action'] == 'inactiveClients') {
			$page = 'inactiveClients';
			$pageName = $pageNameinactiveClients;
		} else if (isset($_GET['action']) && $_GET['action'] == 'newClient') {
			$page = 'newClient';
			$pageName = $pageNamenewClient;
		} else if (isset($_GET['action']) && $_GET['action'] == 'emailClients') {
			$page = 'emailClients';
			$pageName = $pageNameemailClients;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewClient') {
			$page = 'viewClient';
			$pageName = $pageNameviewClient;
		} else if (isset($_GET['action']) && $_GET['action'] == 'openProjects') {
			$page = 'openProjects';
			$pageName = $pageNameopenProjects;
		} else if (isset($_GET['action']) && $_GET['action'] == 'closedProjects') {
			$page = 'closedProjects';
			$pageName = $pageNameclosedProjects;
		} else if (isset($_GET['action']) && $_GET['action'] == 'newProject') {
			$page = 'newProject';
			$pageName = $pageNamenewProject;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewProject') {
			$page = 'viewProject';
			$pageName = $pageNameviewProject;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectAccount') {
			$page = 'projectAccount';
			$pageName = $pageNameprojectAccount;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectPayments') {
			$page = 'projectPayments';
			$pageName = $pageNameprojectPayments;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewPayment') {
			$page = 'viewPayment';
			$pageName = $pageNameviewPayment;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'receipt') {
			$page = 'receipt';
			$pageName = $pageNamereceipt;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectDiscussions') {
			$page = 'projectDiscussions';
			$pageName = $pageNameprojectDiscussions;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewDiscussion') {
			$page = 'viewDiscussion';
			$pageName = $pageNameviewDiscussion;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectFolders') {
			$page = 'projectFolders';
			$pageName = $pageNameprojectFolders;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewFolder') {
			$page = 'viewFolder';
			$pageName = $pageNameviewFolder;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectFiles') {
			$page = 'projectFiles';
			$pageName = $pageNameprojectFiles;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewFile') {
			$page = 'viewFile';
			$pageName = $pageNameviewFile;
		} else if (isset($_GET['action']) && $_GET['action'] == 'activeManagers') {
			$page = 'activeManagers';
			$pageName = $pageNameactiveManagers;
		} else if (isset($_GET['action']) && $_GET['action'] == 'inactiveManagers') {
			$page = 'inactiveManagers';
			$pageName = $pageNameinactiveManagers;
		} else if (isset($_GET['action']) && $_GET['action'] == 'newManager') {
			$page = 'newManager';
			$pageName = $pageNamenewManager;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewManager') {
			$page = 'viewManager';
			$pageName = $pageNameviewManager;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectRequests') {
			$page = 'projectRequests';
			$pageName = $pageNameprojectRequests;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewRequest') {
			$page = 'viewRequest';
			$pageName = $pageNameviewRequest;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'siteAlerts') {
			$page = 'siteAlerts';
			$pageName = $pageNamesiteAlerts;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'templates') {
			$page = 'templates';
			$pageName = $pageNamtemplatese;
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewTemplate') {
			$page = 'viewTemplate';
			$pageName = $pageNameviewTemplate;
		} else if (isset($_GET['action']) && $_GET['action'] == 'invoices') {
			$page = 'invoices';
			$pageName = $pageNameinvoices;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewInvoice') {
			$page = 'viewInvoice';
			$pageName = $pageNameviewInvoice;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'timeTracking') {
			$page = 'timeTracking';
			$pageName = $pageNametimeTracking;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'viewTime') {
			$page = 'viewTime';
			$pageName = $pageNameviewTime;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'siteSettings') {
			$page = 'siteSettings';
			$pageName = $pageNamesiteSettings;
		} else if (isset($_GET['action']) && $_GET['action'] == 'importData') {
			$page = 'importData';
			$pageName = $pageNameimportData;
		}
		// Reports
		else if (isset($_GET['action']) && $_GET['action'] == 'reports') {
			$page = 'reports';
			$pageName = $pageNamereports;
			$addCss = '<link rel="stylesheet" type="text/css" href="../css/datetimepicker.css" />';
		} else if (isset($_GET['action']) && $_GET['action'] == 'clientReport') {
			$page = 'reports/clientReport';
			$pageName = $pageNameclientReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'clientPaymentsReport') {
			$page = 'reports/clientPaymentsReport';
			$pageName = $pageNameclientPaymentsReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectReport') {
			$page = 'reports/projectReport';
			$pageName = $pageNameprojectReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectPaymentsReport') {
			$page = 'reports/projectPaymentsReport';
			$pageName = $pageNameprojectPaymentsReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'allTasksReport') {
			$page = 'reports/allTasksReport';
			$pageName = $pageNameallTasksReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectTasksReport') {
			$page = 'reports/projectTasksReport';
			$pageName = $pageNameprojectTasksReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'allPaymentsReport') {
			$page = 'reports/allPaymentsReport';
			$pageName = $pageNameallPaymentsReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'datedPaymentsReport') {
			$page = 'reports/datedPaymentsReport';
			$pageName = $pageNamedatedPaymentsReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'unpaidInvoicesReport') {
			$page = 'reports/unpaidInvoicesReport';
			$pageName = $pageNameunpaidInvoicesReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'paidInvoicesReport') {
			$page = 'reports/paidInvoicesReport';
			$pageName = $pageNamepaidInvoicesReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'managerTimeReport') {
			$page = 'reports/managerTimeReport';
			$pageName = $pageNamemanagerTimeReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectTimeReport') {
			$page = 'reports/projectTimeReport';
			$pageName = $pageNameprojectTimeReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'managersReport') {
			$page = 'reports/managersReport';
			$pageName = $pageNamemanagersReport;
		} else if (isset($_GET['action']) && $_GET['action'] == 'assignedProjectsReport') {
			$page = 'reports/assignedProjectsReport';
			$pageName = $pageNameassignedProjectsReport;
		}
		// Report Exports
		else if (isset($_GET['action']) && $_GET['action'] == 'clientsExport') {
			$page = 'reports/clientsExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'clientPaymentsExport') {
			$page = 'reports/clientPaymentsExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectsExport') {
			$page = 'reports/projectsExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectPaymentsExport') {
			$page = 'reports/projectPaymentsExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'allTasksExport') {
			$page = 'reports/allTasksExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectTasksExport') {
			$page = 'reports/projectTasksExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'allPaymentsExport') {
			$page = 'reports/allPaymentsExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'datedPaymentsExport') {
			$page = 'reports/datedPaymentsExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'unpaidInvoicesExport') {
			$page = 'reports/unpaidInvoicesExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'paidInvoicesExport') {
			$page = 'reports/paidInvoicesExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'managerTimeExport') {
			$page = 'reports/managerTimeExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'projectTimeExport') {
			$page = 'reports/projectTimeExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'managersExport') {
			$page = 'reports/managersExport';
		} else if (isset($_GET['action']) && $_GET['action'] == 'assignedProjectsExport') {
			$page = 'reports/assignedProjectsExport';
		}
		// Show Dashboard
		else {
			$page = 'dashboard';
			$pageName = $pageNamedashboard;
		}

		// Load the Header for all pages except
		if (
			($page != "reports/clientsExport") && ($page != "reports/clientPaymentsExport") && ($page != "reports/projectsExport") &&
			($page != "reports/projectPaymentsExport") && ($page != "reports/allTasksExport") && ($page != "reports/projectTasksExport") &&
			($page != "reports/allPaymentsExport") && ($page != "reports/datedPaymentsExport") && ($page != "reports/unpaidInvoicesExport") &&
			($page != "reports/paidInvoicesExport") && ($page != "reports/managersExport") && ($page != "reports/assignedProjectsExport") &&
			($page != "reports/managerTimeExport") && ($page != "reports/projectTimeExport")
		) {
			include('includes/header.php');
		}

		if (file_exists('pages/'.$page.'.php')) {
			// Load the Page
			include('pages/'.$page.'.php');
		} else {
			include 'includes/navigation.php';
			// Else Display an Error
			echo '
					<div class="content last">
						<h3>'.$pageNotFoundHeader.'</h3>
						<div class="alertMsg default no-margin">
							<i class="fa fa-warning"></i> '.$pageNotFoundQuip.' "'.$pageName.'"
						</div>
					</div>
				';
		}

		// Load the Footer for all pages except
		if (
			($page != "reports/clientsExport") && ($page != "reports/clientPaymentsExport") && ($page != "reports/projectsExport") &&
			($page != "reports/projectPaymentsExport") && ($page != "reports/allTasksExport") && ($page != "reports/projectTasksExport") &&
			($page != "reports/allPaymentsExport") && ($page != "reports/datedPaymentsExport") && ($page != "reports/unpaidInvoicesExport") &&
			($page != "reports/paidInvoicesExport") && ($page != "reports/managersExport") && ($page != "reports/assignedProjectsExport") &&
			($page != "reports/managerTimeExport") && ($page != "reports/projectTimeExport")
		) {
			include('includes/footer.php');
		}
	}
?>