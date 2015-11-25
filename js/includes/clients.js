$(document).ready(function() {

	// Hide the two links on page load
	$('#hide1, #hide2').hide();

	// Show the Password field as plain text
	$('#show1').click(function(e) {
		e.preventDefault();
		$('#entryPass1').prop('type','text');
		$('#show1').hide();
		$('#hide1').show();
	});
	// Show the Password field as asterisks
	$('#hide1').click(function(e) {
		e.preventDefault();
		$('#entryPass1').prop('type','password');
		$('#hide1').hide();
		$('#show1').show();
	});

	// Show the Retype Password field as plain text
	$('#show2').click(function(e) {
		e.preventDefault();
		$('#entryPass2').prop('type','text');
		$('#show2').hide();
		$('#hide2').show();
	});
	// Show the Retype Password field as asterisks
	$('#hide2').click(function(e) {
		e.preventDefault();
		$('#entryPass2').prop('type','password');
		$('#hide2').hide();
		$('#show2').show();
	});

	// Generate Random Password
	$('#generate').click(function (e) {
		e.preventDefault();

		// You can change the password length by changing the
		// integer to the length you want in generatePassword(8).
		var pwd = generatePassword(8);

		// Populates the fields with the new generated password
        $('#entryPass1').val(pwd);
		$('#entryPass2').val(pwd);
    });

});