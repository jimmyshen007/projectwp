<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <!--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
     -->
    <?php wp_head();
    echo '<script>window.jQuery = window.$ = jQuery;</script>';
    // Dynamically create necessary redirection pages if not exist.
    $schools = 'schools';
    $cities = 'cities';
    create_custom_page('popular-' . $cities, 'Popular Cities');
    create_custom_page('popular-' . $schools, 'Popular Schools');
    create_custom_page('instructions', 'Instructions', 'page_instruction.php');
    create_custom_page('premium', 'Premium', 'page_premium.php');
    ?>
    <!-- <php wp_enqueue_style('hover-effects-1', get_template_directory_uri()  . '/hover-effects/css/set1.css', array(), '', 'all' ); ?> -->
    <?php wp_enqueue_style('hover-effects-2', get_template_directory_uri()  . '/hover-effects/css/set2.css', array(), '', 'all' ); ?>
    <style>
        /* .custom-width {
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
        }*/
        .mb-search-result-a{
            display: block;
        }
        .search-btn-color{
            background-color: #ff5a5f !important;
            color: white !important;
            margin-top: 0px;
        }
    </style>
    <script>
        var textTranslation = {
            "month": "月",
            "months": "月"
        };
        $(document).ready(function(){
            _.setTranslation(textTranslation);
        });
    </script>
</head>
<body <?php body_class(); ?>>

<?php

    function generateSearch(){ ?>
        <!-- Small window search section -->
        <div id="my-epl-form-wrapper-s" class="panel panel-default" style="top: 40%; left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%); position: absolute; z-index: 99; display: none; width: 60%">
            <div class="panel-body" style="width: 100%; text-align: center">
                <div style="float: left; width: 80%; text-align: center; margin: 0 auto"><input id="search-input-sm" style="text-align: center" type="text" class="form-control" placeholder="<?php echo __('Where to go'); ?>" /></div>
                <div style="float: left; width: 20%"><img class="icon icons8-Search" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABWklEQVRIS7WUfU0DQRBHXx0gARSAhKIAcAAOkAAKCgpAAjjAAXUADsBBySM7yfbu9uaabDe53B87O28+fjMrjnxWif9T4Ao4KXa/wDvwvTSuFkDHL8AZ8Abo2CPoGvgCHoGPDDQFuAUeyvfacLAGvNsAz3OQIcCHRux/m0RnNmbwVGCT5jXAB5/AzQLn4cw39uOi1ZcacF8MLdEhx3Las8l3NcCSaJSVZgiPLEJpe/c1YAdksm1lZlCqayTfXgCbbalGsu0JsIej8tYA01Oei6e0Gr6fVnlrgHp2Yk31kKMwrL/f6AznwBTVdKyGDBSzc9daG0PVGI21tFRLIK4L7XyTTnIYWCoBczMRyzBWymUroJburacgV4e7KRqvY526TuyVQZwX9UxCssESpEP74hGk1mOFx8JrQjJA1mTvZyE9ALOQXoAmpCeghoQYtr0BARHwv5eOAdgTxh/xCU8ZbouwdAAAAABJRU5ErkJggg==" width="24" height="24"></div>
            </div>
        </div>

        <!-- large window search section -->
        <div id="my-epl-form-wrapper" class="panel panel-default" style="top: 40%; left: 50%;
-webkit-transform: translate(-50%, -50%);
transform: translate(-50%, -50%); position: absolute; z-index: 99; display: none; width: 60%">
            <div id="search-form-panel-body" class="panel-body" style="width: 100%; text-align: center">
                <div id="search-form-inner-wrapper" style="display: inline-block; position: relative; width: 100%">
                            <ul class="nav nav-tabs" style="background-color: #999; margin-left: 0px; margin-bottom: 20px">
                                <li><a id="search-tab-long" class="btn">Term Rental</a></li>
                                <li><a id="search-tab-daily" class="btn">Daily Rental</a></li>
                            </ul>
                    <!-- <div id="myTabContent" class="tab-content"> -->
                            <div id="mb-main-search-bar" class="custom-geocoder-control col-lg-4" style="">
                                <!-- <a id="mb-search-link" class="leaflet-control-mapbox-geocoder-toggle mapbox-icon mapbox-icon-geocoder"
                                style="visibility: hidden"></a> -->
                                <div id="mb-search-wrap">
                                    <form id="mb-search-form">
                                        <div class="form-group">
                                            <input id="mb-search-input" autocomplete="off" placeholder="<?php echo __('Where to go'); ?>"
                                                   class="form-control label-floating custom-width" style="text-align: center" type="text" />
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <form id="my_epl_form">
                                  <div class="form-group col-lg-2 custom-input">
                                     <input type="text" name="property_available_date[]" placeholder="Start Date" id="datepicker-s"
                                            style="text-align: center; position: relative; z-index: 99"
                                            class="form-control">
                                     <!--<i class="fa fa-calendar form-control"></i>-->
                                  </div>
                            <!-- </td>
                            <td style="display: inline-block"> -->
                                 <div id="end-date-block" class="form-group col-lg-2 custom-input">
                                     <select name="property_min_stay" class="form-control">
                                         <option value>Term</option>
                                         <?php for($x = 0; $x <= 11; $x++) {
                                            if($x == 0):
                                                echo '<option value="1">1 month</option>';
                                            else:
                                                echo '<option value="' . ($x + 1) . '">'
                                                    . ($x + 1) . ' months</option>';
                                            endif;
                                        } ?>
                                    </select>
                                    <input type="hidden" name="property_rent_period" value="week|month" />
                                        <!--<i class="fa fa-calendar"></i>-->
                                </div>
                                <!-- </td>
                                <td style="display: inline-block"> -->
                                <div class="form-group custom-input col-lg-2">
                                    <select name="property_number_guests" data-placeholder="guests" class="form-control">
                                        <option value>Guests</option>
                                        <option value="1">1 guest</option>
                                        <option value="2">2 guests</option>
                                        <option value="3">3 guests</option>
                                        <option value="4">4 guests</option>
                                    </select>
                                </div>
                                <!-- </td>
                                <td style="display: inline-block; padding: 0 8px"> -->
                                <div class="col-lg-2">
                                    <button class="btn btn-sm search-btn-color"><?php echo __('Search', 'materialwp') ?></button>
                                </div>
                            </form>
                            <!-- </div> -->
                            <div id="mb-search-results" class="custom-width" style="position: absolute; top:90px; z-index: 99;
                                background: white; border: 0px solid black; display: block; margin-left: 10px;"></div>
                            </div>
                </div>
        </div>
        <!-- end search section -->

        <!-- Small window search modal -->
        <div id="small-search-modal" class="modal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button id="modal-dismiss-btn" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body" >
                        <div id="search-modal-content" style="overflow: hidden; position: relative; height:auto"></div>
                    </div>
                    <div class="modal-footer">
                        <button id="modal-close-btn" type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close', 'materialwp'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    <?php }

    function generateSlider($cid, $imgs){
        $n_imgs = count($imgs);
        ?>
        <div id="slider-wrapper" style="width:100%; text-align: center; position: relative">
            <?php generateSearch() ?>
            <ul id="<?php echo $cid ?>">
    <?php
             for($x = 0; $x < $n_imgs; $x++) { ?>
                 <li title="<?php echo $imgs[$x]['title'] ?><?php echo $imgs[$x]['desc'] ?>">
                 <img class="img-responsive center-block" src="<?php echo $imgs[$x]['url'] ?>"
                             alt="<?php echo $imgs[$x]->title ?>">
                 </li>
    <?php    }
    ?>
            </ul>
        </div>
        <style>
            .pictures-slider-<?php echo $cid; ?> {
                /*height: 0;
                padding: 56.25% 0 0 0; */
                /*overflow: hidden;*/
            }

            div.sy-slides-wrap {
                /*display: block;
                width: 100%;
                overflow: hidden !important; */

                position: relative;
                height: 0 !important;
                padding: 56.25% 0 0 0 !important;
            }
            div.sy-slides-crop{
                /*
                display: block !important;
                margin: auto !important; */
                position: absolute !important;
                /*
                max-width: 100% !important;
                max-height: 100% !important; */
                left: 0 !important;
                right: 0 !important;
                top: 0 !important;
                bottom: 0 !important;
            }
            ul.sy-controls {
                left: 0 !important;
                right: 0 !important;
                top: 0 !important;
                bottom: 0 !important;
            }
            .ui-datepicker {
                z-index: 9999 !important;
            }
        </style>
        <script>
                $('#colophon').hide();
                $('#<?php echo $cid ?>').slippry({
                    // general elements & wrapper
                    slippryWrapper: '<div class="sy-box pictures-slider-<?php echo $cid; ?>" />', // wrapper to wrap everything, including pager

                    // options
                    adaptiveHeight: false, // height of the sliders adapts to current slide
                    captions: false, // Position: overlay, below, custom, false
                    auto: false,
                    // pager
                    pager: false,
                    preload: false,

                    // controls
                    controls: true,
                    autoHover: false,

                    // transitions
                    transition: 'kenburns', // fade, horizontal, kenburns, false
                    kenZoom: 140,
                    speed: 2000 // time the transition takes (ms)
                });

                function showSearch(){
                    if($(document).width() < 753 ){
                        $('#drawer-body-inner').appendTo($('#main-drawer-body'));
                    }else{
                        $('#drawer-body-inner').appendTo($('#bs-example-navbar-collapse-1'));
                    }
                    if($(window).width() < 1000 ) {
                        $('div#search-modal-content').append($('#search-form-inner-wrapper'));
                        if($('#my-epl-form-wrapper').is(":visible")){
                            $('#my-epl-form-wrapper').hide();
                        }
                        if(!$('#my-epl-form-wrapper-s').is(":visible")){
                            $('#my-epl-form-wrapper-s').show();
                        }
                    }else{
                        $('div#search-form-panel-body').append($('#search-form-inner-wrapper'));
                        if(!$('#my-epl-form-wrapper').is(":visible")){
                            $('#my-epl-form-wrapper').show();
                        }
                        if($('#my-epl-form-wrapper-s').is(":visible")){
                            $('#my-epl-form-wrapper-s').hide();
                        }
                        $('#small-search-modal').hide();
                    }
                    $('.ui-datepicker').hide();
                }

                $(document).ready(function() {
                    $('#small-search-modal').hide();
                    $('#popular-panel-wrapper').show();
                    showSearch();
                    $('#colophon').show();
                    $('#main-purpose-wrapper').show();
                    $('#benefits-wrapper').show();
                    $(window).resize(function(){
                        showSearch();
                    });
                    //$('#slider-wrapper').append($('#my-epl-form-wrapper'))
                    $('input#search-input-sm').on('click', function(){
                        $('#small-search-modal').fadeIn();
                    });
                    $('#modal-dismiss-btn').on('click', function(){
                        $('#small-search-modal').fadeOut();
                    });
                    $('#modal-close-btn').on('click', function(){
                        $('#small-search-modal').fadeOut();
                    });
                });

        </script>
    <?php
    }
    /*
     * $cid: carousel id.
     * $imgs: array of image elements. each image is also an array with url, title and desc.
     */
    function generateCarousel($cid, $imgs){
        $n_imgs = count($imgs);
?>
        <!-- start carousel -->
        <div id="<?php echo $cid ?>" class="carousel slide" data-ride="carousel">

            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php for($x = 0; $x < $n_imgs; $x++) {
                    if($x == 0){
                        $active = 'active';
                    }else{
                        $active = '';
                    }
                    echo '<li data-target="#' . $cid . '" data-slide-to="'
                        . $x . '" class="' . $active . '"></li>';
                    }
                ?>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <?php generateSearch() ?>

                <?php for($x = 0; $x < $n_imgs; $x++){
                    if($x == 0){
                        $active = 'active';
                    }else{
                        $active = '';
                    }
                    ?>
                    <div class="item <?php echo $active?>">
                        <img class="img-responsive center-block" src="<?php echo $imgs[$x]['url'] ?>"
                             alt="<?php echo $imgs[$x]->title ?>" style="width: 100%; height: 30%">
                        <div class="carousel-caption">
                            <h3><?php echo $imgs[$x]['title'] ?></h3>
                            <p><?php echo $imgs[$x]['desc'] ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Left and right controls -->
            <a class="left carousel-control" href="#<?php echo $cid ?>" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#<?php echo $cid ?>" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
        <!-- End carousel -->
<?php };


      /*
       * @$gid: grid id, same as the div id.
       * @$service: name of the service. such as cities or schools.
       */
      function generateGrid($gid, $service, $hoverEffect="julia", $numRecord=7){
          $tdir = get_template_directory_uri();
?>

          <script>
              $(document).ready(function(){
                  function success_func_<?php echo $gid; ?>(images){
                      var images = images.data;
                      var listHtml = '';
                      for (var i = 0; i < images.length; i++) {
                          var refer = '?epl_action=epl_search&distance-scope=auto&post_type=rental&action=load_post_ajax';
                          if(images[i].lat && images[i].lng){
                              refer += '&my_epl_input_lat=' + images[i].lat
                                    + '&my_epl_input_lng=' + images[i].lng;
                          }else{
                              refer += '&my_epl_bb_min_lat=' + images[i].min_lat
                                    + '&my_epl_bb_max_lat=' + images[i].max_lat
                                    + '&my_epl_bb_min_lng=' + images[i].min_lng
                                    + '&my_epl_bb_max_lng=' + images[i].max_lng;
                          }
                          listHtml += '<li class="grid-item"><figure class="effect-<?php echo $hoverEffect; ?>"><img src="'
                              + <?php echo '"' . $tdir . '"' ?>  + '/images/' + images[i].icon_id
                              + '.jpg" /><figcaption><div>'
                              + '<h3><span>' + images[i].name + '</span></h3>'
                              + '<p>' + images[i].state + ', ' + images[i].country  + '</p></div>'
                              + '<a href="'+ refer + '"></a>'
                              + '</figcaption></figure></li>';
                      }
                      var redirectAnchor = '<a href="/wordpress/popular-' + '<?php echo $service ?>">';
                      listHtml += '<li class="grid-item"><figure class="effect-<?php echo $hoverEffect; ?>">'
                          + redirectAnchor + '<img src="'
                          + <?php echo '"' . $tdir . '"' ?>  + '/images/' + 'm' + <?php echo '"' . $service . '"' ?>
                          + '.jpg" /><figcaption><div>'
                          + '<h3><span>More ' + <?php echo '"' . $service . '"' ?> + '</span></h3></div>'
                          + '</figcaption></figure></li>';
                      $('#<?php echo $gid ?>').append(listHtml);

                      $('#' + '<?php echo $gid ?>').masonry({
                          itemSelector: '.grid-item',
                          //columnWidth: 300
                      });
                      new AnimOnScroll( document.getElementById( <?php echo '"' . $gid . '"'; ?> ), {
                          minDuration : 0.6,
                          maxDuration : 0.9,
                          viewportFactor : 0.2
                      } );
                  }
                  $.ajax({
                      type: "GET",
                      url: NODE_API_INIT + <?php echo '"' . $service . '"' ?> + "/hits/99?limit=" + "<?php echo $numRecord; ?>" + "&order=1",
                      dataType: 'json',
                      success: success_func_<?php echo $gid; ?>
                  });
              });
          </script>
          <div id="<?php echo $gid ?>" class="grid effect-1"></div>
<?php
      }
?>

<div id="page" class="hfeed site">
    <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'materialwp' ); ?></a>

    <header id="masthead" class="site-header" role="banner">

        <nav class="navbar navbar-inverse" style="margin-bottom: 0px !important;" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <!--<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
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
                </div>

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
                <!-- </div> <!-- .navbar-collapse -->
            </div><!-- /.container -->
        </nav><!-- .navbar .navbar-default -->
    </header><!-- #masthead -->
    <div id="content" class="site-content">

        <script>
            $(function() {
                var ZOOM = 14;
                L.mapbox.accessToken = MAPBOX_TOKEN;
                var map = L.mapbox.map('general-map-container', 'mapbox.streets', {
                        minZoom: 2,
                        worldCopyJump: true
                    })
                    .addControl(L.mapbox.geocoderControl('mapbox.places', {
                        autocomplete: true,
                        keepOpen: true,
                        pointZoom: ZOOM
                    }));

                $( "#datepicker-s" ).datepicker({ minDate:0});
                $( "#datepicker-s" ).datepicker( "option", "dateFormat", "yy-mm-dd");
                $( "#search-tab-daily").on('click', function(e){
                    e.preventDefault();
                    $('#end-date-block').html('<input type="text" name="property_available_date[]" placeholder="End Date" id="datepicker-e"'
                      + ' style="text-align: center; position: relative; z-index: 99" class="form-control" />'
                      + '<input type="hidden" name="property_rent_period" value="day" />');
                    $( "#datepicker-e" ).datepicker({ minDate:0});
                    $( "#datepicker-e" ).datepicker( "option", "dateFormat", "yy-mm-dd");
                });
                $( "#search-tab-long").on('click', function(e){
                    e.preventDefault();
                    var options = '<option value>Term</option>';
                    for(var x = 0; x <= 11; x++) {
                        if(x == 0) {
                            options += '<option value="1">1 ' + _("month") + '</option>';
                        }else{
                            options += '\n<option value="' + (x + 1) + '">'
                                + (x + 1) + ' ' + _("months") + '</option>';
                        }
                    }
                    $('#end-date-block').html('<select name="property_min_stay" class="form-control">' +
                        options +
                        '</select><input type="hidden" name="property_rent_period" value="week|month" />')
                });

                $('#my_epl_form').on('submit', function(e){
                    e.preventDefault();
                    window.location.href = '<?php echo get_site_url() ?>' + '?' + $(this).serialize()
                        + '&epl_action=epl_search&distance-scope=auto&post_type=rental&action=load_post_ajax';
                });
            } );
        </script>
        <style>
            .custom_input {
                padding: 0 8px;
            }

            /* ---- grid ---- */
            .grid {
                background: white;
                max-width: 100% !important;
            }

            /* clear fix */
            .grid:after {
                content: '';
                display: block;
                clear: both;
            }

            /* ---- .grid-item ---- */

            .grid-sizer,
            .grid-item {
                width: 23%;
                margin-right: 10px !important;
                margin-left: 10px !important;
            }

            .grid-item {
                float: left;
            }

            .grid-item img {
                display: block;
                max-width: 23%;
            }
            .purpose-heading {
                color: #4c4c4c;
                text-align: center;
                line-height: 3rem;
                font-weight: 400;
                font-family: Lantinghei SC, PingFang SC, Helvetica Neue, Microsoft Yahei, Hiragino Sans GB, Microsoft Sans Serif, WenQuanYi Micro Hei, sans-serif;
            }
            .heading-margin {
                margin: 20px auto;
            }
            .sub-heading-margin {
                margin-left: auto ;
                margin-right: auto;
                margin-top: 10px;
                margin-bottom: 30px;
            }
            .small-quote {
                color: #4c4c4c;
                font-weight: 400;
                text-align: center;
                width: 85%;
                margin-left: auto;
                margin-right: auto;
                margin-top: 10px;
                margin-bottom: 20px;
            }
            .popular-heading {
                color: #4c4c4c;
                /*font-size: 2rem; */
                font-family: Lantinghei SC, PingFang SC, Helvetica Neue, Microsoft Yahei, Hiragino Sans GB, Microsoft Sans Serif, WenQuanYi Micro Hei, sans-serif;
                line-height: 3rem;
                font-weight: 400;
                margin: 20px auto;
                text-align: center;
            }
            .block-button{
                position: relative;
                float: left;
                margin: 35px auto;
                width: 40%;
                left: 30%;
                overflow: hidden;
                text-align: center;
                color: white !important;
                background-color: #38b2a6 !important;
            }
            .handle-overflow{
                text-overflow: ellipsis;
                white-space:nowrap;
                text-transform: none !important;
            }
        </style>

        <?php
        $tdir = get_template_directory_uri();
        $img_array = array(
            /*array(
                'url' => $tdir . '/images/img1.jpg',
                'title' => '',
                'desc' => ''),
            array(
                'url' => $tdir . '/images/img2.jpg',
                'title' => '',
                'desc' => ''), */
            array(
                'url' => $tdir . '/images/img3.jpg',
                'title' => '',
                'desc' => ''),
            array(
                'url' => $tdir . '/images/img4.jpg',
                'title' => '',
                'desc' => ''),
            array(
                'url' => $tdir . '/images/img5.jpg',
                'title' => '',
                'desc' => ''),
            array(
                'url' => $tdir . '/images/img6.jpg',
                'title' => '',
                'desc' => ''),
            array(
                'url' => $tdir . '/images/img7.jpg',
                'title' => '',
                'desc' => '')
        );
            //generateCarousel('HomePageCarousel', $img_array)
        generateSlider('HomePageSlider', $img_array)
        ?>

        <div id="main-purpose-wrapper" style="overflow: hidden; display: none; text-align: center; margin-bottom: 0px; padding-bottom: 0px; background-color: #F8F8F6">
            <!--  We Know and We Take Care of The Most Painful Part -->
            <h2 class="purpose-heading heading-margin">Let Rental Abroad Become a Few Clicks.</h2>
            <h3 class="purpose-heading sub-heading-margin">海外租房的"痛", 我们帮你搞定</h3>

            <div class="col-md-4">
                <img class="icon icons8-Receive-Cash" src="data:image/png;base64,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" width="96" height="96">
                <h3>Save</h3>
                <p class="small-quote">Directly connect with landlord, no agent</p>
            </div>
            <div class="col-md-4">
                <img class="icon icons8-Building" src="data:image/png;base64,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" width="96" height="96">
                <h3>Easy</h3>
                <p class="small-quote">Entire application process is done online with just clicks</p>
            </div>
            <div class="col-md-4">
                <img class="icon icons8-Calendar" src="data:image/png;base64,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" width="96" height="96">
                <h3>Flexible</h3>
                <p class="small-quote">Long, short and daily rental available to uniquely cater for your needs.</p>
            </div>
            <!-- <div class="block-button"> -->
            <a href="/wordpress/instructions" class="btn btn-default btn-lg btn-raised block-button handle-overflow">
                Learn More about Us &rarr;
            </a>
            <!-- </div> -->
        </div>

        <!-- map container but hidden in main page -->
        <div id="general-map-container" style="display:none"></div>

        <div id="popular-panel-wrapper" style="display: none">
            <!-- popular school gallery -->
            <div class="panel panel-default" style="margin-bottom: 0px">
                <div class="panel-heading" style="background-color: white; padding-bottom: 0px; margin-bottom: 0px"><h2 class="popular-heading"><?php echo __('Popular School', 'materialwp') ?></h2></div>
                <div class="panel-body" style="background-color: white; padding-top: 0px; margin-bottom: 0px">
                    <?php generateGrid('homePageGrid1', $schools) ?>
                </div>
            </div>
            <!-- end popular school -->

            <!-- popular city gallery -->
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: white; padding-bottom: 0px;margin-top: 0px; margin-bottom: 0px"><h2 class="popular-heading"><?php echo __('Popular City', 'materialwp') ?></h2></div>
                <div class="panel-body" style="background-color: white; padding-top: 0px">
                    <?php generateGrid('homePageGrid2', $cities, 'selena', 7) ?>
                </div>
            </div>
        </div>

        <div id="benefits-wrapper" style="display: none; width: 100%; margin-top: 0px; margin-bottom: 0px; padding-top: 0px; padding-bottom: 0px; background-color: #eeeeee; text-align: center">
            <h2 class="purpose-heading heading-margin"><?php echo __('Why Us', 'materialwp') ?></h2>
            <?php
                $advantages = array(
                    array('Overseas Based', 'Know the market and expertise on rental related issues.'),
                    array('No Agent', 'Directly connect with landlord, save money.'),
                    array('Secure Payment', 'Encrypted payment, based on reputable Stripe platform.'),
                    array('Online Application', 'Entire application process can be done online.'),
                    array('Privacy Protection', 'Documents are preprocessed to protect your privacy.'),
                    array('Verified Users', 'Documents are verified by both our AI and staff.'),
                    array('Review', 'Feedback can be left during or after rental.'),
                    array('Geography-based Search', 'Distance and various neighborhood info available'),
                    array('Daily Rental Service', 'maximally fulfill your short term rental needs')
                );
                $listGroupOpen = '<div class="col-md-4"><div class="list-group">';
                $listGroupClose = '</div></div>';
                for($i = 0; $i < count($advantages); $i++){
                    if($i % 3 == 0){
                        if($i != 0){
                            echo $listGroupClose;
                        }
                        if($i != count($advantages) - 1){
                            echo $listGroupOpen;
                        }
                    } ?>
                    <div class="list-group-item">
                        <div class="row-action-primary">
                            <img class="img-circle" src="https://maxcdn.icons8.com/Color/PNG/24/Astrology/summer-24.png" title="Summer" width="24">
                        </div>
                        <div class="row-content">
                            <!--<div class="least-content">15m</div>-->
                            <h4 class="list-group-item-heading"><?php echo __($advantages[$i][0], 'materialwp'); ?></h4>
                            <p class="list-group-item-text"><?php echo __($advantages[$i][1], 'materialwp'); ?></p>
                        </div>
                    </div>
                    <div class="list-group-separator"></div>
            <?php } ?>
            </div>
        </div>
        </div>
        <!-- end popular city -->
<?php get_footer(); ?>
