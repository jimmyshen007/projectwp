<?php

global $post, $wpdb, $user_ID;
if($post->post_type != "rental" && $post->post_type != "property" && $post->post_type != "business")
	return;

$query = 'SELECT meta_key,meta_value FROM `wp_postmeta` '.
	' WHERE post_id = '.$post->ID.' and (meta_key=\'property_rent\' or meta_key=\'property_rent_period\' or '.
	'meta_key=\'property_address_country\' or meta_key= \'short_long_term\')'.
	'ORDER by meta_key asc';

$query2 = 'SELECT post_author, user_email FROM wp_posts, wp_users where wp_posts.post_author = wp_users.ID and wp_posts.ID='. $post->ID;


$results = $wpdb->get_results( $query, ARRAY_A );
/* results
 * 0: property_address_country
 * 1: property_rent
 * 2: property_rent_period
 * 3: short_long_term
 * */

$currency = ($results[0]['meta_key'] == 'property_address_country' && $results[0]['meta_value'] == 'Australia') ? "AUD" : "USD";
if ($results[1]['meta_key'] == 'property_rent')
	$rent = $results[1]['meta_value'];
if ($results[2]['meta_key'] == 'property_rent_period')
	$rent_period = $results[2]['meta_value'];

$isShortTerm = ($results[3]['meta_key'] == 'short_long_term' && $results[3]['meta_value'] == 'short') ? true : false;

switch ($rent_period) {
	case 'day':
		$rent_days = 1;
		break;
	case 'week':
		$rent_days = 7;
		break;
	case 'month':
		$rent_days = 30;
		break;
	default:
		$rent_days = 1;

		break;
}
/* Get the rental fee for one day */
$rent_per_day = round($rent / $rent_days);

$cutoff = 30;
	
/* Get the deposit amount for long term rent, i.e. 30 days of rentals */
if($rent_period == 'month' && $cutoff == 30)
	$deposit = $rent;
else
	$deposit = $rent_per_day * $cutoff;

$service_rate = 0.06;

$service_fee = ($deposit) * $service_rate;
/*
if($isShortTerm == true) {
	$rest_fee = $rent; 
}
else {
	$total_fee = $deposit + $service_fee*100;
	$preorder_depoist = round($total_fee*0.1, 0);
	$rest_fee = $total_fee - $preorder_depoist;
}*/

$results2 = $wpdb->get_results( $query2, ARRAY_A );

$authorID = $results2[0]['post_author'];
$user_email = $results2[0]['user_email'];

$pp_value = get_user_meta( $user_ID, "Passport", false);
$pp_exiry_value = get_user_meta( $user_ID, "passport_expire_date", false);

if(count($pp_value) > 0 && count($pp_exiry_value)) {
	$isVerified = 1;
} else {
	$isVerified = 0;
}

?>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

<button id="hidelogin" style="display: none" type="button" class="btn btn-primary" data-toggle="modal" data-target="#login-dialog">Open dialog</button>
<div id="login-dialog" class="modal fade" tabindex="-1">
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
									<td style="float: right"><a href="/your-profile/" class="btn btn-default" style="border: 1px solid #009688; border-radius: 2px"><span style="color: #009688">Sign Up</span></a></td>
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

<button id="uploadDocBtn" style="display: none" type="button" class="btn btn-primary" data-toggle="modal" data-target="#upload-dialog">Open dialog</button>
<script type="text/javascript">
	// Expect input as y-m-d
	function isValidDate(s) {
		var bits = s.split('-');
		var d = new Date(bits[1], bits[1] - 1, bits[2]);
		return d && (d.getMonth() + 1) == bits[1];
	}

	$( function() {
		var dateFormat = "yy-mm-dd";
		$( "#pp_expiry_date" ).datepicker({
				dateFormat: dateFormat,
				minDate: 0
			})
			.on( "change", function() {
				var pp_expiry = document.getElementById("pp_expiry_date").value;
				if (pp_expiry != "") {
					if (!isValidDate(pp_expiry)) {
						document.getElementById("pp_expiry_date").value = "";
					}
				}
			});
	} );

	function isValidForm()
	{
		var pp_expiry = document.getElementById("pp_expiry_date").value;

		if(document.getElementById("passport").value == '')
		{
			document.getElementById("errorTxt").innerHTML="Error: Please upload your passport";
			document.getElementById("errorTxt").style.visibility = "visible";
			return false;
		}
		else
		{
			if (pp_expiry == "") {
				document.getElementById("errorTxt").innerHTML="Error: Please provide expiry date of your passport.";
				document.getElementById("errorTxt").style.visibility = "visible";
				return false;
			} else if (!isValidDate(pp_expiry)) {
				document.getElementById("errorTxt").innerHTML="Error: Invalid expiry date of passport.";
				document.getElementById("errorTxt").style.visibility = "visible";
				return false;
			}
		}

		document.getElementById("errorTxt").style.visibility = "hidden";
		return true;
	}

	$(function() {
		$('#passport').change(function () {

			var file = this.files[0];
			var name = file.name;
			var size = file.size;
			var type = file.type;

			if (file.name.length < 1) {

			}
			else if (file.size > 500000) {
				document.getElementById("errorTxt").innerHTML="File too large. Please make sure your file size is less than 500 KB.";
				document.getElementById("errorTxt").style.visibility = "visible";
				return false;
			}
			else if (file.type != 'image/png' && file.type != 'image/jpg' && file.type != 'image/gif' && file.type != 'image/jpeg') {
				document.getElementById("errorTxt").innerHTML="Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
				document.getElementById("errorTxt").style.visibility = "visible";
				return false;
			}
			else {
				clearError();
				var $form = $('#upload-form');
				$form.submit(function(event) {
					if (!isValidForm())
						return false;


					// Disable the submit button to prevent repeated clicks:
					/*$form.find('.submit').prop('disabled', true);
					 document.getElementById("BtnSubmit").disabled = true;

					 jQuery.ajax({
					 url: '/wp-content/themes/materialwp/userinfo.php',
					 dataType: "json",
					 method: "POST",
					 data: {"action": "uploadID", "form": $form},
					 success: function (result) {
					 alert(result['a']);
					 }
					 });*/
				});
			}
		});
	});

	function  clearError() {
		document.getElementById("errorTxt").style.visibility = "hidden";
	}

</script>
<div id="upload-dialog" class="modal fade" tabindex="-1">
	<div class="modal-dialog" style="width: 500px; height:400px;">
		<div class="modal-content">
			<div class="modal-body" style="padding: 0">
				<div class="panel panel-default">
					<div class="panel-heading"><h4>ID Verification</h4></div>
					<div class="panel-body">
						<p>Please upload your passport so we can verify your identification.</p>
						<form action="/wp-content/themes/materialwp/userinfo.php" id="upload-form" method="post" enctype="multipart/form-data">
						<table style="margin-bottom: 3px">
							<tbody>
							<tr>
								<td style="width: 27%"></td>
								<td style="width: 73%">
									<div class="fileinput fileinput-new" data-provides="fileinput">
										<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
										<div>
											<span class="btn btn-raised btn-default btn-file" style="width: 200px;border-radius: 3px;border: 1px solid #009688;"><span class="fileinput-new" style="color: #009688">Upload Passport</span><span class="fileinput-exists" style="color: #009688">Change</span><input type="file" name="passport" id="passport"></span>
											<a href="#" class="btn btn-raised btn-default fileinput-exists" data-dismiss="fileinput" style="border-radius: 3px;border: 1px solid #ff5722;"><span style="color: #ff5722" onclick="clearError()">Remove</span></a>
										</div>
									</div>
								</td>
							</tr>
							<tr>
								<td style="width: 25%"></td>
								<td style="width: 75%">
									<label class="control-label" for="pp_expiry_date">Passport Expiry Date</label>
									<input class="form-control input-lg"  style="width: 200px;" type="text" placeholder="yyyy-mm-dd" name="pp_expiry_date" id="pp_expiry_date"/>
									<input type="hidden" name="action" value="uploadID"/>
									<input type="hidden" name="userID" value="<?php echo $user_ID;?>"/>
									<input type="hidden" name="prevURL" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
								</td>
							</tr>
							</tbody>
						</table>
						<div>
							<p id="errorTxt" class="text-danger" style="visibility: hidden">&nbsp;</p>
						</div>
						<table style="margin-top: -10px">
							<tbody>
							<tr>
								<td width="33%"></td>
								<td width="37%" ><input id="BtnSubmit" type="submit" style="width: 150px;height: 40px; border-radius: 3px;" class="btn btn-primary" name="submit" value="submit"/></td>
								<td width="30%"></td>
							</tr>
							</tbody>
						</table>
						</form>
						<hr>
						<table style="margin-top: 5px">
							<tbody>
							<tr>
								<td width="70%">
									<p style="margin-top: 18px">You can also provide your student ID optionally in</p>
								</td>
								<td width="30%"><a href="/your-profile/#Verification" class="btn btn-default" style="border-radius: 3px;border: 1px solid #009688;"><span style="color: #009688">Profile</span></a></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<button id="chargeform" style="display: none" type="button" class="btn btn-primary" data-toggle="modal" data-target="#charge-dialog">Open dialog</button>
<div id="charge-dialog" class="modal fade" tabindex="-1">
	<div class="modal-dialog" style="width: 500px; height:400px;">
		<div class="modal-content">
			<div class="modal-header">
			</div>
			<div class="modal-body">
				<form action="" method="POST" id="payment-form">
					<table>
						<tr>
							<td class="text-muted" width="33%">Start Date</td>
							<?php if ($isShortTerm == true) {?>
								<td class="text-muted" width="33%">End Date</td>
							<?php }else { ?>
								<td class="text-muted" width="33%">Term</td>
							<?php } ?>
							<td class="text-muted" width="33%">Tenants</td>
						</tr>
						<tr style="border-bottom:1pt solid #cccccc;">
							<td width="33%" style="padding-bottom: 5px;padding-top: 5px"><span id="startDatelabel" style="font-size: 16px"></td>
							<?php if ($isShortTerm == true) {?>
								<td width="33%"><span style="font-size: 16px" id="endDatelabel"></span></td>
							<?php }else { ?>
								<td width="33%"><span style="font-size: 16px" id="termlabel"></span></td>
							<?php } ?>
							<td width="33%"><span style="font-size: 16px" id="tenantslabel"></span></td>
						</tr>
						<tr>
							<?php if ($isShortTerm == true) {?>
								<td class="text-muted" style="padding-bottom: 5px;padding-top: 5px"><span id="daysNumlabel"></span></td>
							<?php }else { ?>
								<td class="text-muted">Deposit</td>
							<?php } ?>
							<td class="text-muted">Service Fee</td>
							<td class="text-muted">Total</td>
						</tr>
						<tr>
							<td><span id="pricelabel" style="padding-bottom: 5px;padding-top: 5px;font-size: 16px"></span></td>
							<td><span style="font-size: 16px" id="serviceFeelabel"></span></td>
							<td><span style="font-size: 16px" id="totallabel"></span></td>
						</tr>
					</table>
					<p class="text-muted" style="margin-bottom: 5px">To apply, you need to pay <strong style="color: black">$<span id="preorderDepositlabel" style="font-size: 18px"></span><?php echo ' '.$currency;?></strong> of service fee first. Meanwhile, we will send a request to the owner for approval. If the owner didn't approve your application within 24 hours, we will refund you $<span id="preorderDepositlabel2"></span><?php echo ' '.$currency;?> immediately. </p>
					<table style="width: 90%">
						<tbody>
						<tr style="height: 50px">
							<td style="width: 30%">
								<label class="control-label" for="name" style="margin-bottom:0">Cardholder Name</label>
							</td>
							<td style="width: 70%">
								<input class="form-control input-lg" width="80%" type="text" id="name" size="20" data-stripe="name" placeholder="cardholder name">
							</td>
						</tr>
						<tr style="height: 50px">
							<td style="width: 30%">
								<label class="control-label" for="cardnum" style="margin-bottom:0">Card Number<span>&nbsp;*</span></label>
							</td>
							<td style="width: 70%">
								<input class="form-control input-lg" width="80%" type="text" id="cardnum" size="20" data-stripe="number" placeholder="credit card number">
							</td>
						</tr>
						<tr style="height: 50px">
							<td style="width: 30%">
								<label class="control-label" for="expire" style="margin-bottom: 0">Expiry (MM/YY)<span>&nbsp;*</span></label>
							</td>
							<td style="width: 70%">
								<table style="width: 28%; margin-bottom: 0">
									<tr>
										<td >
											<input class="form-control input-lg" style="width: 30px" type="text" size="2" data-stripe="exp_month" placeholder="MM">
										</td>
										<td><span> / </span></td>
										<td>
											<input class="form-control input-lg" style="width: 30px" type="text" size="2" data-stripe="exp_year" placeholder="YY">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr style="height: 50px">
							<td style="width: 30%">
								<label class="control-label" for="cvc" style="margin-bottom: 0">CVC<span>&nbsp;*</span></label>
							</td>
							<td style="width: 70%">
								<input class="form-control input-lg" style="width: 70px" type="text" id="cvc" size="4" data-stripe="cvc" placeholder="CVC">
							</td>
						</tr>
					</table>
					<div style="margin-top:-15px;margin-bottom: 10px"><span>*&nbsp;Required fields</span></div>
					<div id="TxtPolicyReminder"><label><strong style="color: #ffb400">Before booking, agree to the House Rules and Terms.</strong></label></div>
					<table>
						<tr>
							<td>
								<div class="checkbox" style="margin-top: -8px; margin-right: 10px">
									<label><input id="policyCheckbox" type="checkbox" onclick="policyCheck()"></label>
								</div>
							</td>
							<td><span>I agree to the House Rules, Cancellation Policy, and to the Guest Refund Policy. I also agree to pay the total amount shown.</span></td>
						</tr>
					</table>
					<div><span class="payment-errors text-danger"></span></div>
					<div><table>
							<tr>
								<td>
									<input id="BtnPay" disabled="disabled" type="submit" class="btn btn-raised btn-info" value="Confirm & Pay">
								</td>
								<td width="30%"></td>
								<td>
								<td><button id="BtnDismissCharge" type="button" class="btn btn-primary" data-dismiss="modal">Dismiss</button></td>
								</td>
							</tr>
						</table>

					</div>
				</form>
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
								<?php if ($isShortTerm == false) {?>
								<th width="45%" style="text-align: center">Term</th>
								<?php } else { ?>
								<th width="45%" style="text-align: center">End Date</th>
								<?php } ?>
								<th width="25%" style="text-align: center">Tenants</th>
							</tr>
							<tr>
								<td><input type="text" class="form-control" id="datepickerStart" placeholder="yyyy-mm-dd" onChange="CheckLogin()"></td>
								<?php if ($isShortTerm == false) {?>
								<td><div class="col-md-10" style="width: 100%">
										<select id="term" class="form-control" onChange="CheckLogin()">
											<option>3 months</option>
											<option>6 months</option>
											<option>12 months</option>
										</select>
									</div>
								</td>
								<?php } else { ?>
									<td style="padding-left: 20px;padding-right: 20px">
										<input type="text" class="form-control" id="datepickerEnd" placeholder="yyyy-mm-dd" onChange="CheckLogin()">
									</td>
								<?php } ?>
								<td><div class="col-md-10" style="width: 100%">
										<select id="tenants" class="form-control" onChange="CheckLogin()">
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
					<p class="text-muted" id="note">Please be noted the date you pick for moving in is in local time of destination.</p>
						<div id="summaryDiv" style="display: none">
							<table  style="margin-top:30px;">
								<tbody style="width: 100%">
								<tr style="border-bottom:1pt solid #cccccc;">
									<?php if ($isShortTerm == true) {?>
										<td align="left" style="padding-bottom: 8px;padding-top:8px"><span id="daysnumlabel"></span></td>
										<td align="right" style="padding-right: 10px;padding-bottom: 8px;padding-top:8px"><span id="dayspricelabel"></span></td>
									<?php }else { ?>
										<td align="left" style="padding-bottom: 8px;padding-top:8px">Deposit</td>
										<td align="right" style="padding-right: 10px;padding-bottom: 8px;padding-top:8px"><span id="depositamountlabel"></span></td>
									<?php } ?>
								</tr>
								<tr style="border-bottom:1pt solid #cccccc;">
									<td align="left" style="padding-bottom: 8px;padding-top:8px">Service Fee</td>
									<td align="right" style="padding-right: 10px;padding-bottom: 8px;padding-top:8px"><span id="servicefeelabel"></span></td>
								</tr>
								<tr style="border-bottom:1pt solid #cccccc;">
									<td align="left" style="padding-bottom: 8px;padding-top:8px">Total</td>
									<td align="right" style="padding-right: 10px;padding-bottom: 8px;padding-top:8px"><span id="totalpricelabel"></span></td>
								</tr>
								</tbody>
							</table>
						</div>
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
			<script type="text/javascript">
				var userID = <?php global $user_ID; echo $user_ID;?>;
				var postID = <?php global $post; echo $post->ID;?>;
				var authorUserID = <?php echo $authorID;?>;
				var urlstr = "/api/0/favorites/user/";
				var urlstr2 = "/api/0/worders/post/";
				var favID = "";
				var liked = false;
				var applied = false;
				var action;
				var stripeProductID = "";
				var proID = "";
				var stripeSkuID = "";
				var skuID = "";
				var stripeAccID = "";
				var datetimeStart = "";
				var datetimeEnd = "";
				var term = 0;
				var tenants = 0;

				// Expect input as y-m-d
				function isValidDate(s) {
					var bits = s.split('-');
					var d = new Date(bits[1], bits[1] - 1, bits[2]);
					return d && (d.getMonth() + 1) == bits[1];
				}

				$( function() {
					var dateFormat = "yy-mm-dd";
					var from = $( "#datepickerStart" )
							.datepicker({
								<?php if($isShortTerm == true) {?>
								numberOfMonths: 2,
								<?php } ?>
								dateFormat: dateFormat,
								minDate: 0,
								onClose: function(dateText, inst) {
									<?php if($isShortTerm == true) {?>
									if(dateText != "")
										document.getElementById("datepickerEnd").focus();
									<?php } ?>
								}
							})
							.on( "change", function() {
								var checkFrom = document.getElementById("datepickerStart").value;
								if(checkFrom != "") {
									if(!isValidDate(checkFrom)) {
										document.getElementById("datepickerStart").value = "";
									}
								}
								<?php if($isShortTerm == true) {?>
								var checkTo = document.getElementById("datepickerEnd").value;
								if(checkTo != "") {
									if(!isValidDate(checkTo)) {
										document.getElementById("datepickerEnd").value = "";
									}
								}
								var date2 = from.datepicker('getDate');
								date2.setDate(date2.getDate()+1);
								to.datepicker( "option", "minDate", date2 );
								if(to.datepicker('getDate') == null) {
									to.datepicker('setDate', date2);
									var dateStart = document.getElementById("datepickerStart").value;
									var dateEnd = document.getElementById("datepickerEnd").value;
									term = getInterval(dateStart, dateEnd);
									if(term > 0) {
										var rent_unit = <?php echo $rent_per_day; ?>;
										var rent = rent_unit * term;
										var service_fee = Math.round(rent * <?php echo $service_rate;?>);
										var total_fee = rent + service_fee;
										document.getElementById("daysnumlabel").innerHTML = '$'+rent_unit + " x " +term + ((term > 1)?" nights":" night");
										document.getElementById("dayspricelabel").innerHTML = '$'+rent;
										document.getElementById("servicefeelabel").innerHTML = '$'+service_fee;
										document.getElementById("totalpricelabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
										document.getElementById("summaryDiv").style.display = 'block';
									}
								}
								else {
									var dateStart = document.getElementById("datepickerStart").value;
									var dateEnd = document.getElementById("datepickerEnd").value;
									term = getInterval(dateStart, dateEnd);
									if(term > 0) {
										var rent_unit = <?php echo $rent; ?>;
										var rent = rent_unit * term;
										var service_fee = Math.round(rent * <?php echo $service_rate;?>);
										var total_fee = rent + service_fee;
										document.getElementById("daysnumlabel").innerHTML = '$'+rent_unit + " x " +term + ((term > 1)?" nights":" night");
										document.getElementById("dayspricelabel").innerHTML = '$'+rent;
										document.getElementById("servicefeelabel").innerHTML = '$'+service_fee;
										document.getElementById("totalpricelabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
										document.getElementById("summaryDiv").style.display = 'block';
									}
								}
								<?php }else { ?>
								var deposit = <?php echo $deposit; ?>;
								var service_fee = <?php echo $service_fee; ?>;
								var total_fee = deposit + service_fee;
								document.getElementById("depositamountlabel").innerHTML = '$'+deposit;
								document.getElementById("servicefeelabel").innerHTML = '$'+service_fee;
								document.getElementById("totalpricelabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
								document.getElementById("summaryDiv").style.display = 'block';
								<?php } ?>
							});
					<?php if($isShortTerm == true) {?>
					var	to = $( "#datepickerEnd" ).datepicker({
								numberOfMonths: 2,
								dateFormat: dateFormat,
								minDate : 0
							})
							.on( "change", function() {
								var checkFrom = document.getElementById("datepickerStart").value;
								if(checkFrom != "") {
									if(!isValidDate(checkFrom)) {
										document.getElementById("datepickerStart").value = "";
									}
								}
								var checkTo = document.getElementById("datepickerEnd").value;
								if(checkTo != "") {
									if(!isValidDate(checkTo)) {
										document.getElementById("datepickerEnd").value = "";
									}
								}

								var date2 = to.datepicker('getDate');
								date2.setDate(date2.getDate()-1);
								from.datepicker( "option", "maxDate", date2 );
								if(from.datepicker('getDate') != null) {
									var dateStart = document.getElementById("datepickerStart").value;
									var dateEnd = document.getElementById("datepickerEnd").value;
									term = getInterval(dateStart, dateEnd);
									if(term > 0) {
										var rent_unit = <?php echo $rent_per_day; ?>;
										var rent = rent_unit * term;
										var service_fee = Math.round(rent * <?php echo $service_rate;?>);
										var total_fee = rent + service_fee;
										document.getElementById("daysnumlabel").innerHTML = '$'+rent_unit + " x " +term + ((term > 1)?" nights":" night");
										document.getElementById("dayspricelabel").innerHTML = '$'+rent;
										document.getElementById("servicefeelabel").innerHTML = '$'+service_fee;
										document.getElementById("totalpricelabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
										document.getElementById("summaryDiv").style.display = 'block';
									}

								}
							});
					<?php } ?>
				});

				if (userID != 0) {
					jQuery.ajax({
						url: urlstr.concat(userID),
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
						url: urlstr2.concat(postID),
						dataType: "json",
						method: "Get",
						success: function (result) {
							for (var i = 0; i < result.data.length; i++) {
								if (result.data[i].userID == userID) {
									var startDate = result.data[i].startDate.substring(0,10);
									term = result.data[i].term;
									numTenant = result.data[i].numTenant;
									document.getElementById("datepickerStart").disabled = true;
									document.getElementById("datepickerStart").value = startDate;
									<?php if($isShortTerm == true) { ?>
									var endDate  = new Date(2000, 0, 1);
									var startdate = new Date(startDate.concat("T15:00:00Z"));
									var one_day = 1000*60*60*24;
									var str = "rental";

									endDate.setTime(startdate.getTime() + term * one_day);
									document.getElementById("datepickerEnd").disabled = true;
									document.getElementById("datepickerEnd").value = endDate.toISOString().substring(0,10);

									if(term > 0) {
										var rent_unit = <?php echo $rent_per_day; ?>;
										var rent = rent_unit * term;
										var service_fee = Math.round(rent * <?php echo $service_rate;?>);
										var total_fee = rent + service_fee;
										document.getElementById("daysnumlabel").innerHTML = '$'+rent_unit + " x " +term + ((term > 1)?" nights":" night");
										document.getElementById("dayspricelabel").innerHTML = '$'+rent;
										document.getElementById("servicefeelabel").innerHTML = '$'+service_fee;
										document.getElementById("totalpricelabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
										document.getElementById("summaryDiv").style.display = 'block';
									}
									<?php } else { ?>
									var str = "deposit";

									document.getElementById("term").disabled = true;
									if(term > 85 && term < 95) // =91
										document.getElementById("term").value = "3 months";
									else if(term > 175 && term < 185) // =182
										document.getElementById("term").value = "6 months";
									else if(term > 360 && term < 370) // =365
										document.getElementById("term").value = "12 months";
									var deposit = <?php echo $deposit; ?>;
									var service_fee = <?php echo $service_fee; ?>;
									var total_fee = deposit + service_fee;
									document.getElementById("depositamountlabel").innerHTML = '$'+deposit;
									document.getElementById("servicefeelabel").innerHTML = '$'+service_fee;
									document.getElementById("totalpricelabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
									document.getElementById("summaryDiv").style.display = 'block';
									<?php } ?>
									document.getElementById("tenants").disabled = true;
									document.getElementById("tenants").value = numTenant;
									//document.getElementById("BtnApply").className = 'btn btn-raised btn-success';
									document.getElementById("BtnApply").innerHTML = "Check order status";

									switch(result.data[i].appStatus) {
										case "Waiting for approval":
											document.getElementById("note").innerHTML = "You've applied for the" +
												" property successfully. The landlord will respond to your application"+
												" within 24 hours. If your application gets approved, you will need to"+
												" pay the " + str + " within 48 hours to complete the application.";
											break;
										case "Approved":
											document.getElementById("note").innerHTML = "The landlord" +
												" has approved your applicaiton. Please click the button below to" +
												" complete this application";
											break;
										case "Completed":
											document.getElementById("note").innerHTML = "Congratulations! You've" +
												" successfully booked the property. Relax now and get ready to check" +
												" in on " + startDate + ".";
											break;
									}

									applied = true;
								}
							}
							document.getElementById("BtnApply").style.pointerEvents = 'auto';
							if (applied == false) {
								if (getCookie('action') == 'apply') {
									//Apply(); TODO: need to pass start_date, term, numTenant via Cookie
								}
							}
						}
					})
						.always(function(data) {
						deleteCookie('action');
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
				function CheckLogin() {
					if (userID == 0) {
						document.getElementById("hidelogin").click();
					}
				}
				function CheckAndCreateSku() {
					var urlstr3 = "/api/0/wskus/post/";
					var urlstr4 = "/api/0/accounts/user/";
					var sku;
					var product;
					var currency = "<?php echo $currency ?>";
					var feePerDay  = <?php echo $rent_per_day * 100 ?>;
					var email = "<?php echo $user_email ?>";
					var country = (currency == "AUD") ? "AU" : "US";
					jQuery.ajax({
						url: urlstr4.concat(authorUserID),
						dataType: "json",
						method: "Get",
						success: function (result) {
							stripeAccID = result.data[0].id;
							if ( typeof stripeAccID != 'undefined' && stripeAccID != "") { // Stripe acc exists.
								jQuery.ajax({ // Check if product and sku existed
									url: urlstr3.concat(postID),
									dataType: "json",
									method: "Get",
									success: function (result) {
										sku = result.data;
										if (sku == "") { /* Product&Sku do not exist. Create a new product and Sku*/
											jQuery.ajax({
												url: '/api/0/skus/pas',
												dataType: "json",
												method: "POST",
												data: {"product": {"name": 'post'.concat(postID), "shippable": false,
													"metadata": {"postID": postID, "stripeAccID":stripeAccID}},
													"sku": {"currency": currency,
														"inventory": {"type": "infinite"},
														"metadata": {"postID": postID,  "stripeAccID":stripeAccID},
														"price": feePerDay}},
												success: function (result) {
													stripeSkuID = result.data.stripeSkuID;
													skuID = result.data._id;
												}
											});
										}
										else { /* Product&Sku existed.*/
											stripeSkuID = result.data[0].stripeSkuID;
											skuID = result.data[0]._id;
										}
									}
								});
							}
							else { // Stripe acc doesn't exist yet. Create account and prod/sku
								jQuery.ajax({
									url: '/api/0/accounts',
									dataType: "json",
									method: "POST",
									data: {"country": country, "managed": true, "email": email, "metadata": {"userID": authorUserID}},
									success: function (result) {
										stripeAccID = result.data.stripeAccID;
										jQuery.ajax({
											url: '/api/0/skus/pas',
											dataType: "json",
											method: "POST",
											data: {"product": {"name": 'post'.concat(postID),
														"shippable": false,
														"metadata": {"postID": postID, "stripeAccID":stripeAccID}},
												"sku": {"currency": currency,
														"inventory": {"type": "infinite"},
														"metadata": {"postID": postID,  "stripeAccID":stripeAccID},
														"price": feePerDay}},
											success: function (result) {
												stripeSkuID = result.data.stripeSkuID;
												skuID = result.data._id;
											}
										});
									}
								});
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
						if (document.getElementById("BtnLike").className == "btn btn-raised btn-default" && liked == false) //Add to Wish List
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
									liked = true;
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
										liked = false;
									} else { //server error: failed to delete
										alert("Oops, we are encountering some server issue. Please try it later.");
									}
								}});
						}
					}
				}
				function Apply() {
					document.getElementById("errorTxt").style.visibility = "hidden";

					if (document.getElementById("BtnApply").className == "btn btn-raised btn-default" && applied == false) { // Apply
						if (userID == 0) {
							document.getElementById("hidelogin").click();
							action = 'apply';
						} else { /* Create an order wrapper */
							var isVerified = "<?php echo $isVerified ?>";
							if (isVerified == "0") {
								document.getElementById("uploadDocBtn").click();
							} else {
								<?php if($isShortTerm == true) {?>
								var dateStart = document.getElementById("datepickerStart").value;
								var dateEnd = document.getElementById("datepickerEnd").value;
								if (dateStart == '')
								{
									document.getElementById("datepickerStart").focus();
								}
								else if (dateEnd == '')
								{
									document.getElementById("datepickerEnd").focus();
								}
								else
								{
									datetimeStart = dateStart.concat(" 15:00:00 UTC");
									datetimeEnd = dateEnd.concat(" 15:00:00 UTC");

									term = getInterval(dateStart, dateEnd);
									document.getElementById("endDatelabel").innerHTML = dateEnd;
									var rent_unit = <?php echo $rent_per_day; ?>;
									var rent = rent_unit * term;
									var service_fee = Math.round(rent * <?php echo $service_rate;?>);
									var total_fee = rent + service_fee;
									document.getElementById("daysNumlabel").innerHTML = '$'+rent_unit + " x " +term + ((term > 1)?" nights":" night");
									document.getElementById("pricelabel").innerHTML = '$'+rent;
									document.getElementById("serviceFeelabel").innerHTML = '$'+service_fee;
									document.getElementById("totallabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
									document.getElementById("preorderDepositlabel").innerHTML = service_fee;
									document.getElementById("preorderDepositlabel2").innerHTML = service_fee;
									
									tenants = document.getElementById("tenants").value;
									document.getElementById("startDatelabel").innerHTML = dateStart;
									document.getElementById("tenantslabel").innerHTML = tenants;
									if (datetimeStart != "" && term > 0 && tenants > 0)
										document.getElementById("chargeform").click();
								}
								<?php } else {?>
								if (stripeSkuID != "") {
									var dateStart = document.getElementById("datepickerStart").value;
									if (dateStart == '')
									{
										document.getElementById("datepickerStart").focus();
									}
									else
									{
										datetimeStart = dateStart.concat(" 15:00:00 UTC");
										var deposit = <?php echo $deposit; ?>;
										var service_fee = <?php echo $service_fee; ?>;
										var total_fee = deposit + service_fee;
										if (document.getElementById("term").value == "3 months")
											term = 91;
										else if(document.getElementById("term").value == "6 months")
											term = 182;
										else if(document.getElementById("term").value == "12 months")
											term = 365;
										document.getElementById("termlabel").innerHTML = document.getElementById("term").value;
										document.getElementById("pricelabel").innerHTML = '$'+deposit;
										document.getElementById("serviceFeelabel").innerHTML = '$'+service_fee;
										document.getElementById("totallabel").innerHTML = '$' + total_fee + ' <?php echo $currency;?>';
										document.getElementById("preorderDepositlabel").innerHTML = service_fee;
										document.getElementById("preorderDepositlabel2").innerHTML = service_fee;
										tenants = document.getElementById("tenants").value;
										document.getElementById("startDatelabel").innerHTML = dateStart;
										document.getElementById("tenantslabel").innerHTML = tenants;
										if (datetimeStart != "" && term > 0 && tenants > 0)
											document.getElementById("chargeform").click();
									}
								}
								<?php }?>
							}
						}
					} else { //Already applied, go checking order status
						window.location.replace("/your-profile/users-orders/");
					}
				}
				Stripe.setPublishableKey('pk_test_JJCG8Qu51sXzY3sIRLhfU2sf');

				$(function() {
					var $form = $('#payment-form');
					$form.submit(function(event) {
						// Disable the submit button to prevent repeated clicks:
						$form.find('.submit').prop('disabled', true);
						document.getElementById("BtnPay").disabled = true;

						// Request a token from Stripe:
						Stripe.card.createToken($form, stripeResponseHandler);

						// Prevent the form from being submitted:
						return false;
					});
				});

				function stripeResponseHandler(status, response) {
					// Grab the form:
					var $form = $('#payment-form');


					if (response.error) { // Problem!

						// Show the errors on the form:
						$form.find('.payment-errors').text(response.error.message);
						$form.find('.submit').prop('disabled', false); // Re-enable submission
						document.getElementById("BtnPay").disabled = false;

					} else { // Token was created!

						$form.find('.payment-errors').text("");

						// Get the token ID:
						var token = response.id;

						// Insert the token ID into the form so it gets submitted to the server:
						//$form.append($('<input type="hidden" name="stripeToken">').val(token));

						var desc = "Apply deposit from user ";
						desc = desc.concat(userID).concat(" for post ").concat(postID);
						var currency = "<?php echo $currency; ?>";
						var amount = "";

						<?php if($isShortTerm == true) {?>
						var dateStart = document.getElementById("datepickerStart").value;
						var dateEnd = document.getElementById("datepickerEnd").value;
						datetimeStart = dateStart.concat(" 15:00:00 UTC");
						datetimeEnd = dateEnd.concat(" 15:00:00 UTC");
						term = getInterval(dateStart, dateEnd);
						var rent_unit = <?php echo $rent_per_day; ?> * 100;
						var rent = rent_unit * term;
						var service_fee = Math.round((rent/100) * <?php echo $service_rate;?>)*100;
						var total_fee = rent + service_fee;
						var fee = total_fee - amount;
						<?php } else {?>
						service_fee  = "<?php echo $service_fee; ?>";
						<?php } ?>

						if(currency != "" && service_fee != "")
						{
							var type = '<?php echo ($isShortTerm == true) ? "day" : "term";?>';

							jQuery.ajax({
								url: '/api/0/charges/create/PercentSKUCharge',
								dataType: "json",
								method: "post",
								data: {
									currency: currency,
									source: token,
									description: desc,
									capture: false,
									metadata: {'postID': postID, 'userID': userID, 'postAuthorID': authorUserID,
												'stripeAccID': stripeAccID, 'stripeSkuID': stripeSkuID,
												'days': term, 'type': type}
								},
								success: function (result) {
									var stripeChargeID = result.data.stripeChargeID;
									AddOrder(skuID, stripeSkuID, stripeAccID, datetimeStart, term, tenants, stripeChargeID);
								},
								error: function () {
									$form.find('.payment-errors').text("Opps, The payment didn't go through. Please make sure your payment info is correct or try it later.");
									document.getElementById("BtnPay").disabled = false;
								}
							});

						}
						// Submit the form:
						//$form.get(0).submit();
					}
				};

				function policyCheck()
				{
					if (document.getElementById('policyCheckbox').checked)
					{
						document.getElementById("BtnPay").disabled = false;
						document.getElementById("TxtPolicyReminder").style.display = 'none';
					} else {
						document.getElementById("BtnPay").disabled = true;
						document.getElementById("TxtPolicyReminder").style.display = 'block';
					}
				}

				function getInterval(dateStart, dateEnd)
				{
					//Get 1 day in milliseconds
					var one_day=1000*60*60*24;
					var startDate = new Date(dateStart.concat("T15:00:00Z"));
					var endDate = new Date(dateEnd.concat("T15:00:00Z"));

					// Convert both dates to milliseconds
					var dateStart_ms = startDate.getTime();
					var dateEnd_ms = endDate.getTime();

					// Calculate the difference in milliseconds
					var difference_ms = dateEnd_ms - dateStart_ms;

					// Convert back to days and return
					var interval =  Math.round(Math.abs(difference_ms)/one_day);
					return interval;
				}

				function AddOrder(skuID, stripeSkuID, stripeAccID, startDate, term, numTenant, stripeChargeID) { /* Create an order wrapper */
					var currency = "<?php echo $currency ?>";
					if (stripeSkuID != "" && stripeAccID != "") {
						jQuery.ajax({
							url: '/api/0/worders',
							dataType: "json",
							method: "POST",
							data: {
								"postID": postID,
								"postAuthorID": authorUserID,
								"currency": currency,
								"userID": userID,
								"skuID": skuID,
								"stripeAccID": stripeAccID,
								"appStatus": "Waiting for approval",
								"startDate": startDate,
								"stripeChargeIDs" : [stripeChargeID],
								"term": term,
								"numTenant": numTenant
							},
							success: function (result) {
								document.getElementById("datepickerStart").disabled = true;
								var start = startDate.substring(0,10);
								document.getElementById("datepickerStart").value = start;
								<?php if($isShortTerm == true) {?>
								var endDate  = new Date(2000, 0, 1);
								var startdate  = new Date(start.concat("T15:00:00Z"));
								var one_day = 1000*60*60*24;
								var str = "rental";

								endDate.setTime(startdate.getTime() + term * one_day);
								document.getElementById("datepickerEnd").disabled = true;
								document.getElementById("datepickerEnd").value = endDate.toISOString().substring(0,10);

								<?php } else { ?>
								var str = "deposit";
								document.getElementById("term").disabled = true;
								if(term > 85 && term < 95) // =91
									document.getElementById("term").value = "3 months";
								else if(term > 175 && term < 185) // =182
									document.getElementById("term").value = "6 months";
								else if(term > 360 && term < 370) // =365
									document.getElementById("term").value = "12 months";
								
								<?php } ?>
								document.getElementById("tenants").disabled = true;
								document.getElementById("tenants").value = numTenant;
								//document.getElementById("BtnApply").className = 'btn btn-raised btn-success';
								document.getElementById("BtnApply").innerHTML = "Check order status";
								document.getElementById("note").innerHTML = "You've applied for the" +
								" property successfully. The landlord will respond to your application" +
								" within 24 hours. If your application gets approved, you will need to" +
								" pay the " + str + " within 48 hours to complete the application.";
								document.getElementById("BtnDismissCharge").click();
								//document.getElementById("summaryDiv").style.display = 'none';
								applied = true;
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


