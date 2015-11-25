<?php
	$jsFile = 'siteSettings';

	// Update Global Site Settings
    if (isset($_POST['submit']) && $_POST['submit'] == 'updateGlobalSettings') {
        // Validation
		if($_POST['installUrl'] == "") {
            $msgBox = alertBox($installUrlMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['siteName'] == "") {
            $msgBox = alertBox($siteNameMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessAddress'] == "") {
            $msgBox = alertBox($businessAddyReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['businessEmail'] == "") {
            $msgBox = alertBox($businessEmailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Add the trailing slash if there is not one
			$installUrl = $mysqli->real_escape_string($_POST['installUrl']);
			if(substr($installUrl, -1) != '/') {
				$install = $installUrl.'/';
			} else {
				$install = $installUrl;
			}

			$localization = $mysqli->real_escape_string($_POST['localization']);
			$allowRegistrations = $mysqli->real_escape_string($_POST['allowRegistrations']);
			$siteName = $mysqli->real_escape_string($_POST['siteName']);
			$businessName = $mysqli->real_escape_string($_POST['businessName']);
			$businessAddress = htmlspecialchars($_POST['businessAddress']);
			$businessEmail = $mysqli->real_escape_string($_POST['businessEmail']);
			$businessPhone = $mysqli->real_escape_string($_POST['businessPhone']);

            $stmt = $mysqli->prepare("
                                UPDATE
                                    sitesettings
                                SET
									installUrl = ?,
									localization = ?,
									siteName = ?,
									businessName = ?,
									businessAddress = ?,
									businessEmail = ?,
									businessPhone = ?,
									allowRegistrations = ?
			");
            $stmt->bind_param('ssssssss',
								   $install,
								   $localization,
								   $siteName,
								   $businessName,
								   $businessAddress,
								   $businessEmail,
								   $businessPhone,
								   $allowRegistrations
			);
            $stmt->execute();
			$msgBox = alertBox($globalSettingsSavedMsg, "<i class='fa fa-check-square'></i>", "success");
            $stmt->close();
		}
	}

	// Update Avatar & Upload Settings
    if (isset($_POST['submit']) && $_POST['submit'] == 'updateUploadSettings') {
        // Validation
		if($_POST['uploadPath'] == "") {
            $msgBox = alertBox($clientUploadsReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['avatarFolder'] == "") {
            $msgBox = alertBox($avatarUploadsReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['templatesPath'] == "") {
            $msgBox = alertBox($templateUploadsReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['fileTypesAllowed'] == "") {
            $msgBox = alertBox($uploadFileTypesReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['avatarTypes'] == "") {
            $msgBox = alertBox($avatarFileTypesReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			// Add the trailing slash if there is not one
			$uploadPath = $mysqli->real_escape_string($_POST['uploadPath']);
			$avatarFolder = $mysqli->real_escape_string($_POST['avatarFolder']);
			$templatesPath = $mysqli->real_escape_string($_POST['templatesPath']);
			if(substr($uploadPath, -1) != '/') {
				$uploads = $uploadPath.'/';
			} else {
				$uploads = $uploadPath;
			}
			if(substr($avatarFolder, -1) != '/') {
				$avatars = $avatarFolder.'/';
			} else {
				$avatars = $avatarFolder;
			}
			if(substr($templatesPath, -1) != '/') {
				$templates = $templatesPath.'/';
			} else {
				$templates = $templatesPath;
			}

			$fileTypesAllowed = $mysqli->real_escape_string($_POST['fileTypesAllowed']);
			$avatarTypes = $mysqli->real_escape_string($_POST['avatarTypes']);

            $stmt = $mysqli->prepare("
                                UPDATE
                                    sitesettings
                                SET
									uploadPath = ?,
									avatarFolder = ?,
									templatesPath = ?,
									fileTypesAllowed = ?,
									avatarTypes = ?
			");
            $stmt->bind_param('sssss',
								   $uploads,
								   $avatars,
								   $templates,
								   $fileTypesAllowed,
								   $avatarTypes
			);
            $stmt->execute();
			$msgBox = alertBox($uploadSettingsSavedMsg, "<i class='fa fa-check-square'></i>", "success");
            $stmt->close();
		}
	}

	// Update Client Payment Settings
    if (isset($_POST['submit']) && $_POST['submit'] == 'updatePaymentSettings') {
		$enablePayments = $mysqli->real_escape_string($_POST['enablePayments']);

		if ($enablePayments == '1') {
			// Validation
			if($_POST['paypalItemName'] == "") {
				$msgBox = alertBox($paypalItemReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paymentCurrency'] == "") {
				$msgBox = alertBox($currencyCodeReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paypalFee'] == "") {
				$msgBox = alertBox($paypalFeeReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paymentCompleteMsg'] == "") {
				$msgBox = alertBox($completedMsgReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else if($_POST['paypalEmail'] == "") {
				$msgBox = alertBox($paypalEmailReqMsg, "<i class='fa fa-times-circle'></i>", "danger");
			} else {
				$paypalItemName = $mysqli->real_escape_string($_POST['paypalItemName']);
				$paymentCurrency = $mysqli->real_escape_string($_POST['paymentCurrency']);
				$paypalFee = $mysqli->real_escape_string($_POST['paypalFee']);
				$paymentCompleteMsg = $mysqli->real_escape_string($_POST['paymentCompleteMsg']);
				$paypalEmail = $mysqli->real_escape_string($_POST['paypalEmail']);

				$stmt = $mysqli->prepare("
									UPDATE
										sitesettings
									SET
										enablePayments = ?,
										paymentCurrency = ?,
										paymentCompleteMsg = ?,
										paypalEmail = ?,
										paypalItemName = ?,
										paypalFee = ?
				");
				$stmt->bind_param('ssssss',
									   $enablePayments,
									   $paymentCurrency,
									   $paymentCompleteMsg,
									   $paypalEmail,
									   $paypalItemName,
									   $paypalFee
				);
				$stmt->execute();
				$msgBox = alertBox($paymentSettingsSavedMsg, "<i class='fa fa-check-square'></i>", "success");
				$stmt->close();
			}
		} else {
			$stmt = $mysqli->prepare("UPDATE sitesettings SET enablePayments = ? ");
			$stmt->bind_param('s', $enablePayments);
			$stmt->execute();
			$msgBox = alertBox($paymentSettingsSavedMsg, "<i class='fa fa-check-square'></i>", "success");
			$stmt->close();
		}
	}

	// Get Data
	$sqlStmt = "SELECT
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
					paymentCompleteMsg,
					paypalEmail,
					paypalItemName,
					paypalFee
				FROM
					sitesettings";
	$res = mysqli_query($mysqli, $sqlStmt) or die('-1' . mysqli_error());
	$row = mysqli_fetch_assoc($res);

	if ($row['localization'] == 'ar') { $ar = 'selected'; } else { $ar = ''; }
	if ($row['localization'] == 'bg') { $bg = 'selected'; } else { $bg = ''; }
	if ($row['localization'] == 'ce') { $ce = 'selected'; } else { $ce = ''; }
	if ($row['localization'] == 'cs') { $cs = 'selected'; } else { $cs = ''; }
	if ($row['localization'] == 'da') { $da = 'selected'; } else { $da = ''; }
	if ($row['localization'] == 'en') { $en = 'selected'; } else { $en = ''; }
	if ($row['localization'] == 'en-ca') { $en_ca = 'selected'; } else { $en_ca = ''; }
	if ($row['localization'] == 'en-gb') { $en_gb = 'selected'; } else { $en_gb = ''; }
	if ($row['localization'] == 'es') { $es = 'selected'; } else { $es = ''; }
	if ($row['localization'] == 'fr') { $fr = 'selected'; } else { $fr = ''; }
	if ($row['localization'] == 'ge') { $ge = 'selected'; } else { $ge = ''; }
	if ($row['localization'] == 'hr') { $hr = 'selected'; } else { $hr = ''; }
	if ($row['localization'] == 'hu') { $hu = 'selected'; } else { $hu = ''; }
	if ($row['localization'] == 'hy') { $hy = 'selected'; } else { $hy = ''; }
	if ($row['localization'] == 'id') { $id = 'selected'; } else { $id = ''; }
	if ($row['localization'] == 'it') { $it = 'selected'; } else { $it = ''; }
	if ($row['localization'] == 'ja') { $ja = 'selected'; } else { $ja = ''; }
	if ($row['localization'] == 'ko') { $ko = 'selected'; } else { $ko = ''; }
	if ($row['localization'] == 'nl') { $nl = 'selected'; } else { $nl = ''; }
	if ($row['localization'] == 'pt') { $pt = 'selected'; } else { $pt = ''; }
	if ($row['localization'] == 'ro') { $ro = 'selected'; } else { $ro = ''; }
	if ($row['localization'] == 'sv') { $sv = 'selected'; } else { $sv = ''; }
	if ($row['localization'] == 'th') { $th = 'selected'; } else { $th = ''; }
	if ($row['localization'] == 'vi') { $vi = 'selected'; } else { $vi = ''; }
	if ($row['localization'] == 'yue') { $yue = 'selected'; } else { $yue = ''; }

	if ($row['allowRegistrations'] == '1') { $allowReg = 'selected'; } else { $allowReg = ''; }
	if ($row['enablePayments'] == '1') { $paymentsystem = 'selected'; } else { $paymentsystem = ''; }

	include 'includes/navigation.php';

	if ($isAdmin != '1') {
?>
	<div class="content">
		<h3><?php echo $accessErrorHeader; ?></h3>
		<div class="alertMsg danger">
			<i class="fa fa-warning"></i> <?php echo $permissionDenied; ?>
		</div>
	</div>
<?php } else { ?>
	<div class="contentAlt">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#global" data-toggle="tab"><i class="fa fa-cogs"></i> Global Site Settings</a></li>
			<li><a href="#uploads" data-toggle="tab"><i class="fa fa-upload"></i> Avatar &amp; Upload Settings</a></li>
			<li><a href="#payments" data-toggle="tab"><i class="fa fa-credit-card"></i> Client Payment Settings</a></li>
			<li class="pull-right"><a href="index.php?action=importData"><i class="fa fa-hdd-o"></i> Import Data</a></li>
		</ul>
	</div>

	<div class="content last">
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="tab-content">
			<div class="tab-pane in active" id="global">
				<h4 class="bg-info">Global Site Settings</h4>
				<form action="" method="post">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="installUrl"><?php echo $installUrlField; ?></label>
								<input type="text" class="form-control" required="" name="installUrl" value="<?php echo $row['installUrl']; ?>" />
								<span class="help-block"><?php echo $installUrlFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="localization"><?php echo $localizationField; ?></label>
								<select class="form-control" name="localization">
									<option value="ar" <?php echo $ar; ?>><?php echo $optionArabic; ?> &mdash; ar.php</option>
									<option value="bg" <?php echo $bg; ?>><?php echo $optionBulgarian; ?> &mdash; bg.php</option>
									<option value="ce" <?php echo $ce; ?>><?php echo $optionChechen; ?> &mdash; ce.php</option>
									<option value="cs" <?php echo $cs; ?>><?php echo $optionCzech; ?> &mdash; cs.php</option>
									<option value="da" <?php echo $da; ?>><?php echo $optionDanish; ?> &mdash; da.php</option>
									<option value="en" <?php echo $en; ?>><?php echo $optionEnglish; ?> &mdash; en.php</option>
									<option value="en-ca" <?php echo $en_ca; ?>><?php echo $optionCanadianEnglish; ?> &mdash; en-ca.php</option>
									<option value="en-gb" <?php echo $en_gb; ?>><?php echo $optionBritishEnglish; ?> &mdash; en-gb.php</option>
									<option value="es" <?php echo $es; ?>><?php echo $optionEspanol; ?> &mdash; es.php</option>
									<option value="fr" <?php echo $fr; ?>><?php echo $optionFrench; ?> &mdash; fr.php</option>
									<option value="ge" <?php echo $ge; ?>><?php echo $optionGerman; ?> &mdash; ge.php</option>
									<option value="hr" <?php echo $hr; ?>><?php echo $optionCroatian; ?> &mdash; hr.php</option>
									<option value="hu" <?php echo $hu; ?>><?php echo $optionHungarian; ?> &mdash; hu.php</option>
									<option value="hy" <?php echo $hy; ?>><?php echo $optionArmenian; ?> &mdash; hy.php</option>
									<option value="id" <?php echo $id; ?>><?php echo $optionIndonesian; ?> &mdash; id.php</option>
									<option value="it" <?php echo $it; ?>><?php echo $optionItalian; ?> &mdash; it.php</option>
									<option value="ja" <?php echo $ja; ?>><?php echo $optionJapanese; ?> &mdash; ja.php</option>
									<option value="ko" <?php echo $ko; ?>><?php echo $optionKorean; ?> &mdash; ko.php</option>
									<option value="nl" <?php echo $nl; ?>><?php echo $optionDutch; ?> &mdash; nl.php</option>
									<option value="pt" <?php echo $pt; ?>><?php echo $optionPortuguese; ?> &mdash; pt.php</option>
									<option value="ro" <?php echo $ro; ?>><?php echo $optionRomanian; ?> &mdash; ro.php</option>
									<option value="sv" <?php echo $sv; ?>><?php echo $optionSwedish; ?> &mdash; sv.php</option>
									<option value="th" <?php echo $th; ?>><?php echo $optionThai; ?> &mdash; th.php</option>
									<option value="vi" <?php echo $vi; ?>><?php echo $optionVietnamese; ?> &mdash; vi.php</option>
									<option value="yue" <?php echo $yue; ?>><?php echo $optionCantonese; ?> &mdash; yue.php</option>
								</select>
								<span class="help-block"><?php echo $localizationFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="allowRegistrations"><?php echo $selfRegField; ?></label>
								<select class="form-control" name="allowRegistrations">
									<option value="0"><?php echo $noBtn; ?></option>
									<option value="1" <?php echo $allowReg; ?>><?php echo $yesBtn; ?></option>
								</select>
								<span class="help-block"><?php echo $selfRegFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="siteName"><?php echo $siteNameField; ?></label>
								<input type="text" class="form-control" required="" name="siteName" value="<?php echo clean($row['siteName']); ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="businessName"><?php echo $businessNameField; ?></label>
								<input type="text" class="form-control" required="" name="businessName" value="<?php echo clean($row['businessName']); ?>" />
							</div>
						</div>
					</div>
					<div class="form-group">
							<label for="businessAddress"><?php echo $businessAddyField; ?></label>
							<textarea class="form-control" required="" name="businessAddress" rows="2"><?php echo clean($row['businessAddress']); ?></textarea>
						</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="businessEmail"><?php echo $businessEmailField; ?></label>
								<input type="text" class="form-control" required="" name="businessEmail" value="<?php echo clean($row['businessEmail']); ?>" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="businessPhone"><?php echo $businessPhoneField; ?></label>
								<input type="text" class="form-control" required="" name="businessPhone" value="<?php echo clean($row['businessPhone']); ?>" />
							</div>
						</div>
					</div>
					<button type="input" name="submit" value="updateGlobalSettings" class="btn btn-info btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $updateGlobalBtn; ?></button>
				</form>
			</div>
			<div class="tab-pane fade" id="uploads">
				<h4 class="bg-warning">Avatar &amp; Upload Settings</h4>
				<form action="" method="post">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="uploadPath"><?php echo $uploadDirField; ?></label>
								<input type="text" class="form-control" required="" name="uploadPath" value="<?php echo clean($row['uploadPath']); ?>" />
								<span class="help-block"><?php echo $uploadDirFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="avatarFolder"><?php echo $avatarDirField; ?></label>
								<input type="text" class="form-control" required="" name="avatarFolder" value="<?php echo clean($row['avatarFolder']); ?>" />
								<span class="help-block"><?php echo $avatarDirFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="templatesPath"><?php echo $templateDirField; ?></label>
								<input type="text" class="form-control" required="" name="templatesPath" value="<?php echo clean($row['templatesPath']); ?>" />
								<span class="help-block"><?php echo $templateDirFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="fileTypesAllowed"><?php echo $fileTypesField; ?></label>
								<input type="text" class="form-control" required="" name="fileTypesAllowed" value="<?php echo clean($row['fileTypesAllowed']); ?>" />
								<span class="help-block"><?php echo $fileTypesFieldHelp; ?></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="avatarTypes"><?php echo $avatarTypesField; ?></label>
								<input type="text" class="form-control" required="" name="avatarTypes" value="<?php echo clean($row['avatarTypes']); ?>" />
								<span class="help-block"><?php echo $avatarTypesFieldHelp; ?></span>
							</div>
						</div>
					</div>
					<button type="input" name="submit" value="updateUploadSettings" class="btn btn-warning btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $uploadsUpdateBtn; ?></button>
				</form>
			</div>
			<div class="tab-pane fade" id="payments">
				<h4 class="bg-success">Client Payment Settings</h4>
				<form action="" method="post">
					<div class="form-group">
						<label for="enablePayments"><?php echo $enablePaymentsField; ?></label>
						<select class="form-control" id="enablePayments" name="enablePayments">
							<option value="0"><?php echo $noBtn; ?></option>
							<option value="1" <?php echo $paymentsystem; ?>><?php echo $yesBtn; ?></option>
						</select>
						<span class="help-block"><?php echo $enablePaymentsFieldHelp; ?></span>
					</div>
					<div id="paymentSystem">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label for="paypalItemName"><?php echo $itemNameField; ?></label>
									<input type="text" class="form-control" name="paypalItemName" id="paypalItemName" value="<?php echo clean($row['paypalItemName']); ?>" />
									<span class="help-block"><?php echo $itemNameFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="paymentCurrency"><?php echo $currencyCodeField; ?></label>
									<input type="text" class="form-control" name="paymentCurrency" id="paymentCurrency" value="<?php echo clean($row['paymentCurrency']); ?>" />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label for="paypalFee"><?php echo $paypalFeeField; ?></label>
									<input type="text" class="form-control" name="paypalFee" id="paypalFee" value="<?php echo clean($row['paypalFee']); ?>" />
									<span class="help-block"><?php echo $paypalFeeFieldHelp; ?></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="paymentCompleteMsg"><?php echo $completedMsgField; ?></label>
									<input type="text" class="form-control" name="paymentCompleteMsg" id="paymentCompleteMsg" value="<?php echo clean($row['paymentCompleteMsg']); ?>" />
									<span class="help-block"><?php echo $completedMsgFieldHelp; ?></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="paypalEmail"><?php echo $paypalEmailField; ?></label>
									<input type="text" class="form-control" name="paypalEmail" id="paypalEmail" value="<?php echo clean($row['paypalEmail']); ?>" />
									<span class="help-block"><?php echo $paypalEmailFieldHelp; ?></span>
								</div>
							</div>
						</div>
					</div>
					<button type="input" name="submit" value="updatePaymentSettings" class="btn btn-success btn-icon"><i class="fa fa-check-square-o"></i> <?php echo $paymentSetUpdateBtn; ?></button>
				</form>
			</div>
		</div>
	</div>
<?php } ?>