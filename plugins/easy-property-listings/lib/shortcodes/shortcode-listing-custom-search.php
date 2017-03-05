<?php
/**
 * SHORTCODE :: Listing Search [listing_search]
 *
 * @package     EPL
 * @subpackage  Shotrcode/listing_search
 * @copyright   Copyright (c) 2014, Merv Barrett
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


// Only load on front
if( is_admin() ) {
    return;
}
/**
 * This shortcode allows for you to specify the property type(s) using
 * [listing_search title="" post_type="property" property_status="current/sold/leased" search_house_category="on/off" search_price="on/off" search_bed="on/off" search_bath="on/off" search_car="on/off" search_other="on/off"] option
 */
function epl_custom_shortcode_listing_search_callback( $atts ) {

    global $wpdb;
    $atts = shortcode_atts( epl_search_get_defaults(), $atts);
    extract($atts);
    $selected_post_types = $atts['post_type'];
    extract( $_GET );
    $queried_post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';

    if(!is_array($selected_post_types)){
        $selected_post_types = explode(",", $selected_post_types);
        $selected_post_types = array_map('trim', $selected_post_types);
    }

    global $epl_settings;
    ob_start();
    $tabcounter = 1;
    if(!empty($selected_post_types)):
        if(count($selected_post_types) > 1):
            echo "<ul class='epl-search-tabs property_search-tabs epl-search-$style'>";
            foreach($selected_post_types as $post_type):

                if( isset($_GET['action'] ) && $_GET['action'] == 'epl_search' ) {

                    if( $queried_post_type ==  $post_type ) {
                        $is_sb_current = 'epl-sb-current';
                    } else {
                        $is_sb_current = '';
                    }
                } else {
                    $is_sb_current = $tabcounter == 1 ? 'epl-sb-current' : '';
                }
                $post_type_label = isset($epl_settings['widget_label_'.$post_type])?$epl_settings['widget_label_'.$post_type]:$post_type;
                echo '<li data-tab="epl_ps_tab_'.$tabcounter.'" class="tab-link '.$is_sb_current.'">'.$post_type_label.'</li>';
                $tabcounter++;

            endforeach;
            echo '</ul>';
        endif;

        ?>
        <style>
            .custom_bgcolor{
                background-color: #fff !important;
            }
        </style>
        <div class="panel-group">
            <div class="panel panel-default bs-component">
                <div class="panel-heading custom_bgcolor">
                    <a data-toggle="collapse" href="#filters-collapse" class="btn btn-default"
                       style="margin-top: 0; margin-bottom: 0;">
                            <?php echo __('Filters', 'epl') ?></a>
                        <!-- <a data-toggle="collapse" href="#filters-collapse"><php echo __('Filters', 'epl') ?></a> -->
                </div>
        <?php
        $tabcounter = 1; // reset tab counter

        foreach($selected_post_types as $post_type):

            if( isset($_GET['action'] ) && $_GET['action'] == 'epl_search' ) {

                if( $queried_post_type ==  $post_type ) {
                    $is_sb_current = 'epl-sb-current';
                } else {
                    $is_sb_current = '';
                }
            } else {
                $is_sb_current = $tabcounter == 1 ? 'epl-sb-current' : '';
            }
            ?>
            </ul>
            <div id="filters-collapse" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    if( isset($show_title) && $show_title == 'true') {
                        if(!empty($title)) {
                            ?><h3><?php echo $title; ?></h3><?php
                        }
                    }
                    ?>
                    <form id="my_epl_form" class="form-horizontal" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
                        <fieldset>
                            <ul class="nav nav-tabs" style="color: white; margin-right: auto; margin-left: auto; margin-bottom: 10px">
                                <li><a id="daily_rental" data-toggle="tab" class="btn">Daily Rental</a></li>
                                <li><a id="term_rental" data-toggle="tab" class="btn">Term Rental</a></li>
                            </ul>
                            <input id="epl_action" type="hidden" name="epl_action" value="epl_search" />
                            <div class="form-group">
                                <label for="distance-scope" class="col-md-3 control-label">
                                    <?php echo __('Distance scope', 'epl') ?></label>
                                <div class="col-md-8">
                                    <select name="distance-scope" class="form-control">
                                        <option value="auto">Auto</option>
                                        <option value="1">1 km</option>
                                        <option value="5">5 km</option>
                                        <option value="10">10 km</option>
                                        <option value="30">30 km</option>
                                        <option value="50">50 km</option>
                                        <option value="100">100 km</option>
                                    </select>
                                </div>
                                <span class="material-input"></span>
                            </div>
                            <div class="form-group">
                                    <label for="Price_range" class="col-md-3 control-label">
                                        <?php echo __('Price', 'epl') ?></label>
                                <div class="col-md-8">
                                    <div id='slider_price' class="slider shor"></div>
                                </div>
                            </div>
                            <script>
                                var slider = document.getElementById('slider_price');
                                var minVal = 0;
                                var maxVal = 10000;
                                noUiSlider.create(slider, {
                                    start: [minVal, maxVal],
                                    connect: true,
                                    step: 10,
                                    format: wNumb({
                                        decimals: 0
                                    }),
                                    range: {
                                        'min': [minVal],
                                        '60%': [ 500, 10 ],
                                        '90%': [ 4000, 100 ],
                                        'max': [maxVal]
                                    },
                                    tooltips: true
                                });
                                slider.noUiSlider.on('update', function ( values, handle ){
                                    if(handle == 0) {
                                        $('#property_price_from').val(values[handle]);
                                    }else if(handle == 1){
                                        $('#property_price_to').val(values[handle]);
                                    }
                                });
                                $('.noUi-tooltip').css({'margin-top': '10px'});
                            </script>
                            <?php
                            $epl_frontend_fields = epl_search_widget_fields_frontend($post_type,$property_status);

                            foreach($epl_frontend_fields as $epl_frontend_field) {

                                if($epl_frontend_field['key'] == 'search_house_category' && isset($house_category_multiple) && $house_category_multiple == 'on') {

                                    $epl_frontend_field['multiple'] 	= true;
                                    $epl_frontend_field['query'] 		= array('query'	=> 'meta','compare' => 'IN' );

                                }

                                $config	=	isset(${$epl_frontend_field['key']}) ? ${$epl_frontend_field['key']} : '';
                                $value	=	isset(${$epl_frontend_field['meta_key']}) ? ${$epl_frontend_field['meta_key']} : '';
                                epl_custom_render_frontend_fields($epl_frontend_field,$config,$value,$post_type,$property_status);
                            }
                            ?>

                            <div class="form-group">
                                <div class="col-md-10 col-md-offset-2">
                                    <button id="epl_form_submit" type="submit" class="btn btn-primary"><?php echo $submit_label != ''
                                            ? $submit_label : __('Search', 'epl'); ?></button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
            <?php $tabcounter++; endforeach; endif; ?>
            </div>
        </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'listing_custom_search', 'epl_custom_shortcode_listing_search_callback' );
