<?php
/*
Template Name: User Listings
*/

get_header();
?>
<?php
    global $wpdb;
    $query1 = 'SELECT post_title, guid FROM wp_posts where post_type = \'rental\' and post_author = '.$userdata->ID;
    $results1 = $wpdb->get_results( $query1, ARRAY_A );
    $rental_count = count($results1);
    $query2 = 'SELECT post_name,post_mime_type FROM wp_posts where post_type = \'attachment\' and post_mime_type LIKE \'image%\' and post_parent in (SELECT ID FROM wp_posts where post_type = \'rental\' and post_author = '.$userdata->ID.')  GROUP BY post_parent';
    $results2 = $wpdb->get_results( $query2, ARRAY_A );
    $query3 = 'SELECT post_id,meta_key,meta_value,post_title FROM wp_postmeta,wp_posts where post_id=ID and post_type=\'rental\' and post_author = '.$userdata->ID;
?>
<div class="container">
    <div class="row">
        <div id="primary" class="col-md-12 col-lg-12">
            <main id="main" class="site-main" role="main">
                <div class="card">
                    <div class="entry-img"></div>
                    <div class="entry-container">
                        <p></p>
                        <div>
                            <ul class="nav nav-pills" style="margin-bottom: 35px; margin-left: 0px;margin-top: -15px;">
                                <li><a href="http://localhost/wordpress/?page_id=118">Profile</a></li>
                                <li><a href="http://localhost/wordpress/?page_id=138">Wish List</a></li>
                                <li  class="active"><a href="http://localhost/wordpress/?page_id=136">Your Listings</a></li>
                                <li><a href="http://localhost/wordpress/?page_id=140 ">Orders</a></li>
                            </ul>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Your listings: <?php echo $rental_count?></div>
                            <table>
                                <tbody>
                                    <tr>
                                        <td style="width: 8%"></td>
                                        <td style="width: 84%">
                                            <div class="panel-body">
                                                <div class="list-group">
                                                    <?php for($i=0;$i<$rental_count;$i++) { ?>
                                                        <div class="list-group-item">
                                                            <div class="row-picture">
                                                                <img class="circle" src="http://lorempixel.com/56/56/people/1" alt="icon">
                                                            </div>
                                                            <div class="row-content">
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td >
                                                                                <h1 class="list-group-item-heading"><?php echo $results1[$i][post_title]?></h1>
                                                                                <p class="list-group-item-text">Donec id elit non mi porta gravida at eget metus</p>
                                                                            </td>
                                                                            <td align="right" valign="middle">
                                                                                <a href="<?php echo $results1[$i][guid]?>" class="btn btn-success btn-sm">View</a>
                                                                                <a href="" class="btn btn-danger btn-sm">Delete</a>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="list-group-separator"></div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 8%"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main><!-- #main -->
        </div><!-- #primary -->

    </div> <!-- .row -->
</div> <!-- .container -->



<?php get_footer(); ?>
