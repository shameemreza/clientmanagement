CREATE TABLE IF NOT EXISTS `adminevents` (
  `admineventId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `isPublic` int(1) NOT NULL DEFAULT '0',
  `startDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventTitle` varchar(50) CHARACTER SET utf8 NOT NULL,
  `eventDesc` text COLLATE utf8_bin,
  `eventColor` varchar(7) CHARACTER SET utf8 NOT NULL DEFAULT '#2f96b4',
  PRIMARY KEY (`admineventId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `admins` (
  `adminId` int(5) NOT NULL AUTO_INCREMENT,
  `adminEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `adminFirstName` varchar(255) COLLATE utf8_bin NOT NULL,
  `adminLastName` varchar(255) COLLATE utf8_bin NOT NULL,
  `adminBio` longtext COLLATE utf8_bin,
  `adminAddress` longtext COLLATE utf8_bin,
  `adminPhone` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `adminCell` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `recEmails` int(1) NOT NULL DEFAULT '1',
  `publicProfile` int(1) NOT NULL DEFAULT '1',
  `adminAvatar` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'adminDefault.png',
  `adminNotes` text COLLATE utf8_bin,
  `lastVisited` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `adminLang` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'en',
  `createDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hash` varchar(32) COLLATE utf8_bin NOT NULL,
  `isAdmin` int(5) NOT NULL DEFAULT '0',
  `adminRole` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT 'Manager',
  `isActive` int(1) NOT NULL DEFAULT '1',
  `isArchived` int(5) NOT NULL DEFAULT '0',
  `archiveDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`adminId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `assignedprojects` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL,
  `assignedTo` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `clientevents` (
  `clienteventId` int(5) NOT NULL AUTO_INCREMENT,
  `clientId` int(5) NOT NULL,
  `isShared` int(1) NOT NULL DEFAULT '0',
  `startDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `eventTitle` varchar(50) CHARACTER SET utf8 NOT NULL,
  `eventDesc` text COLLATE utf8_bin,
  `eventColor` varchar(7) CHARACTER SET utf8 NOT NULL DEFAULT '#cc411a',
  PRIMARY KEY (`clienteventId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `clientprojects` (
  `projectId` int(5) NOT NULL AUTO_INCREMENT,
  `createdBy` int(5) NOT NULL,
  `clientId` int(5) NOT NULL,
  `projectName` varchar(255) COLLATE utf8_bin NOT NULL,
  `percentComplete` int(5) NOT NULL DEFAULT '0',
  `projectFee` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '0',
  `projectPayments` int(5) DEFAULT '0',
  `projectDeatils` longtext COLLATE utf8_bin,
  `startDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dueDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `projectNotes` text COLLATE utf8_bin,
  `fromRequest` int(5) DEFAULT '0',
  `archiveProj` int(1) DEFAULT '0',
  `archiveDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`projectId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `clients` (
  `clientId` int(5) NOT NULL AUTO_INCREMENT,
  `clientEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `clientFirstName` varchar(255) COLLATE utf8_bin NOT NULL,
  `clientLastName` varchar(255) COLLATE utf8_bin NOT NULL,
  `clientCompany` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `clientBio` longtext COLLATE utf8_bin,
  `clientAddress` longtext COLLATE utf8_bin,
  `clientPhone` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `clientCell` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `recEmails` int(1) NOT NULL DEFAULT '1',
  `publicProfile` int(1) NOT NULL DEFAULT '1',
  `clientAvatar` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'clientDefault.png',
  `clientNotes` text COLLATE utf8_bin,
  `lastVisited` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `clientLang` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'en',
  `createDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hash` varchar(32) COLLATE utf8_bin NOT NULL,
  `isActive` int(1) NOT NULL DEFAULT '1',
  `isArchived` int(5) NOT NULL DEFAULT '0',
  `archiveDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`clientId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `filecomments` (
  `commentId` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL,
  `fileId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `clientId` int(5) NOT NULL,
  `commentText` longtext COLLATE utf8_bin NOT NULL,
  `commentDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`commentId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `invitems` (
  `itemId` int(5) NOT NULL AUTO_INCREMENT,
  `invoiceId` int(5) NOT NULL,
  `itemName` varchar(50) COLLATE utf8_bin NOT NULL,
  `itemDesc` varchar(50) COLLATE utf8_bin NOT NULL,
  `itemAmount` varchar(50) COLLATE utf8_bin NOT NULL,
  `itemqty` int(5) NOT NULL DEFAULT '1',
  `itemDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`itemId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `invoices` (
  `invoiceId` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `clientId` int(5) NOT NULL,
  `invoiceTitle` varchar(50) COLLATE utf8_bin NOT NULL,
  `invoiceNotes` text COLLATE utf8_bin NOT NULL,
  `invoiceDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `invoiceDue` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isPaid` int(1) NOT NULL DEFAULT '0',
  `datePaid` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`invoiceId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `privatemessages` (
  `messageId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `clientId` int(5) NOT NULL DEFAULT '0',
  `toAdminId` int(5) NOT NULL DEFAULT '0',
  `toClientId` int(5) NOT NULL,
  `origId` int(5) NOT NULL DEFAULT '0',
  `messageTitle` varchar(255) CHARACTER SET utf8 NOT NULL,
  `messageText` text COLLATE utf8_bin,
  `messageDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `toRead` int(1) NOT NULL DEFAULT '0',
  `toArchived` int(1) NOT NULL DEFAULT '0',
  `toDeleted` int(1) NOT NULL DEFAULT '0',
  `fromDeleted` int(1) NOT NULL DEFAULT '0',
  `updatedDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`messageId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `projectdiscus` (
  `discussionId` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `clientId` int(5) NOT NULL,
  `discussionTitle` varchar(50) COLLATE utf8_bin NOT NULL,
  `discussionText` longtext COLLATE utf8_bin NOT NULL,
  `discussionDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastUpdated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`discussionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `projectfiles` (
  `fileId` int(5) NOT NULL AUTO_INCREMENT,
  `folderId` int(5) NOT NULL,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `clientId` int(5) NOT NULL,
  `fileTitle` varchar(50) COLLATE utf8_bin NOT NULL,
  `fileDesc` longtext COLLATE utf8_bin NOT NULL,
  `fileUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `fileDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`fileId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `projectfolders` (
  `folderId` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `clientId` int(5) NOT NULL,
  `folderTitle` varchar(50) COLLATE utf8_bin NOT NULL,
  `folderDesc` longtext COLLATE utf8_bin NOT NULL,
  `folderUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `folderDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`folderId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `projectpayments` (
  `paymentId` int(5) NOT NULL AUTO_INCREMENT,
  `clientId` int(5) NOT NULL,
  `projectId` int(5) NOT NULL,
  `invoiceId` int(5) NOT NULL DEFAULT '0',
  `enteredBy` int(5) NOT NULL,
  `paymentFor` varchar(255) COLLATE utf8_bin NOT NULL,
  `paymentDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `paidBy` varchar(255) COLLATE utf8_bin NOT NULL,
  `paymentAmount` varchar(50) COLLATE utf8_bin NOT NULL,
  `additionalFee` varchar(255) COLLATE utf8_bin NOT NULL,
  `paymentNotes` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `invoicepayNotes` text COLLATE utf8_bin,
  PRIMARY KEY (`paymentId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `projectrequests` (
  `requestId` int(5) NOT NULL AUTO_INCREMENT,
  `clientId` int(5) NOT NULL,
  `requestTitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `requestDesc` text COLLATE utf8_bin NOT NULL,
  `requestBudget` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `timeFrame` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `requestDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `requestAccepted` int(5) NOT NULL DEFAULT '0',
  `dateUpdated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `openDiscussion` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`requestId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `pwentries` (
  `entryId` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `clientId` int(5) NOT NULL,
  `entryTitle` varchar(50) COLLATE utf8_bin NOT NULL,
  `entryDesc` longtext COLLATE utf8_bin NOT NULL,
  `entryUsername` varchar(255) COLLATE utf8_bin NOT NULL,
  `entryPass` varchar(255) COLLATE utf8_bin NOT NULL,
  `entryUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `entryNotes` longtext COLLATE utf8_bin NOT NULL,
  `entryDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`entryId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `replies` (
  `replyId` int(5) NOT NULL AUTO_INCREMENT,
  `discussionId` int(5) NOT NULL,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `clientId` int(5) NOT NULL,
  `replyText` longtext COLLATE utf8_bin NOT NULL,
  `replyDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`replyId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `requestdisc` (
  `reqDiscId` int(5) NOT NULL AUTO_INCREMENT,
  `requestId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL DEFAULT '0',
  `clientId` int(5) NOT NULL,
  `reqDiscText` text COLLATE utf8_bin NOT NULL,
  `requestDiscDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`reqDiscId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sitealerts` (
  `alertId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL,
  `isActive` int(5) NOT NULL DEFAULT '0',
  `invoicePrint` int(5) NOT NULL DEFAULT '0',
  `alertTitle` varchar(255) COLLATE utf8_bin NOT NULL,
  `alertText` longtext COLLATE utf8_bin NOT NULL,
  `alertDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `alertStart` timestamp NULL DEFAULT NULL,
  `alertExpires` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`alertId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `sitesettings` (
  `installUrl` varchar(100) COLLATE utf8_bin NOT NULL,
  `localization` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'en',
  `siteName` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessName` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessAddress` longtext COLLATE utf8_bin NOT NULL,
  `businessEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `businessPhone` varchar(255) COLLATE utf8_bin NOT NULL,
  `uploadPath` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'uploads/',
  `templatesPath` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'templates/',
  `fileTypesAllowed` varchar(255) COLLATE utf8_bin NOT NULL,
  `avatarFolder` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'avatars/',
  `avatarTypes` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'jpg,png',
  `allowRegistrations` int(1) NOT NULL DEFAULT '1',
  `enablePayments` int(1) NOT NULL DEFAULT '1',
  `paymentCurrency` varchar(255) COLLATE utf8_bin NOT NULL,
  `paymentCompleteMsg` varchar(255) COLLATE utf8_bin NOT NULL,
  `paypalEmail` varchar(255) COLLATE utf8_bin NOT NULL,
  `paypalItemName` varchar(255) COLLATE utf8_bin NOT NULL,
  `paypalFee` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`installUrl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `tasks` (
  `taskId` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL DEFAULT '0',
  `adminId` int(5) NOT NULL,
  `taskTitle` varchar(50) COLLATE utf8_bin NOT NULL,
  `taskDesc` longtext COLLATE utf8_bin NOT NULL,
  `taskNotes` longtext COLLATE utf8_bin,
  `taskPriority` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'Normal',
  `taskStatus` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT 'New',
  `taskStart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `taskDue` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `isClosed` int(1) NOT NULL DEFAULT '0',
  `dateClosed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`taskId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `templates` (
  `templateId` int(5) NOT NULL AUTO_INCREMENT,
  `adminId` int(5) NOT NULL,
  `templateName` varchar(255) COLLATE utf8_bin NOT NULL,
  `templateDesc` longtext COLLATE utf8_bin NOT NULL,
  `templateUrl` varchar(255) COLLATE utf8_bin NOT NULL,
  `templateDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`templateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `timeclock` (
  `clockId` int(5) NOT NULL AUTO_INCREMENT,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `weekNo` int(2) unsigned zerofill NOT NULL,
  `clockYear` int(4) NOT NULL,
  `running` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`clockId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `timeedits` (
  `editId` int(5) NOT NULL AUTO_INCREMENT,
  `entryId` int(5) NOT NULL,
  `editedBy` int(5) NOT NULL,
  `editTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editReason` varchar(100) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`editId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `timeentry` (
  `entryId` int(5) NOT NULL AUTO_INCREMENT,
  `clockId` int(5) NOT NULL,
  `projectId` int(5) NOT NULL,
  `adminId` int(5) NOT NULL,
  `entryDate` date NOT NULL DEFAULT '0000-00-00',
  `startTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `endTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `entryType` enum('Regular','Manual Entry','Edited') CHARACTER SET utf8 NOT NULL DEFAULT 'Regular',
  PRIMARY KEY (`entryId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;