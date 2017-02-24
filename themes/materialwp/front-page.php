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
    create_custom_page('popular-' . $cities, 'Popular Cities', '');
    create_custom_page('popular-' . $schools, 'Popular Schools', '');
    ?>
    <!-- <php wp_enqueue_style('hover-effects-1', get_template_directory_uri()  . '/hover-effects/css/set1.css', array(), '', 'all' ); ?> -->
    <?php wp_enqueue_style('hover-effects-2', get_template_directory_uri()  . '/hover-effects/css/set2.css', array(), '', 'all' ); ?>
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
      function generateGrid($gid, $service){
          $tdir = get_template_directory_uri();
?>
          <style>
                /* ---- grid ---- */

                .grid {
                    background: #DDD;
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
                    width: 33.333%;
                }

                .grid-item {
                    float: left;
                }

                .grid-item img {
                    display: block;
                    max-width: 100%;
                }
          </style>
          <script>
              $(document).ready(function(){
                  function success_func(images){
                      var images = images.data;
                      var listHtml = '';
                      for (var i = 0; i < images.length; i++) {
                          listHtml += '<li class="grid-item"><figure class="effect-julia"><img src="'
                              + <?php echo '"' . $tdir . '"' ?>  + '/images/' + images[i].icon_id
                              + '.jpg" /><figcaption><div>'
                              + '<h3><span>' + images[i].name + '</span></h3>'
                              + '<p>' + images[i].state + ', ' + images[i].country  + '</p></div>'
                              + '<a href="#">View more</a>'
                              + '</figcaption></figure></li>';
                      }
                      var redirectAnchor = '<a href="/wordpress/popular-' + '<?php echo $service ?>">';
                      listHtml += '<li class="grid-item"><figure class="effect-julia">'
                          + redirectAnchor + '<img src="'
                          + <?php echo '"' . $tdir . '"' ?>  + '/images/' + 'm' + <?php echo '"' . $service . '"' ?>
                          + '.jpg" /><figcaption><div>'
                          + '<h3><span>More ' + <?php echo '"' . $service . '"' ?> + '</span></h3></div>'
                          + '</figcaption></figure></li>';
                      $('#<?php echo $gid ?>').append(listHtml);

                      $('.grid').masonry({
                          itemSelector: '.grid-item',
                          //columnWidth: 140
                      });
                      new AnimOnScroll( document.getElementById( <?php echo '"' . $gid . '"'?> ), {
                          minDuration : 0.6,
                          maxDuration : 0.9,
                          viewportFactor : 0.2
                      } );
                  }
                  $.ajax({
                      type: "GET",
                      url: "/node/api/0/" + <?php echo '"' . $service . '"' ?>,
                      dataType: 'json',
                      success: success_func
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
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand hidden-sm hidden-xs" style="padding-top: 10px; padding-bottom: 10px; height: 30px"; rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
                </div>

                <div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1">
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
                </div> <!-- .navbar-collapse -->
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

                $('#my_epl_form').on('submit', function(e){
                    e.preventDefault();
                    var link = window.location.href;
                    var arr=link.split('?');
                    window.location.href = arr[0] + '?' + $(this).serialize()
                        + '&epl_action=epl_search&distance-scope=auto&post_type=rental&action=load_post_ajax';
                });

                $( "#datepicker-s" ).datepicker({ minDate:0});
                $( "#datepicker-s" ).datepicker( "option", "dateFormat", "yy-mm-dd");
                $( "#search-tab-daily").on('click', function(){
                    $('#end-date-block').html('<input type="text" name="departure" placeholder="End Date" id="datepicker-e"'
                      + ' style="text-align: center; position: relative; z-index: 10" class="form-control">');
                    $( "#datepicker-e" ).datepicker({ minDate:0});
                    $( "#datepicker-e" ).datepicker( "option", "dateFormat", "yy-mm-dd");
                });
                $( "#search-tab-long").on('click', function(){
                    var options = '';
                    for(var x = 0; x <= 11; x++) {
                        if(x == 0) {
                            options += '<option value="1">1 ' + _("month") + '</option>';
                        }else{
                            options += '\n<option value="' + (x + 1) + '">'
                                + (x + 1) + ' ' + _("months") + '</option>';
                        }
                    }
                    $('#end-date-block').html('<select name="term" class="form-control">' +
                        '<option>Term</option>' + options + '</select>')
                });
            } );
        </script>
        <style>
            .custom_input {
                padding: 0 8px;
            }
        </style>

        <?php
        $tdir = get_template_directory_uri();
        $img_array = array(
            array(
                'url' => $tdir . '/images/img1.jpg',
                'title' => '',
                'desc' => ''),
            array(
                'url' => $tdir . '/images/img2.jpg',
                'title' => '',
                'desc' => ''),
            array(
                'url' => $tdir . '/images/img3.jpg',
                'title' => '',
                'desc' => '')
        );
            generateCarousel('HomePageCarousel', $img_array) ?>
        <!-- map container but hidden in main page -->
        <div id="general-map-container" style="visibility: hidden; display:none"></div>

        <!-- Search section -->
        <div id="my-epl-form-wrapper" class="panel panel-default" style="margin-top: 10px">
            <div class="panel-body" style="width: 100%; text-align: center">
                <div style="display: inline-block; position: relative">
                    <table class="table-responsive" style="overflow: hidden; border: 0px;
                        border-collapse: separate; border-spacing: 10px;">
                        <tr><td colspan="5">
                                <ul class="nav nav-tabs" style="margin-left: 0px">
                                    <li class="active"><a id="search-tab-long" class="btn">Term Rental</a></li>
                                    <li><a id="search-tab-daily" class="btn">Daily Rental</a></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td style="display: inline-block">
                                <div id="mb-main-search-bar" class="custom-geocoder-control" style="">
                                    <!-- <a id="mb-search-link" class="leaflet-control-mapbox-geocoder-toggle mapbox-icon mapbox-icon-geocoder"
                                    style="visibility: hidden"></a> -->
                                    <div id="mb-search-wrap">
                                        <form id="mb-search-form" style="border: 0; box-shadow: none;">
                                            <div class="form-group is-empty custom-input">
                                                <input id="mb-search-input" autocomplete="off" placeholder="Where to go"
                                                       style="text-align: center" class="form-control custom-width" type="text" />
                                                <span class="material-input"></span>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td style="display: inline-block">
                                <form id="my_epl_form">
                                      <span>
                                          <div class="form-group col-sm-10 custom-input">
                                             <input type="text" name="arrival" placeholder="Start Date" id="datepicker-s"
                                                    style="text-align: center; position: relative; z-index: 10"
                                                    class="form-control">
                                             <!--<i class="fa fa-calendar form-control"></i>-->
                                          </div>
                                      </span>
                            </td>
                            <td style="display: inline-block">
                                      <span>
                                         <div id="end-date-block" class="form-group col-sm-10 custom-input">
                                             <select name="term" class="form-control col-sm-10">
                                                 <option>Term</option>
                                                 <?php for($x = 0; $x <= 11; $x++) {
                                                            if($x == 0):
                                                                echo '<option value="1">1 month</option>';
                                                            else:
                                                                echo '<option value="' . ($x + 1) . '">'
                                                                    . ($x + 1) . ' months</option>';
                                                            endif;
                                                   } ?>
                                             </select>
                                            <!--<i class="fa fa-calendar"></i>-->
                                         </div>
                                      </span>
                            </td>
                            <td style="display: inline-block">
                                        <div class="form-group custom-input">
                                           <select name="guests" data-placeholder="guests" class="form-control">
                                                <option>Guests</option>
                                                <option value="1">1 guest</option>
                                                <option value="2">2 guests</option>
                                                <option value="3">3 guests</option>
                                                <option value="4">4 guests</option>
                                            </select>
                                        </div>
                            </td>
                            <td style="display: inline-block; padding: 0 8px">
                                      <span class="submit-btn">
                                        <button class="btn btn-transparent"><?php echo __('Search', 'materialwp') ?></button>
                                      </span>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <div id="mb-search-results" class="custom-width" style="position: absolute; top:140px; z-index: 99;
                        background: white; border: 0px solid black; display: block; margin-left: 10px;"></div>
                </div>
            </div>
        </div>
        <!-- end search section -->

        <!-- popular school gallery -->
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo __('Popular School', 'materialwp') ?></div>
            <div class="panel-body">
                <?php generateGrid('homePageGrid1', $schools) ?>
            </div>
        </div>
        <!-- end popular school -->

        <!-- popular city gallery -->
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo __('Popular City', 'materialwp') ?></div>
            <div class="panel-body">
                <?php generateGrid('homePageGrid2', $cities) ?>
            </div>
        </div>
        <!-- end popular city -->
<?php get_footer(); ?>
