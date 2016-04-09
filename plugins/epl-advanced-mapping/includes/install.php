<?php
/**
 * Add default settings to epl upon activation
 * @since	2.0.0
**/
function epl_am_install() {

	global $wpdb, $epl_options;

	$epl_settings = epl_settings();

	$new_fields_defaults = array(
		'epl_am_custom_marker'			=>	'0',
		'epl_am_map_enable'			=>	'1',
		'epl_am_single_tab_position'		=>	'1',
		'epl_am_single_map_height'		=>	'400',
		'epl_am_infobox_position'		=>	'left',
		'epl_am_default_map_type'		=>	'SATELLITE',
		'epl_am_infobox_style'			=>	'rounded',
		'epl_am_disable_mousescroll'		=>	0,
		'epl_am_enable_sat_view'		=>	1,
		'epl_am_enable_street_view'		=>	0,
		'epl_am_enable_transit_view'		=>	1,
		'epl_am_enable_bike_view'		=>	1,
		'epl_am_enable_comparables_view'	=>	1,
		'epl_slider_eam_mode'			=>	'false',	
	);
	
	foreach($new_fields_defaults as $key	=>	$value) {
		if(!isset($epl_settings[$key])) {
			$epl_settings[$key] = $value;
		}
	}
	update_option( 'epl_settings', $epl_settings );
}
register_activation_hook( EPL_AM_PLUGIN_FILE, 'epl_am_install' );

/**
 * Delete plugin settings
 * @since	2.0.0
**/
function epl_am_uninstall() {

	global $wpdb, $epl_options;

	$epl_settings = epl_settings();

	$new_fields_defaults = array(
		'epl_am_custom_marker'			=>	'0',
		'epl_am_map_enable'			=>	'1',
		'epl_am_single_tab_position'		=>	'1',
		'epl_am_single_map_height'		=>	'400',
		'epl_am_infobox_position'		=>	'left',
		'epl_am_default_map_type'		=>	'SATELLITE',
		'epl_am_infobox_style'			=>	'rounded',
		'epl_am_disable_mousescroll'		=>	0,
		'epl_am_enable_sat_view'		=>	1,
		'epl_am_enable_street_view'		=>	0,
		'epl_am_enable_transit_view'		=>	1,
		'epl_am_enable_bike_view'		=>	1,
		'epl_am_enable_comparables_view'	=>	1,
		'epl_slider_eam_mode'			=>	'false',
	);
	
	foreach($new_fields_defaults as $key	=>	&$value) {
		if(isset($epl_settings[$key])) {
			unset($epl_settings[$key]);
		}
	}
	update_option( 'epl_settings', $epl_settings );
}
register_deactivation_hook( EPL_AM_PLUGIN_FILE, 'epl_am_uninstall' );