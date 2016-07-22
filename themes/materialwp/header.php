<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package materialwp
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); echo'<script>window.jQuery = window.$ = jQuery;</script>'; ?>
</head>

<body <?php body_class(); ?> >
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'materialwp' ); ?></a>

	<header id="masthead" class="site-header" role="banner">

		<nav class="navbar navbar-inverse" role="navigation">
		  <div class="container">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button>

			<a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
    		</div>

    			<div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1">
				 <?php
		            wp_nav_menu( array(
		                'theme_location'    => 'primary',
		                'depth'             => 2,
		                'container'         => false,
		                'menu_class'        => 'nav navbar-nav navbar-left',
		                'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
		                'walker'            => new wp_bootstrap_navwalker())
		            );
	        	?>
        		</div> <!-- .navbar-collapse -->
			  	<style>
					.fontcolor {
						color: black !important;
					}
					.custom-width {
						width: 300px !important;
					}
				</style>
			    <div id="mb-main-search-bar" class="leaflet-control-mapbox-geocoder leaflet-bar leaflet-control custom-width"
					 style="float: right">
					<a id="mb-search-link" class="leaflet-control-mapbox-geocoder-toggle mapbox-icon mapbox-icon-geocoder"></a>
					<div id="mb-search-results" class="leaflet-control-mapbox-geocoder-results custom-width"></div>
					<div id="mb-search-wrap" class="leaflet-control-mapbox-geocoder-wrap custom-width">
						<form id="mb-search-form" class="leaflet-control-mapbox-geocoder-form">
							<input id="mb-search-input" class="custom-width fontcolor" type="text" />
						</form>
					</div>
				</div>
			  	<script>
						$(document).ready(function(){
							$('#mb-search-results').on('click', function(){
								$('#mb-search-results').hide();
							});
						});
				</script>
        	</div><!-- /.container -->
		</nav><!-- .navbar .navbar-default -->
	</header><!-- #masthead -->

	<div id="content" class="site-content">
