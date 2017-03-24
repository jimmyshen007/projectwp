<?php
/**
 * The template for displaying search results pages.
 *
 * @package materialwp
 */

get_header(); ?>
<style>
	body {
		overflow: hidden;
	}
</style>

<script type="text/javascript">
	var markers = undefined;
	var gmarker = undefined;

	$(document).ready(function() {
		// Bind to StateChange Event
		History.Adapter.bind(window, 'statechange', function () { // Note: We are using statechange instead of popstate
			//var State = History.getState(); // Note: We are using History.getState() instead of event.state
		});

		var ZOOM = 14;
		L.mapbox.accessToken = MAPBOX_TOKEN;
		var map = L.mapbox.map('general-map-container', 'mapbox.streets', {
				minZoom: 2,
				maxZoom: 17,
				worldCopyJump: true
			})
			.addControl(L.mapbox.geocoderControl('mapbox.places', {
				autocomplete: true,
				keepOpen: true,
				pointZoom: ZOOM
			}));
		var oms = new OverlappingMarkerSpiderfier(map, {keepSpiderfied: true, nearbyDistance: 10});
		oms.addListener('spiderfy', function(markers) {
			map.closePopup();
		});
		var layers = {
			Streets: map.tileLayer,
			Satellite: L.mapbox.tileLayer('mapbox.streets-satellite'),
			Outdoors: L.mapbox.tileLayer('mapbox.outdoors')
		};
		L.control.layers(layers).addTo(map);

		// Window resize.
		$('#main_section').height($(window).height() - 180);
		$(window).on('resize', function () {
			$('#main_section').height($(window).height() - 175);
		});
		$('body').css("overflow", "hidden");

		$('#pagination-list a').live('click', function(e){
				e.preventDefault();
				var link = $(this).attr('href');
				var arr=link.split('?');
				var sortby 		= $('#epl-sort-listings').val();
				$('#main').html('Loading...');
				call_submit(arr[1] + '&sortby=' + sortby);
		});

		$('#epl-sort-listings').live('change', function(e){
				e.preventDefault();
				var sortby = $(this).val();
				//reset_gmarker();
				var epl_form = $('#my_epl_form').serialize() + '&action=load_post_ajax&sortby=' + sortby;
				call_submit(epl_form);
		});

		function setup_markers(epl_form){
			var jsonoutput = $.parseJSON($("#posts-jsonoutput").html());
			if(markers) {
				map.removeLayer(markers);
				oms.clearMarkers();
			}
			//$.each(data.data, function(index, value) { $('.data').append(value.a+'<br />'); } );
			/*markers = new L.MarkerClusterGroup({
				spiderfyOnMaxZoom: true,
				showCoverageOnHover: false,
				zoomToBoundsOnClick: true,
				maxClusterRadius: 20
			}); */
			markers = L.featureGroup();
			for (var i = 0; i < jsonoutput.length; i++) {
				var a = jsonoutput[i];
				var lat = parseFloat(a.coord_lat);
				var lng = parseFloat(a.coord_lng);
				if(gmarker) {
					var gmlatlng = gmarker.getLatLng();
					var distance = gmlatlng.distanceTo(new L.LatLng(lat, lng)) / 1000;
					$('div#info-box' + (i + 1)).append('<br><span>' + distance.toFixed(1) + ' km</span>');
				}

				if(i == 0){
					if(epl_form.indexOf('my_epl_bb_min_lat') > -1) {
						//map.setView([lat, lng]);
					}else{
						//map.setView([lat, lng], ZOOM);
						map.setZoom(ZOOM);
					}
				}
				var marker = L.marker(new L.LatLng(lat, lng), {
					icon: L.mapbox.marker.icon({'marker-symbol': (i + 1), 'marker-color': 'FF8000'}),
					title: a.title
				});

				var icon_id = i + 1;
				(function(inner_marker, inner_i) {
					//var ori_color = inner_marker.icon[marker-color];
					$('div#image-box' + inner_i).mouseenter(function(e) {
						inner_marker.setIcon(L.mapbox.marker.icon({
							'marker-symbol': inner_i,
							'marker-color': '#357645'
						}));
					});
					$('div#image-box' + inner_i).mouseleave(function(e) {
						var color_code = '#FF8000';
						if(inner_marker.clicked){
							color_code = '#808080';
						}
						inner_marker.setIcon(L.mapbox.marker.icon({
							'marker-symbol': inner_i,
							'marker-color': color_code
						}));
					});
				})(marker, icon_id);
				(function(inner_i) {
					marker.on('popupclose', function (e) {
						this.setIcon(L.mapbox.marker.icon({
							'marker-symbol': inner_i,
							'marker-color': '#808080'
						}));
						this.clicked = true;
					});
				})(icon_id);
				var pphtml = '';
				pphtml = '<div class="property-info" style="margin-top: 10px"><span class="property_address_suburb">' +
					a.address + '</span><br/>' +
					'<span class="page-price"><span class="price_class">';
				if (a.type == 'rental') {
					pphtml += '<span class="page-price-rent">' +
						'<span class="page-price" style="margin-right:0;"><b>' + a.rent +
						'</b></span><span class="rent-period">/ <b>' + a.period + '</b></span></span>';
				} else if (a.type == 'property') {
					pphtml += '<span class="page-price"><b>' + a.price + '</b></span>';
				}
				bed_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/bed-24.png" title="Bed" width="24" height="24">';
				bath_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/shower_and_tub-24.png" title="Bath" width="24">';
				parking_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/garage-24.png" title="Parking" width="24">';
				air_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Household/air_conditioner-24.png" title="Air Conditioner" width="24">';
				pool_icon = '<img src="https://maxcdn.icons8.com/Color/PNG/24/Sports/swimming-24.png" title="Pool" width="24">';

				pphtml += '</span></span>' +
					'<div class="epl-adv-popup-meta">' +
					'<span class="c-span">' + bed_icon + '</span><span class="c-span">' + a.bed + '</span>' +
					'<span class="c-span">' + bath_icon + '</span><span class="c-span">' + a.bath + '</span>' +
					'<span class="c-span">' + parking_icon + '</span><span class="c-span">' +a.parking + '</span>';
				if (a.air) {
					pphtml += '<span class="c-span">' + air_icon + '</span>';
				}
				if (a.pool) {
					pphtml += '<span class="c-span">' + pool_icon + '</span>';
				}

				pphtml += '</div></div><br/>';
				marker.bindPopup('<a href="' + a.link  + '"><h4 style="color: #ff7346">'
					+ a.title + '</h4>' +
					'<img src="' + a.image + '" /></a>' + pphtml);
				markers.addLayer(marker);
				oms.addMarker(marker);
			}
			map.addLayer(markers);
		}

		//Deal with page refresh case.
		var link = decodeURI(window.location.href);
		var arr=link.split('?');
		var queryJSON = queryStringToJSON(arr[1]);
		if(queryJSON.my_epl_input_lat && queryJSON.my_epl_input_lng){
				var lat = parseFloat(queryJSON.my_epl_input_lat);
				var lng = parseFloat(queryJSON.my_epl_input_lng);
				updateCenterFields([lat, lng]);
				map.setView([lat, lng], ZOOM);
		}else if(queryJSON.my_epl_bb_min_lat && queryJSON.my_epl_bb_max_lat
			&& queryJSON.my_epl_bb_min_lng && queryJSON.my_epl_bb_max_lng){
				var min_lat = parseFloat(queryJSON.my_epl_bb_min_lat);
				var max_lat = parseFloat(queryJSON.my_epl_bb_max_lat);
				var min_lng = parseFloat(queryJSON.my_epl_bb_min_lng);
				var max_lng = parseFloat(queryJSON.my_epl_bb_max_lng);
				updateBBoxFields([min_lat, max_lat, min_lng, max_lng]);
				map.fitBounds([[min_lat, min_lng], [max_lat, max_lng]]);
		}
		toggleRentalMode(queryJSON.property_rent_period);
		updateCommonFields(queryJSON);
		reset_gmarker();
		setup_markers(arr[1]);

		function toggleRentalMode(mode){
			if(mode == 'day'){
				$('input#search_end_date').prop('disabled', false);
				$($('input#search_end_date').parentsUntil('#my_epl_form')[1]).show();
				$($('select#property_min_stay').parentsUntil('#my_epl_form')[1]).hide();
				$('select#property_min_stay').prop('disabled', true);
				$('input#property_rent_period').val('day');
			}else{
				$('select#property_min_stay').prop('disabled', false);
				$($('select#property_min_stay').parentsUntil('#my_epl_form')[1]).show();
				$($('input#search_end_date').parentsUntil('#my_epl_form')[1]).hide();
				$('input#search_end_date').prop('disabled', true);
				$('input#property_rent_period').val('week|month');
			}
		}

		function updateCommonFields(queryJSON){
			$('a#daily_rental').on('click', function(){
				toggleRentalMode('day');
			});
			$('a#term_rental').on('click', function(){
				toggleRentalMode('week|month');
			});

			if(queryJSON.property_available_date) {
				$('input#search_start_date').val(queryJSON.property_available_date[0]);
				$('input#search_end_date').val(queryJSON.property_available_date[1]);
			}

			if(queryJSON.property_space_type) {
				var space_type = ['private_room', 'entire_house'];
				for(var i=0; i < space_type.length; i++) {
					if(queryJSON.property_space_type.indexOf(space_type[i]) != -1 ) {
						$('input#' + space_type[i]).prop('checked', true);
					}else{
						$('input#' + space_type[i]).prop('checked', false);
					}
				}
			}
		}

		function call_submit(epl_form){
			if(gmarker) {
				epl_form += '&mid_lat=' + gmarker.getLatLng().lat + '&mid_lng=' + gmarker.getLatLng().lng;
			}
			$.ajax({
				type: "GET",
				url: "/wordpress/wp-admin/admin-ajax.php",
				dataType: 'html',
				data: epl_form,
				success: function(data){
					$("#main").html(data);
					History.pushState({"state": Math.random()}, null, "?" + epl_form);
					//window.history.pushState({"html":$('html').html(), "pageTitle":document.title},"", "?" + epl_form);
					setup_markers(epl_form);
				}
			});
		}

		// Reset global marker.
		function reset_gmarker(){
			// Reset global search marker
			if(gmarker){
				map.removeLayer(gmarker);
			}
			try {
				var mcenter = map.getCenter();
				gmarker = L.marker(mcenter, {
					icon: L.mapbox.marker.icon({'marker-symbol': 'star', 'marker-color': 'FF0000'}),
					title: 'place'
				});
				map.addLayer(gmarker);
			}catch(err){
				//Do nothing.
			}
		}

		function updateCenterFields(latLngValues){
			// Remove bounding box coordinates input fields.
			var eplform = L.DomUtil.get('my_epl_form');
			var bbMinLat = L.DomUtil.get('my_epl_bb_min_lat');
			if(bbMinLat){
				$(bbMinLat).remove();
			}
			var bbMaxLat = L.DomUtil.get('my_epl_bb_max_lat');
			if(bbMaxLat){
				$(bbMaxLat).remove();
			}
			var bbMinLng = L.DomUtil.get('my_epl_bb_min_lng');
			if(bbMinLng){
				$(bbMinLng).remove();
			}
			var bbMaxLng = L.DomUtil.get('my_epl_bb_max_lng');
			if(bbMaxLng){
				$(bbMaxLng).remove();
			}

			// Add center coordinates input fields.
			var inputLat = L.DomUtil.get('my_epl_input_lat');
			if(!inputLat){
				inputLat = L.DomUtil.create('input', '', eplform);
				inputLat.type = 'hidden';
				inputLat.id = "my_epl_input_lat";
				inputLat.name = "my_epl_input_lat";
			}
			inputLat.value = latLngValues[0];
			var inputLng = L.DomUtil.get('my_epl_input_lng');
			if(!inputLng){
				inputLng = L.DomUtil.create('input', '', eplform);
				inputLng.type = 'hidden';
				inputLng.id = "my_epl_input_lng";
				inputLng.name = "my_epl_input_lng";
			}
			inputLng.value = latLngValues[1];
		}

		// Create or update Bounding box fields, and assign values.
		// @bboxValues: an array of values, must be 4 values;
		//
		function updateBBoxFields(bboxValues){
			var eplform = L.DomUtil.get('my_epl_form');
			// Remove center coodinates input fields.
			var inputLat = L.DomUtil.get('my_epl_input_lat');
			if(inputLat){
				$(inputLat).remove();
			}
			var inputLng = L.DomUtil.get('my_epl_input_lng');
			if(inputLng){
				$(inputLng).remove();
			}

			// Add bounding box coordinates fields.
			var bbMinLat = L.DomUtil.get('my_epl_bb_min_lat');
			if(!bbMinLat){
				bbMinLat = L.DomUtil.create('input', '', eplform);
				bbMinLat.type = "hidden";
				bbMinLat.id = "my_epl_bb_min_lat";
				bbMinLat.name = "my_epl_bb_min_lat";
			}
			bbMinLat.value = bboxValues[0];
			var bbMaxLat = L.DomUtil.get('my_epl_bb_max_lat');
			if(!bbMaxLat){
				bbMaxLat = L.DomUtil.create('input', '', eplform);
				bbMaxLat.type = "hidden";
				bbMaxLat.id = "my_epl_bb_max_lat";
				bbMaxLat.name = "my_epl_bb_max_lat";
			}
			bbMaxLat.value = bboxValues[1];

			var bbMinLng = L.DomUtil.get('my_epl_bb_min_lng');
			if(!bbMinLng){
				bbMinLng = L.DomUtil.create('input', '', eplform);
				bbMinLng.type = "hidden";
				bbMinLng.id = "my_epl_bb_min_lng";
				bbMinLng.name = "my_epl_bb_min_lng";
			}
			bbMinLng.value = bboxValues[2];
			var bbMaxLng = L.DomUtil.get('my_epl_bb_max_lng');
			if(!bbMaxLng){
				bbMaxLng = L.DomUtil.create('input', '', eplform);
				bbMaxLng.type = "hidden";
				bbMaxLng.id = "my_epl_bb_max_lng";
				bbMaxLng.name = "my_epl_bb_max_lng";
			}
			bbMaxLng.value = bboxValues[3];
		}

		// We need to distinguish whether the submit action is from filter or from mapbox.js.
		// filter has a submit button we can use. If the action is from filter, we don't need
		// to reset global marker.
		$("#epl_form_submit").on('click', function(e){
			e.preventDefault();
			var epl_form = $('#my_epl_form').serialize() + '&action=load_post_ajax';
			call_submit(epl_form);
		});

		$("#my_epl_form").submit(function(e) {
			e.preventDefault();
			reset_gmarker();
			var epl_form = $('#my_epl_form').serialize() + '&action=load_post_ajax';
			call_submit(epl_form);
		});

		// Search on current map button.
		$("#search-map-btn").on('click', function(e){
			e.preventDefault();
			// Get current map view bounding box coordinates.
			var bounds = map.getBounds();
			reset_gmarker();
			// Add bounding box coordinates fields.
			updateBBoxFields([bounds.getSouth(), bounds.getNorth(),
				bounds.getWest(), bounds.getEast()]);
			var epl_form_serial = $('#my_epl_form').serialize() + '&action=load_post_ajax';
			call_submit(epl_form_serial);
		});
	});
</script>

<div id="main_section" class="container" style="overflow: hidden !important; width: 100%; height:600px;
    min-height: 600px; margin-left: 0px; margin-right: 0px; max-width: none; padding-right: 5px" xmlns="http://www.w3.org/1999/html">
  	<!-- first column -->
  	<div id="search-result-wrapper" style="overflow-x: hidden; overflow-y: scroll; max-width: 100%; width: 60%; height: 100%; float: left">
		<!-- filter panel -->
		<div><?php echo do_shortcode('[listing_custom_search search_location="off" ' .
			'post_type="rental"]') ?></div>
		<!-- filter panel 2 -->
		<div></div>
		<!-- sort -->
		<div></div>
		<div style="height: 100%; width: 100%">
			<?php do_action( 'epl_property_loop_start' ); ?>
			<section id="primary">
				<main id="main" class="site-main" role="main">
				<?php CustomSearchPageGenerator::generate_post_list(); ?>
				</main><!-- #main -->
			</section><!-- #primary -->

			<!-- <php get_sidebar(); ?> -->
			<?php do_action( 'epl_property_loop_end' ); ?>
		</div> <!-- .row -->
	</div>
	<!-- second column for map  -->
	<div id="map-container-wrapper" style="overflow: hidden; width: 40%; height: 100%; float: left; padding-left: 5px; position: relative">
		<div id="general-map-container" class="panel panel-default" style="height: 100%"></div>
		<button id="search-map-btn" class="btn btn-raised btn-default"
			style="position: absolute; z-index: 10000; bottom: 5px; left: 80px;
		    background-color: rgba(1, 1, 1, 0.10)">
			<?php echo  __( 'Search current map', 'materialwp' ) ?>
		</button>
	</div>
</div> <!-- .container -->

<?php get_footer(); ?>
