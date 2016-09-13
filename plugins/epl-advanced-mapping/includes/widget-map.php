<?php
/**
 * WIDGET :: Map
 *
 * @package     EPL
 * @subpackage  Widget/Map
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class EPL_Widget_Map extends WP_Widget {

	function __construct() {
		parent::__construct( false, $name = __('EPL - Map', 'epl-am') );
	}

	function widget($args, $instance) {
	
		$defaults = array(
			'post_type' 		=>	array('property', 'rental', 'land', 'rural', 'commercial', 'business', 'commercial_land' , 'location_profile'), //Post Type
			'limit'			=>	'30', // Number of maximum posts to show
			'coords'		=>	'', //First property in center by default
			'display'		=>	'card', //card, slider, simple or popup
			'zoom'			=>	'17', //for set map zoom level
			'height'		=>	'', //for set map height level, pass integer value
			'cluster'		=>	false, //Icon grouping on Map
			'property_status'	=>	'',
			'home_open'		=>	false, // False and true
			'location'		=>	''
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults );
	
		$post_type		= $instance['post_type'];
		$limit			= $instance['limit'];
		$coords			= $instance['coords'];
		$display		= $instance['display'];
		$zoom			= $instance['zoom'];
		$height			= $instance['height'];
		$cluster		= $instance['cluster'];
		$property_status	= $instance['property_status'];
		$home_open		= $instance['home_open'];
		$location		= $instance['location'];
		
		echo epl_advanced_map( $instance );
	}
	
	function update($new_instance, $old_instance) {	
		$instance = $old_instance;
		return  $new_instance;
	}

	function form($instance) {
	
		$defaults = array(
				'post_type' 		=>	array('property', 'rental', 'land', 'rural', 'commercial', 'business', 'commercial_land' , 'location_profile'), //Post Type
				'limit'			=>	'30', // Number of maximum posts to show
				'coords'		=>	'', //First property in center by default
				'display'		=>	'card', //card, slider, simple or popup
				'zoom'			=>	'17', //for set map zoom level
				'height'		=>	'', //for set map height level, pass integer value
				'cluster'		=>	false, //Icon grouping on Map
				'property_status'	=>	'',
				'home_open'		=>	false, // False and true
				'location'		=>	''
			);
	
	
		$instance	=	wp_parse_args( (array) $instance, $defaults );
		extract($instance);
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id('post_type'); ?>"><?php _e('Listing Type, hold CTRL to select multiple', 'epl-am'); ?></label> 
			<select multiple class="widefat" id="<?php echo $this->get_field_id('post_type'); ?>" name="<?php echo $this->get_field_name('post_type'); ?>[]">
				<?php
					$supported_post_types = epl_get_active_post_types();
					if(!empty($supported_post_types)) {
						foreach($supported_post_types as $k=>$v) {
							$selected = '';
							if(in_array($k,$post_type)) {
								$selected = 'selected="selected"';
							}
							echo '<option value="'.$k.'" '.$selected.'>'.__($v, 'epl-am').'</option>';
						}
					}
				?>
			</select>
		</p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:', 'epl-am'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" value="<?php echo $limit; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('coords'); ?>"><?php _e('Map centre coordinates:', 'epl-am'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('coords'); ?>" name="<?php echo $this->get_field_name('coords'); ?>" type="text" value="<?php echo $coords; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display Type', 'epl-am'); ?></label> 
			 <select class="widefat" id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
                <option value="">  <?php _e("Display type", "epl-am"); ?>  </option>
                <?php
                    $display_types = array('card', 'slider', 'simple', 'popup');
                    foreach($display_types as $display_type) {
                    	$selected = '';
						if($display_type == $display) {
							$selected = ' selected="selected" ';
						}
                    echo '<option value="'.$display_type.'" '.$selected.'>'.__($display_type, 'epl-am').'</option>';
                    }
                ?>
            </select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('zoom'); ?>"><?php _e('Zoom Level', 'epl-am'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('zoom'); ?>" name="<?php echo $this->get_field_name('zoom'); ?>" type="number" value="<?php echo $zoom; ?>" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Map Height', 'epl-am'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="number" value="<?php echo $height; ?>" />
		</p>
		
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('cluster'); ?>" name="<?php echo $this->get_field_name('cluster'); ?>" <?php if ($instance['cluster']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('cluster'); ?>"><?php _e('Allow Marker Clustering', 'epl-am'); ?></label>
		</p>
		
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('home_open'); ?>" name="<?php echo $this->get_field_name('home_open'); ?>" <?php if ($instance['home_open']) echo 'checked="checked"' ?> />
			<label for="<?php echo $this->get_field_id('home_open'); ?>"><?php _e('Only show listings open for inspection', 'epl-am'); ?></label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('property_status'); ?>"><?php _e('Property Status', 'epl-am'); ?></label> 
			 <select  multiple class="widefat" id="<?php echo $this->get_field_id('property_status'); ?>" name="<?php echo $this->get_field_name('property_status'); ?>[]">
                <option value="">  <?php _e("Property Status", "epl-am"); ?>  </option>
                <?php
                $status_types = array(
					'current'	=>	__('Current','epl-am'),
					'withdrawn'	=>	__('Withdrawn','epl-am'),
					'offmarket'	=>	__('Off Market','epl-am'),
					'sold'		=>	__('Sold','epl-am')
				);
                    foreach($status_types as $k =>	$status_type) {
                    	$selected = '';
					if(in_array( $k , $property_status) ) {
						$selected = 'selected="selected"';
					}
                    ?>
                        <option <?php echo $selected ?> value="<?php echo esc_html($k) ?>"><?php echo esc_html($status_type) ?></option>
                        <?php
                    }
                ?>
            </select>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Location', 'epl-am'); ?></label> 
			 <select class="widefat" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>">
                <option value="">  <?php _e("All locations", "epl-am"); ?>  </option>
                 <?php
                    $locations = get_terms('location');
                    foreach($locations as $this_location) {
                    	$selected = '';
					if($location == $this_location->slug) {
						$selected = ' selected="selected" ';
					}
                    ?>
                        <option <?php echo $selected ?> value="<?php echo esc_html($this_location->slug) ?>"><?php echo esc_html($this_location->name) ?></option>
                        <?php
                    }
            	 ?>
            </select>
		</p>
		
		<?php 
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("EPL_Widget_Map");') );
