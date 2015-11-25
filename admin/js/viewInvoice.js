function formatCurrency(total) {
    var neg = false;
    if(total < 0) {
        neg = true;
        total = Math.abs(total);
    }
    return (neg ? "-" : '') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}

$(function() {
	
	// Get the currency code
	var curSym = $("#curSym").val();

	var sum = 0;
	$(".line-total").each(function() {
		var value = $(this).text();
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});

	// Set the Sub-Total
	$(".invoice-total").html(sum.toFixed(2));
	
	// Set the Total Due
	var invTotal = $(".invoice-total").html();
	var invFees = $(".invoice-fees").html();
	var grandTotal = parseFloat(invTotal) + parseFloat(invFees);

	grandTotal = formatCurrency(grandTotal);
	$(".grand-total").html(curSym+grandTotal);
	
	/** ******************************
    * Date Picker
    ****************************** **/
    $('#invDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});
	$('#paymentDate').datetimepicker({
		format: 'yyyy-mm-dd',
		todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		minView: 2,
		forceParse: 0
	});

});