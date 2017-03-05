<?php
/*
Template Name: Wish List
*/

get_header();
?>
<?php
global $user_ID, $wpdb;

$is_tenant_arr = get_user_meta( $user_ID, "is_tenant", false);
$is_tenant = (count($is_tenant_arr) > 0) ? $is_tenant_arr[0] : 0;
$is_host_arr = get_user_meta( $user_ID, "is_host", false);
$is_host = (count($is_host_arr) > 0) ? $is_host_arr[0] : 0;

$ch = curl_init("http://localhost:3000/api/0/favorites/user/".$user_ID);

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = json_decode(curl_exec($ch))->{'data'};
$post_arr = array();
$post_list_str = '0';
foreach($res as $x => $x_value) {
    if ($x_value->{'fType'} == 'post') {
        array_push($post_arr, $x_value->{'fValue'});
        $post_list_str = $post_list_str.','.$x_value->{'fValue'};
    }
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
$rental_count = count($results);
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
                                <li><a href="/your-profile/">Profile</a></li>
                                <li class="active"><a href="/your-profile/wish-list/">Wish List</a></li>
                                <?php if($is_host == 1) {?>
                                    <li><a href="/your-profile/users-listings/">Your Listings</a></li>
                                <?php }?>
                                <?php if($is_tenant == 1) {?>
                                    <li><a href="/your-profile/users-orders/">Orders</a></li>
                                <?php }?>
                                <?php if($is_host == 1) {?>
                                    <li><a href="/your-profile/account/">Account</a></li>
                                <?php }?>
                            </ul>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">My Wish Lists: <span id="listcount"><?php echo $rental_count;?></span></div>
                            <table>
                                <tbody>
                                <tr>
                                    <td style="width: 8%"></td>
                                    <td style="width: 80%">
                                        <div class="panel-body">
                                            <div class="list-group">
                                                <?php for($i=0; $i < $rental_count; $i++) { ?>
                                                    <div class="list-group-item" id=<?php echo 'Div_'.$results[$i]['id']?>>
                                                        <div class="row-content" style="width:100%">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <td width="40%">
                                                                        <img style="height: 200px; width: 300px" src=<?php echo '/wp-content/uploads/'.$pic_arr[$results[$i]['id']];?> alt="icon">
                                                                    </td>
                                                                    <td valign="top" width="52%">
                                                                        <ul style="list-style-type: none;margin-bottom: 0px;margin-left: 15px">
                                                                            <li>
                                                                                <h3 class="list-group-item-heading"><?php echo $results[$i]['post_title'];?></h3>
                                                                            </li>
                                                                            <li>
                                                                                <p class="list-group-item-text">
                                                                                    <span style="color: grey">
                                                                                        <?php
                                                                                            if ($results2[$i*$subArraySz+2]['meta_value'] !='')
                                                                                                echo $results2[$i*$subArraySz+2]['meta_value'].' ';
                                                                                            echo $results2[$i*$subArraySz+1]['meta_value'].', '.$results2[$i*$subArraySz+3]['meta_value']
                                                                                        ?>
                                                                                    </span>
                                                                                </p>
                                                                            </li>
                                                                            <li><p></p></li>
                                                                            <li><p></br></p></li>
                                                                            <li>
                                                                                <div id="testdiv"></div>
                                                                                <p class="list-group-item-text">
                                                                                    <span class="list-group-item-heading">
                                                                                        <?php
                                                                                            echo '$'.$results2[$i*$subArraySz+4]['meta_value'];
                                                                                            $currency = ($results2[$i*$subArraySz]['meta_value'] == 'Australia' ? 'AUD' : 'USD');
                                                                                            echo ' '.$currency;
                                                                                        ?>
                                                                                    </span><span style="color: grey"> per <?php echo $results2[$i*$subArraySz+5]['meta_value'];?></span></p>
                                                                            </li>
                                                                            <li>
                                                                                <a href=<?php echo $results[$i]['guid'];?> id="<?php echo"btnCheck".$i?>" class="btn btn-raised btn-default" style="margin-top:18px;margin-bottom: 0px">Check Price & Availability</a>
                                                                            </li>
                                                                        </ul>

                                                                    </td>
                                                                    <td valign="top" width="8%" style="text-align:right">
                                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick=
                                                                            <?php
                                                                                foreach($res as $x => $x_value) {
                                                                                    if ($x_value->{'fType'} == 'post' && $x_value->{'fValue'} == $results[$i]['id']) {
                                                                                        $uuid = $x_value->{'_id'};
                                                                                    }
                                                                                  }
                                                                                  echo '"RemoveFav('.$results[$i]['id'].',\''.$uuid.'\')"';
                                                                            ?>>×</button>
                                                                        <script>
                                                                            function RemoveFav(id,uuid) {
                                                                                var urldel = "/api/0/favorites/";
                                                                                jQuery.ajax({
                                                                                    url: urldel + uuid,
                                                                                    dataType: "json",
                                                                                    method: "DELETE",
                                                                                    success: function(result){
                                                                                        if (result.data.ok == 1) {
                                                                                            divID = "Div_"+id;
                                                                                            sepID = "Sep_"+id;
                                                                                            document.getElementById(divID).style.display = "none";
                                                                                            document.getElementById(sepID).style.display = "none";
                                                                                            var listcount = document.getElementById("listcount" ).innerHTML;
                                                                                            listcount = (--listcount) > 0 ? listcount : 0;
                                                                                            document.getElementById("listcount" ).innerHTML = listcount;
                                                                                        } else { //server error: failed to delete
                                                                                            alert("Oops, we are encountering some server issue. Please try it later.");
                                                                                        }
                                                                                    }});
                                                                            }
                                                                        </script>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-separator" id=<?php echo 'Sep_'.$results[$i]['id']?>></div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 12%"></td>
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
