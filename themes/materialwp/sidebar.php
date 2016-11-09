<?php

global $post, $wpdb;
if($post->post_type != "rental" && $post->post_type != "property" && $post->post_type != "business")
	return;

$query = 'SELECT meta_key,meta_value FROM `wp_postmeta` '.
	' WHERE post_id = '.$post->ID.' and (meta_key=\'property_rent\' or meta_key=\'property_rent_period\' or '.
	'meta_key=\'property_address_country\')'.
	'ORDER by meta_key asc';

$results = $wpdb->get_results( $query, ARRAY_A );
/* results
 * 0: property_address_country
 * 1: property_rent
 * 2: property_rent_period
 * */
$currency = ($results[0]['meta_key'] == 'property_address_country' && $results[0]['meta_value'] == 'Australia') ? "AUD" : "USD";
if ($results[1]['meta_key'] == 'property_rent')
	$rent = $results[1]['meta_value'];
if ($results[2]['meta_key'] == 'property_rent_period')
	$rent_period = $results[2]['meta_value'];

switch ($rent_period) {
	case 'day':
		$rent_days = 1;
		$deposit = 10000; //cents
		break;
	case 'week':
		$rent_days = 7;
		$deposit = 10000;
		break;
	case 'month':
		$rent_days = 30.31;
		$deposit = 20000;
		break;
	default:
		$rent_days = 1;
		$deposit = 20000;
		break;
}

?>

<button id="hidelogin" style="display: none" type="button" class="btn btn-primary" data-toggle="modal" data-target="#complete-dialog">Open dialog</button>
<div id="complete-dialog" class="modal fade" tabindex="-1">
	<div class="modal-dialog" style="width: 500px; height:400px;">
		<div class="modal-content">
			<div class="modal-header">
			</div>
			<div class="modal-body">
				<div class="login" id="wpuf-login-form">
					<?php
					$message = apply_filters( 'login_message', '' );
					if ( ! empty( $message ) ) {
						echo $message . "\n";
					}
					?>

					<?php WPUF_Login::init()->show_errors(); ?>
					<?php WPUF_Login::init()->show_messages(); ?>

					<form name="loginform" class="wpuf-login-form" id="loginform" action="<?php echo $action_url; ?>" method="post">
						<p>
							<label for="wpuf-user_login"><?php _e( 'Username', 'wpuf' ); ?></label>
							<input type="text" name="log" id="wpuf-user_login" style="border: 1px solid #c4c4c4; width: 100%; height: 55px; font-size:16px; font-family: Circular,Helvetica,Arial,sans-serif; padding: 10px 10px 10px 10px" placeholder="Username or Email Address" value="" />
						</p>
						<p>
							<label for="wpuf-user_pass"><?php _e( 'Password', 'wpuf' ); ?></label>
							<input type="password" name="pwd" id="wpuf-user_pass" style="border: 1px solid #c4c4c4; width: 100%; height: 55px; font-size:16px; font-family: Circular,Helvetica,Arial,sans-serif; padding: 10px 10px 10px 10px" placeholder="Password" value=""/>
						</p>

						<?php do_action( 'login_form' ); ?>

						<div class="form-group">
							<div class="checkbox">
								<label>
									<input name="rememberme" type="checkbox" id="wpuf-rememberme" value="forever" /> <?php echo esc_attr_e( 'Remember Me' ); ?>
								</label>
								<a href="http://localhost/wordpress/?page_id=154&action=lostpassword" style="float: right">Forgort Password?</a>
							</div>
						</div>

						<p class="submit">
							<?php $redirect = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" ?>
							<input type="submit" class="btn btn-primary" style="width: 100%; height: 55px" name="wp-submit" id="wp-submit" value="<?php esc_attr_e( 'Log In' ); ?>" onclick="AddAction()"/>
							<input type="hidden" name="redirect_to" value=<?php echo $redirect;?> />
							<input type="hidden" name="wpuf_login" value="true" />
							<input type="hidden" name="action" value="login" />
							<?php wp_nonce_field( 'wpuf_login_action' ); ?>
						</p>
					</form>
					<hr>
					<div>
						<table>
							<tbody>
								<tr>
									<td style="vertical-align: middle"><span style="font-size: 16px;">Don't have an account?</span></td>
									<td style="float: right"><a href="http://localhost/wordpress/?page_id=165" class="btn btn-default" style="border: 1px solid #009688; border-radius: 2px"><span style="color: #009688">Sign Up</span></a></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			</div>
		</div>
	</div>
</div>

<div id="secondary" class="widget-area col-md-4 col-lg-4" role="complementary">
	<div id="sidebar-1"class="panel panel-default">
		<div class="panel-heading"><h4><?php echo '$'.$rent.' '.$currency.' Per '.strtoupper(substr($rent_period,0,1)).substr($rent_period,1) ?></h4></div>
		<div class="panel-body">
			<div class="list-group">
				<div class="list-group-item">
					<table>
						<tbody>
							<tr>
								<th width="30%" style="text-align: center">Start Date</th>
								<th width="45%" style="text-align: center">Term</th>
								<th width="25%" style="text-align: center">Tenants</th>
							</tr>
							<tr>
								<td><input type="text" class="form-control" id="datepicker" placeholder="yyyy-mm-dd"></td>
								<td><div class="col-md-10" style="width: 100%">
										<select id="term" class="form-control">
											<option>3 months</option>
											<option>6 months</option>
											<option>12 months</option>
										</select>
									</div>
								</td>
								<td><div class="col-md-10" style="width: 100%">
										<select id="tenants" class="form-control">
											<option>1</option>
											<option>2</option>
											<option>3</option>
											<option>4</option>
											<option>5</option>
										</select>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
					<p class="text-muted">Please be noted the date you pick for moving in is in local time of destination.</p>
					<fieldset id="fs1">
						<a id="BtnApply" href="javascript:void(0)" class="btn btn-raised btn-default" onclick="Apply()" style="width: 100%; pointer-events: none"  >
							<span style="font-size: 15px">Apply</span>
						</a>
					</fieldset>
				</div>
				<hr>
				<div class="list-group-item">
					<fieldset id="fs2">
						<a id="BtnLike" href="javascript:void(0)" class="btn btn-raised btn-default" onclick="ChangeFav()" style="width: 100%" >
							<span style="font-size: 15px">Save to  Wish List</span>
						</a>
					</fieldset>
				</div>

			<div id="div1"></div>
			<script>
				var userID = <?php global $user_ID; echo $user_ID;?>;
				var postID = <?php global $post; echo $post->ID;?>;
				var urlstr = "/api/0/favorites/user/";
				var urlstr2 = "/api/0/worders/post/";
				var favID;
				var liked = false;
				var applied = false;
				var action;
				var stripeSkuID = "";
				var skuID = "";

				$( function() {
					$( "#datepicker" ).datepicker({ minDate:0});
					$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd");
				} );

				if (userID != 0) {
					jQuery.ajax({
						url: urlstr.concat(<?php global $user_ID; echo $user_ID;?>),
						dataType: "json",
						method: "Get",
						success: function (result) {
							for (var i = 0; i < result.data.length; i++) {
								if (result.data[i].fType == "post" && result.data[i].fValue == postID) {
									favID = result.data[i]._id;
									document.getElementById("BtnLike").className = 'btn btn-raised btn-danger';
									document.getElementById("BtnLike").innerHTML = "Saved to Wish List";
									liked = true;
								}
							}
							if (liked == false) {
								if (getCookie('action') == 'like') {
									ChangeFav();
								}
							}
						},
					})
						.always(function(data) {
							deleteCookie('action');
						});
					jQuery.ajax({
						url: urlstr2.concat(<?php global $post; echo $post->ID;?>),
						dataType: "json",
						method: "Get",
						success: function (result) {
							for (var i = 0; i < result.data.length; i++) {
								if (result.data[i].userID == userID) {
									document.getElementById("BtnApply").className = 'btn btn-raised btn-success';
									document.getElementById("BtnApply").innerHTML = "Check order status";
									applied = true;
								}
							}
							document.getElementById("BtnApply").style.pointerEvents = 'auto';
							if (applied == false) {
								if (getCookie('action') == 'apply') {
									Apply();
								}
							}
						}
					});
				}
				else {
					document.getElementById("BtnApply").style.pointerEvents = 'auto';
				}
				CheckAndCreateSku();
				function createCookie(name,value,days) {
					if (days) {
						var date = new Date();
						date.setTime(date.getTime()+(days*24*60*60*1000));
						var expires = "; expires="+date.toGMTString();
					}
					else var expires = "";
					document.cookie = name+"="+value+expires;
				}
				function getCookie(cname) {
					var name = cname + "=";
					var ca = document.cookie.split(';');
					for(var i = 0; i <ca.length; i++) {
						var c = ca[i];
						while (c.charAt(0)==' ') {
							c = c.substring(1);
						}
						if (c.indexOf(name) == 0) {
							return c.substring(name.length,c.length);
						}
					}
					return "";
				}
				function deleteCookie( name ) {
					document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
				}
				function AddAction() {
					createCookie('action', action,'0.1');

				}
				function CheckAndCreateSku() {
					var urlstr = "/api/0/wskus/post/";
					var sku;
					var currency = "<?php echo $currency ?>";
					var deposit  = <?php echo $deposit ?>;
					jQuery.ajax({
						url: urlstr.concat(postID),
						dataType: "json",
						method: "Get",
						success: function (result) {
							sku = result.data;
							if (sku == "") { /* Product&Sku do not exist. Create a new product and Sku*/
								jQuery.ajax({
									url: '/api/0/skus/pas',
									dataType: "json",
									method: "POST",
									data: {"product": {"name": 'post'.concat(postID), "shippable": false, "metadata": {"postID": postID, "stripeAccID":"acct_197bmLIw2qaoeMzL"}},"sku": {"currency": currency, "inventory": {"type": "finite", "quantity": 1}, "metadata": {"postID": postID,  "stripeAccID":"acct_197bmLIw2qaoeMzL"}, "price": deposit}},
									success: function (result) {
										stripeSkuID = result.data.stripeSkuID;
										skuID = result.data._id;
									}
								});
							}
							else { /* Product&Sku existed.*/
								stripeSkuID = result.data[0].stripeSkuID;
								skuID = result.data[0]._id;;
							}
						}
					});
				}
				function ChangeFav(){
					if (userID == 0) {
						document.getElementById("hidelogin").click();
						action = 'like';
					}
					else {
						if (document.getElementById("BtnLike").className == "btn btn-raised btn-default") //Add to Wish List
						{
							jQuery.ajax({
								url: "/api/0/favorites",
								dataType: "json",
								method: "POST",
								data: {"fType": "post", "fValue": postID,"userID" : userID},
								success: function(result){
									favID = result.data._id;
									document.getElementById("BtnLike").className = 'btn btn-raised btn-danger';
									document.getElementById("BtnLike").innerHTML = "Saved to Wish List";
								}});
						} else { //Remove from Wish List
							var urldel = "/api/0/favorites/";
							jQuery.ajax({
								url: urldel + favID,
								dataType: "json",
								method: "DELETE",
								success: function(result){
									if (result.data.ok == 1) {
										document.getElementById("BtnLike").className = 'btn btn-raised btn-default';
										document.getElementById("BtnLike").innerHTML = "Save to Wish List";
									} else { //server error: failed to delete
										alert("Oops, we are encountering some server issue. Please try it later.");
									}
								}});
						}
					}
				}
				function Apply() {
					if (document.getElementById("BtnApply").className == "btn btn-raised btn-default") { // Apply
						if (userID == 0) {
							document.getElementById("hidelogin").click();
							action = 'apply';
						} else { /* Create an order wrapper */
							if (stripeSkuID != "") {
								var date = document.getElementById("datepicker").value;
								if (date == '')
								{
									document.getElementById("datepicker").focus();
								} else {
									var datetime = date.concat(" 15:00:00 UTC");
									var term = document.getElementById("term").value;
									var tenants = document.getElementById("tenants").value;
									alert(datetime);
									AddOrder(skuID, stripeSkuID, datetime, term, tenants);
								}
							}
						}
					} else { //Already applied, go checking order status
						window.location.replace("/wordpress/?page_id=140");
					}
				}
				function AddOrder(skuID, stripeSkuID, startDate, term, numTenant) { /* Create an order wrapper */
					var currency = "<?php echo $currency ?>";
					if (stripeSkuID != null) {
						jQuery.ajax({
							url: '/api/0/worders',
							dataType: "json",
							method: "POST",
							data: {
								"postID": postID,
								"postAuthorID": userID,
								"currency": currency,
								"userID": userID,
								"skuID": skuID,
								"stripeAccID": "acct_197bmLIw2qaoeMzL",
								"appStatus": "Waiting for approval",
								"startDate": startDate,
								term: term,
								numTenant: numTenant
							},
							success: function (result) {
								document.getElementById("BtnApply").className = 'btn btn-raised btn-success';
								document.getElementById("BtnApply").innerHTML = "Check order status";
							}
						});
					}
				}
			</script>
		</div>
	</div>

	<script>
		var distance = $('#secondary').offset().top;

		$(window).scroll(function() {
			if ( $(window).scrollTop() >= distance ) {
				document.getElementById("sidebar-1").style.position = "fixed";
				document.getElementById("sidebar-1").style.top = "0";
				document.getElementById("sidebar-1").style.zIndex = "1000";
				document.getElementById("sidebar-1").style.width = "350px";
			}
			if ( $(window).scrollTop() < distance ) {
				document.getElementById("sidebar-1").style.position = "absolute";
				document.getElementById("sidebar-1").style.width = "350px";
			}
		});
	</script>

	
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	
</div><!-- #secondary -->


