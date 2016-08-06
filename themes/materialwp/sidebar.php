<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package materialwp
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area col-md-4 col-lg-4" role="complementary">
	<script>
		var distance = $('#secondary').offset().top;

		$(window).scroll(function() {
			if ( $(window).scrollTop() >= distance ) {
				document.getElementById("text-2").style.position = "fixed";
				document.getElementById("text-2").style.top = "0";
				document.getElementById("text-2").style.zIndex = "999";
				document.getElementById("text-2").style.width = "360px";
			}
			if ( $(window).scrollTop() < distance ) {
				document.getElementById("text-2").style.position = "absolute";
				document.getElementById("text-2").style.width = "360px";
			}
		});
	</script>

	
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	
</div><!-- #secondary -->


