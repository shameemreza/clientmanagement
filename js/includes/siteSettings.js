$(document).ready(function() {

	// PayPal/Payment System options
	$("#enablePayments").change(function() {
		if ($('#enablePayments').val() !== '1') {
			// Hide if Disabled
			$('#paymentSystem').slideUp('slow');
		} else {
			// Show if Enabled
			$('#paymentSystem').slideDown('slow');
		}
	});

	// Hide PayPal options on Page Load if not Enabled
	if ($("#enablePayments").val() !== '1') {
		$('#paymentSystem').hide();
	}

});