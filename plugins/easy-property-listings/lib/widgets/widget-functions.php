<?php
	
	/**
	 * search widget form fields for search widget
	 * @since 2.2
	 */
	function epl_search_widget_fields() {
		$fields = apply_filters( 'epl_search_widget_fields',  array(
	
			array(
				'key'			=>	'title',
				'label'			=>	__('Title','epl'),
				'type'			=>	'text',
				'default'		=>	''
			),
			array(
				'key'			=>	'post_type',
				'label'			=>	__('Post Type','epl'),
				'default'		=>	array('property'),
				'type'			=>	'select',
				'multiple'		=>	true,
				'options'		=>	epl_get_active_post_types(),
			),
			array(
				'key'			=>	'style',
				'label'			=>	__('Style','epl'),
				'default'		=>	'default',
				'type'			=>	'select',
				'options'		=>	array(
					'default'	=>	__('Default' , 'epl'),
					'wide'		=>	__('Wide' , 'epl'),
					'slim'		=>	__('Slim' , 'epl'),
					'fixed'		=>	__('Fixed Width' , 'epl'),
				)
			),
			array(
				'key'			=>	'property_status',
				'label'			=>	__('Status','epl'),
				'default'		=>	'',
				'type'			=>	'select',
				'options'		=>	array(
					''		=>	__('Any' , 'epl'),
					'current'	=>	__('Current' , 'epl'),
					'sold'		=>	apply_filters( 'epl_sold_label_status_filter' , __('Sold', 'epl') ),
					'leased'	=>	apply_filters( 'epl_leased_label_status_filter' , __('Leased', 'epl') )
				),
			),
			array(
				'key'			=>	'search_id',
				'label'			=>	__('Property ID','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_location',
				'label'			=>	epl_tax_location_label(),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_city',
				'label'			=>	epl_labels('label_city'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_state',
				'label'			=>	epl_labels('label_state'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_postcode',
				'label'			=>	epl_labels('label_postcode'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_country',
				'label'			=>	__('Country','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_house_category',
				'label'			=>	__('Category','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'house_category_multiple',
				'label'			=>	__('Categories: Multi select','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_price',
				'label'			=>	__('Price','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_bed',
				'label'			=>	__('Bed','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_bath',
				'label'			=>	__('Bath','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_rooms',
				'label'			=>	__('Rooms','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_car',
				'label'			=>	__('Car','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_land_area',
				'label'			=>	__('Land Area','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_building_area',
				'label'			=>	__('Building Area','epl'),
				'default'		=>	'off',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'search_other',
				'label'			=>	__('Other Search Options','epl'),
				'default'		=>	'on',
				'type'			=>	'checkbox',
			),
			array(
				'key'			=>	'submit_label',
				'label'			=>	__('Submit Label','epl'),
				'type'			=>	'text',
				'default'		=>	__('Search','epl')
			),
		) );
		
		return $fields;
	}

	function epl_number_suffix_callback($v,$suffix=' +') {
		return $v.''.$suffix;
	}
	
	/**
	 * search widget form fields for search widget - frontend 
	 * @since 2.2
	 */
	function epl_search_widget_fields_frontend($post_type='',$property_status='') {
		
		if( $post_type == 'rental' || $post_type == 'holiday_rental' ) {
		
			$price_array 	= array_combine(range(50,5000,50),array_map('epl_currency_formatted_amount',range(50,5000,50)) );
			$price_array 	= apply_filters('epl_listing_search_price_rental',$price_array);
		} else {
			$price_array 	= array_combine(range(50000,10000000,50000),array_map('epl_currency_formatted_amount',range(50000,10000000,50000)) );
			$price_array 	= apply_filters('epl_listing_search_price_sale',$price_array);
		}
		
		if( 
			isset($post_type) && 
			($post_type == 'rental' 
				|| $post_type == 'holiday_rental' 
			) 
		) {
			$price_meta_key = 'property_rent';
		} else {
			$price_meta_key = 'property_price';
		}

		$fields = apply_filters( 'epl_search_widget_fields_frontend',  array(
	
			array(
				'key'			=>	'post_type',
				'meta_key'		=>	'post_type',
				'type'			=>	'hidden',
			),
			array(
				'key'			=>	'property_status',
				'meta_key'		=>	'property_status',
				'type'			=>	'hidden',
				'query'			=>	array('query'	=>	'meta')
			),
			array(
				'key'			=>	'search_id',
				'meta_key'		=>	'property_id',
				'label'			=>	__('Search by Property ID / Address', 'epl'),
				'type'			=>	'text',
				'class'			=>	'epl-search-row-full',
				'query'			=>	array('query'	=>	'meta' , 'key'	=>	'property_unique_id')
			),
			array(
				'key'			=>	'search_location',
				'meta_key'		=>	'property_location',
				'label'			=>	epl_tax_location_label(),
				'type'			=>	'select',
				'option_filter'		=>	'location',
				'options'		=>	epl_get_available_locations($post_type,$property_status),
				'query'			=>	array('query'	=>	'tax'),
				'class'			=>	'epl-search-row-full',
			),
			array(
				'key'			=>	'search_city',
				'meta_key'		=>	'property_address_city',
				'label'			=>	epl_labels('label_city'),
				'type'			=>	'select',
				'option_filter'		=>	'city',
				'options'		=>	epl_get_unique_post_meta_values('property_address_city', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_state',
				'meta_key'		=>	'property_address_state',	
				'label'			=>	epl_labels('label_state'),
				'type'			=>	'select',
				'option_filter'		=>	'state',
				'options'		=>	epl_get_unique_post_meta_values('property_address_state', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_postcode',
				'meta_key'		=>	'property_address_postal_code',
				'label'			=>	epl_labels('label_postcode'),
				'type'			=>	'select',
				'option_filter'		=>	'postcode',
				'options'		=>	epl_get_unique_post_meta_values('property_address_postal_code', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_country',
				'meta_key'		=>	'property_address_country',
				'label'			=>	__( 'Country' , 'epl'),
				'type'			=>	'select',
				'option_filter'		=>	'country',
				'options'		=>	epl_get_unique_post_meta_values('property_address_country', $post_type ),
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_house_category',
				'meta_key'		=>	'property_category',
				'label'			=>	__('House Category','epl'),
				'option_filter'		=>	'category',
				'options'		=>	epl_get_meta_values( 'property_category', $post_type),
				'type'			=>	'select',
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-full',
				'exclude'		=>	array('rural','land','commercial','commercial_land','business'),
			),
			array(
				'key'			=>	'search_house_category',
				'meta_key'		=>	'property_rural_category',
				'label'			=>	__('Rural Category','epl'),
				'option_filter'		=>	'category',
				'options'		=>	epl_get_meta_values( 'property_rural_category', $post_type),
				'type'			=>	'select',
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-full',
				'exclude'		=>	array('property','rental','land','commercial','commercial_land','business'),
			),
			array(
				'key'			=>	'search_house_category',
				'meta_key'		=>	'property_land_category',
				'label'			=>	__('Land Category','epl'),
				'option_filter'		=>	'category',
				'options'		=>	epl_get_meta_values( 'property_land_category', $post_type),
				'type'			=>	'select',
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-full',
				'exclude'		=>	array('property','rental','rural','commercial','commercial_land','business'),
			),
			array(
				'key'			=>	'search_house_category',
				'meta_key'		=>	'property_commercial_category',
				'label'			=>	__('Commercial Category','epl'),
				'option_filter'		=>	'category',
				'options'		=>	epl_get_meta_values( 'property_commercial_category', $post_type),
				'type'			=>	'select',
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-full',
				'exclude'		=>	array('property','rental','land','rural','business'),
			),
			array(
				'key'			=>	'search_house_category',
				'meta_key'		=>	'property_business_category',
				'label'			=>	__('Business Category','epl'),
				'option_filter'		=>	'category',
				'options'		=>	epl_get_meta_values( 'property_business_category', $post_type),
				'type'			=>	'select',
				'query'			=>	array('query'	=>	'meta'),
				'class'			=>	'epl-search-row-full',
				'exclude'		=>	array('property','rental','land','rural','commercial','commercial_land'),
			),
			array(
				'key'			=>	'search_price',
				'meta_key'		=>	'property_price_from',
				'label'			=>	__('Price From','epl'),
				'type'			=>	'select',
				'option_filter'		=>	'price_from',
				'options'		=>	$price_array,
				'type'			=>	'select',
				'query'			=>	array(
									'query'		=>	'meta',
									'key'		=>	$price_meta_key,
									'type'		=>	'numeric',
									'compare'	=>	'>='
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_price',
				'meta_key'		=>	'property_price_to',
				'label'			=>	__('Price To','epl'),
				'type'			=>	'select',
				'option_filter'		=>	'price_to',
				'options'		=>	$price_array,
				'type'			=>	'select',
				'query'			=>	array(
									'query'		=>	'meta', 
									'key'		=>	$price_meta_key, 
									'type'		=>	'numeric', 
									'compare'	=>	'<=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_bed',
				'meta_key'		=>	'property_bedrooms_min',
				'label'			=>	__('Bedrooms Min', 'epl'),
				'option_filter'		=>	'bedrooms_min',
				'options'		=>	apply_filters(
									'epl_listing_search_bed_select_min',
									array_combine(range(1,10),array_map('epl_number_suffix_callback',range(1,10)) )
								),
				'type'			=>	'select',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'key'		=>	'property_bedrooms', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_bed',
				'meta_key'		=>	'property_bedrooms_max',
				'label'			=>	__('Bedrooms Max', 'epl'),
				'option_filter'		=>	'bedrooms_max',
				'options'		=>	apply_filters(
										'epl_listing_search_bed_select_max',
										array_combine(range(1,10),array_map('epl_number_suffix_callback',range(1,10)) )
									),
				'type'			=>	'select',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'key'		=>	'property_bedrooms', 
									'type'		=>	'numeric', 
									'compare'	=>	'<=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_bath',
				'meta_key'		=>	'property_bathrooms',
				'label'			=>	__('Bathrooms', 'epl'),
				'option_filter'		=>	'bathrooms',
				'options'		=>	apply_filters(
										'epl_listing_search_bath_select',
										array_combine(range(1,3),array_map('epl_number_suffix_callback',range(1,3)) )
									),
				'type'			=>	'select',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=' 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_rooms',
				'meta_key'		=>	'property_rooms',
				'label'			=>	__('Rooms', 'epl'),
				'option_filter'		=>	'rooms',
				'options'		=>	apply_filters(
										'epl_listing_search_room_select',
										array_combine(range(1,3),array_map('epl_number_suffix_callback',range(1,3)) )
									),
				'type'			=>	'select',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=' 
								),
				'class'			=>	'epl-search-row-half',
			), 
			array(
				'key'			=>	'search_car',
				'meta_key'		=>	'property_carport',
				'label'			=>	__('Car Spaces', 'epl'),
				'option_filter'		=>	'carport',
				'options'		=>	apply_filters(
										'epl_listing_search_parking_select',
										array_combine(range(1,3),array_map('epl_number_suffix_callback',range(1,3)) )
									),
				'type'			=>	'select',
				'class'			=>	'epl-search-row-half',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'multiple'	=>	true,
									'query'		=>	'meta',
									'relation'	=>	'OR',
									'sub_queries'	=> array( 
										array(
											'key'		=>	'property_carport',
											'type'		=>	'numeric',
											'compare'	=>	'>='
										),
										array(
											'key'		=>	'property_garage',
											'type'		=>	'numeric',
											'compare'	=>	'>='
										)
									)
								)
			), 
			array(
				'key'			=>	'search_land_area',
				'meta_key'		=>	'property_land_area_min',
				'label'			=>	__('Land Min','epl'),
				'type'			=>	has_filter('epl_property_land_area_min') ? apply_filters('epl_property_land_area_min','') : 'number',
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=', 
									'key'		=>	'property_land_area' 
								),
				'class'			=>	'epl-search-row-third',
				'wrap_start'		=>	'epl-search-row epl-search-land-area'
			),
			array(
				'key'			=>	'search_land_area',
				'meta_key'		=>	'property_land_area_max',
				'label'			=>	__('Land Max','epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	has_filter('epl_property_land_area_max') ? apply_filters('epl_property_land_area_max','') : 'number',
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'<=', 
									'key'		=>	'property_land_area' 
								)
			),
			array(
				'key'			=>	'search_land_area',
				'meta_key'		=>	'property_land_area_unit',
				'label'			=>	__('Area Unit', 'epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	'select',
				'option_filter'		=>	'land_area_unit',
				'options'		=>	apply_filters( 'epl_listing_search_land_unit_label',
										array(
											'square'	=>	'Square',
											'squareMeter'	=>	'Square Meter',
											'acre'		=>	'Acre',
											'hectare'	=>	'Hectare',
											'sqft'		=>	'Square Feet',
										)
									),
				'query'			=>	array('query'	=>	'meta'),
				'wrap_end'		=>	true

			),
			array(
				'key'			=>	'search_building_area',
				'meta_key'		=>	'property_building_area_min',
				'label'			=>	__('Building Min','epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	has_filter('epl_property_building_area_min') ? apply_filters('epl_property_building_area_min','') : 'number',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'>=', 
									'key'		=>	'property_building_area' 
								),
				'wrap_start'		=>	'epl-search-row epl-search-building-area'
			),
			array(
				'key'			=>	'search_building_area',
				'meta_key'		=>	'property_building_area_max',
				'label'			=>	__('Building Max','epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	has_filter('epl_property_building_area_max') ? apply_filters('epl_property_building_area_max','') : 'number',
				'exclude'		=>	array('land'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'type'		=>	'numeric', 
									'compare'	=>	'<=', 
									'key'		=>	'property_building_area'
								)
			),
			array(
				'key'			=>	'search_building_area',
				'meta_key'		=>	'property_building_area_unit',
				'label'			=>	__('Area Unit', 'epl'),
				'class'			=>	'epl-search-row-third',
				'type'			=>	'select',
				'option_filter'		=>	'building_area_unit',
				'options'		=>	apply_filters( 'epl_listing_search_building_unit_label',
									array(
										'square'	=>	'Square',
										'squareMeter'	=>	'Square Meter',
										'acre'		=>	'Acre',
										'hectare'	=>	'Hectare',
										'sqft'		=>	'Square Feet',
									)
								),
				'exclude'		=>	array('land'),
				'query'			=>	array('query'	=>	'meta'),
				'wrap_end'		=>	true
			),

			array(
				'key'			=>	'search_other',
				'meta_key'		=>	'property_air_conditioning',
				'label'			=>	__('Air Conditioning', 'epl'),
				'type'			=>	'checkbox',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'query'		=>	'meta', 
									'compare'	=>	'IN', 
									'value'		=>	array('yes','1') 
								),
				'class'			=>	'epl-search-row-half',
				'wrap_start'		=>	'epl-search-row epl-search-other'
			),
			array(
				'key'			=>	'search_other',
				'meta_key'		=>	'property_pool',
				'label'			=>	__('Pool', 'epl'),
				'type'			=>	'checkbox',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'query'		=>	'meta',
									'compare'	=>	'IN', 
									'value'		=>	array('yes','1') 
								),
				'class'			=>	'epl-search-row-half',
			),
			array(
				'key'			=>	'search_other',
				'meta_key'		=>	'property_security_system',
				'label'			=>	__('Security', 'epl'),
				'type'			=>	'checkbox',
				'exclude'		=>	array('land','commercial','commercial_land','business'),
				'query'			=>	array(
									'query'		=>	'meta',
									'compare'	=>	'IN', 
									'value'		=>	array('yes','1') 
								),
				'class'			=>	'epl-search-row-half',
				'wrap_end'		=>	true
			)
		) );
		return $fields;
	}
	/**
	 * search widget form fields defaults
	 * @since 2.2
	 */
	function epl_search_get_defaults() {
		
		$defaults 	= array();
		$fields 	= epl_search_widget_fields();
		
		foreach($fields as $field) {
			$defaults[$field['key']] = $field['default'];
		}
		return $defaults;	
			
	}
	
	/**
	 * render widget field blocks -- for backend form
	 * @since 2.2
	 */
	
	function epl_widget_render_backend_field($field,$object,$value='') {

		switch ($field['type']) {
		
			// checkbox
			case "checkbox": ?>
				<p>
					<input 
						id="<?php echo $object->get_field_id($field['key']); ?>" 
						name="<?php echo $object->get_field_name($field['key']); ?>" 
						type="checkbox" 
							<?php 
								if(isset($value) && $value == 'on') { 
									echo 'checked="checked"'; 
								} 
							?>
					/>
					<label for="<?php echo $object->get_field_id($field['key']); ?>">
						<?php echo $field['label']; ?>
					</label>
				</p> <?php
			
			break;
			
			// text
			case "text": ?>
				<p>
					<label for="<?php echo $object->get_field_id($field['key']); ?>">
						<?php echo $field['label']; ?>
					</label>
					<input 
						id="<?php echo $object->get_field_id($field['key']); ?>" 
						name="<?php echo $object->get_field_name($field['key']); ?>" 
						type="text" 
						value="<?php echo $value; ?>"
					/>
				</p> <?php
			
			break;
			
			// select
			case "select": ?>
				<p>
					<label for="<?php echo $object->get_field_id($field['key']); ?>">
						<?php echo $field['label']; ?>
					</label>
					
					<select
					
						<?php echo isset($field['multiple']) ? ' multiple ':' '; ?>
						class="widefat" 
						id="<?php echo $object->get_field_id($field['key']); ?>" 
						name="<?php echo $object->get_field_name($field['key']); echo isset($field['multiple']) ? '[]':''; ?>">
						
						<?php
							foreach($field['options'] as $k=>$v) {
								$selected = '';
								if( isset($field['multiple']) ) {
								
									if(in_array( $k, $value) ) {
										$selected = 'selected="selected"';
									}
									
								} else {
								
									if(isset($value) && $k == $value) {
										$selected = 'selected="selected"';
									}
								}
								echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
							}
						?>
						
					</select>
				</p> <?php

			break;
		}
	}
	
	/**
	 * render widget field blocks -- for front-end form
	 * @since 2.2
	 */
	function epl_widget_render_frontend_fields($field,$config='',$value='',$post_type='',$property_status='') {

		if( $field['type'] != 'hidden') {
			if( $config != 'on' )
				return;
		}
		
		if( !empty($field['exclude']) && in_array($post_type,$field['exclude']) )
			return; 
			
		if( isset($field['wrap_start']) ) {
			echo '<div class="'.$field['wrap_start'].'">';
		}
		
		switch ($field['type']) {
			// checkbox
			case "checkbox": ?>
				<span class="epl-search-row epl-search-row-checkbox <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
						<input type="checkbox" name="<?php echo $field['meta_key']; ?>" id="<?php echo $field['meta_key']; ?>" class="in-field" 
						<?php if(isset($value) && !empty($value)) { echo 'checked="checked"'; } ?> />
						<label for="<?php echo $field['meta_key']; ?>" class="check-label">
						<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'],__($field['label'], 'epl') ); ?>
						</label>
				</span> <?php
			break;
			
			// text
			case "text": ?>
				<div class="epl-search-row epl-search-row-text epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
				
					<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
						<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
					</label>

					<div class="field">
						<input 
							type="text" 
							class="in-field field-width" 
							name="<?php echo $field['meta_key']; ?>" 
							id="<?php echo $field['meta_key']; ?>"
							value="<?php echo $value; ?>" 
						/>
					</div>
				</div> <?php
			break;
			
			// number
			case "number": ?>
				<div class="epl-search-row epl-search-row-number epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
				
					<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
						<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
					</label>

					<div class="field">
						<input 
							type="number" 
							class="in-field field-width" 
							name="<?php echo $field['meta_key']; ?>" 
							id="<?php echo $field['meta_key']; ?>"
							value="<?php echo $value; ?>" 
						/>
					</div>
					
				</div> <?php
			break;
			
			// select
			case "select": ?>
				<div class="epl-search-row epl-search-row-select epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">
				
					<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
						<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
					</label>
					
					<div class="field">
							<select
								<?php echo isset($field['multiple']) ? ' multiple ':' '; ?>
								name="<?php echo $field['meta_key']; echo isset($field['multiple']) ? '[]':''; ?>" 
								id="<?php echo $field['meta_key']; ?>" 
								class="in-field field-width">
								<option value="">
									<?php echo apply_filters('epl_search_widget_option_label_'.$field['option_filter'],__('Any', 'epl') ); ?>
								</option>
							<?php
								if( isset($field['options']) && !empty($field['options'])  ) {
									foreach($field['options'] as $k=>$v) {
										$selected = '';
										if( isset($field['multiple']) ) {
								
											if(in_array( $k, $value) ) {
												$selected = 'selected="selected"';
											}
									
										} else {
								
											if(isset($value) && $k == $value) {
												$selected = 'selected="selected"';
											}
										}
										echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
									}
								}
							?>
						</select>
					</div>								
				</div> <?php
			break;
			
			// hidden
			case "hidden": ?>
				<input 
					type="hidden" 
					class="in-field field-width" 
					name="<?php echo $field['meta_key']; ?>" 
					id="<?php echo $field['meta_key']; ?>"
					value="<?php echo $value; ?>" 
				/> <?php
			
			break;
		}
		if( isset($field['wrap_end']) ) {
			echo '</div>';
		}
	}
	
//Property Search Query
function epl_search_pre_get_posts( $query ) {
	global $custom_ajax_search;
	if(!$custom_ajax_search) {
		if (is_admin() || !$query->is_main_query()) {
			return;
		}
	}
	
	if( epl_is_search() ) {
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		
		$query->init();
		$query->set('posts_per_page', get_option('posts_per_page'));
		$query->set('paged', $paged);
		$query->is_search = true;
		extract($_REQUEST);
		
		if(isset($property_id) ) {
			if(is_numeric($property_id)) {
				
			} else {
				$query->set( 'epl_post_title', sanitize_text_field($property_id) );
			}
				
		}
		
		if(isset($property_agent) ) {
			$property_agent = sanitize_title_with_dashes($property_agent);
				
				if( $property_agent = get_user_by('slug',$property_agent) ) {
			
					$query->set( 'post_author', $property_agent->ID );
				}
				
		}
		
		if(isset($post_type) && !empty($post_type)) {
			$query->set('post_type', $post_type);
		} else {
			$epl_post_types = epl_get_active_post_types();
			if(!empty($epl_post_types)) {
				$epl_post_types = array_keys($epl_post_types);
				$query->set('post_type', $epl_post_types);
			}
		}
		
		$epl_meta_query = array();
		
		$epl_search_form_fields = epl_search_widget_fields_frontend($post_type,$property_status);
		
		foreach($epl_search_form_fields as $epl_search_form_field) {
			
			
			
			if( isset($epl_search_form_field['query']) ) {
				
				if($epl_search_form_field['query']['query'] == 'meta') {
				
					$this_meta_query = array();
					
					if( isset($epl_search_form_field['query']['multiple']) && $epl_search_form_field['query']['multiple'] == true) {
					
						if( isset(${$epl_search_form_field['meta_key']}) && !empty(${$epl_search_form_field['meta_key']}) ) {
						
							$this_meta_query['relation'] = 
								isset($epl_search_form_field['query']['relation']) ?
								$epl_search_form_field['query']['relation'] : 'OR';
							
							foreach($epl_search_form_field['query']['sub_queries'] as $sub_query) {
						
								$this_sub_query = array(
									'key'		=>	$sub_query['key'],
									'value'		=>	${$epl_search_form_field['meta_key']},
									'type'		=>	$sub_query['type'],
									'compare'	=>	$sub_query['compare']
								);
								$this_meta_query[] = $this_sub_query;
							}
							$epl_meta_query[] = $this_meta_query;
						}
						
					} else {
						
						$query_meta_key = isset($epl_search_form_field['query']['key']) ? 
						$epl_search_form_field['query']['key'] :
						$epl_search_form_field['meta_key'];
						
						if($query_meta_key == 'property_unique_id' && isset(${$epl_search_form_field['meta_key']}) &&  !is_numeric(${$epl_search_form_field['meta_key']}) ) {
							continue;
						}
						
						if( isset(${$epl_search_form_field['meta_key']}) && !empty(${$epl_search_form_field['meta_key']}) ) {
						
							$this_meta_query = array(
								'key'	=>	$query_meta_key,
								'value'	=>	${$epl_search_form_field['meta_key']}
							);
						
							isset($epl_search_form_field['query']['compare']) ? $this_meta_query['compare'] = $epl_search_form_field['query']['compare'] : '';
							isset($epl_search_form_field['query']['type']) ? $this_meta_query['type'] = $epl_search_form_field['query']['type'] : '';
							isset($epl_search_form_field['query']['value']) ? $this_meta_query['value'] = $epl_search_form_field['query']['value'] : '';
							$epl_meta_query[] = $this_meta_query;
						}
					}
				}
			}
		}
		if(!empty($epl_meta_query)) {
			$query->set('meta_query', $epl_meta_query);
		}
		
		$tax_query = array();
		if(isset($property_location) && !empty($property_location)) {
			$tax_query[] = array(
				'taxonomy'	=>	'location',
				'field'		=>	'id',
				'terms'		=>	$property_location
			);
		}
		
		if(!empty($tax_query)) {
			$query->set('tax_query', $tax_query);
		}
		$query->parse_query();
		$query->request;
	}
}
add_action( 'pre_get_posts', 'epl_search_pre_get_posts' );

function set_postfields($field, $wp_query) {
	global $wpdb;
	if ( epl_is_search()) {
		if(isset($_GET['my_epl_input_lat']) && isset($_GET['my_epl_input_lng'])) {
			$km = '6371';
			$mile = '3959';
			$slat = floatval($_GET['my_epl_input_lat']);
			$slng = floatval($_GET['my_epl_input_lng']);
			$lat = "lat_postmeta.meta_value";
			$lng = "lng_postmeta.meta_value";
			$dist = ' , (' . $km . ' * acos( cos( radians(' . $slat . ') ) * cos( radians(' . $lat . ') )' .
				'* cos( radians(' . $lng . ')' . '- radians(' . $slng . ') ) + sin( radians(' . $slat . ') )' .
				'* sin( radians(' . $lat . ') ) ) ) as DISTANCE';
			$field .= $dist;
		}
	}
	return $field;
}

add_filter('posts_fields', 'set_postfields', 10, 2);

function set_postjoin($join, $wp_query) {
	if ( epl_is_search()) {
		if ((isset($_GET['my_epl_input_lat']) && isset($_GET['my_epl_input_lng']))
			|| (isset($_GET['my_epl_bb_max_lat']) && isset($_GET['my_epl_bb_min_lat']) &&
			isset($_GET['my_epl_bb_max_lng']) && isset($_GET['my_epl_bb_min_lng']))){
			$addition = " LEFT JOIN wp_postmeta lat_postmeta on lat_postmeta.post_id = wp_posts.ID"
				. " and lat_postmeta.meta_key = 'property_coordinate_lat'
			LEFT JOIN wp_postmeta lng_postmeta on lng_postmeta.post_id = wp_posts.ID"
				. " and lng_postmeta.meta_key = 'property_coordinate_lng'";
			$join .= $addition;
		}
	}
	return $join;
}

add_filter('posts_join', 'set_postjoin', 10, 2);

function set_groupby($groupby, $wp_query){
	global $wpdb;
	if ( epl_is_search()) {
		if (isset($_GET['my_epl_input_lat']) && isset($_GET['my_epl_input_lng'])) {
			$groupby = "{$wpdb->posts}.ID";
		}
	}
	return $groupby;
}

add_filter('posts_groupby', 'set_groupby', 10, 2);

function set_having($having, $wp_query){
	if ( epl_is_search()) {
		if (isset($_GET['my_epl_input_lat']) && isset($_GET['my_epl_input_lng'])) {
			$distance = '100';
			if (isset($_GET['distance-scope']) && $_GET['distance-scope'] != 'auto'){
				$distance = floatval($_GET['distance-scope']);
			}
			$addition = " having DISTANCE < $distance";
			$having .= $addition;
		}
	}
	return $having;
}

add_filter('posts_groupby', 'set_having', 10000, 2);

function set_bbox_where($where, &$wp_query)
{
	if (epl_is_search()) {
		if (isset($_GET['my_epl_bb_max_lat']) && isset($_GET['my_epl_bb_min_lat']) &&
			isset($_GET['my_epl_bb_max_lng']) && isset($_GET['my_epl_bb_min_lng'])){
				$lat = "lat_postmeta.meta_value";
				$lng = "lng_postmeta.meta_value";
				$min_lat = floatval($_GET['my_epl_bb_min_lat']);
				$max_lat = floatval($_GET['my_epl_bb_max_lat']);
				$min_lng = floatval($_GET['my_epl_bb_min_lng']);
				$max_lng = floatval($_GET['my_epl_bb_max_lng']);
				$where .= " AND ($lat BETWEEN $min_lat AND $max_lat)";

				if($min_lng > 180){
					$min_lng -= 360;
				}else if($min_lng < -180){
					$min_lng += 360;
				}
				if($max_lng > 180){
					$max_lng -= 360;
				}else if($max_lng < -180){
					$max_lng += 360;
				}
				if($min_lng > $max_lng){
					$where .= " AND (($lng BETWEEN $min_lng AND 180.0)"
						. " OR ($lng BETWEEN -180.0 AND $max_lng))";
				}else{
					$where .= " AND ($lng BETWEEN $min_lng AND $max_lng)";
				}
		}
	}
	return $where;
}

add_filter('posts_where', 'set_bbox_where', 10, 2);

//Is Property Search
function epl_is_search() {
	if((isset($_REQUEST['action']) && $_REQUEST['action'] == 'epl_search') ||
		(isset($_REQUEST['epl_action']) && $_REQUEST['epl_action'] == 'epl_search')) {
			return true;
	}
	return false;
}

class CustomSearchPageGenerator
{
	private function custom_get_pagenum_link($pagenum = 1, $escape = true ) {
		$pagenum = (int) $pagenum;

		$request = remove_query_arg( 'paged' );

		$home_root = parse_url(home_url());
		$home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
		$home_root = preg_quote( $home_root, '|' );

		$request = preg_replace('|^'. $home_root . '|i', '', $request);
		$request = preg_replace('|^/+|', '', $request);

		$base = trailingslashit( get_bloginfo( 'url' ) );

		if ( $pagenum > 1 ) {
			$result = add_query_arg( 'paged', $pagenum, $base . $request );
		} else {
			$result = $base . $request;
		}

		/**
		 * Filter the page number link for the current request.
		 *
		 * @since 2.5.0
		 *
		 * @param string $result The page number link.
		 */
		$result = apply_filters( 'get_pagenum_link', $result );

		if ( $escape )
			return esc_url( $result );
		else
			return esc_url_raw( $result );
	}

	public static function generate_post_pagination($query)
	{
		$big = 999999999; // need an unlikely integer

		$pages = paginate_links(array(
			'base' => str_replace($big, '%#%', self::custom_get_pagenum_link($big, $escape = false)),
			'format' => '?paged=%#%',
			'current' => max(1, get_query_var('paged')),
			'total' => $query->max_num_pages,
			'type' => 'array',
		));

		if (is_array($pages)) {
			echo '<div class="pagination-wrap"><ul id="pagination-list" class="pagination">';
			foreach ($pages as $page) {
				if (strpos($page, 'current') !== false) {
					echo "<li class=\"active\">$page</li>";
				} else {
					echo "<li>$page</li>";
				}
			}
			echo '</ul></div>';
		};
	}

	public static function generate_post_list()
	{
		global $wp_query, $property, $post;
		$posts_json = array();
		if (have_posts()) :
			$custom_styles = '<style>
			.c-span{
				height: 24px !important;
				width: 24px !important;
				font-size: 12px !important;
				padding: 0 5px !important;
			}
			.c-font{
				font-family: Circular, Helvetica Neue, Helvetica, Arial, sans-serif;
				font-size: 18px;
				padding: 5px;
			}
			.c-text-overflow{
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
			}
			@media(max-width: 700px) {
				.c-responsive{
					display: inline-block;
					width: 100%;
				}
			}
			@media(min-width: 701px) {
				.c-responsive{
					min-width: 40%;
					max-width: 48%;
					width: 45%;
				}
			}
			</style>';
			echo $custom_styles;
			$cs_path = plugins_url('custom_support');
			echo '<table class="table-responsive" style="border-collapse: separate; border-spacing: 10px">';
			$idx = 0;
			$cols = 2;
			$num_idx = 1;
			global $user_ID;
			/* script to add to or remove from wishlist */
			$like_scrpit2 = '<script> 
								var urlstr = "/api/0/favorites/user/";
								var wishlist;
								alert(wishlist.data.length);
								function LikeSwitch(imgElem){
									postID = imgElem.id.substring(4);
									if (imgElem.name == "like") {  // add to wishlist
										jQuery.ajax({
											url: "/api/0/favorites",
											dataType: "json",
											method: "POST",
											data: {"fType": "post", "fValue": postID,"userID" : '.$user_ID.'},
											success: function(result){									
												wishlist.data.push(result.data); // add to the array
												imgElem.src = "https://maxcdn.icons8.com/Color/PNG/24/Messaging/filled_like-24.png";
												imgElem.name = "liked";
												imgElem.style.opacity = 1;
											}});
									} else {   // remove from wishlist
										var urldel = "/api/0/favorites/";
										for (var i=0; i<wishlist.data.length; i++ )
										{
											if (wishlist.data[i].fType == "post" && wishlist.data[i].fValue == postID) {
												favID = wishlist.data[i]._id;
											}
										}
										jQuery.ajax({
											url: urldel + favID,
											dataType: "json",
											method: "DELETE",
											success: function(result){
												if (result.data.ok == 1) {
													for (var i = 0; i < wishlist.data.length; i++) {
														if (wishlist.data[i]._id == favID) {
															wishlist.data.splice(i,1);
														}
													}
													imgElem.src = "https://maxcdn.icons8.com/Android_L/PNG/24/Messaging/filled_like-24.png";
													imgElem.name = "like";
													imgElem.style.opacity = 0.2;
													
											} else { //server error: failed to delete
												alert("Oops, we are encountering some server issue. Please try it later.");
											}
										}});
									}
								}
							</script>';
			
			echo $like_scrpit2;
			// the Loop
			while (have_posts()) : the_post();
				//do_action('epl_property_blog');
				$idx = $idx % $cols;

				if (has_post_thumbnail()) {
					$image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'medium');
					$prop_image = $image[0];
				} else {
					$prop_image = $cs_path . '/images/no_photo_ph.jpg';
				}
				$post_title = $post->post_title;
				$post_link = esc_url(get_permalink());
				$post_address = epl_property_get_the_full_address();
				$post_bed = $property->get_property_bed_raw();
				$post_bath = $property->get_property_bath_raw();
				$post_parking = $property->get_property_parking_raw();
				$post_air = $property->get_property_air_conditioning_raw();
				$post_pool = $property->get_property_pool_raw();

				$post_json = array('coord_lat' => $property->get_property_meta('property_coordinate_lat'),
					'coord_lng' => $property->get_property_meta('property_coordinate_lng'),
					'title' => $post_title,
					'image' => $prop_image,
					'address' => $post_address,
					'bed' => $post_bed,
					'bath' => $post_bath,
					'parking' => $post_parking,
					'air' => $post_air,
					'pool' => $post_pool,
					'type' => $post->post_type,
					'link' => $post_link);

				$post_price_tag = '';
				if ($post->post_type == 'property') {
					$post_json['price'] = epl_get_property_price();
					$post_price_tag .= $post_json['price'];
				} elseif ($post->post_type == 'rental') {
					$post_json['rent'] = $property->get_property_rent();
					$post_json['period'] = $property->get_property_meta('property_rent_period');
					$post_price_tag .= $post_json['rent'] . ' /' . $post_json['period'];
				}
				if ($idx == 0) {
					echo '<tr>';
				}

				$bed_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/bed-24.png" title="Bed" width="24" height="24">';
				$bath_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/shower_and_tub-24.png" title="Bath" width="24">';
				$parking_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/garage-24.png" title="Parking" width="24">';
				$air_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/air_conditioner-24.png" title="Air Conditioner" width="24">';
				$pool_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Sports/swimming-24.png" title="Pool" width="24">';
				$fhtml = '<div class="epl-adv-popup-meta" style="position: relative;">';
				$fhtml .= '<span class="c-span">' . $bed_icon . '</span><span class="c-span">' . $post_bed . '</span>';
				$fhtml .= '<span class="c-span">' . $bath_icon . '</span><span class="c-span">' . $post_bath . '</span>';
				$fhtml .= '<span class="c-span">' . $parking_icon . '</span><span class="c-span">' . $post_parking . '</span>';
				if ($post_air) {
					$fhtml .= '<span class="c-span">' . $air_icon . '</span>';
				}
				if ($post_pool) {
					$fhtml .= '<span class="c-span">' . $pool_icon . '</span>';
				}
				$fhtml .= '</div>';

				$contents = '<div style="position:absolute; width: 100%; height: 20%; left: 10px; bottom: 5px;
 				padding:5px">';
				$title_elem = '<div class="c-text-overflow" style="max-width: 80%; height: 29px;">' .
					'<span class="c-font" style="color:rgb(255, 128, 0)">' . $num_idx
					. '</span><a class="c-font c-text-overflow" href="' . $post_link . '"
				 title="' . $post_title . '" style="color:rgb(86, 90, 92);">' . $post_title
					. '</a></div>';

				$author_sticker = epl_get_author_sticker();
				$author_elem = '<div style="position: absolute; right: 25px; bottom: 25px">' . $author_sticker . '</div>';
				$like_sticker = '<button class="close" style="margin-right: 30%; opacity: 1" ><img id="like'.$post->ID.'" style="opacity: .2" src="https://maxcdn.icons8.com/Android_L/PNG/24/Messaging/filled_like-24.png" title="Add to Wish List"  width="30" name="like" onclick="LikeSwitch(this)"></button>';
				$like_elem = '<div style="position:absolute; right: 0px; top: 9%; width: 55px;
			    display: block; padding: 4px; background-color: rgba(230,232,232,0.618)"><span>'
					. $like_sticker . '</span></div>';
				$like_scrpit = '<script>
										if(wishlist == null) 
										{
											jQuery.ajax({
												url: urlstr.concat('.$user_ID.'),
												dataType: "json",
												method: "Get",
												success: function(result){
													wishlist = result;
                                        			for (var i=0; i < wishlist.data.length; i++){
                                        			
                                        				if (wishlist.data[i].fType == "post" && wishlist.data[i].fValue == '.$post->ID.') {
															document.getElementById("like'.$post->ID.'").src = \'https://maxcdn.icons8.com/Color/PNG/24/Messaging/filled_like-24.png\';
															document.getElementById("like'.$post->ID.'").name = "liked";
															document.getElementById("like'.$post->ID.'").style.opacity = 1;
													}
                                        		}
											}});
										} else 
										{
											for (var i=0; i < wishlist.data.length; i++){
                                        		if (wishlist.data[i].fType == "post" && wishlist.data[i].fValue == '.$post->ID.') {
													document.getElementById("like' . $post->ID . '").src = \'https://maxcdn.icons8.com/Color/PNG/24/Messaging/filled_like-24.png\';
													document.getElementById("like' . $post->ID . '").name = "liked";
													document.getElementById("like' . $post->ID . '").style.opacity = 1;
												}
											}
										}
 										
                                        
								</script>';
				$contents .= $title_elem . $fhtml . '</div>';
				$price_elem = '<div style="position:absolute; left: 0px; top: 61.8%;
			    display: block; padding: 5px; background-color: rgba(230,232,232,0.618)"><span>'
					. $post_price_tag . '</span></div>';
				$img_elem = '<div style="max-width:100%; height: auto; margin-bottom: 20%;">' .
					'<a href="' . $post_link . ' "><img class="img-responsive" src="' . $prop_image . '"
				 style="margin-left: auto; margin-right: auto;" /></a></div>';
				echo '<td class="jumbotron c-responsive" style="position: relative;">' .
					$img_elem . $contents . $price_elem . $like_elem. $like_scrpit. $author_elem .
					'</td>';
				if ($idx == $cols - 1) {
					echo '</tr>';
				}
				$idx += 1;
				array_push($posts_json, $post_json);
				$num_idx++;
				// define the structure of your posts markup
			endwhile;
			// We only have one cell in the row. Add a fake cell to make the align well.
			if ($idx == 1) {
				echo '<td class="c-responsive" style="position: relative;"></td></tr>';
			}
			echo '</table>';
			CustomSearchPageGenerator::generate_post_pagination($wp_query);
		else:
			get_template_part('content', 'none');
		endif;
		echo '<div id="posts-jsonoutput" style="visibility: hidden; display:none">' . json_encode($posts_json) . '</div>';

	}
}

function load_post_ajax()
{
	// initialsise your output
	global $wp_query, $wp_the_query, $custom_ajax_search;
	$output = '';
	$custom_ajax_search = true;
	$wp_the_query = $wp_query;
	if (isset($_REQUEST['paged'])) {
		$paged_val = intval($_REQUEST['paged']);
		$wp_query->set('paged', $paged_val);
	}
	$wp_query->get_posts();
	CustomSearchPageGenerator::generate_post_list();
	die($output);
}

add_action('wp_ajax_load_post_ajax', 'load_post_ajax');
add_action('wp_ajax_nopriv_load_post_ajax', 'load_post_ajax');

function epl_get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {
	if( empty($key) ) {
		return;
	}
	
	global $wpdb;	
	$results = $wpdb->get_results( $wpdb->prepare( "SELECT distinct(pm.`meta_value`) FROM {$wpdb->postmeta} pm LEFT JOIN {$wpdb->posts} p ON p.`ID` = pm.`post_id` WHERE pm.`meta_key` = '%s' AND p.`post_status` = '%s' AND p.`post_type` = '%s' AND pm.`meta_value` != ''", $key, $status, $type ));
	if(!empty($results)) {
		$return = array();
		if($key == 'property_category') {
			 $defaults = epl_listing_load_meta_property_category();
		}
		foreach($results as $result) {
			if(isset( $defaults ) && !empty( $defaults )) {
				if( isset($defaults[$result->meta_value]) )
					$return[$result->meta_value] = $defaults[$result->meta_value];
				else
					$return[$result->meta_value] = $result->meta_value;
			} else {
				$return[] = $result->meta_value;
			}
			
		}
		if(isset( $defaults ) )
			return $return;
		else
			return array_combine($return,$return);
	}
}

function epl_esc_like ($text) {
	 return addcslashes( $text, '_%\\' );
}

function epl_listings_where( $where, &$wp_query ) {
	global $wpdb;
    if ( $epl_post_title = $wp_query->get( 'epl_post_title' ) ) {
        $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( epl_esc_like( $epl_post_title ) ) . '%\'';
    }
    return $where;
}
add_filter( 'posts_where', 'epl_listings_where', 10, 2 );

function epl_get_available_locations($post_type='',$property_status='') {
	global $wpdb;
	$available_loc_query = "
	SELECT DISTINCT (
		tt.term_id
	)
	FROM {$wpdb->prefix}posts p
	LEFT JOIN {$wpdb->prefix}postmeta pm ON ( p.ID = pm.post_id )
	LEFT JOIN {$wpdb->prefix}term_relationships tr ON ( p.ID = tr.object_id )
	LEFT JOIN {$wpdb->prefix}term_taxonomy tt ON ( tr.term_taxonomy_id = tt.term_taxonomy_id ) WHERE
	tt.taxonomy 			= 'location'
	AND p.post_status 		= 'publish'
	AND p.post_type 		= '{$post_type}'";
	if($property_status != '') {
		$available_loc_query .= "
			AND pm.meta_key 		= 'property_status'
			AND pm.meta_value 		= '{$property_status}'";
	}
	$available_locs	= $wpdb->get_col($available_loc_query);
	$locations	= get_terms('location',array('hide_empty'	=> true,'include'	=>	$available_locs));
	$arr = array();
	foreach($locations as $location) {
		$arr[$location->term_id] = $location->name;
	}
	return $arr;

}

/** example to enable multiple house category via filter 

function epl_filter_search_widget_fields_frontend($fields) {
	
	foreach($fields as &$field) {
		if($field['key'] == 'search_house_category') {
			$field['multiple'] 	= true;
			$field['query'] 	= array('query'	=>	'meta','compare'	=>	'IN' );
			break;
		}
	}
	
	return $fields;
}
add_filter('epl_search_widget_fields_frontend','epl_filter_search_widget_fields_frontend');
 **/

/**
 * render custom frontend field blocks -- for front-end form
 * @since 2.2
 */
function epl_custom_render_frontend_fields($field,$config='',$value='',$post_type='',$property_status='') {

	if( $field['type'] != 'hidden') {
		if( $config != 'on' )
			return;
	}

	if( !empty($field['exclude']) && in_array($post_type,$field['exclude']) )
		return;

	if( isset($field['wrap_start']) ) {
		echo '<div class="'.$field['wrap_start'].'">';
	}

	switch ($field['type']) {
		// checkbox
		case "checkbox": ?>
			<div class="col-md-offset-2 col-md-10">
				<div class="checkbox">
					<label>
            			<input type="checkbox" name="<?php echo $field['meta_key']; ?>" id="<?php echo $field['meta_key']; ?>"
							<?php if(isset($value) && !empty($value)) { echo 'checked="checked"'; } ?> />
						<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'],__($field['label'], 'epl') ); ?>
          			</label>
          		</div>
          	</div>
			<?php
			break;

		// text
		case "text": ?>
			<div class="epl-search-row epl-search-row-text epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">

				<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
					<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
				</label>

				<div class="field">
					<input
						type="text"
						class="in-field field-width"
						name="<?php echo $field['meta_key']; ?>"
						id="<?php echo $field['meta_key']; ?>"
						value="<?php echo $value; ?>"
					/>
				</div>
			</div> <?php
			break;

		// number
		case "number": ?>
			<div class="epl-search-row epl-search-row-number epl-<?php echo $field['meta_key']; ?> fm-block <?php echo isset($field['class']) ? $field['class'] : ''; ?>">

				<label for="<?php echo $field['meta_key']; ?>" class="epl-search-label fm-label">
					<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
				</label>

				<div class="field">
					<input
						type="number"
						class="in-field field-width"
						name="<?php echo $field['meta_key']; ?>"
						id="<?php echo $field['meta_key']; ?>"
						value="<?php echo $value; ?>"
					/>
				</div>

			</div> <?php
			break;

		// select
		case "select": ?>
		    <div class="form-group">
				<label for="<?php echo $field['meta_key']; ?>" class="col-md-3 control-label">
					<?php echo apply_filters('epl_search_widget_label_'.$field['meta_key'], $field['label'] ); ?>
				</label>

				<div class="col-md-8">
					<select
						<?php echo isset($field['multiple']) ? ' multiple ':' '; ?>
						name="<?php echo $field['meta_key']; echo isset($field['multiple']) ? '[]':''; ?>"
						id="<?php echo $field['meta_key']; ?>"
						class="form-control">
						<option value="">
							<?php echo apply_filters('epl_search_widget_option_label_'.$field['option_filter'],__('Any', 'epl') ); ?>
						</option>
						<?php
						if( isset($field['options']) && !empty($field['options'])  ) {
							foreach($field['options'] as $k=>$v) {
								$selected = '';
								if( isset($field['multiple']) ) {

									if(in_array( $k, $value) ) {
										$selected = 'selected="selected"';
									}

								} else {

									if(isset($value) && $k == $value) {
										$selected = 'selected="selected"';
									}
								}
								echo '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
							}
						}
						?>
					</select>
				</div>
			</div> <?php
			break;

		// hidden
		case "hidden": ?>
			<input
				type="hidden"
				class="in-field field-width"
				name="<?php echo $field['meta_key']; ?>"
				id="<?php echo $field['meta_key']; ?>"
				value="<?php echo $value; ?>"
			/> <?php

			break;
	}
	if( isset($field['wrap_end']) ) {
		echo '</div>';
	}
}