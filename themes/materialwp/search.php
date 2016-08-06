<?php
/**
 * The template for displaying search results pages.
 *
 * @package materialwp
 */

get_header(); ?>
<script>
	(function(window,undefined) {

		// Bind to StateChange Event
		History.Adapter.bind(window, 'statechange', function () { // Note: We are using statechange instead of popstate
			var State = History.getState(); // Note: We are using History.getState() instead of event.state
		});
	})(window);
	$(document).ready(function(e) {
		$('#main_section').height($(window).height() - 180);
		$(window).on('resize', function () {
			$('#main_section').height($(window).height() - 175);
		});
	});
</script>
<script type="text/javascript">
	var markers = undefined;
	var gmarker = undefined;

	$(document).ready(function() {
		var ZOOM = 14;
		L.mapbox.accessToken = 'pk.eyJ1IjoianNvbnd1IiwiYSI6ImNpa3YwZnpzMzAwZTN1YWtzYWcwNXg2ZzMifQ.v6YZ9axqDwZSlzbjmMOfTg';
		var map = L.mapbox.map('general-map-container', 'mapbox.streets', {
				minZoom: 2,
				worldCopyJump: true
			})
			.addControl(L.mapbox.geocoderControl('mapbox.places', {
				autocomplete: true,
				keepOpen: true,
				pointZoom: ZOOM
			}));

		var layers = {
			Streets: map.tileLayer,
			Satellite: L.mapbox.tileLayer('mapbox.streets-satellite'),
			Outdoors: L.mapbox.tileLayer('mapbox.outdoors')
		};
		L.control.layers(layers).addTo(map);

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
				reset_gmarker();
				var epl_form = $('#my_epl_form').serialize() + '&action=load_post_ajax&sortby=' + sortby;
				call_submit(epl_form);
		});

		function setup_markers(epl_form){
			var jsonoutput = $.parseJSON($("#posts-jsonoutput").html());
			if(markers) {
				map.removeLayer(markers);
			}
			//$.each(data.data, function(index, value) { $('.data').append(value.a+'<br />'); } );
			markers = new L.MarkerClusterGroup();
			for (var i = 0; i < jsonoutput.length; i++) {
				var a = jsonoutput[i];
				var lat = parseFloat(a.coord_lat);
				var lng = parseFloat(a.coord_lng);
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
				(function(inner_i) {
					marker.on('popupclose', function (e) {
						this.setIcon(L.mapbox.marker.icon({
							'marker-symbol': inner_i,
							'marker-color': '#808080'
						}));
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
			}
			map.addLayer(markers);
		}

		// Function to parse queryString into JSON object.
		var queryStringToJSON = function (url) {
			if (url === '')
				return '';
			var pairs = (url || location.search).slice(1).split('&');
			var result = {};
			for (var idx in pairs) {
				var pair = pairs[idx].split('=');
				if (!!pair[0])
					result[pair[0].toLowerCase()] = decodeURIComponent(pair[1] || '');
			}
			return result;
		}

		//Deal with page refresh case.
		var link = window.location.href;
		var arr=link.split('?');
		var queryJSON = queryStringToJSON(arr[1]);
		if(queryJSON.my_epl_input_lat && queryJSON.my_epl_input_lng){
				var lat = parseFloat(queryJSON.my_epl_input_lat);
				var lng = parseFloat(queryJSON.my_epl_input_lng);
				map.setView([lat, lng], ZOOM);
		}else if(queryJSON.my_epl_bb_min_lat && queryJSON.my_epl_bb_max_lat
			&& queryJSON.my_epl_bb_min_lng && queryJSON.my_epl_bb_max_lng){
				var min_lat = parseFloat(queryJSON.my_epl_bb_min_lat);
				var max_lat = parseFloat(queryJSON.my_epl_bb_max_lat);
				var min_lng = parseFloat(queryJSON.my_epl_bb_min_lng);
				var max_lng = parseFloat(queryJSON.my_epl_bb_max_lng);
				map.fitBounds([[min_lat, min_lng], [max_lat, max_lng]]);
		}
		reset_gmarker();
		setup_markers(arr[1]);

		function call_submit(epl_form){
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
			var eplform = L.DomUtil.get('my_epl_form');
			reset_gmarker();
			// Add bounding box coordinates fields.
			var bbMinLat = L.DomUtil.get('my_epl_bb_min_lat');
			if(!bbMinLat){
				bbMinLat = L.DomUtil.create('input', '', eplform);
				bbMinLat.id = "my_epl_bb_min_lat";
				bbMinLat.name = "my_epl_bb_min_lat";
			}
			bbMinLat.value = bounds.getSouth()
			var bbMaxLat = L.DomUtil.get('my_epl_bb_max_lat');
			if(!bbMaxLat){
				bbMaxLat = L.DomUtil.create('input', '', eplform);
				bbMaxLat.id = "my_epl_bb_max_lat";
				bbMaxLat.name = "my_epl_bb_max_lat";
			}
			bbMaxLat.value = bounds.getNorth();

			var bbMinLng = L.DomUtil.get('my_epl_bb_min_lng');
			if(!bbMinLng){
				bbMinLng = L.DomUtil.create('input', '', eplform);
				bbMinLng.id = "my_epl_bb_min_lng";
				bbMinLng.name = "my_epl_bb_min_lng";
			}
			bbMinLng.value = bounds.getWest();
			var bbMaxLng = L.DomUtil.get('my_epl_bb_max_lng');
			if(!bbMaxLng){
				bbMaxLng = L.DomUtil.create('input', '', eplform);
				bbMaxLng.id = "my_epl_bb_max_lng";
				bbMaxLng.name = "my_epl_bb_max_lng";
			}
			bbMaxLng.value = bounds.getEast();
			var epl_form_serial = $('#my_epl_form').serialize() + '&action=load_post_ajax';
			call_submit(epl_form_serial);
		});
	});
</script>

<div id="main_section" class="container" style="overflow: hidden; width: 100%; height:600px; min-height: 600px" xmlns="http://www.w3.org/1999/html">
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
	<div id="map-container-wrapper" style="width: 40%; height: 100%; float: left; padding-left: 5px; position: relative">
		<div id="general-map-container" class="panel panel-default" style="height: 100%"></div>
		<button id="search-map-btn" class="btn btn-raised btn-default"
			style="position: absolute; z-index: 10000; bottom: 5px; left: 80px;
		    background-color: rgba(1, 1, 1, 0.10)">
			<?php echo  __( 'Search current map', 'materialwp' ) ?>
		</button>
	</div>
	<script>
		//Google map js
		jQuery(document).ready(function($) {
			// Code that uses jQuery's $ can follow here.
			$(document).ready(function(e){
				L.mapbox.accessToken = 'pk.eyJ1IjoianNvbnd1IiwiYSI6ImNpa3YwZnpzMzAwZTN1YWtzYWcwNXg2ZzMifQ.v6YZ9axqDwZSlzbjmMOfTg';
				L.mapbox.map('general-map-container', 'mapbox.streets')
					.addControl(L.mapbox.geocoderControl('mapbox.places', {
						autocomplete: true,
						keepOpen: true
					}));
			});
		});
	</script>
</div> <!-- .container -->

<?php get_footer(); ?>
