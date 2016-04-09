<?php
/**
 * Override default map with advanced map
 * @since	2.0.0
**/
function epl_override_default_map() {
	global $epl_settings;
	if(is_epl_post()) {
		if(has_action('epl_property_map')){
		
			// remove default map of epl core
			remove_action('epl_property_map','epl_property_map_default_callback');
			
			$enabled_default_map	= isset($epl_settings['epl_am_map_enable']) ? $epl_settings['epl_am_map_enable']:1;
			$map_position		= isset($epl_settings['epl_am_single_tab_position']) ? $epl_settings['epl_am_single_tab_position'] : 1;
			
			// show default tabbed map ?
			if($enabled_default_map) {
			 
			 	// map tabs position 
				if($map_position == '1') {
					add_action('epl_property_map','epl_am_tabbed_map_top',10,5);
				} else {
			
					add_action('epl_property_map','epl_am_tabbed_map_bottom',1,5);
				}

			} else {
			
				// make tab positioning work with custom action too
				$map_position = $map_position == 1 ? 'top':'bottom';
				
				// custom actions to render tabbed map
				add_action('epl_property_map_show','epl_am_tabbed_map_'.$map_position,1,5);
			}
			
			
		}
	}
}

add_action('wp','epl_override_default_map');

function epl_js_for_tabbed_maps () {
	if(is_epl_post()) {
		wp_enqueue_script('infobox',plugins_url('js/infobox_packed.js',__DIR__),array('epl-am-map-api'));
	}
}
add_action('wp_enqueue_scripts','epl_js_for_tabbed_maps');


function epl_am_make_infobox () {
	global $property, $epl_settings;
	ob_start();
	if(has_post_thumbnail()) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $property->post->ID ), 'medium' );
		$epl_am_prop_image = $image[0];
	} else {
		$epl_am_prop_image = EPL_AM_PLUGIN_URL_IMAGES . 'no-image.jpg';
	}
	?>
	<div class="epl-adv-infobox-popup">
		<div class="epl-adv-closebtn">
		</div>
		<div class="epl-adv-popup-contents">
			<div class="epl-adv-popup-img">
				<?php
					echo '<img
							class="property-thumb"
							src="'.$epl_am_prop_image.'"
							alt="'.get_the_title($property->post->ID).'"
							title="'.get_the_title($property->post->ID).'" 
						/>';
				?>
			</div>
			<div class="epl-adv-popup-address">
				<span class="title"><?php echo epl_property_get_the_full_address(); ?></span>
			</div>
			<div class="epl-adv-popup-price">
				<?php
					echo '<span class="price_class">'.epl_get_property_price().'</span>';
					// echo '<span class="price_sticker">'.epl_get_price_sticker().'</span>';
				?>
			</div>
			<div class="epl-adv-popup-meta">
				<?php epl_property_icons(); ?>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
	<?php
	$return =  ob_get_clean();
	$return = trim(preg_replace('/\s\s+/', ' ', $return));
	return apply_filters('epl_am_infobox_content',$return);

}

function epl_am_get_default_tab_list() {
	return array(
		'sat'			=>	__('Satellite','epl-am'),
		'street'		=>	__('Street View','epl-am'),
		'transit'		=>	__('Transit','epl-am'),
		'bike'			=>	__('Bike','epl-am'),
		'comparables'	=>	__('Comparables','epl-am'),
	);
}

function epl_am_tabbed_map_bottom($tablocation='bottom',$width='100%', $zoom=16) {

	global $epl_settings;
	$enabled_tabs = array();
	foreach(epl_am_get_default_tab_list() as $key	=>	$label) {

		if( isset($epl_settings['epl_am_enable_'.$key.'_view']) ) {
			if($epl_settings['epl_am_enable_'.$key.'_view'] == 1 ) {
				$enabled_tabs[$key] = $label;
			}
		} else {
			if($key !== 'street') {
				$enabled_tabs[$key] = $label;
			}
		}
	}
	$tabs =  apply_filters('epl_adv_tabs',$enabled_tabs);
	
	/** 
	* for custom post types, option to override settings and add/remove tabs based on post_type
	* the function must return the  tabs array
	**/
	if( function_exists('epl_am_post_type_'.str_replace('-','_',get_post_type() ).'_map_tabs') ) {
		$tabs =  call_user_func('epl_am_post_type_'.str_replace('-','_',get_post_type() ).'_map_tabs');	
	}
        if(!checkChinaIP()){
	?>
	<div id="epl-advanced-map-single" class="epl-advanced-map-single epl_tabbed_maps_wrapper epl-clearfix">
		<div class="epl_tabbed_map_wrapper" >
			<?php
				// reset tab counter 
				$tabcounter = 1;
				foreach($tabs as $tab_key 	=>	$tab_label) {
					$style = $tabcounter == 1? 'display:block;':'';
					$maptab_current = $tabcounter == 1? 'maptab-current':'';
					echo '<div class="epl_adv_tab_map" id="epl_adv_tab_map_'.$tab_key.'" style="width:'.$width.'; height:100%;max-height:100%;'.$style.'" >';
				
						if(function_exists("epl_adv_tab_map_$tab_key")){
							call_user_func("epl_adv_tab_map_$tab_key");
						}
					echo '</div>';
					$tabcounter++;
				}
			?>
		</div>
		
		<div class="epl_tabbed_map_control epl-tab-top">
			
			<ul class="epl_adv_map_list map-tabs">
				<?php
					$tabcounter = 1;
					foreach($tabs as $tab_key 	=>	$tab_label) {
						$maptab_current = $tabcounter == 1? 'maptab-current':'';
						echo '<li class="'.$maptab_current.'" data-map="'.$tab_key.'">'.$tab_label.'</li>';
						$tabcounter++;
					}
				?>
			</ul>
		</div>
	</div>
	<?php
	}else{ ?>
        <div class="panel panel-info">
          <div class="panel-header"><h5 class="epl-tab-title epl-tab-title-property-features tab-title">
              <?php echo __('Map', 'custom_sp') ?></div></h5>
          <div class="panel-body">
	    <div id="map-alt"></div>
          </div>
        </div>
	<?php }
	
}

function epl_am_tabbed_map_top($tablocation='top',$width='100%',$zoom=16) {

	global $epl_settings;
	$enabled_tabs = array();
	foreach(epl_am_get_default_tab_list() as $key	=>	$label) {

		if( isset($epl_settings['epl_am_enable_'.$key.'_view']) ) {
			if($epl_settings['epl_am_enable_'.$key.'_view'] == 1 ) {
				$enabled_tabs[$key] = $label;
			}
		} else {
			if($key !== 'street') {
				$enabled_tabs[$key] = $label;
			}
		}
	}
	$tabs =  apply_filters('epl_adv_tabs',$enabled_tabs);
	
	/** 
	* for custom post types, option to override settings and add/remove tabs based on post_type
	* the function must return the  tabs array
	**/
	if( function_exists('epl_am_post_type_'.str_replace('-','_',get_post_type() ).'_map_tabs') ) {
		$tabs =  call_user_func('epl_am_post_type_'.str_replace('-','_',get_post_type() ).'_map_tabs');	
	}
        if(!checkChinaIP()){	
	?>
	<div id="epl-advanced-map-single" class="epl-advanced-map-single epl_tabbed_maps_wrapper epl-am-infobox-<?php echo $infobox_class;?> epl-clearfix">
	
		<div class="epl_tabbed_map_control">
			
			<ul class="epl_adv_map_list map-tabs">
				<?php
					$tabcounter = 1;
					foreach($tabs as $tab_key 	=>	$tab_label) {
						$maptab_current = $tabcounter == 1? 'maptab-current':'';
						echo '<li class="'.$maptab_current.'" data-map="'.$tab_key.'">'.$tab_label.'</li>';
						$tabcounter++;
					}
				?>
			</ul>
		</div>

		<div class="epl_tabbed_map_wrapper ">
			<?php
				// reset tab counter 
				$tabcounter = 1;
				foreach($tabs as $tab_key 	=>	$tab_label) {
					$style = $tabcounter == 1? 'display:block;':'';
					$maptab_current = $tabcounter == 1? 'maptab-current':'';
					echo '<div class="epl_adv_tab_map" id="epl_adv_tab_map_'.$tab_key.'" style="width:'.$width.'; height:100%;max-height:100%; '.$style.'" >';
				
						if(function_exists("epl_adv_tab_map_$tab_key")){
							call_user_func("epl_adv_tab_map_$tab_key");
						}
					echo '</div>';
					
					
					$tabcounter++;
				}
			?>
		</div>
		
	</div>
	<?php }else{
	?>
	<div class="panel panel-info">
          <div class="panel-header"><h5 class="epl-tab-title epl-tab-title-property-features tab-title">
              <?php echo __('Map', 'custom_sp') ?></div></h5>

          <div class="panel-body">
	    <div id="map-alt"></div>
          </div>
        </div>

        <?php }
	
}

// get the position of infobox coordinates based on infobox poistion
function epl_am_get_position_coordinates($position = 'top') {
	global $epl_settings;
	
	$coordinates = array(
		'top'		=>	array('-110', '-350'),
		'bottom'	=>	array('-110', '30'),
		'left'		=>	array('-260', '-150'),
		'right'		=>	array('30', '-150')
	);
	$coordinates = apply_filters('epl_am_infobox_position',$coordinates);
	return $coordinates[$position];
}

function inline_js_tabbed_map() {
	global $epl_settings;

	$epl_am_single_map_height 	= isset($epl_settings['epl_am_single_map_height']) ? intval($epl_settings['epl_am_single_map_height']) : 400;
	$position			= isset($epl_settings['epl_am_infobox_position']) ? $epl_settings['epl_am_infobox_position'] : 'top';
	$coordinates 			= epl_am_get_position_coordinates($position);
	$coord_left			= $coordinates[0];
	$coord_top			= $coordinates[1];
	
	if(!is_epl_post()){
		return;
	}
	
	global $property;
	
	if ( $property->get_property_meta('property_address_display') == 'yes' ) { 
		$property_address_coordinates = $property->get_property_meta('property_address_coordinates');
	} else {
		$property_address_coordinates = '';
	}
	// if coordinates are not present than geocode address to get coordinates
	if(trim($property_address_coordinates) == '' ) {
	
		if ( $property->get_property_meta('property_address_display') == 'yes' ) { 
			$address = epl_property_get_the_full_address();
		} else {
			$address = $property->get_property_meta('property_address_suburb').', ';
			$address .= $property->get_property_meta('property_address_state').', ';
			$address .= $property->get_property_meta('property_address_postal_code');
		}
	
		$address = apply_filters('epl_map_address',$address);
		$address = urlencode(strtolower(trim($address)));
		$geourl = "http://maps.google.com/maps/api/geocode/json?address=". urlencode($address) ."&sensor=false";
		$response = epl_remote_url_get($geourl);
		if(!empty($response)) {
			$geocoordinates = $response[0]->geometry->location->lat . ',' . $response[0]->geometry->location->lng;
			update_post_meta($property->post->ID,'property_address_coordinates',$geocoordinates);
			$property_address_coordinates = $property->get_property_meta('property_address_coordinates');
		}

	}
	
	$title 			= $property->post->post_title;
	$zoom 			= apply_filters('epl_am_tabbed_map_zoom',16);
	$zoom_sat 		= $zoom+2;
	$content 		= epl_am_make_infobox();
	$map_start		= isset( $epl_settings['epl_am_default_map_type'] ) ? $epl_settings['epl_am_default_map_type'] : 'SATELLITE';
	$marker_icon		= apply_filters('epl_am_marker_icon', epl_am_get_property_image($property) );
	?>
	<script>
		var boxText = '<?php echo $content; ?>';
		var myOptions = {
			 content: boxText,
			disableAutoPan: false,
            maxWidth: 0,
            pixelOffset: new google.maps.Size(<?php echo $coord_left ?>, <?php echo $coord_top ?>),
            alignBottom: false,
            zIndex: null,
            closeBoxMargin: "10px 2px 2px 2px",
            closeBoxURL: "<?php echo EPL_AM_PLUGIN_URL_IMAGES.'close.png'?>",
            infoBoxClearance: new google.maps.Size(10, 30),
            isHidden: false,
            pane: "floatPane",
            enableEventPropagation: false
		};
		var infowindow = new InfoBox(myOptions);
		var mapsat,maptransit,mapbike,mapstreet;
		window.onload = function() {

			var g = google.maps;
			
			// satellite
			if (jQuery('#epl_adv_tab_map_sat').length > 0) {

				var satcoords = new g.LatLng(<?php echo $property_address_coordinates ?>);
				mapsat = new g.Map(document.getElementById("epl_adv_tab_map_sat"), {
					center: satcoords,
					zoom: <?php echo $zoom_sat;?>,
					mapTypeId: google.maps.MapTypeId.<?php echo $map_start ?>,
					streetViewControl: true,
					<?php echo epl_get_option('epl_am_disable_mousescroll') == 1 ? 'scrollwheel: false,': ''?>
					zoomControlOptions: {
						style: g.ZoomControlStyle.SMALL
					}
				});

				var markersat = new google.maps.Marker({
					position: satcoords,
					map: mapsat,
					title: '<?php echo $title; ?>',
					icon : '<?php echo $marker_icon ?>'
				});
			
				google.maps.event.addListener(markersat, 'click', function() {
					infowindow.open(mapsat,markersat);
					mapsat.panTo(satcoords);
				}); 
			
				// resize map on window resize
				google.maps.event.addDomListener(window, "resize", function() {
					var center = mapsat.getCenter();
					google.maps.event.trigger(mapsat, "resize");
					mapsat.setCenter(center);
				});
			}
			
			// Transit
			if (jQuery('#epl_adv_tab_map_transit').length > 0) {
				var transit = new g.LatLng(<?php echo $property_address_coordinates ?>);
				maptransit = new g.Map(document.getElementById("epl_adv_tab_map_transit"), {
					center: transit,
					zoom: <?php echo $zoom;?>,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					streetViewControl: false,
					<?php echo epl_get_option('epl_am_disable_mousescroll') == 1 ? 'scrollwheel: false,': ''?>
					zoomControlOptions: {
						style: g.ZoomControlStyle.SMALL
					}
				});

				var markertransit = new google.maps.Marker({
					position: transit,
					map: maptransit,
					title: '<?php echo $title; ?>',
					icon : '<?php echo $marker_icon ?>'

				});

				var transitLayer = new google.maps.TransitLayer();
				transitLayer.setMap(maptransit);

				google.maps.event.addListener(markertransit, 'click', function() {
					infowindow.open(maptransit,markertransit);
					maptransit.panTo(transit);
				});
			
				// resize map on window resize
				google.maps.event.addDomListener(window, "resize", function() {
					var center = maptransit.getCenter();
					google.maps.event.trigger(maptransit, "resize");
					maptransit.setCenter(center);
				});
			}
			
			// Bike
			if (jQuery('#epl_adv_tab_map_bike').length > 0) {
				var bikeCoord = new g.LatLng(<?php echo $property_address_coordinates ?>);
				mapbike = new g.Map(document.getElementById("epl_adv_tab_map_bike"), {
					center: bikeCoord,
					zoom: <?php echo $zoom;?>,
					mapTypeId: google.maps.MapTypeId.ROADMAP,
					streetViewControl: false,
					<?php echo epl_get_option('epl_am_disable_mousescroll') == 1 ? 'scrollwheel: false,': ''?>
					zoomControlOptions: {
						style: g.ZoomControlStyle.SMALL
					}
				});

				var markerbike = new google.maps.Marker({
				position: bikeCoord,
				map: mapbike,
				title: '<?php echo $title; ?>',
				icon : '<?php echo $marker_icon ?>'
				});

				var bikeLayer = new google.maps.BicyclingLayer();
				bikeLayer.setMap(mapbike);

				google.maps.event.addListener(markerbike, 'click', function() {
					infowindow.open(mapbike,markerbike);
					mapbike.panTo(bikeCoord);
				});
			
				// resize map on window resize
				google.maps.event.addDomListener(window, "resize", function() {
					var center = mapbike.getCenter();
					google.maps.event.trigger(mapbike, "resize");
					mapbike.setCenter(center);
				});
			}
			
			// street view
			if (jQuery('#epl_adv_tab_map_street').length > 0) {
				var streetCoord = new g.LatLng(<?php echo $property_address_coordinates ?>);
			
				mapstreet = new google.maps.StreetViewPanorama( document.getElementById("epl_adv_tab_map_street"), {
					position: streetCoord,
					zoom: 1,
					pov: {
					  heading: 0,
					  pitch: 0
					},
					<?php echo epl_get_option('epl_am_disable_mousescroll') == 1 ? 'scrollwheel: false,': ''?>
				});
			
				mapstreet.setVisible(true);
			
				// street view marker
				var markerstreet = new google.maps.Marker({
					position: satcoords,
					map: mapstreet,
					title: '<?php echo $title; ?>',
					icon : '<?php echo $marker_icon ?>'
				});

				// resize map on window resize
				google.maps.event.addDomListener(window, "resize", function() {
					google.maps.event.trigger(mapstreet, "resize");
				});
			}
		};
	 
		/*** Tabbed map on single pages ****/
	
		jQuery(document).ready(function($) {
			
			/* tabs for maps on single properties pages for different view types */
			jQuery('ul.map-tabs li').click(function(){
				var map_tab_id = jQuery(this).attr('onclick');

				jQuery('ul.map-tabs li').removeClass('current');
				jQuery('.author-tab-content').removeClass('current');

				jQuery(this).addClass('current');
				jQuery("#"+map_tab_id).addClass('current');
			});
	


			if($('.epl_adv_map_list').length) {
				$('ul.epl_adv_map_list li').on('click',function(){
					$('ul.epl_adv_map_list li').removeClass('maptab-current');
					$(this).addClass('maptab-current');
					$(".epl_adv_tab_map").hide();
					$("#epl_adv_tab_map_"+$(this).data('map')).show();
					
					switch($(this).data('map')){
						case 'sat':
							var center = mapsat.getCenter();
							google.maps.event.trigger(mapsat, "resize");
							mapsat.setCenter(center);
						break;
						case 'transit':
							var center = maptransit.getCenter();
							google.maps.event.trigger(maptransit, "resize");
							maptransit.setCenter(center);
						break;
						case 'bike':
							var center = mapbike.getCenter();
							google.maps.event.trigger(mapbike, "resize");
							mapbike.setCenter(center);
						break;
						case 'street':
							var center = mapstreet.getPosition();
							google.maps.event.trigger(mapstreet, "resize");
							mapstreet.setPosition(center);
						break;
						case 'comparables':
							comparablesmap 	= myGmap.gmap3('get');
							var center 	= comparablesmap.getCenter();
							google.maps.event.trigger(comparablesmap, 'resize');
							comparablesmap.setCenter(new google.maps.LatLng (<?php echo $property_address_coordinates ?>) );
							comparablesmap.setZoom(comparablesmap.getZoom());
							comparablesmap.panToBounds(comparablesmap.getBounds());
						break;
					}
				});
			}
			

			var eplsingleMapHeight = '';
			if($('.epl_tabbed_map_wrapper').length) {
			
				if($('#epl_adv_tab_map_gallery').length && $('#epl_adv_tab_map_gallery').find('.epl_slider_container').length) { 
					var eplsingleMapHeight = jQuery('.epl-slider-single-wrapper').actual('height');
					jQuery('.epl_tabbed_map_wrapper ').height(eplsingleMapHeight);
					jQuery('.epl_tabbed_map_wrapper ').find('.slider-map').height(eplsingleMapHeight);
					jQuery('[id^="epl_adv_tab_map_"]').each(function() {
					  jQuery(this).height(eplsingleMapHeight);
					});

				} else {
					var wrapHeight, wrapWidth = $('.epl_tabbed_map_wrapper').width();
					// calculate ratio for height
					eplsingleMapHeight = ( parseFloat('<?php echo $epl_am_single_map_height; ?>')*100) /wrapWidth;
					var wrapHeight = (wrapWidth * parseFloat(eplsingleMapHeight) ) / 100;
					$('.epl_tabbed_map_wrapper').height(wrapHeight);
					$('.epl_adv_tab_map div').css("max-height",wrapHeight);
					$('#epl-advanced-map').css("height",wrapHeight);
					$('#epl-advanced-map > div:first').css("height",wrapHeight);
				}

			}
			
			// attempt to make maps responsive
			$( window ).resize(function() {
				if($('.epl_tabbed_map_wrapper').length) {
					if($('#epl_adv_tab_map_gallery').length && $('#epl_adv_tab_map_gallery').find('.epl_slider_container').length) { 
						var eplsingleMapHeight = jQuery('.epl-slider-single-wrapper').actual('height');
						jQuery('.epl_tabbed_map_wrapper ').height(eplsingleMapHeight);
						jQuery('.epl_tabbed_map_wrapper ').find('.slider-map').height(eplsingleMapHeight);
						jQuery('[id^="epl_adv_tab_map_"]').each(function() {
						  jQuery(this).height(eplsingleMapHeight);
						});

					} else {
						var wrapHeight, wrapWidth = $('.epl_tabbed_map_wrapper').width();
						// calculate ratio for height 
						var wrapHeight = (wrapWidth * parseFloat(eplsingleMapHeight) ) / 100;
						$('.epl_tabbed_map_wrapper').height(wrapHeight);
						$('.epl_adv_tab_map div').css("max-height",wrapHeight);
						$('#epl-advanced-map').css("height",wrapHeight);
						$('#epl-advanced-map > div:first').css("height",wrapHeight);
					}
				}
			});
		});
	 

	</script>

	<?php

}

function alt_inline_js_tabbed_map() {
	global $epl_settings;	
	$epl_am_single_map_height 	= isset($epl_settings['epl_am_single_map_height']) ? intval($epl_settings['epl_am_single_map_height']) : 400;
	$position			= isset($epl_settings['epl_am_infobox_position']) ? $epl_settings['epl_am_infobox_position'] : 'top';
	$coordinates 			= epl_am_get_position_coordinates($position);
	$coord_left			= $coordinates[0];
	$coord_top			= $coordinates[1];

	if(!is_epl_post()){
		return;
	}

	global $property;

	if ( $property->get_property_meta('property_address_display') == 'yes' ) {
		$property_address_coordinates = $property->get_property_meta('property_address_coordinates');
	} else {
		$property_address_coordinates = '';
	}
	// if coordinates are not present than geocode address to get coordinates
	if(trim($property_address_coordinates) == '' ) {

		if ( $property->get_property_meta('property_address_display') == 'yes' ) {
			$address = epl_property_get_the_full_address();
		} else {
			$address = $property->get_property_meta('property_address_suburb').', ';
			$address .= $property->get_property_meta('property_address_state').', ';
			$address .= $property->get_property_meta('property_address_postal_code');
		}

		$address = apply_filters('epl_map_address',$address);
		$address = urlencode(strtolower(trim($address)));
		$geourl = "http://maps.google.com/maps/api/geocode/json?address=". urlencode($address) ."&sensor=false";
		$response = epl_remote_url_get($geourl);
		if(!empty($response)) {
			$geocoordinates = $response[0]->geometry->location->lat . ',' . $response[0]->geometry->location->lng;
			update_post_meta($property->post->ID,'property_address_coordinates',$geocoordinates);
			$property_address_coordinates = $property->get_property_meta('property_address_coordinates');
		}

	}

	$title 			= $property->post->post_title;
	$zoom 			= apply_filters('epl_am_tabbed_map_zoom',16);
	$zoom_sat 		= $zoom+2;
	$content 		= epl_am_make_infobox();
	$map_start		= isset( $epl_settings['epl_am_default_map_type'] ) ? $epl_settings['epl_am_default_map_type'] : 'SATELLITE';
	$marker_icon		= apply_filters('epl_am_marker_icon', epl_am_get_property_image($property) );
	?>
	<style>
		#map-alt { position:relative; top:0; bottom:0;height:400px; max-width:800px; width:100%; }
	</style>
	<script>
        jQuery(document).ready(function(){
	  L.mapbox.accessToken = 'pk.eyJ1IjoianNvbnd1IiwiYSI6ImNpa3YwZnpzMzAwZTN1YWtzYWcwNXg2ZzMifQ.v6YZ9axqDwZSlzbjmMOfTg';
          var latlng = <?php echo '"' . $property_address_coordinates . '"' ?>;
          var latlngArry = latlng.split(",");
          var lat = 0;
          var lng = 0;
          if(latlngArry.length == 2){
              var lat = parseFloat(latlngArry[0]);
              var lng = parseFloat(latlngArry[1]);
          }
	  var map = L.mapbox.map('map-alt', null, {
	      maxZoom: 18
	  }).setView([lat, lng], 16);
	
	  var layers = {
	      Satellite: L.mapbox.tileLayer('mapbox.streets-satellite'),
	      Streets: L.mapbox.tileLayer('mapbox.streets'),
	      Outdoors: L.mapbox.tileLayer('mapbox.outdoors')
	  };
	  featureLayer = L.mapbox.featureLayer({
    	      // this feature is in the GeoJSON format: see geojson.org
              // for the full specification
              type: 'Feature',
              geometry: {
                 type: 'Point',
                 // coordinates here are in longitude, latitude order because
                 // x, y is the standard for GeoJSON and many formats
                 coordinates: [
		     lng,
                     lat
                 ]
              },
              properties: {
                 title: <?php echo '"' . $title . '"' ?>,
                 //description: '',
                 // one can customize markers by adding simplestyle properties
                 // https://www.mapbox.com/guides/an-open-platform/#simplestyle
                 'marker-size': 'large',
                 'marker-color': '#BE9A6B',
                 'marker-symbol': 'building'
              }
          }).addTo(map);
	  layers.Satellite.addTo(map);
	  L.control.layers(layers).addTo(map);
          L.control.scale().addTo(map);
          featureLayer.on('click', function(e) {
              map.panTo(e.layer.getLatLng());
          });
        });
	</script> 

	<?php

}

if(!checkChinaIP()){
    add_action('wp_head','inline_js_tabbed_map');
}else{
    add_action('wp_head','alt_inline_js_tabbed_map');
}

function listing_map_tabbed_callback($atts, $content = null) {
	extract( shortcode_atts( array(
		'width' => '100%',
		'height' => '350',
		'zoom' => '16',
		'q' => '',
		'mode' => ''
	), $atts) );

	if(!empty($q)) {
		return '<div class="epl-tab-section"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/?q='.$q.'&amp;output=embed&amp;&z='.$zoom.'"></iframe></div>';
	}
}
add_shortcode('listing_map_tabbed', 'epl_shortcode_googlemap_callback');

function epl_am_get_property_image($result) {
	global $epl_settings;
	$image = '';
	switch($result->post_type) {
		case 'property':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_property_blue.png';
					if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_property_orange.png';
					} else {
						$image = 'icon_property.png';
					}
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_property_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_property_orange.png';
					} else {
						$image = 'icon_property.png';
					}
			}
			break;
			
		case 'land':
		case 'commercial_land':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_land_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_land_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_land_orange.png';
					} else {
						$image = 'icon_land.png';
					}
			}
			break;
			
		case 'rental':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_rental_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_rental_red.png';
					break;
				
				default:
			  		if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_rental_orange.png';
					} else {
						$image = 'icon_rental.png';
					}
			}
			break;
			
		case 'rural':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_rural_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_rural_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_rural_orange.png';
					} else {
						$image = 'icon_rural.png';
					}  
			}
			break;
			
		case 'commercial':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_commercial_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_commercial_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer')  == '1' || $result->get_property_meta('property_under_offer')  == 'yes') {
						$image = 'icon_commercial_orange.png';
					} else {
						$image = 'icon_commercial.png';
					}
			}
			break;
			
		case 'business':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_business_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_business_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_business_orange.png';
					} else {
						$image = 'icon_business.png';
					}
			}
			break;
			
		case 'location_profile':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_location_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_location_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer') == '1' ||$result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_location_orange.png';
					} else {
						$image = 'icon_location.png';
					}
			}
			break;
		
		case 'epl_office':
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_business_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_business_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_business_orange.png';
					} else {
						$image = 'icon_business.png';
					}
			}
			break;
			
		default:
			switch($result->get_property_meta('property_status')) {
				case 'current':
					$image = 'icon_blue.png';
					break;
					
				case 'sold':
				case 'leased':
					$image = 'icon_red.png';
					break;
				
				default:
					if($result->get_property_meta('property_under_offer') == '1' || $result->get_property_meta('property_under_offer') == 'yes') {
						$image = 'icon_orange.png';
					} else {
						$image = 'icon.png';
					}
			}
	}
	
	
	if(!empty($image)) {
		if(isset($epl_settings['epl_am_custom_marker']) && $epl_settings['epl_am_custom_marker'] == 1) {
			$image = get_stylesheet_directory_uri() . '/easypropertylistings/map/'.$image;
		} else {
			$image = EPL_AM_PLUGIN_URL_IMAGES_ICONS . $image;
		}
		
		return apply_filters('epl_am_get_property_image_filter', $image);
	}
}

function epl_am_get_property_options($property_option_val, $property_type, $property_status, $class="") {
	$class .= " icon-symbol icon-type-$property_type icon-status-$property_status";
	
	$property_option_content = '';
	if(!empty($property_option_val)) {
		$property_option_content .= '<span class="icon-val">'.$property_option_val.'</span>';
	}
	
	$return = '<span class="property_icon"><span class="'.$class.'"></span>'.$property_option_content.'</span>';
	return apply_filters('epl_am_get_property_options_filter', $return);
}

function epl_adv_tab_map_comparables () {
	global $post;
	echo do_shortcode('[advanced_map post_type="'.$post->post_type.'" limit="30" display="simple" zoom="14" cluster="false"]');
}

/**
 * Adjust infobox position on single listings
 * @since	2.0.2
**/
function epl_adv_get_infobox_css($position='top') {
	
	switch($position) {
		
		case 'top':
		
		$css = '.overlay-featured-marker.infowindow {
					left: -115px !important;
					top: -173px !important;
				}

				.overlay-featured-marker.infowindow .arrow {
					left: 148px !important;
				}';
		break;
		case 'left':
		
		$css = '.overlay-featured-marker.infowindow {
					left: -235px !important;
					top: -20px !important;
				}

				.overlay-featured-marker.infowindow .arrow {
					left: 250px !important;
					top: 113px;
					transform: rotate(270deg);
				}';
		break;
		case 'right':
		
		$css = '.overlay-featured-marker.infowindow .arrow {
					left: -12px !important;
					top: 113px;
					transform: rotate(90deg);
				}

				.overlay-featured-marker.infowindow {
					left: 70px !important;
					top: -20px !important;
				}';
		break;
		case 'bottom':
		
		$css = '.overlay-featured-marker.infowindow .arrow {
					left: 148px !important;
					top: -9px !important;
					transform: rotate(180deg);
				}

				.overlay-featured-marker.infowindow {
					left: -115px !important;
					top: 137px !important;
				}';
		break;
		
		
	} 
	return $css;
}

function epl_adv_map_position_css() {
	global $epl_settings;
	$position	= isset($epl_settings['epl_am_infobox_position']) ? $epl_settings['epl_am_infobox_position'] : 'top'; ?>
	<style>
		<?php echo epl_adv_get_infobox_css($position); ?>
	</style> <?php
}
add_action('wp_head','epl_adv_map_position_css');

if( !function_exists('epl_all_post_types') ) {
	function epl_all_post_types() {
		
		$epl_posts  = epl_get_active_post_types();
		$epl_posts	= array_keys($epl_posts);
		return apply_filters('epl_additional_post_types',$epl_posts);
	}
}
