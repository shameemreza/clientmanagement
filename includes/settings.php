<?php
	// Get Settings Data
	$setSql = "
		SELECT
			installUrl,
			localization,
			siteName,
			businessName,
			businessAddress,
			businessEmail,
			businessPhone,
			uploadPath,
			templatesPath,
			fileTypesAllowed,
			avatarFolder,
			avatarTypes,
			allowRegistrations,
			enablePayments,
			paymentCurrency,
			paypalEmail,
			paypalItemName,
			paypalFee
		FROM
			sitesettings
	";
	$setRes = mysqli_query($mysqli, $setSql) or die('-99'.mysqli_error());
?>