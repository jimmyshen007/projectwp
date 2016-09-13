(function($) {
	$.fn.extend({
		addFeaturedMarker: function(i, the_address, lat, lng, marker, linkUrl, thumb, pTitle, pContent, current, cluster) {
			var myGmap = this;
			if(lat == '' && lng == '') {
				var geocoder = new google.maps.Geocoder();
				var latLng = geocoder.geocode({
					address: the_address
				}, function(results, status) {
					//console.log(status);
					if(status == google.maps.GeocoderStatus.OK) {
						lat = results[0].geometry.location.lat();
						lng = results[0].geometry.location.lng();
						myGmap.addFeaturedMarkerAction(i, lat, lng, marker, linkUrl, thumb, pTitle, pContent, current);
					} else {
						console.log('Oops! Could not find the address for the property '+the_address);
					}
				});
			} else {
				myGmap.addFeaturedMarkerAction(i, lat, lng, marker, linkUrl, thumb, pTitle, pContent, current);
			}
		},
		
		addFeaturedMarkerAction: function(i, lat, lng, marker, linkUrl, thumb, pTitle, pContent, current) {
			var attr_display = $('input[name="slider[display]"]').val();
			if(attr_display == 'simple') {
				var overlayContent = '<div class="overlay-featured-marker overlay-featured-marker-'+attr_display+' overlay-ajax-image" id="marker_overlay_featured_'+i+'"><span class="thumb">'+thumb+'</span><span class="arrow"></span><span class="latlng">'+lat+','+lng+'</span><span class="title" style="display: none">'+pTitle+'</span><span class="link" style="display: none">'+linkUrl+'</span><div class="content" style="display: none">'+pContent+'</div><div class="overlay-close" onclick="javascript:return myGmap.closeInfoWindow(event);"></div></div>';
			} else {
				var overlayContent = '<div class="overlay-featured-marker overlay-featured-marker-'+attr_display+'" id="marker_overlay_featured_'+i+'"><img class="property-thumb" src="'+thumb+'" /><span class="arrow"></span><span class="latlng">'+lat+','+lng+'</span><span class="title" style="display: none">'+pTitle+'</span><span class="link" style="display: none">'+linkUrl+'</span><div class="content" style="display: none">'+pContent+'</div><div class="overlay-close" onclick="javascript:return myGmap.closeInfoWindow(event);"></div></div>';
			}
			overlayContent = epl.hooks.applyFilters('epl_am_infobox_content',overlayContent);
			myGmap.gmap3({
				marker: {
					latLng: [lat, lng],
					id: i,
					options: {
						icon: marker
					},
					events: {
						click: function(marker, event, context) {
							/*//// IF ITS MOBILE
							if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
								//// MAKES SURE WE DONT REDIRECT THE USER STRAIGHT AWAY
								if(jQuery('#marker_overlay_featured_'+context.id).css('opacity') < 1) {
									
								} else {
									window.location = linkUrl;
								}
							} else {
								//// REDIRECTS USER
								window.location = linkUrl;
							}*/
							
							//$(this).gmap3("get").setCenter(marker.getPosition());							
							$(this).gmap3('get').panTo(marker.getPosition());
							
							if( jQuery('#marker_overlay_featured_'+context.id).hasClass('overlay-ajax-image') ) {
								if( jQuery('#marker_overlay_featured_'+context.id).find('.thumb').length ) {
									var overlayThumb = jQuery('#marker_overlay_featured_'+context.id).find('.thumb').html();
									jQuery('#marker_overlay_featured_'+context.id).find('.thumb').remove();
									jQuery('#marker_overlay_featured_'+context.id).prepend('<img class="property-thumb" src="'+overlayThumb+'" />');
								}
							}
							
							if(jQuery('#marker_overlay_featured_'+context.id).attr('class').indexOf('current') == -1) {
								jQuery('.overlay-featured-marker-current').animate({ opacity: .65, left: '15px', top: '30px', width: '42px', height: '42px' }, { duration: 200, easing: 'easeOutBack', complete: function() { jQuery(this).removeClass('overlay-featured-marker-current'); } });
								jQuery('.overlay-featured-marker-current .arrow').animate({ left: '18px' }, { duration: 200, easing: 'easeOutBack' });
								jQuery('#marker_overlay_featured_'+i).animate({ opacity: 1, left: '0px', top: '0px', width: '72px', height: '72px' }, { duration: 200, easing: 'easeOutBack', complete: function() { jQuery(this).addClass('overlay-featured-marker-current'); } }).removeClass('infowindow');
								jQuery('#marker_overlay_featured_'+i+' .arrow').animate({ left: '33px' }, { duration: 200, easing: 'easeOutBack' });
							}
								
							if(jQuery('.slider-map-featured ul').length > 0) { //For Slider
								jQuery('.slider-map-featured ul').playProperty(context.id);
							} else if( $('#bpopup-gmap3').length ) { //Else For Popup
								$('#bpopup-gmap3').bPopup({
									easing: 'easeOutBack',
									speed: 450,
									transition: 'slideDown',
									modalClose: true,
									contentContainer:'.bpopup-inner',
									loadUrl: ajaxurl + '?action=gmap3_load_popup&id='+context.id
								});
							} else {
								jQuery('.overlay-featured-marker').removeClass('infowindow');
								jQuery('#marker_overlay_featured_'+i).addClass('overlay-featured-marker-current');
								jQuery('#marker_overlay_featured_'+i).addClass('infowindow');
							}
						},
						mouseover: function(marker, event, context) {
							if(jQuery('#marker_overlay_featured_'+context.id).attr('class').indexOf('current') == -1) {
								jQuery('.overlay-featured-marker-current').animate({ opacity: .65, left: '15px', top: '30px', width: '42px', height: '42px' }, { duration: 200, easing: 'easeOutBack', complete: function() { jQuery(this).removeClass('overlay-featured-marker-current'); } });
								jQuery('.overlay-featured-marker-current .arrow').animate({ left: '18px' }, { duration: 200, easing: 'easeOutBack' });
								jQuery('#marker_overlay_featured_'+i).animate({ opacity: 1, left: '0px', top: '0px', width: '72px', height: '72px' }, { duration: 200, easing: 'easeOutBack', complete: function() { jQuery(this).addClass('overlay-featured-marker-current'); } }).removeClass('infowindow');
								jQuery('#marker_overlay_featured_'+i+' .arrow').animate({ left: '33px' }, { duration: 200, easing: 'easeOutBack' });
								
								if(jQuery('.slider-map-featured ul').length > 0) { jQuery('.slider-map-featured ul').playProperty(context.id); }
								
								jQuery('.overlay-featured-marker').removeClass('infowindow');
							}
						},					
					},
					callback: function(marker) {						
						thisMarkerId = marker.__gm_id;						
					}					
				},
								
				//// ADDS OUR OVERLAY
				overlay: {
					
					latLng: [lat, lng],
					options: {
						
						//// OUR HTML AND OFFSET
						content: overlayContent,
						offset: { y: -118, x: -42 }	
						
					},
					
					events: {
						
						click: function(marker, event, context) {
						
							//// IF ITS MOBILE
							if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {
								
								//// MAKES SURE WE DONT REDIRECT THE USER STRAIGHT AWAY
								if(jQuery('#marker_overlay_featured_'+i).css('opacity') < 1) {
									
									
									
								} else {
								
									window.location = linkUrl;
									
								}
								
							} else {
							
								//// REDIRECTS USER
								
								window.location = linkUrl;
							
							}
							
						}
						
					},
				
					//// CALLBACK
					callback: function(results) {
						
						var marker = jQuery(results.getDOMElement()).children('div');
						marker.addClass('overlay-featured-marker-id-'+thisMarkerId);
						
						setTimeout(function() {
							
							var marker = jQuery(results.getDOMElement()).children('div');
							var finalOpacity = .65;
							
							if(typeof theCluster == 'undefined') {
							
								if(current == true) { 
									finalOpacity = 1; marker.addClass('overlay-featured-marker-current');
									marker.css({ opacity: .1, display: 'block', top: '-50px' }).animate({ top: '0', opacity: finalOpacity }, { duration: 600, easing: 'easeOutBounce' });
								}
								else {
									marker.css({ opacity: .1, display: 'block', top: '-80px' }).animate({ top: '30px', opacity: finalOpacity }, { duration: 600, easing: 'easeOutBounce' });
								}
								
							}
							
							//// IF ITS CURRENT
							//// WHEN WE MOUSE OVER OUR OVERLAY
							jQuery('#marker_overlay_featured_'+i).hover(function() {
								
								//// IF IT'S NOT ALREADY CURRENT
								if(jQuery('#marker_overlay_featured_'+i).attr('class').indexOf('current') == -1) {
							
									//// LET'S FADE OUR CURRENT OUT AND THE HOVERED IN
									jQuery('.overlay-featured-marker-current').animate({ opacity: .65, left: '15px', top: '30px', width: '42px', height: '42px' }, { duration: 200, easing: 'easeInOutBack', complete: function() { jQuery(this).removeClass('overlay-featured-marker-current'); } });
									jQuery('.overlay-featured-marker-current .arrow').animate({ left: '18px' }, { duration: 200, easing: 'easeInOutBack' });
									
									jQuery('#marker_overlay_featured_'+i).animate({ opacity: 1, left: '0px', top: '0px', width: '72px', height: '72px' }, { duration: 200, easing: 'easeInOutBack', complete: function() { jQuery(this).addClass('overlay-featured-marker-current'); } }).removeClass('infowindow');
									jQuery('#marker_overlay_featured_'+i+' .arrow').animate({ left: '33px' }, { duration: 200, easing: 'easeInOutBack' });
									
									//// PLAYS THE SLIDERS - IF WE HAVE OUR FEATURED SLIDER ENABLED
									if(jQuery('.slider-map-featured ul').length > 0) { jQuery('.slider-map-featured ul').playProperty(i); }
									
								}
								
							}, function() {

							});
						}, 700);
					}
				}
				
			});
			
			//// ADDS AN EXTRA OVERLAY SO WHEN WE PAN OUR MAP IT SHOWS THE TARGET
			myGmap.gmap3({
				overlay: {
					latLng: [lat, lng],
					options: {
						content : '<div class="overlay-target" id="marker_overlay_target_'+i+'"></div>',
						offset: { y: -41, x: -28 }
					}
				}
			});
		},
		
		gmapZoomIn: function(mapObj) {
			var zoomCont = this;
			zoomCont.click(function() {
				var zoomLevel = jQuery(mapObj).gmap3('get').getZoom();
				zoomLevel++;
				if(zoomLevel>20) { zoomLevel = 20; }
				mapObj.gmap3({ map:{
					options: {
						zoom: zoomLevel
					}
				}});
			});
		},
		
		gmapZoomOut: function(mapObj) {
			var zoomCont = this;
			zoomCont.click(function() {
				var zoomLevel = jQuery(mapObj).gmap3('get').getZoom();
				zoomLevel--;
				if(zoomLevel<=2) { zoomLevel = 2; }
				mapObj.gmap3({ map:{
					options: {
						zoom: zoomLevel
					}
				}});
			});
		},
		
		sliderMapFeaturedInit: function() {			
			var mainCont = this;
			var ulCont = this.children('ul');
			var currentCont = ulCont.children('li:first').addClass('current');
			var mapIndex = currentCont.attr('id').split('_');
			var mapIndex = mapIndex[2];
			var prevSlider = jQuery('.slider-map-featured-left');
			var nextSlider = jQuery('.slider-map-featured-right');
			
			ulCont.centerSliderMapFeatured(0, function() {				
				ulCont.children('li').fadeIn(300);
			});
			
			jQuery(window).resize(function() {
				var currentItemIndex = ulCont.children('li.current').index();
				ulCont.centerSliderMapFeatured(currentItemIndex);
			});
			
			nextSlider.click(function() {
				var nextItem = ulCont.children('li.current').next().next().attr('id').split('_');
				ulCont.playProperty(nextItem[2]);
				if(myGmap != undefined) { myGmap.shoeFeaturedOverlay(nextItem[2]); }
			});
			
			prevSlider.click(function() {
				var nextItem = ulCont.children('li.current').prev().prev().attr('id').split('_');
				ulCont.playProperty(nextItem[2]);
				if(myGmap != undefined) { myGmap.shoeFeaturedOverlay(nextItem[2]);  }
			});
			
			//// IF MOBILE
			if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent)) {				
				mainCont.css({ overflow: 'auto' });				
			} else {				
				//// WHEN USER HOVERS OUR FEATURED SLIDER WE DISPLAY OUR ARROWS
				prevSlider.css({ display: 'block', opacity: 0 });
				nextSlider.css({ display: 'block', opacity: 0 });				
				mainCont.hover(function() {					
					prevSlider.stop().animate({ opacity: .6 }, 200);
					nextSlider.stop().animate({ opacity: .6 }, 200);					
				},function() {					
					prevSlider.stop().animate({ opacity: 0 }, 400);
					nextSlider.stop().animate({ opacity: 0 }, 400);					
				});
				
				prevSlider.hover(function() { jQuery(this).stop().animate({ opacity: 1 }, 200); }, function() { jQuery(this).stop().animate({ opacity: .7 }, 200); });
				nextSlider.hover(function() { jQuery(this).stop().animate({ opacity: 1 }, 200); }, function() { jQuery(this).stop().animate({ opacity: .7 }, 200); });				
			}
			
			ulCont.children('li').click(function() {
				if(jQuery(this).attr('class').indexOf('current') == -1) {
					var nextItem = jQuery(this).attr('id').split('_');
					ulCont.playProperty(nextItem[2]);
					if(myGmap != undefined) { myGmap.shoeFeaturedOverlay(nextItem[2]); }
				} else {
					var url = jQuery(this).find('.property-info a').attr('href');
					window.location = url;
				}
			});
		},
		
		centerSliderMapFeatured: function(centerIndex, callBack) {			
			//// VARS
			var mainCont = this;
			var currentCont = mainCont.children('li:eq('+centerIndex+')');
			var windowWidth = jQuery(window).width();
			var liWidth = mainCont.children('li').outerWidth();
			var currentLeft = parseInt(mainCont.css('left'));
			var qtyBeforeLis = mainCont.children('li:lt('+centerIndex+')').length;
			
			//// CALCULATES THE LEFT BASED ON OUR WINDOW WIDTH AND NUMBER OF LIs
			var wrapperWidth = jQuery('.wrapper').width();
			var leftSpace = (windowWidth - wrapperWidth) / 2;
			var newLeft = (qtyBeforeLis*liWidth)-leftSpace;
			if(qtyBeforeLis === 0) { newLeft = leftSpace; mainCont.css({ left: newLeft+'px' }); }
			else { mainCont.css({ left: '-'+newLeft+'px' }); }
			
			//// LET'S CALCULATE HOW MANY ITEMS WE NEED TO PUT IN FRON OF IT TO FILL PAGE
			//// THEN WE APPEND THE LAS ITEMS TO THE FRONT
			var countLeft = Math.ceil(leftSpace / liWidth);
			
			//// IF IT'S THE FIRST TIME LET'S ADD AN EXTRA ONE
			if(qtyBeforeLis === 0) { countLeft++; }
			
			//// MAKES SURE WE ALSO DONT ALREADY HAVE THIS LI'S BEFORE
			if(countLeft > centerIndex) {
				for(var i = 1; i<= countLeft; i++) {
					
					//// LETS PREPEND IT
					mainCont.children('li:last').prependTo(mainCont);
					var currentLeft = parseInt(mainCont.css('left'));
					
					//// FIXES LEFT
					var newLeft = currentLeft - liWidth;
					mainCont.css({ left: newLeft+'px' });
					
				}
			}
			
			//// CALLBACK
			if(typeof callBack == 'function') { callBack.call(mainCont); }			
		},
		
		playProperty: function(mapIndex) {			
			//// vars
			var mainCont = this;
			var currentLi = mainCont.children('li.current');
			var nextLi = mainCont.children('#marker_featured_'+mapIndex);
			var realIndex = nextLi.index();
			var windowWidth = jQuery(window).width();
			var liWidth = mainCont.children('li').outerWidth();
			var currentLeft = parseInt(mainCont.css('left'));
			var indexDifference = realIndex-currentLi.index();
			
			//// LETS FIND OUT IF WE'rE PLAYING BACK OR FORWARD
			//// PLAYING FORWARD
			if(realIndex > currentLi.index()) {
				
				//// LET'S FIND OUT THE AMOUNT OF ITEMS WE NEED TO CLONE
				var wrapperWidth = jQuery('.wrapper').width();
				var availableSpace = (windowWidth - wrapperWidth) / 2;
				var countRight = Math.ceil(availableSpace / liWidth);
				var lengthAfer = mainCont.children('li:gt('+realIndex+')').length;
				if(lengthAfer === 0) { countRight++; }
				
				//// IF WE DONT HAVE ENOUGH LIS AFTER
				if(countRight >= lengthAfer) {
					
					//// LET'S CLONE STUFF
					for(var i = 1; i<= countRight; i++) {
					
						//// LETS PREPEND IT
						mainCont.children('li:first').appendTo(mainCont);
						var currentLeft = parseInt(mainCont.css('left'));
						
						//// FIXES LEFT
						var newLeft = currentLeft + liWidth;
						mainCont.css({ left: newLeft+'px' });
						
					}
					
				}
				
				//// NOW WE CALCULATE THE NEW LEFT BASED ON THE INDEX
				var currentLeft = parseInt(mainCont.css('left'));
				var realIndexNew = nextLi.index();
				var newLeft = availableSpace-(realIndexNew*liWidth);
				
				//// ANIMATES STUFF
				currentLi.removeClass('current').children('.property-info').slideUp();
				nextLi.addClass('current').children('.property-info').hide();
				mainCont.stop().animate({ left: newLeft+'px' }, { duration: 300, ease: 'easeInOutQuint', complete: function() {
					
					nextLi.children('.property-info').slideDown({ duration: 100, ease: 'easeInOutQuint' });
					
				}});				
			}
			//// PLAYING BACK
			else {				
				//// LET'S FIND OUT THE AMOUNT OF ITEMS WE NEED TO CLONE
				var availableSpace = (windowWidth - 1000) / 2;
				var countLeft = Math.ceil(availableSpace / liWidth);
				var lengthBefore = mainCont.children('li:lt('+realIndex+')').length;
				if(lengthBefore === 0) { countLeft++; }
				
				//// IF WE DONT HAVE ENOUGH LIS AFTER
				if(countLeft >= lengthBefore) {
					
					//// LET'S CLONE STUFF
					for(var i = 1; i<= countLeft; i++) {
					
						//// LETS PREPEND IT
						mainCont.children('li:last').prependTo(mainCont);
						var currentLeft = parseInt(mainCont.css('left'));
						
						//// FIXES LEFT
						var newLeft = currentLeft - liWidth;
						mainCont.css({ left: newLeft+'px' });
						
					}
					
				}
				
				//// NOW WE CALCULATE THE NEW LEFT BASED ON THE INDEX
				var currentLeft = parseInt(mainCont.css('left'));
				var realIndexNew = nextLi.index();
				var newLeft = availableSpace-(realIndexNew*liWidth);
				
				//// ANIMATES STUFF
				currentLi.removeClass('current').children('.property-info').slideUp();
				nextLi.addClass('current').children('.property-info').hide();
				mainCont.stop().animate({ left: newLeft+'px' }, { duration: 300, ease: 'easeInOutQuint', complete: function() {
					
					nextLi.children('.property-info').slideDown({ duration: 100, ease: 'easeInOutQuint' });
					
				}});				
			}			
		},
	
		shoeFeaturedOverlay: function(index) {
			//// VARS
			var myGmap = this;
			var thisOverlay = jQuery('#marker_overlay_featured_'+index);
			var latlng = thisOverlay.children('span.latlng').text().split(',');
			var thisTarget = jQuery('#marker_overlay_target_'+index);

			var theLat = latlng[0];
			var theLng = latlng[1];
			//// PANS OUR MAP TO OUR NEW LOCATION
			var myLatLng = new google.maps.LatLng(theLat,theLng);
			jQuery(myGmap).gmap3('get').panTo(myLatLng);
			
			//// ANIMATES OUR OVERLAY
			jQuery('.overlay-featured-marker-current').animate({ opacity: .65, left: '15px', top: '30px', width: '42px', height: '42px' }, { duration: 200, easing: 'easeInOutBack', complete: function() { jQuery(this).removeClass('overlay-featured-marker-current'); thisTarget.fadeIn(600, function() { thisTarget.delay(200).fadeOut(600); }); } });
			jQuery('.overlay-featured-marker-current .arrow').animate({ left: '18px' }, { duration: 200, easing: 'easeInOutBack' });
			
			jQuery('#marker_overlay_featured_'+index).animate({ opacity: 1, left: '0px', top: '0px', width: '72px', height: '72px' }, { duration: 200, easing: 'easeInOutBack', complete: function() { jQuery(this).addClass('overlay-featured-marker-current'); } }).removeClass('infowindow');
			jQuery('#marker_overlay_featured_'+index+' .arrow').animate({ left: '33px' }, { duration: 200, easing: 'easeInOutBack' });			
		},
		
		replaceSelect: function() {			
			//// vars
			var selCont = this;
			
			//// WRAPS IT AROUND SELECT
			selCont.wrap('<div class="select-replace"></div>');
			var mainCont = selCont.parent();
			var selectedItem = selCont.children('option:selected');
			mainCont.append('<span>'+selectedItem.text()+'</span>');
			var mainContHeight = mainCont.height();
			
			//// MAKES IT OVERLAY THE CONTAINER
			selCont.css({ display: 'block', opacity: 0 });
			
			//// WHEN WE CHANGE SELECT
			selCont.change(function() {
				
				//// NEW SELECTED ITEM
				var selectedItem = selCont.children('option:selected');
				mainCont.children('span').text(selectedItem.text());
				
			});			
		},
		
		showSearchSection: function() {			
			//// GETS VALUE OF SELECTED ITEM
			var selCont = this;
			var newSelVal = jQuery(this).children('option:selected').val();
			var newItemToShow = jQuery('#property-search-'+newSelVal);		
			
			//// IF SELECTED VALUE IS HIDDEN
			if(newItemToShow.is(':visible')) {  } else { selCont.parent().parent().siblings('div').slideUp(200, function() {
				
				//// SHOWS THE OTHER
				newItemToShow.slideDown(200);
				
			}); }			
		},
		
		rbChangeView: function() {			
			//// MAIN VARS
			var aCont = this;
			var mainCont = jQuery('#properties');
			
			aCont.click(function(e) {
				
				//// MAKES SURE WE ARE NOT IN THE SELECTED VIEW
				if(jQuery(this).parent().attr('class').indexOf('current') == -1 && jQuery(this).parent().attr('class').indexOf('map') == -1) {
				
					var clickedView = jQuery(this).parent().attr('class');
					
					//// CHANGES VIEW
					mainCont.stop().animate({ opacity: 0 }, 200, function() {
						
						jQuery(this).attr('class', clickedView).stop().animate({ opacity: 1 }, 200);
						
					});
					
					//// SETS COOKIE
					jQuery.cookie('property_view', clickedView, { expires: 30 });
					
					jQuery(this).parent().siblings('.current').removeClass('current');
					jQuery(this).parent().addClass('current');
				
				}
				
				//// IF ITS NOT MAP
				if(jQuery(this).parent().attr('class').indexOf('map') == -1) {				
					//// PREVENTS A CLICK
					e.preventDefault();
					return false;					
				} else {					
					jQuery.cookie('property_view', 'map', { expires: 30 });
					return true;					
				}				
			})			
		},
		
		propertyGallery: function() {			
			//// VARS
			var thumbCont = this;
			var mainCont = jQuery('#property-gallery a.image');
			
			//// WHEN THE USER CLICKS A HTUMB
			thumbCont.children('li').click(function() {
				
				//// IF NOT CURRENT
				if(jQuery(this).attr('class').indexOf('current') == -1) {
					
					var liCont = jQuery(this);
					
					//// FADES OUT MAIN CONTAINER
					mainCont.addClass('loading').children('img').fadeOut(200, function() { jQuery(this).remove(); });
					
					//// CHANGES THE CURRENT
					thumbCont.find('.current').removeClass('current');
					liCont.addClass('current');
					
					//// LOADS OUR NEW IMAGE
					var mainImage = liCont.children('.main').text();
					var fullImage = liCont.children('.full').text();
					
					var imgObj = new Image();
					jQuery(imgObj).attr('src', mainImage).load(function() {						
						//// REPLACES IT IN THE MAIN CONTAINER
						mainCont.append(this);
						mainCont.children('img:last').addClass('next').fadeIn(300, function() { jQuery(this).removeClass('next'); mainCont.removeClass('loading') });						
						//// UPDATES LINK
						mainCont.attr('href', fullImage);						
					});					
				}				
			});			
		},
		
		clusterMarkers: function() {			
			var gMap = this;
						
			//// GETS CLUSTERS
			var clusters = clusterManager.getClusters();
			
			//// REMOVES CLUSTER OVERLAYS FOR STACKED PROPERTIES
			google.maps.event.addListener(jQuery(gMap).gmap3('get'), 'bounds_changed', function() { jQuery('.overlay-markup-cluster').fadeOut(200, function() { jQuery(this).remove(); }); });
			
			//// WHEN THE USER CLICKS OUR CLUSTERER
			google.maps.event.addListener(clusterManager, 'clusterclick', function(cluster) {
				
				//// WHEN WE CLICK WE ZOOM IN ONE LEVEL
				var zoomLevel = jQuery('.slider-map').gmap3('get').getZoom();
				
				//// IF ZOOM LEVEL IS !& OR LESS
				if(zoomLevel <= 15) {
				
					zoomLevel++;
					if(zoomLevel>20) { zoomLevel = 20; }
					
					//// GETS MARKER CENTER
					var centerMap = cluster.getCenter();
				
					//// SETS NEW ZOOM
					jQuery('.slider-map').gmap3({ map:{						
						options: {							
							zoom: zoomLevel,
							center: centerMap							
						}						
					}});
				} else {					
					var overlayMarkup = '<div class="overlay-markup-cluster"><span class="arrow"></span><ul>';
					
					//// LET'S GET OUR PINS UNDER THIS AND GET THEIR TITLES AND LINKS
					var allMarkers = cluster.getMarkers();
					
					//// LOOPS MARKERS
					jQuery.each(allMarkers, function(i, marker) {
						
						//// LET'S CHECK IF IT'S A FEATURED OVERLAY
						var featuredOverlay = jQuery('.overlay-featured-marker-id-'+marker.__gm_id);
						if(featuredOverlay.length > 0) {
							
							//// WE HAVE A FEATURED OVERLAY - LETS GET TITLE AND LINK
							var thisTitle = featuredOverlay.find('.title').text();
							var thisLink = featuredOverlay.find('.link').text();
							
							//// LET'S ADD THIS TO OUR OVERLAY MARKUP
							overlayMarkup += '<li><a href="'+thisLink+'">'+thisTitle+'</a></li>';
						} else {							
							var simpleOverlay = jQuery('.overlay-simple-marker-id-'+marker.__gm_id);
							
							//// WE HAVE A FEATURED OVERLAY - LETS GET TITLE AND LINK
							var thisTitle = simpleOverlay.find('.title').text();
							var thisLink = simpleOverlay.find('.link').text();
							
							//// LET'S ADD THIS TO OUR OVERLAY MARKUP
							overlayMarkup += '<li><a href="'+thisLink+'">'+thisTitle+'</a></li>';
						}						
					});
					
					///// CLOSES OUR MARKUP
					overlayMarkup + '</ul></div>';
					
					var latLng = cluster.getCenter();
					var theLat = latLng.jb;
					var theLng = latLng.kb;
					
					//// LET'S ADD OUR OVERLAY TO THE MAP
					jQuery(gMap).gmap3({						
						overlay: {							
							latLng: [theLat, theLng],
							options: {								
								content: overlayMarkup,
								offset: {									
									x: 150,
									y: -17									
								}								
							},
							callback: function(results) {								
								var overlay = jQuery(results.getDOMElement()).children('div');
								overlay.css({ display: 'block', opacity: 0, left: '-100px' }).animate({ top: '200px', left: '600px', opacity: 1 }, { duration: 200, easing: 'easeInOutQuint' });
								
							}							
						}						
					});					
				}
				
				setTimeout(function() {				
					//// NOW WE NEED TO SEE WHICH MARKERS WE SHOULD SHOW THE OVERLAY
					jQuery('.slider-map').gmap3({						
						get: {							
							name: 'marker',
							all:  true,
							callback: function(objs) {								
								//// GOES THROUGH THEM AND GETS THE VISIBLE ONES
								jQuery.each(objs, function(i, obj) {									
									if(obj.getMap()) {										
										//// SHOW THIS OVERLAY â€“ IF THERE'S ONE
										jQuery('.overlay-featured-marker-id-'+obj.__gm_id).show();
									};									
								});								
							}							
						}						
					});				
				}, 400);				
			});
			
			//// LET'S SHOW ALL OUR MARKERS
			jQuery('.overlay-featured-marker').css({ opacity: .65, display: 'block', top: '30px' });
			
			//// WHEN ZOOM CHANGES
			google.maps.event.addListener(jQuery('.slider-map').gmap3('get'), 'zoom_changed', function() {				
				//// GOES THROUGH OUR MARKERS AND LOOK FOR OVERLAYS		
				//// LETS GET ALL MARKERS THAT ARE NOT VISIBLE
				clusterManager.repaint();
				
				jQuery('.overlay-featured-marker').css({ top: '30px', opacity: .65 }).show();
				jQuery('.overlay-featured-marker-current').css({ opacity: 1, left: '0px', top: '0px', width: '72px', height: '72px' }).removeClass('infowindow');
				jQuery('.overlay-featured-marker-current .arrow').css({ left: '33px' });
				
				//// GETS CLUSTERS
				var clusters = clusterManager.getClusters();
				
				//// LOOPS CLUSTERS TO GET MARKERS
				jQuery.each(clusters, function(i, cluster) {					
					var markers = cluster.getMarkers();					
					if(markers.length > 1) { 					
						//// LOOPS MARKERS
						jQuery.each(markers, function(i, marker) {							
							//// HIDES THIS MARKERS OVERLAY
							jQuery('.overlay-featured-marker-id-'+marker.__gm_id).stop().hide();
						});					
					}					
				});				
			});
			
			//// LETS GO MARKER BY MARKER AND SEE IF ITS IN THE CLUSTER
			jQuery.each(clusters, function(i, cluster) {				
				var _markers = cluster.getMarkers();				
				if(_markers.length > 1) {				
					jQuery.each(_markers, function(i, thisMarker) {						
						//// LET'S HIDE MARKERS WITH THIS ID
						jQuery('.overlay-featured-marker-id-'+thisMarker.__gm_id).stop().hide();						
					});				
				}				
			});			
		},
		
		closeInfoWindow: function(event) {
			var infowindow_obj = jQuery('.infowindow');
			infowindow_obj.removeClass('infowindow')
			infowindow_obj.animate({ opacity: 1, left: '0px', top: '0px', width: '72px', height: '72px' }, { duration: 200, easing: 'easeInOutBack', complete: function() { } });
			infowindow_obj.find('.arrow').animate({ left: '33px' }, { duration: 200, easing: 'easeInOutBack' });
			event.stopImmediatePropagation()
			return false;
		},
	});
})(jQuery);

/*
 * Custom Added Additional Scripts
 */
theCluster = '';
jQuery(document).ready(function($) {
	if( $('.slider-map').length ) {
		myGmap = $('.slider-map');
		
		var attr_zoom = $('input[name="slider[zoom]"]').val();
		if(parseInt(attr_zoom) > 0) {
			zoomLevel = parseInt(attr_zoom);
		} else {
			zoomLevel = 14;
		}
		
		var attr_height = $('input[name="slider[height]"]').val();
		if(parseInt(attr_height) > 0) {
			myGmap.css('height', parseInt(attr_height)+'px');
		} else {
			myGmap.css('height', '440px');
		}
		
		myGmap.gmap3({
			map: {
				options: {
					zoom: 			zoomLevel,
					mapTypeControl: 	false,
					navigationControl:	false,
					streetViewControl: 	false,
					scrollwheel: 		false,
					center: new google.maps.LatLng('-37.8369887', '144.99299919999999'),					
				},
				events: {
					mousedown: function() {
						$('#property-search-form').stop().animate({ opacity: .15 }, 150);
						$('.overlay-markup-cluster').fadeOut(200, function() { $(this).remove(); });
					},
					mouseup: function() {
						$('#property-search-form').stop().animate({ opacity: 1 }, 150);
					}
				}
			}
		});
	
		$('.slider-map-zoom-in').gmapZoomIn(myGmap);
		$('.slider-map-zoom-out').gmapZoomOut(myGmap);
	}
	
	if( $('.slider-map-featured').length ) {
		$('.slider-map-featured').sliderMapFeaturedInit();
	}
});

jQuery(window).load(function() {
	if(typeof clusterManager != 'undefined') {
		if(clusterManager.ready_ == true) {
			jQuery('.slider-map').clusterMarkers();
		} else {
			//// MAKES SURE OUR CLUSTER IS READY TO RECEIVE THE PINS
			clusterInt = setInterval(function() {
			if(clusterManager.ready_ == true) { jQuery('.slider-map').clusterMarkers(); clearInterval(clusterInt); }
			}, 300);
		}
	}
});

function set_markers(img_path) {
	if( styles == '' || (typeof styles == 'undefined') ) {
		var styles = [
			{
				url: img_path+'cluster_1.png',
				height: 57,
				width: 57,
				anchor: [21, 26],
				textColor: '#ffffff',
				textSize: 13
			}, {
				url: img_path+'cluster_2.png',
				height: 75,
				width: 75,
				anchor: [29, 30],
				textColor: '#ffffff',
				textSize: 15
			}, {
				url: img_path+'cluster_3.png',
				height: 100,
				width: 100,
				anchor: [31, 42],
				textColor: '#ffffff',
				textSize: 18
			}
		];
	}
	
	if( jQuery('.slider-map').length ) {
		jQuery('.slider-map').gmap3({
			get: {
				name: 		'marker',
				all: 		true,
				callback: function(objs){
					//// adds it to our cluster
					var mapObject = jQuery('.slider-map').gmap3('get');
					
					minimumClusterSize = 2;
					var attr_cluster = jQuery('input[name="slider[cluster]"]').val();
					if(attr_cluster != 'true') {
						minimumClusterSize = 99999; //For cluster making false
					}
						
					//// ADDS MARKERS TO OUR CLUSTERS
					clusterManager = new MarkerClusterer(mapObject, objs, {
						styles: styles,
						zoomOnClick: false,
						minimumClusterSize:minimumClusterSize
					});
				}
			}
		});
	}
}

/*
 * jQuery Easing v1.3 - http://gsgd.co.uk/sandbox/jquery/easing/
 *
 * Uses the built in easing capabilities added In jQuery 1.1
 * to offer multiple easing options
 *
 * TERMS OF USE - jQuery Easing
 * 
 * Open source under the BSD License. 
 * 
 * Copyright Â© 2008 George McGinley Smith
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * Redistributions of source code must retain the above copyright notice, this list of 
 * conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list 
 * of conditions and the following disclaimer in the documentation and/or other materials 
 * provided with the distribution.
 * 
 * Neither the name of the author nor the names of contributors may be used to endorse 
 * or promote products derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY 
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *  COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 *  EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 *  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED 
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 *  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE. 
 *
*/

// t: current time, b: begInnIng value, c: change In value, d: duration
jQuery.easing['jswing'] = jQuery.easing['swing'];

jQuery.extend( jQuery.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) {
		//alert(jQuery.easing.default);
		return jQuery.easing[jQuery.easing.def](x, t, b, c, d);
	},
	easeInQuad: function (x, t, b, c, d) {
		return c*(t/=d)*t + b;
	},
	easeOutQuad: function (x, t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	easeInOutQuad: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t + b;
		return -c/2 * ((--t)*(t-2) - 1) + b;
	},
	easeInCubic: function (x, t, b, c, d) {
		return c*(t/=d)*t*t + b;
	},
	easeOutCubic: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t + 1) + b;
	},
	easeInOutCubic: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t + b;
		return c/2*((t-=2)*t*t + 2) + b;
	},
	easeInQuart: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t + b;
	},
	easeOutQuart: function (x, t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	easeInOutQuart: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t + b;
		return -c/2 * ((t-=2)*t*t*t - 2) + b;
	},
	easeInQuint: function (x, t, b, c, d) {
		return c*(t/=d)*t*t*t*t + b;
	},
	easeOutQuint: function (x, t, b, c, d) {
		return c*((t=t/d-1)*t*t*t*t + 1) + b;
	},
	easeInOutQuint: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b;
		return c/2*((t-=2)*t*t*t*t + 2) + b;
	},
	easeInSine: function (x, t, b, c, d) {
		return -c * Math.cos(t/d * (Math.PI/2)) + c + b;
	},
	easeOutSine: function (x, t, b, c, d) {
		return c * Math.sin(t/d * (Math.PI/2)) + b;
	},
	easeInOutSine: function (x, t, b, c, d) {
		return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;
	},
	easeInExpo: function (x, t, b, c, d) {
		return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;
	},
	easeOutExpo: function (x, t, b, c, d) {
		return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;
	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) {
		return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;
	},
	easeOutCirc: function (x, t, b, c, d) {
		return c * Math.sqrt(1 - (t=t/d-1)*t) + b;
	},
	easeInOutCirc: function (x, t, b, c, d) {
		if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;
		return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;
	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158; 
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

/** jquery plugin to get height & width of hidden elements **/
(function(a){if(typeof define==="function"&&define.amd){define(["jquery"],a);
}else{a(jQuery);}}(function(a){a.fn.addBack=a.fn.addBack||a.fn.andSelf;a.fn.extend({actual:function(b,l){if(!this[b]){throw'$.actual => The jQuery method "'+b+'" you called does not exist';
}var f={absolute:false,clone:false,includeMargin:false,display:"block"};var i=a.extend(f,l);var e=this.eq(0);var h,j;if(i.clone===true){h=function(){var m="position: absolute !important; top: -1000 !important; ";
e=e.clone().attr("style",m).appendTo("body");};j=function(){e.remove();};}else{var g=[];var d="";var c;h=function(){c=e.parents().addBack().filter(":hidden");
d+="visibility: hidden !important; display: "+i.display+" !important; ";if(i.absolute===true){d+="position: absolute !important; ";}c.each(function(){var m=a(this);
var n=m.attr("style");g.push(n);m.attr("style",n?n+";"+d:d);});};j=function(){c.each(function(m){var o=a(this);var n=g[m];if(n===undefined){o.removeAttr("style");
}else{o.attr("style",n);}});};}h();var k=/(outer)/.test(b)?e[b](i.includeMargin):e[b]();j();return k;}});}));

/** end **/
