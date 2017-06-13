
	<?php include 'includes/nav-mobile.php'; ?>
	    
	<script type="text/javascript" src="js/modernizr.js"></script>
	<script type="text/javascript" src="js/lightbox.min.js"></script>
	
    <?php wp_footer(); ?>

	<script>
	$(document).ready(function() {
		if(!Modernizr.svg) {
			$('img[src*="svg"]').attr('src', function() {
			    return $(this).attr('src').replace('.svg', '.png');
			});
		}
	});
	</script>

</body>
</html>