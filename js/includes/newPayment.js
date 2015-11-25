$(function() {

	/** ******************************
	 * PayPal Payments
	 ****************************** **/
	$('#paypal').submit(function(){
		if ($('#priceSet').val() == "") {
			result = '<div class="alertMsg error"><i class="icon-remove-sign"></i> Please enter an amount to pay by PayPal.</div>';
			$('.errorNote').show().html(result);
			return(false);
		}
	});

	$('#priceSet').change(function() {
		// If an error is displayed, hide it
		$('.errorNote').fadeOut('slow');

		var priceInp = $("#priceSet").val();
		var priceFee = $("#payFee").val();

		var fee = Math.round(((priceInp / 100) * priceFee)*100)/100;	// Figure the PayPal Fee
		var sum = Number(priceInp) + Number(fee);						// Add the User amount and the PayPal Fee
		var sumAmount = sum.toFixed(2);									// Format the amount to currency

		$("#pricePlusFee").val(sumAmount);
		$("[name=amount]").val(sumAmount);
	});

});