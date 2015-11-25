		</div>
	</div>

	<div id="footer" class="no-print">
		<div class="container">
			<div class="row">
				<div class="col-md-4">
					<p class="text-center">&copy; <?php echo date('Y'); ?> <?php echo $footerCopyright; ?></p>
				</div>
				<div class="col-md-4">
					<p class="text-center"><a href="index.php"><img alt ="clientmanage" src="images/footer_logo.png" /></a></p>
				</div>
				<div class="col-md-4">
					<p class="text-center"><?php echo $footerCreatedBy; ?></p>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script type="text/javascript" src="js/slimscroll.js"></script>
	<?php if (isset($fullcalendar)) { echo '<script type="text/javascript" src="js/fullcalendar.js"></script>'; } ?>
	<?php if (isset($datePicker)) { echo '<script type="text/javascript" src="js/datetimepicker.js"></script>'; } ?>
	<?php if (isset($colorPicker)) { echo '<script type="text/javascript" src="js/colorpicker.js"></script>'; } ?>
	<?php if (isset($jsFile)) { echo '<script type="text/javascript" src="js/includes/'.$jsFile.'.js"></script>'; } ?>
	<?php if (isset($calinclude)) { include 'includes/calendar.php'; } ?>
	<script type="text/javascript" src="js/custom.js"></script>
	
</body>
</html>