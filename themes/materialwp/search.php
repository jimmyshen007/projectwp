<?php
/**
 * The template for displaying search results pages.
 *
 * @package materialwp
 */

get_header(); ?>
<script>
	$(document).ready(function(e) {
		$('#main_section').height($(window).height() - 180);
		$(window).on('resize', function () {
			$('#main_section').height($(window).height() - 175);
		});
		$('body').css("overflow", "hidden");
	});
</script>
<div id="main_section" class="container" style="overflow: hidden; width: 100%; height:600px; min-height: 600px" xmlns="http://www.w3.org/1999/html">
  	<!-- first column -->
  	<div id="search-result-wrapper" style="overflow-y: scroll; max-width: 100%; width: 60%; height: 100%; float: left">
		<!-- filter panel -->
		<div><?php echo do_shortcode('[listing_custom_search post_type="property"]') ?></div>
		<!-- filter panel 2 -->
		<div></div>
		<!-- sort -->
		<div></div>
		<div style="height: 100%; width: 100%">

			<section id="primary">
				<main id="main" class="site-main" role="main">

				<?php if ( have_posts() ) : ?>
					<!--
					<header class="page-header">
						<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'materialwp' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
					</header><!-- .page-header -->
					<?php /* Start the Loop */ ?>
					<?php do_action( 'epl_property_loop_start' ); ?>
					<?php while ( have_posts() ) : the_post(); ?>
						<?php
						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						 //get_template_part( 'content', 'search' );
						 do_action('epl_property_blog');
						?>
					<?php endwhile; ?>
					<?php do_action( 'epl_property_loop_end' ); ?>
					<?php materialwp_paging_nav(); ?>

				<?php else : ?>

					<?php get_template_part( 'content', 'none' ); ?>

				<?php endif; ?>

				</main><!-- #main -->
			</section><!-- #primary -->

			<!-- <php get_sidebar(); ?> -->

		</div> <!-- .row -->
	</div>
	<!-- second column for map  -->
	<div id="map-container-wrapper" style="width: 40%; height: 100%; float: left; padding-left: 5px">
		<div id="general-map-container" class="panel panel-default" style="height: 100%"></div>
	</div>
	<script>
		//Google map js
		$(document).ready(function(e){
			L.mapbox.accessToken = 'pk.eyJ1IjoianNvbnd1IiwiYSI6ImNpa3YwZnpzMzAwZTN1YWtzYWcwNXg2ZzMifQ.v6YZ9axqDwZSlzbjmMOfTg';
			L.mapbox.map('general-map-container', 'mapbox.streets')
				.addControl(L.mapbox.geocoderControl('mapbox.places', {
					autocomplete: true,
					keepOpen: true
				}));
		});
	</script>
</div> <!-- .container -->

<?php get_footer(); ?>
