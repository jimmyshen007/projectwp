<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package materialwp
 */
?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer" role="contentinfo">

		<div class="container" >
			<div class="row">
				<div class="col-lg-12">
					<div class="site-info pull-right">
						<?php printf( __( '©%1$s %2$s.', 'materialwp' ), 'Ulieve', '<strong style="color: darkgray">  &#38; we proudly take care of your journey</strong>' ); ?>
						<br><a href="https://icons8.com">Icon pack by Icons8</a>
					</div><!-- .site-info -->
				</div> <!-- col-lg-12 -->
			</div><!-- .row -->
		</div><!-- .containr -->

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
