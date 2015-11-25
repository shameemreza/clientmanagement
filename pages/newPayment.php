<?php
	$projectId = $_GET['projectId'];
	if (isset($_GET['invoiceId'])) {
		$invId = $_GET['invoiceId'];
		$invoiceId = ' - Invoice ID '.$invId;
		$fromInvoice = 'true';
	} else {
		$invId = $invoiceId = $fromInvoice = '';
	}
	$jsFile = 'newPayment';

	// Get Project Data
    $query = "SELECT
                clientprojects.projectId,
                clientprojects.clientId,
                clientprojects.projectName,
                clientprojects.projectFee,
				clientprojects.projectPayments
            FROM
                clientprojects
				LEFT JOIN projectpayments ON clientprojects.projectId = projectpayments.projectId
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

	if ($amtDue == '0.00') { $due = $paidInFullText; $highlight = ''; } else { $due = $totalDue; $highlight = 'text-danger'; }

	if ($fromInvoice != '' ) {
		$qry = "SELECT
					itemAmount,
					itemqty
				FROM
					invitems
				WHERE invoiceId = ".$invId;
		$result = mysqli_query($mysqli, $qry) or die('-4'.mysqli_error());

		$lineTotal = 0;
		while ($col = mysqli_fetch_assoc($result)) {
			$lineItem = $col['itemAmount'] * $col['itemqty'];
			$lineTotal += $lineItem;
		}
		$lineTotal = $curSym.format_amount($lineTotal, 2);
	}

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
	<div class="content">
		<h3>
			<?php
				if ($fromInvoice == '' ) {
					echo $pageName;
				} else {
					echo $invoicePaymentText;
				}
			?>
		</h3>
		<?php if ($msgBox) { echo $msgBox; } ?>

		<div class="row mt10">
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-folder-open"></i> <?php echo $projectText; ?>:</td>
						<td class="infoVal">
							<a href="index.php?page=viewProject&projectId=<?php echo $projectId; ?>" data-toggle="tooltip" data-placement="right" title="<?php echo $viewProject; ?>">
								<?php echo clean($row['projectName']); ?>
							</a>
						</td>
					</tr>
					<tr>
						<td class="infoKey"><i class="fa fa-credit-card"></i> <?php echo $totalPaidText; ?>:</td>
						<td class="infoVal"><?php echo $totalPaid; ?>*</td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="infoTable">
					<tr>
						<td class="infoKey"><i class="fa fa-usd"></i> <?php echo $projFeeText; ?>:</td>
						<td class="infoVal"><?php echo $projectFee; ?></td>
					</tr>
					<?php if ($fromInvoice != '' ) { ?>
						<tr>
							<td class="infoKey"><i class="fa fa-money"></i> <?php echo $invAmountText; ?>:</td>
							<td class="infoVal <?php echo $highlight; ?>"><strong><?php echo $lineTotal; ?></strong></td>
						</tr>
					<?php } else { ?>
						<tr>
							<td class="infoKey"><i class="fa fa-money"></i> <?php echo $amtOwedText; ?>:</td>
							<td class="infoVal <?php echo $highlight; ?>"><strong><?php echo $due; ?></strong></td>
						</tr>
					<?php } ?>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8">
				<p class="mt10"><?php echo $projPaymentQuip; ?></p>
			</div>
			<div class="col-md-4">
				<?php if ($fromInvoice != '' ) { ?>
					<span class="pull-right">
						<a href="index.php?page=viewInvoice&invoiceId=<?php echo $invId; ?>" class="btn btn-default btn-icon mt10"><i class="fa fa-print"></i> <?php echo $printInvoiceBtn; ?></a>
					</span>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="content">
		<h4 class="bg-info">
			<?php
				if ($fromInvoice == '' ) {
					echo $payByPayPal1;
				} else {
					echo $payByPayPal2;
				}
			?>
		</h4>
		<p><strong><?php echo $enterAmtToPay; ?></strong></p>
		<p><?php echo $enterAmtToPayQuip; ?></p>

		<div class="errorNote"></div>

		<form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal" class="mt20">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="priceSet"><?php echo $paymentAmtField; ?></label>
						<input type="text" class="form-control" name="priceSet" id="priceSet">
						<span class="help-block"><?php echo $paymentAmtFieldHelp; ?></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label for="pricePlusFee"><?php echo $totalToPayField; ?></label>
						<input type="text" class="form-control" name="pricePlusFee" id="pricePlusFee" readonly="readonly">
						<span class="help-block"><?php echo $totalToPayFieldHelp; ?></span>
					</div>
				</div>
			</div>

			<!-- Identify your business so that you can collect the payments. -->
			<input type="hidden" name="business" value="<?php echo $set['paypalEmail'];?>" />
			<input type="hidden" name="cmd" value="_xclick" />
			<!-- Specify details about the item that buyers will purchase. -->
			<input type="hidden" name="item_name" value="<?php echo $set['paypalItemName'];?>" />
			<input type="hidden" name="item_number" value="Project: <?php echo $row['projectName'].$invoiceId;?>" />
			<input type="hidden" name="amount" value="" />
			<input type="hidden" name="currency_code" value="<?php echo $set['paymentCurrency'];?>" />
			<input type="hidden" name="no_shipping" value="0" />
			<!-- Include the PayPal Fee %. -->
			<input type="hidden" name="payFee" id="payFee" value="<?php echo $set['paypalFee'];?>" />
			<!-- Display the payment button. -->
			<p>
				<button type="input" name="submit" value="Paypal" class="btn btn-success btn-icon"><i class="fa fa-credit-card"></i> <?php echo $payBypayPalBtn; ?></button>
				<input type="hidden" name="return" value="<?php echo $set['installUrl']; ?>index.php?page=paymentComplete&projectId=<?php echo $projectId; ?>" />
			</p>
		</form>
	</div>

	<div class="content">
		<h4 class="bg-primary">
			<?php
				if ($fromInvoice == '' ) {
					echo $payByOther1;
				} else {
					echo $payByOther2;
				}
			?>
		</h4>
		<p><?php echo $payByOtherQuip; ?></p>

		<?php if ($fromInvoice != '' ) { ?>
			<div class="alertMsg info mt20">
				<i class="fa fa-info-circle"></i>
				<?php echo $payInvoiceQuip; ?>
			</div>
		<?php } ?>

		<div class="row">
			<div class="col-md-6">
				<div class="well well-sm bg-trans no-margin mt10">
					<strong><?php echo $payableToText; ?></strong><br /><?php echo $set['businessName'];?>
					<br /><br />
				</div>
			</div>
			<div class="col-md-6">
				<div class="well well-sm bg-trans no-margin mt10">
					<strong><?php echo $mailPaymentsToText; ?></strong><br /><?php echo nl2br($set['businessAddress']);?>
				</div>
			</div>
		</div>
	</div>

	<div class="content last">
		<h4 class="bg-warning"><?php echo $paymentQuestionsText; ?></h4>
		<p><?php echo $paymentQuestionsQuip; ?></p>
	</div>
<?php } ?>