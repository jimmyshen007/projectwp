<?php
/*
Template Name: User Listings
*/

get_header();
?>
<?php
global $wpdb;
$query1 = 'SELECT id, post_title, guid FROM wp_posts where post_type = \'rental\' and post_author = '.$userdata->ID;
$results1 = $wpdb->get_results( $query1, ARRAY_A );
$rental_count = count($results1);
$post_list_str = '0';
foreach($results1 as $x => $x_value) {
    $post_list_str = $post_list_str.','.$x_value['id'];
}

$query2 = 'SELECT meta_key,meta_value FROM `wp_postmeta` '.
    ' WHERE post_id in ('.$post_list_str.') and (meta_key=\'property_rent\' or meta_key=\'property_rent_period\' or '.
    'meta_key=\'property_address_country\' or meta_key=\'property_address_street\' or meta_key=\'property_address_suburb\' '.
    'or meta_key=\'property_address_street_number\')'.
    'ORDER by post_id desc, meta_key asc';

$query3 ='SELECT p1.id, p3.meta_value, p1.id from wp_posts as p1, wp_posts as p2, wp_postmeta as p3'.
    ' where p1.id = p2.post_parent and p2.id = p3.post_id and p1.post_type = \'rental\' and p1.post_author ='.$userdata->ID.' and p3.meta_key = \'_wp_attached_file\''.
    ' ORDER by p1.id DESC, p2.id desc';

$results2 = $wpdb->get_results( $query2, ARRAY_A );
$results3 = $wpdb->get_results( $query3, ARRAY_A );
$subArraySz = 6;
/* results2
 * ix6+0: property_address_country
 * ix6+1: property_address_street
 * ix6+2:property_address_street_number
 * ix6+3: property_address_suburb
 * ix6+4: property_rent
 * ix6+5: property_rent_period
 * */
/* Find the first pic for each post */
$pic_arr=array();
for($i = 0; $i < count($results1); $i++){
    for($j = 0; $j < count($results3); $j++) {
        if($results1[$i]['id'] == $results3[$j]['id']) {
            $pic_arr[$results1[$i]['id']] = $results3[$j]['meta_value'];
            break;
        }
    }
}
?>

<div id="complete-dialog" class="modal fade" tabindex="-1">
    <div class="modal-dialog" style="width: 500px; height:400px;">
        <div class="modal-content">
            <div class="modal-header">
                <table>
                    <tbody>
                        <tr>
                            <td><h4 class="modal-title">Applications</h4></td>
                            <td><button type="button" class="close" data-dismiss="modal" aria-hidden="true" style="float: right">&times;</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body">
                <p> <button id="button">Accept</button></p>
                <table id="appTable"  class="table table-striped table-hover ">
                    <thead>
                    <tr>
                        <th>Column 1</th>
                        <th>Column 2</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr>
                        <th>Column 1</th>
                        <th>Column 2</th>
                    </tr>
                    </tfoot>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Dismiss</button>
            </div>
        </div>
    </div>
</div>
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
                                                                <img class="circle" src=<?php echo '/wordpress/wp-content/uploads/'.$pic_arr[$results1[$i]['id']];?> alt="icon">
                                                            </div>
                                                            <div class="row-content">
                                                                <table>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td >
                                                                                <h1 class="list-group-item-heading"><?php echo $results1[$i][post_title]?></h1>
                                                                                <p class="list-group-item-text">
                                                                                    <?php
                                                                                    if ($results2[$i*$subArraySz+2]['meta_value'] !='')
                                                                                        echo $results2[$i*$subArraySz+2]['meta_value'].'/';
                                                                                    echo $results2[$i*$subArraySz+1]['meta_value'].', '.$results2[$i*$subArraySz+3]['meta_value']
                                                                                    ?>
                                                                                </p>
                                                                            </td>
                                                                            <td align="right" valign="middle">
                                                                                <a href="<?php echo $results1[$i][guid]?>" class="btn btn-success btn-sm">View</a>
                                                                                <a href="" class="btn btn-danger btn-sm">Delete</a>
                                                                                <button id="app<?php echo $results1[$i][id];?>" type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#complete-dialog" onclick="checkApp(this.id)">Applications</button>
                                                                                <script>
                                                                                    //$(document).ready(function() {
                                                                                    function checkApp(id){
                                                                                        var dataSet;
                                                                                        var urlstr = "/api/0/worder/post/";
                                                                                        var postID = id.substring(3);
                                                                                        jQuery.ajax({
                                                                                            url: urlstr.concat(postID),
                                                                                            dataType: "json",
                                                                                            method: "Get",
                                                                                            success: function (result) {
                                                                                                dataSet = result.data;

                                                                                                var table = $('#appTable').DataTable( {
                                                                                                    data: dataSet,
                                                                                                    select: {
                                                                                                        style: 'single'
                                                                                                    },
                                                                                                    "pageLength": 25,
                                                                                                    "lengthChange": false
                                                                                                } );

                                                                                            }
                                                                                        });

                                                                                        var table = $('#appTable').DataTable( {
                                                                                            ajax: dataSet,
                                                                                            select: {
                                                                                                style: 'single'
                                                                                            },
                                                                                            "pageLength": 25,
                                                                                            "lengthChange": false
                                                                                        } );





                                                                                        $('#button').click( function () {
                                                                                            alert(table.row('.selected').data());
                                                                                        } );

                                                                                    } //);

                                                                                </script>
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
