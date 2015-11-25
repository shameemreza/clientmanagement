<?php
// All Pages - Globals
// --------------------------------------------------------------------------------------------------
$curSym		 				= "$";
$accessErrorHeader			= "Access Error";
$permissionDenied			= "Permission Denied. You can not access this page.";
$pageNotFoundHeader			= "Page Not Found &mdash; 404 Error";
$pageNotFoundQuip			= "Can not find the page";
$htmlNotAllowed				= "HTML not allowed &amp; will be saved as plain text.";
$numbersOnlyHelp			= "No currency symbols (Format: 500.00).";
$hoursMinsSecsTooltip		= "Hours:Minutes:Seconds";
$dateFormatHelp				= "(Date Format: 0000-00-00).";

$cancelBtn					= "Cancel";
$closeBtn					= "Close";
$okBtn						= "OK";
$yesBtn						= "Yes";
$noBtn						= "No";
$saveChangesBtn				= "Save Changes";
$saveBtn					= "Save";
$deleteBtn					= "Delete";
$updateBtn					= "Update";
$selectOption				= "Select...";

$emailAddressField			= "Email Address";
$validEmailAddyHelp			= "A Valid email. Used for logging in and notifications.";
$passwordField				= "Password";
$passwordFieldHelp			= "Passwords are case-sensitive";
$repeatPasswordField		= "Retype Password";
$repeatpasswordHelp			= "Please type the Password again. Passwords MUST Match.";
$passwordsNotMatchMsg		= "Passwords do not match. Please check your entries.";

$emailAddressHelp			= "The email address associated with your account.";
$emailReqMsg				= "Email Address is required.";
$passworReqMsg				= "Account Password is required.";
$firstNameReqMsg			= "First Name is required.";
$lastNameReqMsg				= "Last Name is required.";
$phoneReqMsg				= "Primary Phone Number is required.";
$mailingAddyReqMsg			= "Mailing Address is required.";

$dateFormat					= "Format: YYYY-MM-DD";
$timeFormat					= "Format: HH:MM:SS";
$timeFormat2				= "Format: HH:MM";
$dateTimeFormat				= "Format: YYYY-MM-DD HH:MM:SS";

$viewProject				= "View Project";
$actionsText				= "Actions";
$activeText					= "Active";
$inactiveText				= "Inactive";
$archivedText				= "Archived";

$emailLink					= "You can log in to your account at ".$set['installUrl'];
$emailThankYou				= "Thank you,<br>".$set['siteName'];
$emailErrorMsg				= "There was an error, and the email could not be sent at this time.";

// Login
// --------------------------------------------------------------------------------------------------
$pageHeadTitle				= "Log In";
$loginTitle					= "Please Sign In";
$resetPasswordBtn			= "Reset Password";
$signInBtn					= "Sign In";
$dontHaveAccountLinkQuip	= "Don't have an Account?";
$createAccountLink			= "Create One";
$passwordResetTitle			= "Your password has been successfully reset.";
$passwordResetQuip			= "Please check your email for your new password, and information on how to update your account.";
$createAccountTitle			= "Create a New Account";
$validEmailAddressHelp		= "A valid email address. The new account information will be sent to this address.";
$newAccountFirstName		= "First Name";
$newAccountLastName			= "Last Name";
$newAccountPassword			= "Type a Password for your new Account.";
$createAccountBtn			= "Create Account";
$loginFailedMsg				= "Log in failed. Please check your entries.";
$inactiveAccountMsg			= "Your account is not currently active, and you can not log in.";
$accountNotFoundMsg			= "Account not found for that email address. Please check your entries.";
$passwordResetMsg			= "Your password has been reset, and an email containing instructions on how to update your account has been sent.";
$newAccountCreateError		= "There was an error, and the New Account could not be created at this time.";
$emailInUseMsg				= "There is all ready an account registered with that email address.";
$newAccountCreatedMsg		= "Your New Account has been created and an email has been sent.";

// Manager Account Activation
// --------------------------------------------------------------------------------------------------
$activatePageTitle 			= "Manager Account Activation";
$activateMsg1				= "Thank you for verifying your email addess and activating your account.";
$activateMsg2				= "Your Manager account has been activated, and you can now log in.";
$activateLoginBtn			= "All Set! Go ahead and Sign In";
$activateMsg3				= "You have all ready verified your email addess and activated your Manager account.";
$activateMsg4				= "Please check your email for the Manager Account Activation Link.";
$activateMsg5				= "You cannot directly access this page. please use the link that has been sent to your email.";
$activateLoginBtn1			= "Get Started by Signing In";

// index.php
// --------------------------------------------------------------------------------------------------
$pageNamesearchResults			= "Search Results";
$pageNamemyProfile				= "My Profile";
$pageNametimeLogs 				= "My Time Logs";
$pageNamemyCalendar 			= "My Calendar";
$pageNameinbox 					= "Private Messages: Inbox";
$pageNamesent 					= "Private Messages: Sent Items";
$pageNamearchived 				= "Private Messages: Archived Items";
$pageNamepersonalTasks 			= "Personal Tasks";
$pageNameprojectTasks 			= "Project Tasks";
$pageNameclosedTasks 			= "Closed Tasks";
$pageNameviewTask 				= "View Task";
$pageNameactiveClients 			= "Active Clients";
$pageNameinactiveClients 		= "Inactive/Archived Clients";
$pageNamenewClient 				= "Add a New Client";
$pageNameemailClients 			= "Email All Active Clients";
$pageNameviewClient 			= "View Client";
$pageNameopenProjects 			= "Open Projects";
$pageNameclosedProjects 		= "Closed/Completed Projects";
$pageNamenewProject 			= "Add a New Project";
$pageNameviewProject 			= "View Project";
$pageNameprojectAccount 		= "View Project Account";
$pageNameprojectPayments 		= "Project Payments Received";
$pageNameviewPayment 			= "Project Payment Received";
$pageNamereceipt 				= "Project Payment Receipt";
$pageNameprojectDiscussions 	= "Project Discussions";
$pageNameviewDiscussion 		= "View Discussion";
$pageNameprojectFolders 		= "Project Folders &amp; Files";
$pageNameviewFolder 			= "View Project Folder";
$pageNameprojectFiles 			= "Uploaded Project Files";
$pageNameviewFile 				= "View Uploaded Project File";
$pageNameactiveManagers 		= "Active Managers";
$pageNameinactiveManagers 		= "Inactive/Archived Managers";
$pageNamenewManager 			= "Add a New Manager";
$pageNameviewManager 			= "View Manager Account";
$pageNameprojectRequests 		= "Project Requests";
$pageNameviewRequest 			= "View Project Request";
$pageNamesiteAlerts 			= "Site Alerts &amp; Notifications";
$pageNamtemplatese 				= "Forms & Templates";
$pageNameviewTemplate 			= "View Form / Template";
$pageNameinvoices 				= "Project Invoices";
$pageNameviewInvoice 			= "View Project Invoice";
$pageNametimeTracking 			= "Time Tracking";
$pageNameviewTime 				= "View Time Entry";
$pageNamesiteSettings 			= "Global Site Settings";
$pageNameimportData 			= "Import Data";
$pageNamereports 				= "Global Reports";
$pageNameclientReport 			= "Clients Report &mdash; All Clients";
$pageNameclientPaymentsReport 	= "Payments Report &mdash; Specific Client";
$pageNameprojectReport 			= "Projects Report &mdash; All Projects";
$pageNameprojectPaymentsReport	= "Projects Report &mdash; Payments by Specific Project";
$pageNameallTasksReport 		= "Project Tasks Report &mdash; All Tasks";
$pageNameprojectTasksReport 	= "Project Tasks Report &mdash; Specific Project";
$pageNameallPaymentsReport 		= "Payment Reports &mdash; All Payments Received";
$pageNamedatedPaymentsReport 	= "Payment Reports &mdash; All Payments Received by Date";
$pageNameunpaidInvoicesReport 	= "Invoice Reports &mdash; All Outstanding Invoices";
$pageNamepaidInvoicesReport 	= "Invoice Reports &mdash; All Paid Invoices by Date";
$pageNamemanagerTimeReport 		= "Time Logs &mdash; Specific Manager";
$pageNameprojectTimeReport 		= "Time Logs &mdash; Specific Project";
$pageNamemanagersReport 		= "Manager Reports &mdash; All Managers";
$pageNameassignedProjectsReport = "Manager Reports &mdash; Assigned Projects";
$pageNamedashboard 				= "Dashboard";

// Navigation Include
// --------------------------------------------------------------------------------------------------
$toggleNav					= "Toggle Navigation";
$dashboardNavLink			= "Dashboard";
$calendarNavLink			= "Calendar";
$myMsgNavLink				= "My Messages";
$unreadText1				= "You have";
$unreadText2				= "Unread Messages";
$viewAllText				= "View All";
$inboxPvtMsgNavLink			= "Inbox";
$sentPvtMsgNavLink			= "Sent";
$archivedPvtMsgNavLink		= "Archived";
$myTasksNavLink				= "My Tasks";
$openTasksText				= "Open Tasks";
$statusText					= "Status";
$dueOnText					= "Due on";
$clientsNavLink				= "Clients";
$activeClientsNavLink		= "Active Clients";
$inactiveClientsNavLink		= "Inactive Clients";
$newClientNavLink			= "Add a New Client";
$emailClientsNavLink		= "Email All Clients";
$projectsNavLink			= "Projects";
$openProjNavLink			= "Open Projects";
$closedProjNavLink			= "Closed Projects";
$newProjNavLink				= "Add a New Project";
$managersNavLink			= "Managers";
$activeManagersNavLink		= "Active Managers";
$inactiveManagersNavLink	= "Inactive Managers";
$newManagerNavLink			= "Add a New Manager";
$adminNavLink				= "Admin";
$projRequestsNavLank		= "Project Requests";
$siteAlertsNavLink			= "Site Alerts";
$tempaltesNavLink			= "Templates";
$invoicesNavLink			= "Invoices";
$reportsNavLink				= "Reports";
$timeTrackNavLink			= "Time Tracking";
$siteSettingsNavLink		= "Site Settings";
$searchTootip				= "Search";
$searchPlaceholder			= "Single Word Search";
$memberSinceText			= "Member since";
$myProfileNavLink			= "My Profile";
$timeLogsNavLink			= "Time Logs";
$signOutNavLink				= "Sign Out";
$signOutConf				= "are you sure you want to signout of your account?";

// Footer Include
// --------------------------------------------------------------------------------------------------
$footerCopyright			= "<a href=\"http://shameemreza.com\">clientManagement</a>";
$footerCreatedBy			= "Created by <a href=\"http://shameemreza.com\">Shameem Reza</a>";

// Calendar Include File
// --------------------------------------------------------------------------------------------------
$todayLink					= "Today";
$newEventLink				= "New Event";
$monthLink					= "Month";
$weekLink					= "Week";
$dayLink					= "Day";
$noTimesSet					= "No times have been set";
$sharedEvent				= "Shared Event";
$pulicEvent					= "Public Event";
$eventPostedBy				= "Posted By";
$editEvent					= "Edit";
$deleteEvent				= "Delete";

// Pagination Class Include File
// --------------------------------------------------------------------------------------------------
$previousLink				= "Prev";
$nextLink					= "Next";

// Dashboard
// --------------------------------------------------------------------------------------------------
$dashboardWelcomeMsg		= "Welcome to your ".$set['siteName']." Manager's Dashboard.
You can view Client's profiles, projects &amp information, create folders and upload files, discus and more.";
$workedTitle				= "Total Hours Worked this Week";
$managerHoursText			= "All Manager Hours Logged";
$allHoursText				= "All Hours You Have Worked";
$viewTimeLogsText			= "View Time Logs";
$unreadMsgText				= "Unread Messages in Your Inbox";
$viewMsgText				= "View Messages";
$assignedTasksText			= "Assigned Tasks to Complete";
$viewTasksText				= "View Tasks";
$viewProjectsText			= "View Projects";
$recentDiscText				= "Recent Discussions";
$noDiscMsg					= "No Discussions Found";
$recentUploadsText			= "Recent Uploads";
$projectText				= "Project";
$noUploadsMsg				= "No Uploads Found";
$recentPymtsRecvd			= "Recent Payments Received";
$noRecentPymntsMsg			= "No Recent Payments have been made.";
$paymentDateText			= "Payment Date";
$receivedByText				= "Received By";
$paymentAmtText				= "Payment Amount";
$feeAmtText					= "Fee Amount";
$totalPaidText				= "Total Paid";
$printReceiptTooltip		= "View/Print Receipt";
$viewProjectText			= "View Project";
$projBoxText1 				= "Total Open Projects";
$projTotalTooltip1 			= 'All Client Projects';
$projBoxText2 				= "Assigned Open Projects";
$projTotalTooltip2			= 'Projects Only Assigned to You';

// Calendar
// --------------------------------------------------------------------------------------------------
$clickEventText				= "Click on an Event Title for more information &amp; options.";
$editEventModal				= "Edit Event";
$startDateField				= "Start Date";
$startTimeField				= "Start Time";
$endDateField				= "End Date";
$endTimeField				= "End Time";
$eventColorField			= "Event Color";
$eventColorFieldHelp		= "Hexadecimal Format (ie. #e96f50).";
$saveNewEventBtn			= "Save New Event";
$deleteEventConf			= "Are you sure you want to DELETE the event:";
$addNewEventModalTitle		= "Add a New Event";
$shareEventField			= "Share Event with all Managers &amp; Admins";
$shareEventFieldHelp		= "Sharing an Event makes it visible on all Managers &amp; Admins calendars.";
$eventTimeField				= "Event Time";
$eventTimeFieldHelp			= "Format: HH:MM or leave blank for an All Day event.";
$eventTitleField			= "Event Title";
$eventTitleFieldHelp		= "Max 50 Characters.";
$eventDescField				= "Event Description";
$eventDescFieldHelp			= "Description of the Event (not required).";
$eventDateReqMsg			= "Event Date is required.";
$eventTitleReqMsg			= "Event Title is required.";
$newEventSavedMsg			= "The New Event has been saved.";
$eventUpdatedMsg			= "The Event has been updated.";
$eventDeletedMsg			= "The Event has been deleted.";
$editEventErrorMsg			= "You can only edit an Event you created.";

// Site Alerts
// --------------------------------------------------------------------------------------------------
$siteAlertsPageTitle		= "Site Alerts";
$manageAlertsText			= "Manage your Site Alerts.";
$managerAlertsQuip			= "You can edit or delete an existing Site Alert, or create a new one.";
$titleTableHead				= "Title";
$createdByTableHead			= "Created By";
$dateCreatedTableHead		= "Date Created";
$activeTableHead			= "Active";
$invoiceTableHead			= "Invoice";
$startsOnTableHead			= "Starts On";
$endsOnTableHead			= "Ends On";
$newAlertTooltip			= "Create a New Site Alert";
$editAlertTooltip			= "Edit Alert";
$deleteAlertTooltip			= "Delete Alert";
$editSiteAlertTitle			= "Edit Site Alert";
$alertDatesQuip				= "To use a Start Date and/or an End Date, set the new Alert as inactive.
Site Alerts set to Active will display regardless of what dates are set.";
$startDateField				= "Start Date";
$startDateFieldHelp			= "Leave blank if the new Alert does not have a start date.";
$endDateField				= "End Date";
$endDateFieldHelp			= "Leave blank if the new Alert never expires.";
$activeAlertField			= "Active Alert?";
$activeAlertFieldHelp		= "Selecting Yes makes this Alert visible for everyone on their Dashboard.";
$invoicePrintField			= "Print on Invoice?";
$invoicePrintFieldHelp		= "Setting this to Yes prints the alert in the Notes Section of the Client's Invoice.";
$alertTitleField			= "Alert Title";
$alertTextField				= "Alert Text";
$deleteAlertQuip			= "Are you sure you want to Delete this Site Alert?";
$alertTitleReqMsg			= "The Alert Title is required.";
$alertTextReqMsg			= "The Alert Text is required.";
$newAlertCreatedMsg			= "The new Site Alert has been created.";
$alertUpdatedMsg			= "The Site Alert has been updated.";
$alertDeletedMsg			= "The Site Alert has been deleted.";

// Site Settings
// --------------------------------------------------------------------------------------------------
$globalSettingsTitle		= "Global ".$set['siteName']." Site Settings";
$installUrlField			= "Installation URL";
$installUrlFieldHelp		= "Used in all uploads &amp; email notifications. Must include the trailing slash.";
$localizationField			= "Localization";
$localizationFieldHelp		= "Choose the Default Language file to use throughout ".$set['siteName'].".";
$optionArabic				= "Arabic";
$optionBulgarian			= "Bulgarian";
$optionChechen				= "Chechen";
$optionCzech				= "Czech";
$optionDanish				= "Danish";
$optionEnglish				= "English";
$optionCanadianEnglish		= "Canadian English";
$optionBritishEnglish		= "British English";
$optionEspanol				= "Espanol";
$optionFrench				= "French";
$optionGerman				= "German";
$optionCroatian				= "Croatian";
$optionHungarian			= "Hungarian";
$optionArmenian				= "Armenian";
$optionIndonesian			= "Indonesian";
$optionItalian				= "Italian";
$optionJapanese				= "Japanese";
$optionKorean				= "Korean";
$optionDutch				= "Dutch";
$optionPortuguese			= "Portuguese";
$optionRomanian				= "Romanian";
$optionSwedish				= "Swedish";
$optionThai					= "Thai";
$optionVietnamese			= "Vietnamese";
$optionCantonese			= "Cantonese";
$selfRegField				= "Enable Self-Registrations?";
$selfRegFieldHelp			= "Set to No to disable the ability for anonymous users Creating New Accounts.";
$projServInfoField			= "Enable Project Server Info?";
$projServInfoFieldHelp		= "Set to No to disable each Project's Server Information.";
$siteNameField				= "Site Name";
$businessNameField			= "Business Name";
$businessAddyField			= "Business Address";
$businessEmailField			= "Business Email";
$businessPhoneField			= "Business Phone";
$updateGlobalBtn			= "Update Global Site Settings";
$uploadSettingsTitle		= "Avatar &amp; Upload Settings";
$uploadDirField				= "Client Upload Directory";
$uploadDirFieldHelp			= "Where all client files upload to.<br />Must include the trailing slash.";
$avatarDirField				= "Avatar Upload Directory";
$avatarDirFieldHelp			= "Where all Avatars upload to.<br />Must include the trailing slash.";
$templateDirField			= "Template Upload Directory";
$templateDirFieldHelp		= "Where all template files upload to.<br />Must include the trailing slash.";
$fileTypesField				= "Upload File Types Allowed";
$fileTypesFieldHelp			= "Client & Template file types you allow to be uploaded. NO spaces & each separated by a comma (Format: jpg,jpeg,png).";
$avatarTypesField			= "Avatar File Types Allowed";
$avatarTypesFieldHelp		= "Avatar file types you allow to be uploaded. NO spaces & each separated by a comma<br />(Format: jpg,jpeg,png).";
$uploadsUpdateBtn			= "Update Avatar &amp; Upload Settings";
$uploadsQuip				= "If you change any of the Upload Directory names here, you will also need to rename the folders on your host account
or uploading will not function.";
$paymentSettingsTitle		= "Client Payment Settings";
$enablePaymentsField		= "Enable Payments Through PayPal?";
$enablePaymentsFieldHelp	= "Set to Yes to allow Clients to make project payments via PayPal.";
$itemNameField				= "PayPal Item Name";
$itemNameFieldHelp			= "The item name that appears on the PayPal payment.";
$currencyCodeField			= "PayPal Currency Code";
$paypalFeeField				= "PayPal Use Fee";
$paypalFeeFieldHelp			= "Fee charged by PayPal. Do not include '%' symbol (ie. 0.5).";
$completedMsgField			= "Payment Completed Message";
$completedMsgFieldHelp		= "What the Client will see once they have completed a PayPal payment.";
$paypalEmailField			= "PayPal Account Email";
$paypalEmailFieldHelp		= "Your PayPal Account's email &mdash; where PayPal payments will be sent to.";
$paymentSetUpdateBtn		= "Update Client Payment Settings";
$managerRoleTitle			= "Manager Role Titles";
$managerRoleQuip			= "Manager Roles are just titles for the different types of managers you may have. They do not affect System Permissions.";
$roleIdHead					= "Role ID";
$roleTitleHead				= "Manager Role Title";
$editRoleTooltip			= "Edit Role";
$deleteRoleTooltip			= "Delete Role";
$editRoleModalTitle			= "Edit Manager Role";
$roleTitleField				= "Manager Role Title";
$deleteRoleQuip				= "Are you sure you want to delete this Manager Role?";
$addRoleBtn					= "Add a New Manager Role";
$installUrlMsg				= "The Installation URL is required (include the trailing slash).";
$siteNameMsg				= "The Site Name is required.";
$siteEmalMsg				= "Please enter the Site's Email.";
$businessAddyReqMsg			= "The Business Address is required.";
$businessEmailReqMsg		= "The Business Email address is required.";
$globalSettingsSavedMsg		= "The Global Site Settings have been saved.";
$clientUploadsReqMsg		= "The folder location where Client Files will be saved is required.";
$avatarUploadsReqMsg		= "The folder location where Avatar images will be saved is required.";
$templateUploadsReqMsg		= "The folder location where Templates will be saved is required.";
$uploadFileTypesReqMsg		= "The File Type Extensions allowed to be uploaded is required.";
$avatarFileTypesReqMsg		= "The Avatar File Type Extensions allowed to be uploaded is required.";
$uploadSettingsSavedMsg		= "The Avatar & Upload Settings have been saved.";
$paypalItemReqMsg			= "The PayPal Item Name is required.";
$currencyCodeReqMsg			= "The PayPal Payment Currency Code is required.";
$paypalFeeReqMsg			= "The PayPal Use Fee is required.";
$completedMsgReqMsg			= "The Payment Completed Message is required.";
$paypalEmailReqMsg			= "The PayPal Account Email is required.";
$paymentSettingsSavedMsg	= "The Client Payment Settings have been saved.";
$roleTitleReqMsg			= "The Manager Role Title is required.";
$roleUpdatedMsg				= "The Manager Role has been updated.";
$roleDeletedMsg				= "The Manager Role has been deleted.";
$newRoleSavedMsg			= "The new Manager Role has been saved.";

// Private Messages
// --------------------------------------------------------------------------------------------------
$sendMsgBtn					= "Send Message";
$inboxTabLink				= "Inbox";
$sentTabLink				= "Sent Items";
$archiveTabLink				= "Archive";
$composeTabLink				= "Compose";
$sendReplyBtn				= "Send Reply";

// inbox.php
$noMessagesMsg				= "No Messages Found";
$fromText					= "From";
$subjectText				= "Subject";
$dateRecvdText				= "Date Received";
$sendaReplyBtn				= "Send a Reply";
$messageText				= "Message";
$deleteMessageConf			= "Are you sure you want to DELETE the message:";
$composeNewModal			= "Compose a New Private Message";
$selectManageField			= "Select a Manager/Admin";
$selectManageFieldHelp		= "Leave blank to send just to a Client.";
$selectClientField			= "Select a Client";
$selectClientFieldHelp		= "Leave blank to send just to an Admin/Manager.";
$selectInboxMsgQuip			= "Select a Private Message to view the message content &amp; options.";
$markedAsRead				= "The Private Message has been Marked as Read.";
$markedAsArchived			= "The Private Message has been Archived.";
$markedAsDeleted			= "The Private Message has been Deleted.";
$msgSubjectReq				= "The Message Subject is required.";
$msgTextReq					= "The Message Text is required.";
$inboxEmailSubject			= "You have received a new Personal Message from";
$inboxReplySubject			= "You have received a new reply from";
$loginURL1					= "You can log in to your account at ".$set['installUrl']."admin";
$loginURL2					= "You can log in to your account at ".$set['installUrl'];
$privateMsgSent				= "Your Private Message has been sent, and the recipient has been notified.";
$replyMsgSent				= "Your Reply Message has been sent, and the recipient has been notified.";

// sent.php
$noSentMessages				= "No Sent Messages Found";
$sentToText					= "Sent To";
$dateSentText				= "Date Sent";
$selectSentMsgQuip			= "Select a Sent Message to view the message content &amp; options.";

// archived.php
$noArchivedMsg				= "No Archived Messages Found";
$selectArchivedMsgQuip		= "Select an Archived Message to view the message content &amp; options.";
$sentToInbox				= "The Private Message has been sent to your Inbox.";

// Clients
// --------------------------------------------------------------------------------------------------
$activeClientsTabLink		= "Active Clients";
$inactiveClientsTabLink		= "Archived/Inactive Clients";
$newClientTabLink			= "Add a New Client";

// activeClients.php
$noActiveClients			= "No Active Clients Found";
$clientText					= "Client";
$companyText				= "Company";
$emailText					= "Email";
$phoneText					= "Phone";
$lastLoginText				= "Last Login";

// inactiveClients.php
$noInactiveClients			= "No Active Clients Found";
$dateArchivedText			= "Date Archived";
$resendEmailTooltip			= "Resend Activation Email";
$deleteClientTooltip		= "Delete Client";
$resendEmailConf			= "Resend the Account Activation Email to client:";
$deleteClientConf			= "Are you sure you want to permanently DELETE the client:";
$reactivateEmailSubject		= "Your ".$set['siteName']." Client Account needs to be activated";
$reactivateEmail1			= "You must activate your account before you will be able to log in. Please click (or copy/paste) the following link to activate your account:<br>";
$reactivateEmail2			= "Once you have activated your account and logged in, please take the time to update your account profile details.";
$reactivateEmailSentMsg		= "The Account Activation Email has been sent to the Client.";
$clientAccountDeletedMsg	= "The Client's account has been deleted.";

// newClient.php
$setAccountActiveField		= "Set the Account as Active?";
$setAccountActiveHelp		= "Selecting No will require the Client to activate the Account via a link sent to the account email address.";
$showPlainText				= "Show Plain Text";
$hidePlainText				= "Hide Plain Text";
$newClientEmailSubject		= "Your ".$set['siteName']." Account has been created";
$newClientEmail1			= "Your new Account details:";
$newClientEmail2			= "Username: Your email address<br>Password:";
$newClientEmail3			= "You must activate your account before you will be able to log in. Please click (or copy/paste) the following link to activate your account:<br>";
$newClientEmail4			= "Once you have activated your new account and logged in, please take the time to update your account profile details.";
$newClientAcctEmailSent		= "The new Client account has been created, and an email to activate the account has been sent.";
$newClientAccountActive		= "The new Client has been created, and set as active.";
$addNewClientBtn			= "Add New Client";

// emailClients.php
$emailContentField			= "Email Content";
$sendClientsEmailBtn		= "Send the Email";
$sendCntEmailThanks			= "Thank you,<br>";
$sendCntEmailSentMsg		= "The email has been sent to all Active Clients.";

// viewClient.php
$currentStatusText			= "Current Status";
$joinDateText				= "Join Date";
$altPhoneText				= "Alternate Phone";
$mailingAddressText			= "Mailing Address";
$noOpenProj1				= "This Client does not have any Projects assigned to you.";
$noOpenProj2				= "This Client does not have any Open Projects.";
$assignedToText				= "Assigned To";
$percentCompleteText		= "% Complete";
$dateDueText				= "Date Due";
$updClientAcctLink			= "Update Client's Account";
$clientAvatarLink			= "Client's Profile Avatar";
$editClientBioLink			= "Edit Client's Profile Bio";
$updateClientInfoLink		= "Update Client's Personal Information";
$editClientEmailLink		= "Change Client's Account Email";
$changeClientPassLink		= "Change Client's Password";
$changeClientStatusLink		= "Change Client's Account Status";
$clientAccountQuip			= "As a Manager, you can update and/or archive this Client's Account. The Client's account status can only be changed when the client
does not have any active projects or invoices outstanding. The Client can still login to an Archived account, as long as their account remains active.";
$clientAccountQuip2			= "All Personally Identifiable information is encrypted in the database.";
$removeClientAvatarModal	= "Remove Client's Avatar";
$removeClientAvatarQuip1	= "You can remove the Client's current Avatar, and use the default Avatar. This is handy in the case of a client uploading a questionable image.";
$removeClientAvatarQuip2	= "The Client does not have a custom Avatar uploaded at this time.";
$removeClientAvatarBtn		= "Remove Current Avatar Image";
$removeClientAvatarConf		= "Are you sure you want to remove the Avatar for:";
$clientBioField				= "Profile Bio";
$newPasswordField			= "New Password";
$newPasswordFieldHelp		= "Type a new Password for the Account.";
$confNewPasswordField		= "Confirm New Password";
$confNewPasswordFieldHelp	= "Type the new password again. Passwords MUST Match.";
$changeClientStatusQuip		= "The Client's account status can only be changed when the client does not have any active projects or invoices outstanding.
The Client can still login to an Archived account, as long as their account remains active.";
$clientStatusField			= "Account Status";
$archiveClientField			= "Archive the Client's Account?";
$clientAvatarRemovedMsg		= "The Client's Avatar Image has been removed.";
$clientAvatarRemoveError	= "An Error was encountered &amp; the Client's Avatar image could not be deleted at this time.";
$clientBioUpdatedMsg		= "The Client's Profile Bio has been updated.";
$clientInfoUpdatedMsg		= "The Client's Personal information has been updated.";
$clientEmailUpdatedMsg		= "The Client's Account Email has been updated.";
$clientPassUpdatedMsg		= "The Client's Account Password has been updated.";
$clientStatusMsg1			= "The Client's Account Status has been updated.";
$clientStatusMsg2			= "The Client has unpaid invoices and the account status can not be changed at this time.";
$clientStatusMsg3			= "The Client has open projects and the account status can not be changed at this time.";

// My Profile
// --------------------------------------------------------------------------------------------------
$changeAvatarLink			= "Change My Avatar";
$changeBioLink				= "Change My Profile Bio";
$updateAccountLink			= "Update My Account Information";
$changeEmailLink			= "Change My Account Email";
$changePasswordLink			= "Change My Account Password";
$noProfileBioQuip			= "Profile Bio not found. <a data-toggle=\"modal\" href=\"#profileBio\">Create One <i class=\"fa fa-long-arrow-right icon-quote\"></i></a>";
$personalInfoTitle			= "Your Personal Information is secure.";
$personalInfoQuip			= "We store your personal information in our database in an encrypted format. We do not sell or make your information available to any one
for any reason. We value your privacy and appreciate your trust in us.";
$myAvatarModal				= "My Profile Avatar";
$myAvatarQuip				= "You can remove your current Avatar, and use the default Avatar.<br />
<small>To upload a new Avatar image you will need to first remove your current Avatar.</small>";
$uploadNewAvatarModal		= "Upload a New Avatar Image";
$allowedFileTypesQuip		= "Allowed Avatar File Types";
$selectAvatarField			= "Select New Avatar";
$deleteAvatarConf			= "Are you sure you want to remove your Profile Avatar?";
$profileBioField			= "Profile Bio";
$currentPassField			= "Current Password";
$currentPassFieldHelp		= "Your Current Account Password.";
$avatarRemovedMsg			= "Your Avatar Image has been removed.";
$avatarRemoveError			= "An Error was encountered &amp; your Avatar image could not be deleted at this time.";
$invalidAvatarType			= "The File was not an accepted Avatar type.";
$avatarUploadedMsg			= "Your new Avatar has been uploaded.";
$avatarUploadError			= "There was an error uploading yourAvatar, please check the file type &amp; try again.";
$profileUpdatedMsg			= "Your Profile Bio has been updated.";
$accountInfoUpdatedMsg		= "Your Account information has been updated.";
$emailUpdatedMsg			= "Your Account Email has been updated.";
$currentAccountPassReq		= "Your Current Account Password is required.";
$currentpasswordErrorMsg	= "Your current password is incorrect. Please check your entry.";
$newAccountPassReq			= "Please enter a new Password for your account.";
$repeatNewAccountPassReq	= "Please type the new Password again.";
$newPassDoNotMatch			= "New Passwords do not match. Please check your entries.";
$accountPassUpdated			= "Your Account Password has been updated.";

// Open Projects
// --------------------------------------------------------------------------------------------------
$noOpenAssigned				= "There are not any Open Projects assigned to you.";
$noOpenProj					= "No Open Projects found.";
$projectFeeText				= "Project Fee";

// Closed Projects
// --------------------------------------------------------------------------------------------------
$noClosedProj				= "No Closed Projects found.";
$paymentsMadeText			= "Payments Made";
$dateClosedText				= "Date Closed";
$deleteProjectTooltip		= "Delete Project";
$deleteProjConf				= "Are you sure you want to DELETE the project:";
$projectDeletedMsg			= "The Project has been deleted.";

// New Project
// --------------------------------------------------------------------------------------------------
$sendEmailToClient			= "Send Email to the Client?";
$sendEmailToClientHelp		= "Selecting \"Yes\" Sends an email to the Client with the details of the new project";
$selectClientField			= "Select Client";
$selectClientFieldHelp		= "Select the Client this New Project is For.";
$projectNameField			= "Project Name";
$dateDueByField				= "Date Due By";
$projectDescField			= "Project Description";
$projectDescFieldHelp		= "Please describe the Project. This description IS visible to the Client.";
$projectNotesField			= "Project Notes";
$projectNotesFieldHelp		= "Private Notes about the Project. Notes are NOT visible to the Client.";
$saveNewProjBtn				= "Save New Project";
$selectClientReq			= "Please select the Client this New Project is for.";
$projTitleReq				= "The Projects Title is required.";
$projFeeReq					= "The Projects Fee is required.";
$projDescReq				= "The Projects Description is required.";
$projDueByDateReq			= "The Projects Due By Date is required.";
$newProjEmailSubject		= "A new ".$set['siteName']." Project has been created";
$newProjEmailSent			= "The New Client Project has been created and an email has been sent.";
$newProjCreated				= "The New Project has been created.";

// View Project
// --------------------------------------------------------------------------------------------------
$projectInfoTitle			= "Project Information";
$projManagerText			= "Project Manager";
$unassignedText				= "Unassigned";
$dateStartedText			= "Date Started";
$amountOwedText				= "Amount Owed";
$totalPaidQuip				= "Total Paid will include any PayPal Transaction Fees the Client may have paid.";
$updateProjBtn				= "Update Project";
$assignProjBtn				= "Assign Project";
$closeProjBtn				= "Close/Archive Project";
$closeProjQuip				= "Projects can be Archived at any time regardless of completion status.";
$reopenProjBtn				= "Reopen Project";
$reopenProjQuip				= "You can Reopen this Project.";
$projCurrProgress			= "Current Project Progress";
$outstnadingProjInvoices	= "Outstanding Project Invoices";
$paymentDueText				= "Payment Due";
$invoiceAmtText				= "Invoice Amount";
$viewInvoiceText			= "View/Update Invoice";
$paidText					= "Paid";
$unpaidText					= "Unpaid";
$accountEntryTitle			= "Project Accounts & Passwords";
$accountEntryTitleQuip		= "All Project Account information is encrypted in the database.";
$newEntryBtn				= "Add a New Entry";
$accountText				= "Account";
$usernameText				= "Username";
$urlText					= "URL";
$viewPassTooltip			= "View Password";
$editEntryTooltip			= "Update/Edit Entry";
$deleteEntryTooltip			= "Delete Entry";
$deleteEntryConf			= "Are you sure you want to DELETE the entry titled:";
$descriptionText			= "Description";
$notesText					= "Notes";
$youAreCurrentlyText		= "You Are Currently";
$clockInText				= "Clock In";
$openProjTasksText			= "Open Project Tasks";
$noOpenProjTasks			= "No Open Project Tasks found.";
$dueByText					= "Due By";
$priorityText				= "Priority";
$viewAllProjTasks			= "View All Project Tasks";
$projPaymentsText			= "Project Payments";
$noProjPayments				= "No Payments have been made.";
$paymentTotalText			= "Payment Total";
$paidByText					= "Paid By";
$viewPaymentBtn				= "View Payment";
$receiptBtn					= "Receipt";
$deleteBtn					= "Delete";
$deletePaymentConf			= "Are you sure you want to DELETE the Payment received on:";
$forText					= "for";
$recordPaymentBtn			= "Record a Project Payment";
$viewProjPaymentsBtn		= "View All Project Payments";
$createInvoiceBtn			= "Create a New Project Invoice";
$createInvoiceQuip			= "Once an Invoice is created, you will be able to add invoice items to it.";
$invoiceTitleField			= "Invoice Title";
$invoiceDateDueField		= "Date Invoice Payment is Due By";
$invoiceNotesField			= "Invoice Notes";
$invoiceNotesFieldHelp		= "Not Required. Invoice Notes ARE visible to the Client.";
$notifyClientCheckbox		= "Notify Client?";
$notifyClientCheckboxHelp	= "Check to send an email to the Client notifying them of the New Invoice.";
$postedOnText				= "Posted On";
$lastUpdatedText			= "Last Updated";
$viewAllProjDisc			= "View All Project Discussions";
$uploadedOnText				= "Uploaded On";
$viewFileText				= "View File";
$viewAllProjFiles			= "View All Project Folders";
$projectNameFieldHelp		= "A short title/name for this Project.";
$projFeeField				= "Total Project Fee";
$ProjStartDateField			= "Project Start Date";
$ProjDueDateField			= "Project Due Date";
$percentCompleteHelp		= "Numbers Only &mdash; no percent symbol.";
$selectProjManagerField		= "Select Project Manager";
$selectProjManagerFieldHelp	= "Assign this Project to a Manager.";
$recordPaymentModal			= "Manually Record a Project Payment";
$recordPaymentModalQuip		= "If you are recording an Invoice payment received, use the <strong><a href=\"index.php?action=invoices\">Record an Invoice Payment</a></strong>
link from the View Invoice page.";
$paymentForField			= "Payment For";
$paymentForFieldHelp		= "ie. Initail Project payment, Deposit, Final Project payment, etc.";
$datePayReceivedField		= "Date Payment Received";
$paidByFieldHelp			= "ie. Cash, Check, Paypal etc.";
$baseAmountField			= "Base Payment Amount";
$baseAmountFieldHelp		= "Don't include any extra fees here (ie. Paypal Fees).<br />No currency symbols (Format: 500.00).";
$feesAmountField			= "Fees in addition to the base Payment Amount";
$feesAmountFieldHelp		= "Any extra fees the Client may have paid (ie. PayPal Fees).<br />No currency symbols (Format: 500.00).";
$paymentNotesField			= "Payment Notes";
$paymentNotesFieldHelp		= "Not Required. Visible to the Client.";
$projStartDateReq			= "The Projects Start Date is required.";
$projPercentCompReq			= "Project's current Completion % (0 - 100) is required.";
$projUpdatedMsg				= "The Client Project has been updated.";
$projManagerUpdatedMsg		= "The Project's Manager has been updated.";
$projManagerAssignedMsg		= "The Manager has been assigned to this Project.";
$projArchivedMsg			= "The Project has been Archived.";
$projReopenedMsg			= "The Project has been Reopened.";
$entryNameReq				= "The Account Name is required.";
$entryUsernameReq			= "The Account Username is required.";
$entryPasswordReq			= "The Account Password is required.";
$newEntrySavedMsg			= "The New Account entry has been saved.";
$entryDeletedMsg			= "The Account Entry has been deleted.";
$paymentForReq				= "The Payment For is required.";
$paymentDateReq				= "The Date the Payment was made is required.";
$paidByReq					= "The Paid By is required.";
$paymentAmountReq			= "The Amount of the Payment is required.";
$paymentSavedMsg			= "The Project Payment has been saved.";
$paymentDeletedMsg			= "The Project Payment has been deleted.";
$invoiceDueDateReq			= "The Invoice Due Date is required.";
$invoiceTitleReq			= "The Invoice Title is required.";
$newInvEmailSubject			= "A new ".$set['siteName']." Project Invoice has been created";
$invoiceCreatedEmailSent	= "The New Invoice has been created and an email has been sent to the Client.";
$invoiceCreatedMsg			= "The New Invoice has been created.";
$projHoursWorked1			= "Total Project Hours Worked";
$projHoursWorked2			= "Total Hours You Worked";
$paidInFullText				= "Paid in Full";
$openProjText				= "Open Project";
$closedProjText				= "Project Closed/Archived on";

// Project Account
// --------------------------------------------------------------------------------------------------
$enteredByText				= "Entered By";
$updateEntryBtn				= "Update/Edit Account Entry";
$updateAccEntryBtn			= "Update Account Entry";
$accountEntryUpdatedMsg		= "The Account Entry has been updated.";

// Project Payments
// --------------------------------------------------------------------------------------------------
$recordNewPaymentBtn		= "Record a New Payment Received";
$noProjPaymentsFound		= "No Project Payments have been received.";
$capForText					= "For";
$editPaymentTooltip			= "Edit/Update Payment";
$deletePaymentTooltip		= "Delete Payment Record";
$deletePaymentConf			= "Are you sure you want to DELETE the Payment received on:";
$recordNewPaymentModal		= "Manually Record a Project Payment Received";

// View Payment
// --------------------------------------------------------------------------------------------------
$amountPaidText				= "Amount Paid";
$feesPaidText				= "Fees Paid";
$totalRcvdText				= "Total Received";
$updatePaymentBtn			= "Update/Edit Payment Received";
$projPaymentUpdatedMsg		= "The Project Payment has been updated.";

// Receipt
// --------------------------------------------------------------------------------------------------
$printedOnText				= "Printed on";
$paidToText					= "Paid To";
$receivedFromText			= "Received From";
$paymentIdText				= "Payment ID";
$amountText					= "Amount";
$additionalFeeText			= "Additional Fee";
$subtotalText				= "Subtotal";
$editPaymentBtn				= "View/Edit Payment";
$receiptPrintBtn			= "Print Receipt";

// Search Results
// --------------------------------------------------------------------------------------------------
$resultsFoundText1			= "result found";
$resultsFoundText2			= "results found";
$noResultsMsg				= "No Results Found for your Search Term.";
$discussionText				= "Discussion";
$onText						= "on";
$uploadedFileText			= "Uploaded File";
$folderText					= "Folder";
$viewFolderText				= "View Folder";
$uploadedByText				= "Uploaded By";
$taskText					= "Task";
$viewTaskText				= "View Task";

// Tasks
// --------------------------------------------------------------------------------------------------
$personalTasksTabLink		= "Personal Tasks";
$projectTaskTabLink			= "Project Tasks";
$closedTasksTabLink			= "Closed/Completed Tasks";
$newTaskTabLink				= "New Task";
$newTaskTitle				= "Add a New Task";
$selectProjectField			= "Select Project";
$taskTitleField				= "Task Title";
$selectProjectFieldHelp		= "If this is a Project Task, select the Project this task is for.";
$taskDescField				= "Task Description";
$priorityField				= "Priority";
$statusField				= "Status";
$dueDateField				= "Task Due Date";
$addToCalendarField			= "Add to Personal Calendar";
$addToCalendarFieldHelp		= "Check to add the Task to your Personal Calendar. The Task will display on the Task Due Date.";
$saveTaskBtn				= "Save Task";
$taskCompletedMsg			= "The Task has been set as Completed.";
$taskTitleReqMsg			= "The Task Title is required.";
$taskDescReqMsg				= "The Task Description is required.";
$taskPriorityReqMsg			= "The Task Priority is required.";
$taskStatusReqMsg			= "The Task Status is required.";
$taskDueByReqMsg			= "The date the Task is Due by is required.";
$projectTaskUpdatedMsg		= "The Project Task has been updated.";
$taskDeletedMsg				= "The Task has been successfully deleted.";
$newTaskAddedCalMsg			= "The New Task has been saved and has been added to your Calendar.";
$newTaskAddedMsg			= "The New Task has been saved.";
$taskReopenedMsg			= "The Task has been re-opened.";
$noClosedPersTasksFound		= "No Closed Personal Tasks found";
$taskTableHead				= "Task";
$projectTableHead			= "Project";
$reopenTaskTooltip			= "Re-open Task";
$editTaskTooltip			= "View/Edit Task";
$markCompletedTooltip		= "Mark Task Completed";
$deleteTaskTooltip			= "Delete Task";
$noClosedProjTasksFound		= "No Closed Project Tasks found";

// personalTasks.php
$noPersonalFound			= "No Personal Tasks Found";
$createdOnText				= "Created On";
$completeTaskText			= "Complete Task";
$taskDeleteConfQuip			= "Are you sure you want to Delete the Task:";
$personalTaskUpdatedMsg		= "The Task has been updated.";

// projectTasks.php
$noProjectFound				= "No Project Tasks Found";

// closedTasks.php
$noClosedFound				= "No Closed/Completed Tasks Found";
$completedOnText			= "Completed On";
$reopenedText				= "Re-opened";

// viewTask.php
$typeText					= "Type";
$taskNotesText				= "Task Notes";
$editTaskBtn				= "Update/Edit Task";
$markTaskCompleted			= "Mark Task as Completed/Closed";
$updateTaskBtn				= "Update Task";
$taskType1					= "Personal Task";
$taskType2					= "Project Task";
$closedTaskText				= "Completed/Closed Task";

// Time Logs
// --------------------------------------------------------------------------------------------------
$addTimeBtn					= "Add Time";
$noTimeFound				= "No Time Entries found.";
$weekText					= "Week";
$yearText					= "Year";
$dateInText					= "Date In";
$timeInText					= "Time In";
$dateOutText				= "Date Out";
$timeOutText				= "Time Out";
$hoursText					= "Hours";
$editTimeEntryTooltip		= "View/Edit Time Entry";
$deleteTimeEntryTooltip		= "Delete Time Entry";
$deleteTimeEntryConf1		= "Are you sure you want to permanently DELETE the Time Entry for:";
$totalHoursText				= "Total Hours";
$hoursFormatTooltip			= "hh:mm:ss";
$totalText					= "Total";
$addTimeModal				= "Add Time to Your Time Card";
$saveTimeEntryBtn			= "Save Time Entry";
$deleteTimeEntryMsg			= "The Time Entry has been deleted.";
$selectProjReq				= "A Project is required.";
$dateInReq					= "The Date In is required.";
$timeInReq					= "The Time In is required.";
$dateOutReq					= "The Date Out is required.";
$timeOutReq					= "The Time Out is required.";
$manualEntryText			= "Manual Entry";
$timeEntrySavedMsg			= "The Manual Time Entry has been saved.";

// Time Tracking
// --------------------------------------------------------------------------------------------------
$noTimeEntriesFor			= "No Time Entries found for";
$msgrsClockedInTitle		= "Managers Currently Clocked In";
$noMngrsClockedIn			= "No Managers are Clocked In at this time.";
$msgrsClockedInQuip			= "You can manually clock a Manager Out. This is handy in cases where a Manager has forgotten to clock out.";
$managerText				= "Manager";
$weekNoText					= "Week No";
$clockYearText				= "Clock Year";
$clockMngrOutBtn			= "Clock Manager Out";
$clockMngrOutConf1			= "Clock the Manager";
$clockMngrOutConf2			= "Out";
$addMngrTimeModal			= "Add Time to a Managers Time Card";
$selectManagerReq			= "A Manager is required.";
$adminManualEntryText		= "Admin Manual Entry";
$mngrClockedOutMsg			= "The Manager has been Clocked Out.";

// View Time
// --------------------------------------------------------------------------------------------------
$entryTypeText				= "Entry Type";
$recordDateText				= "Record Date";
$clockRunningText			= "Clock Running";
$editTimeEntryBtn			= "Update/Edit Time Entry";
$previousEditsTitle			= "Previous Time Entry Edits";
$noTimeEditsFound			= "No Time Entry edits found.";
$dateOfEditText				= "Date of Edit";
$editedByText				= "Edited By";
$editReasonText				= "Reason for Edit";
$editReasonHelp				= "Please type a short reason for this Edit.";
$updateTimeEntryBtn			= "Update Time Entry";
$editReasonReq				= "The Edit Reason is required.";
$timeEntryUpdatedMsg		= "The Time Entry has been updated.";

// Project Discussions
// --------------------------------------------------------------------------------------------------
$createNewDiscTabLink		= "Create a New Discussion";
$noProjDiscFound			= "No Project Discussions Found.";
$topicText					= "Topic";
$commentsText				= "Comments";
$viewDiscTooltip			= "View Discussion";
$deleteDiscTooltip			= "Delete Discussion Thread";
$deleteDiscConf1			= "Are you sure you want to DELETE the Discussion Thread:";
$deleteDiscConf2			= "and all of it's comments?";
$discTopicText				= "Discussion Topic";
$discTextText				= "Discussion Text";
$saveNewDiscBtn				= "Save New Discussion";
$discTopicReq				= "The Discussion Topic is required.";
$discTextReq				= "The Discussion Text is required.";
$newDiscSavedMsg			= "The New Discussion Topic has been saved.";
$discThreadDeletedMsg		= "The Discussion Thread and all of its Comments has been deleted.";

// View Discussion
// --------------------------------------------------------------------------------------------------
$editTopicBtn				= "Edit Topic";
$editDiscTopicModal			= "Edit Discussion Topic";
$commentedText				= "commented";
$editCommentTooltip			= "Edit Comment";
$deleteCommentTooltip		= "Delete Comment";
$deleteCommentConf			= "Are you sure you want to DELETE this Comment?";
$noCommentsFound			= "No Comments Found.";
$addCommentTitle			= "Add a Comment";
$addCommentBtn				= "Add Comment";
$discTopicUpdatedMsg		= "The Discussion Topic has been updated.";
$commentsReq				= "Your Comments are required.";
$commentsUpdatedMsg			= "The Comment has been updated.";
$commentDeletedMsg			= "The Comment has been Deleted.";
$newDiscCmtEmailSubject1	= "A new Discussion Comment from";
$newDiscCmtEmailSubject2	= "has been added for the Discussion";
$newCommentSavedMsg			= "Your Comments have been saved.";

// Project Folders
// --------------------------------------------------------------------------------------------------
$projectFoldersTabLink		= "Project Folders";
$upldProjFilesTabLink		= "Uploaded Project Files";
$createNewFoldTabLink		= "Create a New Folder";
$projectFoldersQuip			= "Project Folders can only be Deleted if the folder is empty.";
$noProjFoldersFound			= "No Project Folders Found.";
$folderNameText				= "Folder Name";
$deleteFolderTooltip		= "Delete Folder";
$deleteFolderConf			= "Are you sure you want to DELETE the Project Folder:";
$createNewProjFoldModal		= "Create a New Project Folder";
$createNewProjFoldQuip		= "A Folder must first be created before a file can be uploaded to it.<br />
Folder names must be unique for each Project. Folder Names can be reused for different Projects.";
$createFoldBtn				= "Create Folder";
$folderNameReq				= "The Folder Name is reqired.";
$folderDescReq				= "The Folder Description is required.";
$newFolderErrorMsg			= "An error was encountered and the New Folder could not be created.";
$newFolderEmailSubject		= "A new Project Folder has been created for the Project";
$folderText					= "Folder";
$newFolderCreatedMsg		= "The New Folder has been created.";
$deleteFolderMsg1			= "The Project Folder has been Deleted.";
$deleteFolderMsg2			= "There was an error and the Project Folder could not be deleted.";
$deleteFolderMsg3			= "There Project Folder contains Uploaded Files and can not be deleted.";

// View Folder
// --------------------------------------------------------------------------------------------------
$uploadFileTabLink			= "Upload a New File";
$editFolderDescpBtn			= "Edit Folder Description";
$projectFilesTitle			= "Project Files";
$noUploadedFilesFound		= "No Uploaded Files found.";
$fileTitleText				= "File Title";
$viewFileTooltip			= "View File";
$deleteFileTooltip			= "Delete File";
$deleteFileConf				= "Are you sure you want to DELETE the Project File:";
$uploadNewProjFileModal		= "Upload a New Project File";
$uploadNewProjFileQuip		= "A Folder must first be created before a file can be uploaded to it.";
$allowedFileTypesQuip		= "Allowed File Types:";
$maxFileSizeQuip			= "Max Upload File Size:";
$mbText						= "mb";
$selectFileField			= "Select File";
$uploadFileBtn				= "Upload File";
$folderDescUpdtMsg			= "The Folder Description has been updated.";
$fileNameReq				= "The Files Name is required.";
$fileDescReq				= "The Files Description is required.";
$selectFileReq				= "Please select a File to upload.";
$fileUploadErrorMsg			= "The File was not an accepted file type or was too large in file size.";
$newFileEmailSubject		= "A new Project File has been uploaded for the Project";
$fileText					= "File";
$newFileUpldMsg				= "The New File has been uploaded.";
$fileDeletedMsg				= "The File has been Deleted.";
$fileDeleteErrorMsg			= "An error was encountered and the File could not be deleted at this time.";

// Project Files
// --------------------------------------------------------------------------------------------------
$deleteFileConf				= "Are you sure you want to DELETE the Project File:";
$selectFolderField			= "Select Folder";
$selectFolderFieldHelp		= "Select the Folder to upload the File to.";
$selectFolderReq			= "Select the Folder to upload to.";

// View File
// --------------------------------------------------------------------------------------------------
$dateUploadedText			= "Date Uploaded";
$editFileDescBtn			= "Edit File Description";
$noPreviewMsg				= "No preview available for File";
$downloadFileMsg			= "Download File";
$fileDescUpdatedMsg			= "The File Description has been updated.";
$fileCommentEmailSubject1	= "A new Comment from";
$fileCommentEmailSubject2	= "has been added for the uploaded File";

// Active Managers
// --------------------------------------------------------------------------------------------------
$activeManagersTabLink		= "Active Managers";
$inactiveManagersTabLink	= "Inactive/Archived Managers";
$newManagerTabLink			= "Add a New Manager";
$noActiveMngrsFound			= "No Active Managers Found";
$viewManagerTooltip			= "View Manager";

// Inactive Managers
// --------------------------------------------------------------------------------------------------
$noInactiveMngrsFound		= "No Inactive/Archived Managers Found";
$deleteMngrTooltip			= "Delete Manager Account";
$deleteManagerConf			= "Are you sure you want to permanently DELETE the Manager Account for:";
$managerDeletedMsg			= "The Manager's account has been deleted.";

// New Manager
// --------------------------------------------------------------------------------------------------
$setMngrActiveQuip			= "Selecting No will require the Manager to activate the Account via a link sent to the account email address.";
$newMngrEmailSubject1		= "Your";
$newMngrEmailSubject2		= "Manager Account has been created";
$newMngrEmailText1			= "Your new Account details:";
$newMngrEmailText2			= "Username: Your email address<br>Password:";
$newMngrEmailText3			= "You must activate your account before you will be able to log in. Please click (or copy/paste) the following link to activate your account:<br>";
$newMngrEmailText4			= "admin/activate.php?adminEmail=";
$newMngrEmailText5			= "&hash=";
$newMngrEmailText6			= "Once you have activated your new account and logged in, please take the time to update your account profile details.";
$newMngrEmailSent			= "The new Manager account has been created, and an email to activate the account has been sent.";
$newMngrActive				= "The new Manager has been created, and set as active.";

// View Manager
// --------------------------------------------------------------------------------------------------
$assignedProjText			= "Assigned Projects";
$noAssignedProj				= "This Manager does not have any Assigned Projects.";
$updateMngrAccountLink		= "Update Manager's Account";
$mngrAvatarLink				= "Manager's Profile Avatar";
$mngrBioLink				= "Edit Manager's Profile Bio";
$mngrPersonalInfoLink		= "Update Manager's Personal Information";
$mngrEmailLink				= "Change Manager's Account Email";
$mngrPasswordLink			= "Change Manager's Password";
$mngrStatusLink				= "Change Manager's Account Status";
$mngrAccountTypeLink		= "Change Manager's Account Type";
$viewMngrQuip				= "As an Admin, you can update and/or archive this Manager's Account.
The Manager's account status can only be changed when the Manager does not have any active projects assigned to them.";
$removeMngrAvatar			= "Remove Manager's Avatar";
$removeMngrAvatarQuip		= "You can remove the Manager's current Avatar, and use the default Avatar. This is handy in the case of a Manager uploading a questionable image.";
$noMngrAvatar				= "The Manager does not have a custom Avatar uploaded at this time.";
$mngrStatusQuip				= "The Manager's account status can only be changed when the Manager does not have any active projects assigned to them.";
$archiveMngrAccountField	= "Archive the Manager's Account?";
$accountLevelField			= "Account Level";
$accountLevelFieldHelp		= "Administrators have full Access &amp; Add/Modify &amp; Delete permissions.<br />Managers cannot Add/Modify other Admins, Limited Payment System and Site Settings Access.";
$administratorText			= "Administrator";
$mngrRoleFiledHelp			= "The Admin/Manager's Role or Title. (ie. Site Administrator, Project Manager etc.)";
$mngrAvatarRemovedMsg		= "The Manager's Avatar Image has been removed.";
$mngrAvatarRemoveError		= "An Error was encountered &amp; the Manager's Avatar image could not be deleted at this time.";
$managerBioUpdatedMsg		= "The Manager's Profile Bio has been updated.";
$mngrInfoUpdatedMsg			= "The Manager's Personal information has been updated.";
$mngrEmailUpdatedMsg		= "The Manager's Account Email has been updated.";
$mngrPasswordUpdatedMsg		= "The Manager's Account Password has been updated.";
$mngrStatusUpdatedMsg		= "The Manager's Account Status has been updated.";
$mngrStatusError1			= "The Manager has Assigned Projects and the account status can not be changed at this time.";
$mngrStatusError2			= "You can not modify the Primary Admin's Account.";
$mngrTypeUpdatedMsg			= "The Manager's Account Type has been updated.";

// Project Requests
// --------------------------------------------------------------------------------------------------
$noRequestsFound			= "No Project Requests Found.";
$requestText				= "Request";
$busdgetText				= "Budget";
$dateRequestedText			= "Date Requested";
$timeFrameText				= "Time Frame";
$viewRequestTooltip			= "View Request";
$deleteRequestConf			= "Are you sure you want to DELETE the Project Request:";
$deleteRequestMsg			= "The Project Request has been deleted.";

// View Request
// --------------------------------------------------------------------------------------------------
$requestedByText			= "Requested By";
$requestDescText			= "Request Description";
$editRequestBtn				= "Update/Edit Request";
$editRequestModal			= "Update/Edit Project Request Quote";
$projectTitleText			= "Project Title";
$projectTitleTextHelp		= "The title of the project (ie. Contact Form, WordPress Blog etc.).";
$projTimeFrameText			= "Project Time Frame";
$projTimeFrameTextHelp		= "Time in weeks when the project needs to be completed by.<br />(ie. 6 weeks)";
$projBudgetText				= "Project Budget";
$projBudgetTextHelp			= "Numbers Only.<br />(Format: 999.99)";
$requestStatusText			= "Request Status";
$newText					= "New";
$acceptedText				= "Accepted";
$declinedText				= "Declined";
$discussionsText			= "Discussions";
$closedText					= "Closed";
$openText					= "Open";
$projectDescriptionHelp		= "Please be as descriptive as possible.";
$updateRequestBtn			= "Update Request";
$commentsClosedMsg			= "Comments are closed.";
$projRequestUpdatedMsg		= "The Project Quote Request has been updated.";
$projReqEmailSubject1		= "A new Comment from";
$projReqEmailSubject2		= "has been added for the Request";
$notSpecifiedText			= "Not Specified";

// Templates
// --------------------------------------------------------------------------------------------------
$uplNewTemplateTabLink		= "Upload a New Template";
$noTemplatesFound			= "No Uploaded Templates found.";
$templateText				= "Template";
$viewTemplateTooltip		= "View Template";
$deleteTemplateTooltip		= "Delete Template";
$deleteTemplateConf			= "Are you sure you want to DELETE the Template:";
$templateNameText			= "Template Name";
$uploadTemplateBtn			= "Upload Template";
$tempNameReq				= "The Templates Name is required.";
$tempDescReq				= "The Templates Description is required.";
$selectTemplateFileReq		= "Please select a Template to upload.";
$templateUploadError		= "The Template was not an accepted file type or was too large in file size.";
$templateUploadedMsg		= "The New Template has been uploaded.";
$templateDeletedMsg			= "The Template has been Deleted.";
$templateDeleteError		= "An error was encountered and the Template could not be deleted at this time.";

// View Template
// --------------------------------------------------------------------------------------------------
$templatesText				= "Templates";
$editTempDescBtn			= "Edit Template Description";
$notempPreviewMsg			= "No preview available for Template:";
$downloadTempMsg			= "Download Template:";
$tempDescUpdatedMsg			= "The Template Description has been updated.";

// Invoices
// --------------------------------------------------------------------------------------------------
$createNewInvoiceTabLink	= "Create a New Invoice";
$noInvoicesFound			= "No Invoices found.";
$viewInvoiceTooltip			= "View Invoice";
$noInvoiceDeleteTooltip		= "You can not delete this Invoice";
$deleteInvoiceTooltip		= "Delete Invoice";
$deleteInvoiceConf			= "Are you sure you want to permanently DELETE the Invoice:";
$createNewInvoiceQuip		= "Once an Invoice is created, you will be able to add invoice items to it on the View Invoice page.";
$dateInvDueBy				= "Date Invoice Payment is Due By";
$invoiceTitleText			= "Invoice Title";
$invoiceDeletedMsg			= "The Project Invoice and all of its Items have been deleted.";
$invoiceSelectProjectReq	= "You need to select a Project.";
$newInvoiceEmailSubject1	= "A new";
$newInvoiceEmailSubject2	= "Project Invoice has been created";
$invCreatedEmailSent		= "The New Invoice has been created and an email has been sent to the Client.";
$invCreatedNoEmail			= "The New Invoice has been created.";

// View Invoice
// --------------------------------------------------------------------------------------------------
$remitPymntToText			= "Remit Payment To";
$billedToText				= "Billed To";
$invoiceIdText				= "Invoice ID";
$invoiceDateText			= "Invoice Date";
$datePaidText				= "Date Paid";
$itemText					= "Item";
$qtyText					= "Qty";
$editItemTooltip			= "Edit Line Item";
$deleteItemTooltip			= "Delete Line Item";
$itemNameText				= "Item Name";
$quantityText				= "Quantity";
$itemDescText				= "Item Description";
$deleteLineItemConf			= "Are you sure you want to permanently DELETE the Line Item:";
$totalDueText				= "Total Due";
$editInvoiceBtn				= "Edit Invoice";
$addLineItemBtn				= "Add a Line Item";
$recordInvPaymentBtn		= "Record an Invoice Payment";
$printInvBtn				= "Print Invoice";
$editInvoiceModal			= "Update/Edit Invoice";
$addLineItemModal			= "Add a Line Item to this Invoice";
$invNumbersOnlyText			= "Numbers Only. (ie. 12.99)";
$recordInvPaymentModal		= "Manually Record an Invoice Payment";
$invPaymentForHelp			= "ie. Invoice Payment, etc.";
$lineItemNameReq			= "The Line Item Name is required.";
$qtyReq						= "The Quantity is required.";
$lineItemAmtReq				= "The Line Item Amount is required.";
$lineItemDescReq			= "The Line Item Description is required.";
$lineItemUpdatedMsg			= "The Line Item has been updated.";
$lineItemDeletedMsg			= "The Line Item has been deleted.";
$invUpdatedMsg				= "The Project Invoice has been updated.";
$newLineItemAddedMsg		= "The New Line Item has been created and added to the Invoice.";
$invPaymentSavedMsg			= "The Invoice Payment has been saved.";

// Import Data
// --------------------------------------------------------------------------------------------------
$importDataInsTitle			= "Import Data Instructions";

$importDataQuip1			= "You can import your Backup data into clientManagement. If you choose to upload your old data,
you will need to do this <strong>BEFORE</strong> you add any new data through clientManagement. Once you have added any Clients, Projects, or Managers,
you will no longer be able to import your old data. This is to prevent duplicate ID's in the database.";

$importDataAlertMsg			= "Thanks for using my Application - Shameem Reza.";

$importDataQuip2			= "To export your old data from clientManagement, please refer to yoursite.com/clientmanagement/export";

$importDataQuip3			= "If this is your first time using clientManagement, you do not need to do anything special. The Import Data page will not
effect clientManagement in any way.";

$importClientsText			= "Import Clients";
$importClientsError			= "The clients table in the database is not empty.";
$importClientProjText		= "Import Client Projects";
$importClientProjError		= "The clientprojects table in the database is not empty.";
$importAdminsText			= "Import Admins/Managers";
$importAdminsError			= "The admins table in the database has more then 1 record.";
$importPaymentsText			= "Import Payments";
$importPaymentsError		= "The projectpayments table in the database is not empty.";
$importClientsMsg			= "The Client Data has been imported.";
$importFileError			= "Invalid File Type. The file must be a Comma Separated file saved with an extension of .csv.";
$importClientprojMsg		= "The Client Project Data has been imported.";
$importAdminDataMsg			= "The Admin/Manager Data has been imported.";
$importPaymentDataMsg		= "The Project Payment Data has been imported.";

// Reports
// --------------------------------------------------------------------------------------------------
$runReportBtn				= "Run Report";
$filterOptionsQuip			= "All filter options for each report are required for the report to run.";
$fromDateField				= "From";
$fromDateFieldHelp			= "Please select or type a beginning Date.<br />Format: yyyy-mm-dd.";
$toDateField				= "To";
$toDateFieldHelp			= "Please select or type an end Date.<br />Format: yyyy-mm-dd.";
$clientsText				= "Clients";
$projectsText				= "Projects";
$tasksText					= "Tasks";
$paymentsInvTabLink			= "Payments &amp; Invoices";
$clientReports1				= "Clients Report &mdash; All Clients";
$includeInactive			= "Include Inactive &amp; Archived Clients?";
$allActiveText				= "All Active";
$allArchived				= "All Archived";
$allInactiveText			= "All Inactive";
$allClientsText				= "Show All Clients";
$clientReports2				= "Payments Report &mdash; Specific Client";
$selectReportClientText		= "Select the Client to run the Report on";
$selectReportClientHelp		= "Inactive &amp; Archived Clients are included as an option and marked with an asterisk *.";
$projReports1				= "rojects Report &mdash; All Projects";
$includeClosedProj			= "Include Closed/Archived Projects?";
$includeClosedProjHelp		= "Show all Projects &mdash; Open, Active, Completed, Closed &amp; Archived.";
$projReports2				= "Projects Report &mdash; Payments by Specific Project";
$selectProjReport			= "Select the Project to run the Report on";
$selectProjReportHelp		= "Closed/Archived Projects are included as an option and marked with an asterisk *.";
$tasksReport1				= "Project Tasks Report &mdash; All Tasks";
$includeCompTasks			= "Include Completed Tasks?";
$includeCompTasksHelp		= "Show all Project Tasks &mdash; Open, Active, Completed, &amp; Closed.";
$tasksReport2				= "Project Tasks Report &mdash; Specific Project";
$paymentsReport1			= "Payment Reports &mdash; All Payments Received";
$noOptionsAvailQuip			= "No options available for this report, just click \"Run Report\".";
$paymentsReport2			= "Payment Reports &mdash; All Payments Received by Date";
$invoiceReport1				= "Invoice Reports &mdash; All Outstanding Invoices";
$invoiceReport2				= "Invoice Reports &mdash; All Paid Invoices by Date";
$timeReports1				= "Time Logs &mdash; Specific Manager";
$selectManagerReport		= "Select the Manager to run the Report on";
$selectManagerReportHelp	= "Active Managers only.";
$timeReports2				= "Time Logs &mdash; Specific Project";
$activeProjectsText			= "Active Projects";
$currActiveProjects			= "Current Active Projects.";
$closedProjectsText			= "Closed/Archived Projects";
$allClosedProjectsText		= "All Closed/Archived Projects.";
$managerReports1			= "Manager Reports &mdash; All Managers";
$includeInactManagers		= "Include Inactive Managers?";
$managerReports2			= "Manager Reports &mdash; Assigned Projects";

// reports/allPaymentsReport.php
// --------------------------------------------------------------------------------------------------
$reportLabelAllPayments		= "Report: All Payments Received";
$totalRecordsLabel			= "Total Records";
$reportsLabel				= "Reports";
$noReportResults			= "No results found that match your selections.";
$exportDataBtn				= "Export Report Data to CSV";
$grandTotalText				= "Grand Total";
$reportError1				= "Report Run Error &mdash; Please select a Client.";
$reportError2				= "Report Run Error &mdash; Please select or type a beginning Date.";
$reportError3				= "Report Run Error &mdash; Please select or type an end Date.";
$reportOptionsLabel			= "Report Options";
$clientNameText				= "Client Name";
$reportError4				= "Report Run Error &mdash; Please select a Project.";
$fromInvoiceText			= "From Invoice";
$openClosedText				= "Open &amp; Closed";
$unpaidInvReportLabel		= "Report: All Outstanding Invoices";
$lastUpdatedDateText		= "Last Updated Date";
$dueByDateText				= "Due By Date";
$amountDueText				= "Amount Due";
$dateSpanText				= "Date Span";
$totalTimeText				= "Total Time";
$reportError5				= "Report Run Error &mdash; Please select an Active or Closed Project.";
$accountTypeText			= "Account Type";
$accountRoleText			= "Account Role";
$assignedProjForText		= "Assigned Projects for";
$paidFromInvoiceText		= "Paid from Invoice";
$openslashClosedText		= "Open/Closed";
$fromRequestText			= "From Request";
$clientEmailText			= "Client Email";
$clientCompanyText			= "Company Name";
$primaryPhoneText			= "Primary Phone";
$activeAccountText			= "Active Account";
$archivedAccountText		= "Archived Account";
$dateAccCreatedText			= "Date Account Created";
$clientLastLoginText		= "Client Last Login";
$managerNameText			= "Manager Name";
$managerEmailText			= "Manager Email";
$managerNotesText			= "Manager Notes";
$activeProjectText			= "Active Project";
$closedArchivedText			= "Closed/Archived";

/* --------------------------------------------------------------------------------------------------
 * Updates added July 22, 2014
 * --------------------------------------------------------------------------------------------------
 * ------------- ALWAYS MAKE A BACKUP OF YOUR DATABASE AND ALL FILES BEFORE UPGRADING!! -------------
 * -------------------------------------------------------------------------------------------------- */
$acceptRequestBtn			= "Accept Request";
$editRequestQuip			= "You can accept a Project Request by using the \"Accept Request\" button, which will convert the Request into a
New Project for the specified Client.";
$acceptRequestModal			= "Accept Project Request";
$acceptRequestQuip			= "Accepting this Project Request will set the Request as \"Accepted\", and will create a New Project for the Client.
You can choose whether or not to send a Notification Email.";
$projReqAcceptedEmailSent	= "The Project Request has been Accepted, the Project has been created and an email has been sent.";
$projReqAccepted			= "The Project Request has been Accepted and the New Project has been created.";

/* --------------------------------------------------------------------------------------------------
 * Updates added November 11, 2014
 *
 * *** IMPORTANT! PLEASE READ ***
 * If you have all ready translated this file, just copy/paste the new localizations below to
 * you translated file. DO NOT over-write your file, or all translations will be lost.
 * --------------------------------------------------------------------------------------------------
 * ------------- ALWAYS MAKE A BACKUP OF YOUR DATABASE AND ALL FILES BEFORE UPGRADING!! -------------
 * -------------------------------------------------------------------------------------------------- */
 $assignFirstMsg1			= "You must first Assign the Project to an Admin/Manager before you can create Project Folders.";
 $assignFirstMsg2			= "You must first Assign the Project to an Admin/Manager before you can upload Project Files.";