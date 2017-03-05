<?php
/*
Template Name: User Orders
*/

get_header();
?>
<?php
global $user_ID, $wpdb;

$is_tenant_arr = get_user_meta( $user_ID, "is_tenant", false);
$is_tenant = (count($is_tenant_arr) > 0) ? $is_tenant_arr[0] : 0;
$is_host_arr = get_user_meta( $user_ID, "is_host", false);
$is_host = (count($is_host_arr) > 0) ? $is_host_arr[0] : 0;

$ch = curl_init("http://localhost:3000/api/0/worders/user/".$user_ID);

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = json_decode(curl_exec($ch))->{'data'};
$post_arr = array();
$status_arr=array();
$post_list_str = '0';
foreach($res as $x => $x_value) {
    array_push($post_arr, $x_value->{'postID'});
    $status_arr[$x_value->{'postID'}] = $x_value->{'appStatus'};
    $post_list_str = $post_list_str.','.$x_value->{'postID'};
}

$query = 'SELECT id, post_title, guid, post_author FROM `wp_posts` WHERE ID in ('.$post_list_str.') ORDER by id desc';
$query2 = 'SELECT meta_key,meta_value FROM `wp_postmeta` '.
    ' WHERE post_id in ('.$post_list_str.') and (meta_key=\'property_rent\' or meta_key=\'property_rent_period\' or '.
    'meta_key=\'property_address_country\' or meta_key=\'property_address_street\' or meta_key=\'property_address_suburb\' '.
    'or meta_key=\'property_address_street_number\' or meta_key= \'short_long_term\')'.
    'ORDER by post_id desc, meta_key asc';
$query3 ='SELECT p1.id, p3.meta_value, p1.id from wp_posts as p1, wp_posts as p2, wp_postmeta as p3'.
    ' where p1.id = p2.post_parent and p2.id = p3.post_id and p1.id in ('.$post_list_str.') and p3.meta_key = \'_wp_attached_file\''.
    ' ORDER by p1.id DESC, p2.id desc';

$results = $wpdb->get_results( $query, ARRAY_A );
$results2 = $wpdb->get_results( $query2, ARRAY_A );
$results3 = $wpdb->get_results( $query3, ARRAY_A );
$subArraySz = 7;

$order_count = count($results);
/* results2
 * ix7+0: property_address_country
 * ix7+1: property_address_street
 * ix7+2: property_address_street_number
 * ix7+3: property_address_suburb
 * ix7+4: property_rent
 * ix7+5: property_rent_period
 * ix7+6: short_long_term
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
<script>
    function createOrder(currency, postID, isShortTerm) // Check if stripe order exist or not. If not, create one.
    {
        var urlstr1 = "/api/0/worders/user/";
        var urlstr2 = "/api/0/wskus/";
        var urlstr3 = "/api/0/worders/addSOrder/";
        var userID = <?php global $user_ID; echo $user_ID; ?>;
        var postAuthorID;
        var skuID;
        var stripeAccID;
        var orderStripeID = "";
        var orderID;
        var term;

        // Look for orders belongs to this user
        jQuery.ajax({
            url: urlstr1.concat(userID),
            dataType: "json",
            method: "Get",
            success: function (result) {
                for (var i = 0; i < result.data.length; i++) {
                    if (result.data[i].postID == postID) {  // Found this user's order for this post
                        if (typeof result.data[i].stripeOrderID == 'undefined' || result.data[i].stripeOrderID == "") // stripe order not created yet.
                        {
                            postAuthorID = result.data[i].postAuthorID;
                            skuID = result.data[i].skuID;
                            stripeAccID = result.data[i].stripeAccID;
                            orderID = result.data[i]._id;
                            term = result.data[i].term;
                        }
                        else {  // stripe order has been created already
                            orderStripeID = result.data[i].stripeOrderID;
                            orderID = result.data[i]._id;
                            submitForm(orderStripeID, orderID);
                        }
                        break;
                    }
                }
                if (orderStripeID == "") // stripe order not created yet. Create stripe order
                {
                    if(isShortTerm == false && term > 30) {
                        term = 30;
                    }
                    jQuery.ajax({
                        url: urlstr2.concat(skuID),
                        dataType: "json",
                        method: "Get",
                        success: function (result) {
                            var skuStripeID = result.data.stripeSkuID;
                            jQuery.ajax({
                                url: urlstr3.concat(orderID),
                                dataType: "json",
                                method: "post",
                                data: {
                                    "currency": currency,
                                    "metadata": {
                                        "postID": postID,
                                        "postAuthorID": postAuthorID,
                                        "userID": userID,
                                        "skuID": skuID
                                    },
                                    "items": [{"type": "sku", "quantity": term, "parent": skuStripeID}]
                                },
                                success: function (result) {
                                    jQuery.ajax({
                                        url: urlstr1.concat(userID),
                                        dataType: "json",
                                        method: "Get",
                                        success: function (result) {
                                            for (var i = 0; i < result.data.length; i++) {
                                                if (result.data[i].postID == postID) {
                                                    if (typeof result.data[i].stripeOrderID != 'undefined' && result.data[i].stripeOrderID != "")
                                                    {
                                                        orderStripeID = result.data[i].stripeOrderID;
                                                        submitForm(orderStripeID, orderID);
                                                    }
                                                }
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            }
        });
    }

    function  submitForm(orderStripeID, orderID) {
        document.getElementById('orderStripeId').value = orderStripeID;
        document.getElementById('orderId').value = orderID;
        document.getElementById("payForm").submit();
    }
</script>
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
                                <li><a href="/your-profile/">Profile</a></li>
                                <li><a href="/your-profile/wish-list/">Wish List</a></li>
                                <?php if($is_host == 1) {?>
                                    <li><a href="/your-profile/users-listings/">Your Listings</a></li>
                                <?php }?>
                                <?php if($is_tenant == 1) {?>
                                    <li class="active"><a href="/your-profile/users-orders/">Orders</a></li>
                                <?php }?>
                                <?php if($is_host == 1) {?>
                                    <li><a href="/your-profile/account/">Account</a></li>
                                <?php }?>
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
                                                                        <img style="height: 200px; width: 300px" src=<?php echo '/wp-content/uploads/'.$pic_arr[$results[$i]['id']];?> alt="icon">
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
                                                                                            echo $results2[$i*$subArraySz+2]['meta_value'].' ';
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
                                                                                            <td style="width: 25%"><span style="color: <?php echo ($status_arr[$results[$i]['id']] == "Completing application")?"#03a9f4":"lightgrey"; ?>">Completing your application</span></td>
                                                                                            <td style="width: 25%"><span style="color: <?php echo ($status_arr[$results[$i]['id']] == "Waiting for approval")?"#4caf50":"lightgrey"; ?>">Waiting for landloard's approval</span></td>
                                                                                            <td style="width: 25%">
                                                                                                <?php
                                                                                                    $currency = ($results2[$i*$subArraySz]['meta_value'] == 'Australia' ? 'AUD' : 'USD');
                                                                                                    $postID = $results[$i]['id'];
                                                                                                    $postAuthor = $results[$i]['post_author'];
                                                                                                    $isShortTerm = ($results2[$i*$subArraySz+6]['meta_value'] == 'short') ? 'true' : 'false';
                                                                                                    echo ($status_arr[$results[$i]['id']] == "Approved")?"<a href=\"javascript:void(0)\" onclick=\"createOrder('".$currency."','".$postID."',".$isShortTerm.")\"" : "<span ";
                                                                                                ?> style="color:
                                                                                                <?php
                                                                                                    echo ($status_arr[$results[$i]['id']] == "Approved")?"#ff5722":"lightgrey";
                                                                                                ?>">Securing your next home</a>
                                                                                                <form id="payForm" method="post" action="/your-profile/payment/" style="visibility: hidden">
                                                                                                    <input id="orderStripeId" name="orderStripeId" type="hidden">
                                                                                                    <input id="orderId" name="orderId" type="hidden">
                                                                                                </form>
                                                                                            </td>
                                                                                            <td style="width: 25%"><span style="color: <?php echo ($status_arr[$results[$i]['id']] == "Completed")?"#f44336":"lightgrey"; ?>">Congrats! Get Ready to move!</span></td>
                                                                                        </tr>
                                                                                    </tbody>
                                                                                </table>
                                                                            </li>
                                                                            <li>
                                                                                <div class="bs-component">
                                                                                    <div class="progress">
                                                                                        <div class="progress-bar progress-bar-info" style="width: 25%;"></div>
                                                                                        <div class="progress-bar progress-bar-success" style="width: 25%;<?php if($status_arr[$results[$i]['id']] == "Completing application")echo "visibility: hidden";?>"></div>
                                                                                        <div class="progress-bar progress-bar-warning" style="width: 25%;<?php if($status_arr[$results[$i]['id']] == "Completing application" || $status_arr[$results[$i]['id']] == "Waiting for approval")echo "visibility: hidden";?>"></div>
                                                                                        <div class="progress-bar progress-bar-danger" style="width: 25%;<?php if($status_arr[$results[$i]['id']] != "Completed")echo "visibility: hidden";?>"></div>
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
