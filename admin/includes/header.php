<?php
	// Globals used throughout clientManagement
	$msgBox = '';
	
	// Get the Current Year & Month Name
	$currentYear = date('Y');
	$thisMonth = date('F');
	// Get the Current Week number
	$theDate = date('Y-m-d');
	$currentMonth = date('m');
	$weekNo = date('W', strtotime($theDate) + 60 * 60 * 24 );
	if ($currentMonth == '12' && $weekNo == '01') { $weekNum = '52'; } else { $weekNum = $weekNo; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<title><?php echo $set['siteName']; ?> &middot; <?php echo $pageName; ?></title>
	
	<link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Raleway:200,300,400,700'>
	<link rel="stylesheet" type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,300italic,400italic,600italic'>

	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/custom.css">
	<?php if (isset($addCss)) { echo $addCss; } ?>
	<link rel="stylesheet" type="text/css" href="../css/clientmanagement.css">
	<link rel="stylesheet" type="text/css" href="../css/font-awesome.css">
	
	<!--[if lt IE 9]>
		<script src="../js/html5shiv.js"></script>
		<script src="../js/respond.min.js"></script>
	<![endif]-->
</head>
