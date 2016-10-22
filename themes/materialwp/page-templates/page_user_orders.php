<?php
/*
Template Name: User Orders
*/

get_header();
?>
<?php
global $user_ID, $wpdb;
$ch = curl_init("http://localhost:3000/api/0/worders/user/".$user_ID);

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = json_decode(curl_exec($ch))->{'data'};
$post_arr = array();
$status_arr=array();
$post_list_str = '0';
foreach($res as $x => $x_value) {
    array_push($post_arr, $x_value->{'postID'});
    array_push($status_arr, $x_value->{'appStatus'});
    $post_list_str = $post_list_str.','.$x_value->{'postID'};
}
$query = 'SELECT id, post_title, guid FROM `wp_posts` WHERE post_author='.$user_ID.' and ID in ('.$post_list_str.') ORDER by id desc';
$query2 = 'SELECT meta_key,meta_value FROM `wp_postmeta` '.
    ' WHERE post_id in ('.$post_list_str.') and (meta_key=\'property_rent\' or meta_key=\'property_rent_period\' or '.
    'meta_key=\'property_address_country\' or meta_key=\'property_address_street\' or meta_key=\'property_address_suburb\' '.
    'or meta_key=\'property_address_street_number\')'.
    'ORDER by post_id desc, meta_key asc';
$query3 ='SELECT p1.id, p3.meta_value, p1.id from wp_posts as p1, wp_posts as p2, wp_postmeta as p3'.
    ' where p1.id = p2.post_parent and p2.id = p3.post_id and p1.id in ('.$post_list_str.') and p3.meta_key = \'_wp_attached_file\''.
    ' ORDER by p1.id DESC, p2.id desc';

$results = $wpdb->get_results( $query, ARRAY_A );
$results2 = $wpdb->get_results( $query2, ARRAY_A );
$results3 = $wpdb->get_results( $query3, ARRAY_A );
$subArraySz = 6;
$order_count = count($results);
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
for($i = 0; $i < count($post_arr); $i++){
    for($j = 0; $j < count($results3); $j++) {
        if($post_arr[$i] == $results3[$j]['id']) {
            $pic_arr[$post_arr[$i]] = $results3[$j]['meta_value'];
            break;
        }
    }
}
curl_close($ch);
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
                                <li><a href="http://localhost/wordpress/?page_id=136">Your Listings</a></li>
                                <li class="active"><a href="http://localhost/wordpress/?page_id=140 ">Orders</a></li>
                            </ul>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">My orders: <?php echo $order_count?></div>
                            <table>
                                <tbody>
                                <tr>
                                    <td style="width: 2%"></td>
                                    <td style="width: 96%">
                                        <div class="panel-body">
                                            <div class="list-group">
                                                <?php for($i=0;$i<$order_count;$i++) { ?>
                                                    <div class="list-group-item">
                                                        <div class="row-content">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <td style="width: 30%">
                                                                        <img style="height: 200px; width: 300px" src=<?php echo '/wordpress/wp-content/uploads/'.$pic_arr[$results[$i]['id']];?> alt="icon">
                                                                    </td>
                                                                    <td valign="top" style="width: 70%">
                                                                        <ul style="list-style-type: none;">
                                                                            <li>
                                                                                <h3 class="list-group-item-heading"><?php echo $results[$i]['post_title'];?></h3>
                                                                            </li>
                                                                            <li>
                                                                                <p class="list-group-item-text">
                                                                                    <span style="color: grey">
                                                                                        <?php
                                                                                        if ($results2[$i*$subArraySz+2]['meta_value'] !='')
                                                                                            echo $results2[$i*$subArraySz+2]['meta_value'].'/';
                                                                                        echo $results2[$i*$subArraySz+1]['meta_value'].', '.$results2[$i*$subArraySz+3]['meta_value'];
                                                                                        ?>
                                                                                    </span>
                                                                                </p>
                                                                                <p class="list-group-item-text">
                                                                                    <span style="color: grey">
                                                                                             <?php
                                                                                             echo '$'.$results2[$i*$subArraySz+4]['meta_value'];
                                                                                             $currency = ($results2[$i*$subArraySz]['meta_value'] == 'Australia' ? 'AUD' : 'USD');
                                                                                             echo ' '.$currency. ' per ';
                                                                                             echo $results2[$i*$subArraySz+5]['meta_value'];
                                                                                             ?>
                                                                                    </span>
                                                                                </p>
                                                                            </li>
                                                                            <li>
                                                                                <p> </br></p>
                                                                                <p> </br></p>
                                                                                <table>
                                                                                    <tbody>
                                                                                        <tr align="middle">
                                                                                            <td style="width: 25%"><span style="color: <?php echo ($status_arr[$i] == "Completing application")?"#03a9f4":"lightgrey"; ?>">Completing your application</span></td>
                                                                                            <td style="width: 25%"><span style="color: <?php echo ($status_arr[$i] == "Waiting for approval")?"#4caf50":"lightgrey"; ?>">Waiting for landloard's approval</span></td>
                                                                                            <td style="width: 25%"><span style="color: <?php echo ($status_arr[$i] == "Paying")?"#ff5722":"lightgrey"; ?>">Securing your next home</span></td>
                                                                                            <td style="width: 25%"><span style="color: <?php echo ($status_arr[$i] == "Completed")?"#f44336":"lightgrey"; ?>">Hi5Fang! Get Ready to move!</span></td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </li>
                                                                            <li>
                                                                                <div class="bs-component">
                                                                                    <div class="progress">
                                                                                        <div class="progress-bar progress-bar-info" style="width: 25%;"></div>
                                                                                        <div class="progress-bar progress-bar-success" style="width: 25%;<?php if($status_arr[$i] == "Completing application")echo "visibility: hidden";?>"></div>
                                                                                        <div class="progress-bar progress-bar-warning" style="width: 25%;<?php if($status_arr[$i] == "Completing application" || $status_arr[$i] == "Waiting for approval")echo "visibility: hidden";?>"></div>
                                                                                        <div class="progress-bar progress-bar-danger" style="width: 25%;<?php if($status_arr[$i] != "Completed")echo "visibility: hidden";?>"></div>
                                                                                    </div>
                                                                                </div>
                                                                            </li>

                                                                        </ul>

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
                                    <td style="width: 2%"></td>
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
