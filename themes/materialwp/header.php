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

<?php wp_head();
echo '<script>window.jQuery = window.$ = jQuery;</script>'; ?>
	<style>
		.custom-width {
			width: 300px !important;
		}

		@media (max-width: 414px){
			.custom-width {
				width: 250px !important;
			}
		}

		@media (min-width: 768px) {
		   .navbar-nav>li>a {
			   padding-top: 15px !important;
			   padding-bottom: 15px !important;
		   }
		}
		.mb-search-result-a{
			display: block;
		}
	</style>
	<script>
		function showMenu() {
			if ($(window).width() < 753) {
				$('#drawer-body-inner').appendTo($('#main-drawer-body'));
			} else {
				$('#drawer-body-inner').appendTo($('#bs-example-navbar-collapse-1'));
			}
		}
		$(document).ready(function() {
			showMenu();
			$(window).resize(function(){
				showMenu();
			});
		});
	</script>
</head>

<body <?php body_class(); ?> >
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'materialwp' ); ?></a>

	<?php
	$template_name = get_current_template();
	if($template_name != 'search.php') {
		attach_map_search();
	}
	?>
	<header id="masthead" class="site-header" role="banner">

		<nav class="navbar navbar-inverse" style="margin-bottom: 5px !important;" role="navigation">
		  <div class="container">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <!-- <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		        <span class="sr-only">Toggle navigation</span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		        <span class="icon-bar"></span>
		      </button> -->
				<button href="#main-nav-drawer" data-toggle="drawer" aria-foldedopen="false" aria-controls="main-nav-drawer" class="navbar-toggle collapsed">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand hidden-sm hidden-xs" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
				<div id="mb-main-search-bar" class="custom-geocoder-control" style="margin-right: 10px; display: inline-block">
					<!-- <a id="mb-search-link" class="leaflet-control-mapbox-geocoder-toggle mapbox-icon mapbox-icon-geocoder"
                    style="visibility: hidden"></a> -->
					<div id="mb-search-wrap" class="" style="position: relative">
						<form id="mb-search-form" class="navbar-form" style="border: 0; box-shadow: none; margin-left: 10px;
						    margin-top: 10px; padding-top: 0px; padding-bottom: 0px">
							<div class="form-group is-empty">
								<input id="mb-search-input" autocomplete="off" placeholder="Search" class="form-control custom-width" type="text" />
								<span class="material-input"></span>
							</div>
						</form>
						<div id="mb-search-results" class="custom-width" style="position: absolute; top:30px; z-index: 10;
							background: white; border: 0px solid black; display: block; margin-left: 25px"></div>
					</div>
				</div>
    		</div>

    			<!--<div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1">-->
			  <div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1">
				  <div id="drawer-body-inner">
					  <?php
					  wp_nav_menu( array(
							  'theme_location'    => 'primary',
							  'depth'             => 2,
							  'container'         => false,
							  'menu_class'        => 'nav navbar-nav navbar-right',
							  'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
							  'walker'            => new wp_bootstrap_navwalker())
					  );
					  ?>
				  </div>
			  </div>
			  <div id="main-nav-drawer" class="drawer dw-xs-6 dw-sm-3 dw-md-2 fold" aria-labelledby="main-nav-drawer">
				  <div class="drawer-contents">
					  <div class="drawer-heading">
						  <h2 class="drawer-title">Ulieve</h2>
					  </div>
					  <div id="main-drawer-body" class="drawer-body">
					  </div>
				  </div>
			  </div>
        		<!--</div> --> <!-- .navbar-collapse -->
        	</div><!-- /.container -->
		</nav><!-- .navbar .navbar-default -->
	</header><!-- #masthead -->
	<div id="content" class="site-content">
