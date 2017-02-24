<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/

global $user_ID;

$pp_value = get_user_meta( $user_ID, "Passport", false);
$pp_exiry_value = get_user_meta( $user_ID, "passport_expire_date", false);
$stuID_value = get_user_meta( $user_ID, "StudentID", false);
$stuID_exiry_value = get_user_meta( $user_ID, "stuid_expire_date", false);
$is_tenant_arr = get_user_meta( $user_ID, "is_tenant", false);
$is_tenant = (count($is_tenant_arr) > 0) ? $is_tenant_arr[0] : 0;
$is_host_arr = get_user_meta( $user_ID, "is_host", false);
$is_host = (count($is_host_arr) > 0) ? $is_host_arr[0] : 0;
$isBankAccCreated_arr = get_user_meta($user_ID, "isBankAccCreated", false);
$isBankAccCreated = (count($isBankAccCreated_arr) > 0) ? $isBankAccCreated_arr[0] : "";

if(count($pp_value) > 0 && count($pp_exiry_value)) {
	$isPassportUploaded = true;
} else {
	$isPassportUploaded = false;
}

if(count($stuID_value) > 0 && count($stuID_exiry_value)) {
	$isStuIDUploaded = true;
} else {
	$isStuIDUploaded = false;
}




?>
<div>
	<script type="text/javascript">
		$( "h1" ).hide();

		function isValidForm()
		{
			var pp_expiry = document.getElementById("pp_expiry_date").value;
			var stu_expiry = document.getElementById("stuid_expiry_date").value;

			// If passport is about to upload, then expiry date field must be filled
			if(document.getElementById("passport").value != '')
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
			} else { // if no passport file is to be uploaded, then expriy date should be null
				if (pp_expiry != "") {
					document.getElementById("errorTxt").innerHTML="Error: Please upload your passport";
					document.getElementById("errorTxt").style.visibility = "visible";
					return false;
				}
			}

			// If student id is about to upload, then expiry date field must be filled
			if(document.getElementById("studentID").value != '')
			{
				//if it's school offer, set expiry date to "offer" indicating the id it's an offer
				if (document.getElementById("optionsRadios2").checked == true) {
					document.getElementById("stuid_expiry_date").value = "offer";
				}
				else if (stu_expiry == "") {
					document.getElementById("errorTxt").innerHTML="Error: Please provide expiry date of your student card.";
					document.getElementById("errorTxt").style.visibility = "visible";
					return false;
				} else if (!isValidDate(stu_expiry)) {
					document.getElementById("errorTxt").innerHTML="Error: Invalid expiry date of student card.";
					document.getElementById("errorTxt").style.visibility = "visible";
					return false;
				}
			} else { // if no student id file is to be uploaded, then expriy date should be null
				if (document.getElementById("optionsRadios1").checked && stu_expiry != "" ) {
					document.getElementById("errorTxt").innerHTML="Error: Please upload your student card";
					document.getElementById("errorTxt").style.visibility = "visible";
					return false;
				}
			}

			document.getElementById("errorTxt").style.visibility = "hidden";
			return true;
		}

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

		$( function() {
			var dateFormat = "yy-mm-dd";
			$( "#stuid_expiry_date" ).datepicker({
					dateFormat: dateFormat,
					minDate: 0
				})
				.on( "change", function() {
					var stu_expiry = document.getElementById("stuid_expiry_date").value;
					if (stu_expiry != "") {
						if (!isValidDate(stu_expiry)) {
							document.getElementById("stuid_expiry_date").value = "";
						}
					}
				});
		} );

		$(document).on("change","input[type=radio]",function(){
			if($('input[name="stuIDRadios"]:checked').val() == "offer")
			{
				document.getElementById("stuIdDateDiv").style.display = "none";
				document.getElementById("offerTxt").style.display = "block";

			} else {
				document.getElementById("stuIdDateDiv").style.display = "block";
				document.getElementById("offerTxt").style.display = "none";
			}
		});

		function enableUpdatePassport() {
			if(document.getElementById("update_ppChkBox").checked) {
				document.getElementById("updateppBtn").disabled = false;
			}
			else {
				if ($("#idColl1").hasClass('collapse in')) {
					document.getElementById("PassportRmBtn").click();
					document.getElementById("updateppBtn").click();
					document.getElementById("pp_expiry_date").value = "";
				}
				document.getElementById("updateppBtn").disabled = true;
			}
		}

		function enableUpdateStudentID() {
			if(document.getElementById("update_stuChkBox").checked) {
				document.getElementById("updatestuBtn").disabled = false;
			}
			else {
				if ($("#idColl2").hasClass('collapse in')) {
					document.getElementById("StuIDRmBtn").click();
					document.getElementById("updatestuBtn").click();
					document.getElementById("stuid_expiry_date").value = "";
				}
				document.getElementById("updatestuBtn").disabled = true;
			}
		}
	</script>
	<ul class="nav nav-pills" style="margin-bottom: 35px; margin-left: 0px;margin-top: -15px;">
		<li class="active"><a href="/your-profile/">Profile</a></li>
		<li><a href="/your-profile/wish-list/">Wish List</a></li>
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
<div class="tml tml-profile" id="theme-my-login<?php $template->the_instance(); ?>">
	<?php $template->the_action_template_message( 'profile' ); ?>
	<?php $template->the_errors(); ?>
	<form id="your-profile" onsubmit="return isValidForm()" action="<?php $template->the_action_url( 'profile', 'login_post' ); ?> " method="post" enctype="multipart/form-data">
		<?php wp_nonce_field( 'update-user_' . $current_user->ID ); ?>
		<p>
			<input type="hidden" name="from" value="profile" />
			<input type="hidden" name="checkuser_id" value="<?php echo $current_user->ID; ?>" />
		</p>
		<div class="panel panel-default">
			<div class="panel-heading">Profile</div>
			<div class="panel-body">
				<table>
					<tbody>
					<tr>
						<td>
							<table>
								<tbody>
								<tr>
									<td style="text-align: center; vertical-align: top; width: 30%">
										<?php
										do_action( 'show_user_profile', $profileuser ); ?>
									</td>
									<td style="width: 5%"></td>
									<td style="width: 50%">
										<?php

											do_action( 'profile_personal_options', $profileuser ); ?>
										<table class="tml-form-table">
											<tr class="form-group">
												<td>
													<label class="control-label" for="user_login"><?php _e( 'Username', 'theme-my-login' ); ?></label>
													<input class="form-control input-lg" type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" />
												</td>
											</tr>

											<tr>
												<td><label class="control-label" for="first_name"><?php _e( 'First Name', 'theme-my-login' ); ?> <span class="description"><?php _e( '(required)', 'theme-my-login' ); ?></span></label>
													<input class="form-control input-lg"  type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profileuser->first_name ); ?>"/>
												</td>
											</tr>

											<tr class="form-group">
												<th><label class="control-label" for="last_name"><?php _e( 'Last Name', 'theme-my-login' );  ?> <span class="description"><?php _e( '(required)', 'theme-my-login' ); ?></span></label></th>
												<td><input  class="form-control input-lg" type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $profileuser->last_name ); ?>" /></td>
											</tr>

											<tr class="form-group">
												<th><label class="control-label" for="nickname"><?php _e( 'Nickname', 'theme-my-login' ); ?> <span class="description"><?php _e( '(required)', 'theme-my-login' ); ?></span></label></th>
												<td><input class="form-control input-lg" type="text" name="nickname" id="nickname" value="<?php echo esc_attr( $profileuser->nickname ); ?>"  /></td>
											</tr>
										</table>
									</td>
									<td style="width: 15%"></td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<table>
								<tbody>
								<tr>
									<td style="width: 8%"></td>
									<td style="width: 77%">
										<table>
											<tbody>
											<tr>
												<td>
													<label class="control-label" for="email"><?php _e( 'E-mail', 'theme-my-login' ); ?> <span class="description"><?php _e( '(required)', 'theme-my-login' ); ?></span></label>
													<input class="form-control input-lg" type="text" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ); ?>" /></td>
												<?php
												$new_email = get_option( $current_user->ID . '_new_email' );
												if ( $new_email && $new_email['newemail'] != $current_user->user_email ) : ?>
													<div class="updated inline">
														<p><?php
															printf(
																__( 'There is a pending change of your e-mail to %1$s. <a href="%2$s">Cancel</a>', 'theme-my-login' ),
																'<code>' . $new_email['newemail'] . '</code>',
																esc_url( self_admin_url( 'profile.php?dismiss=' . $current_user->ID . '_new_email' ) )
															); ?>
														</p>
													</div>
												<?php endif; ?>
												</td>
											</tr>
											<tr>
												<td>
													<p></p>
													<div class="form-group">
														<label class="col-md-2 control-label" style="padding-left: 0;" for="description"><?php _e( 'About Yourself', 'theme-my-login' ); ?></label>
														<textarea class="form-control" name="description" id="description" rows="3" cols="30"><?php echo esc_html( $profileuser->description ); ?></textarea>
														<span class="help-block"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'theme-my-login' ); ?></span>
													</div>
												</td>
											</tr>
											</tbody>
										</table>
									<td style="width: 15%"></td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div></br>
		<?php if($is_tenant == 1) {?>
		<div id="Verification" class="panel panel-default">
			<div class="panel-heading">Verification</div>
			<div class="panel-body">
				<table>
					<tbody>
						<tr>
							<td style="width: 8%"></td>
							<td style="width: 77%">
								<?php if($isPassportUploaded) {?>
									<p style="margin-bottom: 5px;">You have successfully uploaded your passport.</p>
									<p style="margin-bottom: 5px;">Please make sure to update before the expiry date (<?php echo $pp_exiry_value[0];?>).</p>
									<table><tr>
										<td style="width: 30%">
											<button type="button" id="updateppBtn" class="btn btn-raised btn-default" style="width: 200px;" disabled="disabled" data-toggle="collapse" data-target="#idColl1">Update Passport</button>
										</td>
										<td>
											<div class="form-group">
												<div class="checkbox">
													<label>
														<input type="checkbox" id="update_ppChkBox" name="update_pp" onchange="enableUpdatePassport()"/>
														<span>I want to update my passport.</span>
													</label>
												</div>
											</div>
										</td>
									</tr></table>
									<div id="idColl1" class="collapse">
										<table>
											<tbody>
											<tr>
												<td style="width: 40%">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
														<div>
															<span class="btn btn-raised btn-success btn-file" style="width: 200px;"><span class="fileinput-new">Upload Passport</span><span id="changePassportBtn" class="fileinput-exists">Change</span><input type="file" name="passport" id="passport"></span>
															<a href="#" id="PassportRmBtn" class="btn btn-raised btn-warning fileinput-exists" data-dismiss="fileinput">Remove</a>
														</div>
													</div>
												</td>
												<td style="width: 5%"></td>
												<td style="width: 55%">
													<label class="control-label" for="pp_expiry_date">Expiry Date</label>
													<input class="form-control input-lg"  style="width: 235px;" type="text" name="pp_exname="pp_expiry_date" placeholder="yyyy-mm-dd" id="pp_expiry_date"piry_date" placeholder="yyyy-mm-dd" id="pp_expiry_date"/>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								<?php }else {?>
									<p style="margin-bottom: 5px;">You’ll need to provide identification before you book, so get a head start by doing it now. </p>
									<button type="button" class="btn btn-raised btn-default" style="width: 200px;" data-toggle="collapse" data-target="#idColl1">Add Passport</button>
									<div id="idColl1" class="collapse">
										<table>
											<tbody>
											<tr>
												<td style="width: 40%">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
														<div>
															<span class="btn btn-raised btn-success btn-file" style="width: 200px;"><span class="fileinput-new">Upload Passport</span><span class="fileinput-exists">Change</span><input type="file" name="passport" id="passport"></span>
															<a href="#" id="PassportRmBtn" class="btn btn-raised btn-warning fileinput-exists" data-dismiss="fileinput">Remove</a>
														</div>
													</div>
												</td>
												<td style="width: 5%"></td>
												<td style="width: 55%">
													<label class="control-label" for="pp_expiry_date">Expiry Date</label>
													<input class="form-control input-lg"  style="width: 235px;" type="text" name="pp_expiry_date" placeholder="yyyy-mm-dd" id="pp_expiry_date"/>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								<?php }?>
								<?php if($isStuIDUploaded) {?>
									<?php if($stuID_exiry_value[0] == "offer") {?>
										<p style="margin-bottom: 5px; margin-top:20px">You have successfully uploaded your school offer.</p>
										<p style="margin-bottom: 5px;">Please make sure to provide your student id once you have it.</p>
									<?php } else {?>
										<p style="margin-bottom: 5px; margin-top:20px">You have successfully uploaded your student id.</p>
										<p style="margin-bottom: 5px;">Please make sure to update before the expiry date (<?php echo $stuID_exiry_value[0];?>).</p></p>
									<?php } ?>
									<table><tr>
											<td style="width: 30%">
												<button type="button" id="updatestuBtn" class="btn btn-raised btn-default" style="width: 200px;" disabled="disabled" data-toggle="collapse" data-target="#idColl2">Update student id</button>
											</td>
											<td>
												<div class="form-group">
													<div class="checkbox">
														<label>
															<input type="checkbox" id="update_stuChkBox" name="update_stu" onchange="enableUpdateStudentID()"/>
															<span>I want to update my student id.</span>
														</label>
													</div>
												</div>
											</td>
										</tr></table>
									<div id="idColl2" class="collapse">
										<table>
											<tbody>
											<tr>
												<td style="width: 40%">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
														<div>
															<span class="btn btn-raised btn-success btn-file" style="width: 200px;"><span class="fileinput-new">Upload Student ID</span><span class="fileinput-exists">Change</span><input type="file" name="studentID" id="studentID"></span>
															<a href="#" id="StuIDRmBtn" class="btn btn-raised btn-warning fileinput-exists" data-dismiss="fileinput">Remove</a>
														</div>
													</div>
												</td>
												<td style="width: 5%"></td>
												<td style="width: 55%">
													<table>
														<tbody>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-md-10" style="padding-left: 0px; margin-left: -12px;">
																		<div class="radio radio-primary">
																			<label>
																				<input type="radio" name="stuIDRadios" id="optionsRadios1" value="stuCard" checked="">
																				Student Card
																			</label>
																		</div>
																		<div class="radio radio-primary">
																			<label>
																				<input type="radio" name="stuIDRadios" id="optionsRadios2" value="offer">
																				School Offer
																			</label>
																		</div>
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div id="offerTxt" style="display: none;width: 300px;">
																	<p class="text-muted" >Please provide your school offer if you don't have a student card yet, and upload your student card later once you have it.</p>
																</div>
																<div class="form-group" id="stuIdDateDiv" style="margin-top: 13px;">
																	<label class="control-label" for="stuid_expiry_date">Expiry Date</label>
																	<input class="form-control input-lg"  style="width: 235px;" type="text" name="stuid_expiry_date"  placeholder="yyyy-mm-dd" id="stuid_expiry_date"/>
																</div>
															</td>
														</tr>
														</tbody>
													</table>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								<?php } else {?>
									<p style="margin-bottom: 5px; margin-top:20px">As a student tenant, providing student id or school offer helps building trust between you and hosts.</p>
									<button type="button" class="btn btn-raised btn-default" style="width: 200px;" data-toggle="collapse" data-target="#idColl2">Add student id</button>
									<div id="idColl2" class="collapse">
										<table>
											<tbody>
											<tr>
												<td style="width: 40%">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
														<div>
															<span class="btn btn-raised btn-success btn-file" style="width: 200px;"><span class="fileinput-new">Upload Student ID</span><span class="fileinput-exists">Change</span><input type="file" name="studentID" id="studentID"></span>
															<a href="#" id="StuIDRmBtn" class="btn btn-raised btn-warning fileinput-exists" data-dismiss="fileinput">Remove</a>
														</div>
													</div>
												</td>
												<td style="width: 5%"></td>
												<td style="width: 55%">
													<table>
														<tbody>
														<tr>
															<td>
																<div class="form-group">
																	<div class="col-md-10" style="padding-left: 0px; margin-left: -12px;">
																		<div class="radio radio-primary">
																			<label>
																				<input type="radio" name="stuIDRadios" id="optionsRadios1" value="stuCard" checked="">
																				Student Card
																			</label>
																		</div>
																		<div class="radio radio-primary">
																			<label>
																				<input type="radio" name="stuIDRadios" id="optionsRadios2" value="offer">
																				School Offer
																			</label>
																		</div>
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<div id="offerTxt" style="display: none;width: 300px;">
																	<p class="text-muted" >Please provide your school offer if you don't have a student card yet, and upload your student card later once you have it.</p>
																</div>
																<div class="form-group" id="stuIdDateDiv" style="margin-top: 13px;">
																	<label class="control-label" for="stuid_expiry_date">Expiry Date</label>
																	<input class="form-control input-lg"  style="width: 235px;" type="text" name="stuid_expiry_date"  placeholder="yyyy-mm-dd" id="stuid_expiry_date"/>
																</div>
															</td>
														</tr>
														</tbody>
													</table>
												</td>
											</tr>
											</tbody>
										</table>
									</div>
								<?php } ?>
							</td>
							<td style="width: 15%"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">Reset Password</div>
			<div class="panel-body">
				<table>
					<tbody>
					<tr>
						<td style="width: 8%"></td>
						<td style="width: 77%">

							<?php
							$show_password_fields = apply_filters( 'show_password_fields', true, $profileuser );
							if ( $show_password_fields ) :
							?>
							<style>
								#pass1 {display: none;}
							</style>
							<table class="tml-form-table">
								<tr id="password" class="user-pass1-wrap">
									<td>
										<input class="hidden" value=" " /><!-- #24364 workaround -->
										<button type="button" class="btn btn-raised btn-default wp-generate-pw hide-if-no-js"><?php _e( 'Generate Password', 'theme-my-login' ); ?></button>
										<div class="wp-pwd hide-if-js">
													<span class="password-input-wrapper">
														<input type="password" name="pass1" id="pass1" class="form-control input-lg regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
													</span>
											<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
											<button type="button" class="btn btn-raised btn-default wp-cancel-pw hide-if-no-js" style="padding: 8px 15px;" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change', 'theme-my-login' ); ?>">
												<span class="text"><?php _e( 'Cancel', 'theme-my-login' ); ?></span>
											</button>
										</div>
									</td>
								</tr>
								<tr class="user-pass2-wrap hide-if-js">
									<th scope="row"><label for="pass2"><?php _e( 'Repeat New Password', 'theme-my-login' ); ?></label></th>
									<td>
										<input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="off" />
										<p class="description"><?php _e( 'Type your new password again.', 'theme-my-login' ); ?></p>
									</td>
								</tr>
								<tr class="pw-weak" style="display: none">
									<td>
										<div class="form-group">
											<div class="checkbox">
												<label>
													<input type="checkbox" name="pw_weak" />
													<?php _e( 'Confirm use of weak password', 'theme-my-login' ); ?>
												</label>
											</div>
										</div>
									</td>
								</tr>
								<?php endif; ?>

							</table>


						</td>
						<td style="width: 15%"></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php if(!$is_host) {?>
			<div class="panel panel-default">
				<div class="panel-heading">Host your space on Ulieve</div>
				<div class="panel-body">
					<table>
						<tbody>
						<tr>
							<td style="width: 8%"></td>
							<td style="width: 77%">
								<p class="text-muted" style="margin-bottom: 5px;">Host your space on Ulieve by two easy steps.</p>
								<p class="text-muted" style="margin-bottom: 5px;">Step 1: Add your bank account</p>
								<p class="text-muted" style="margin-bottom: 5px;">Step 2: Post your space</p>
								<a href="/your-profile/account/" type="button" id="beHostBtn" class="btn btn-raised btn-info" style="width: 205px; border-radius: 2px; vertical-align: middle">become a host</a>
							</td>
							<td style="width: 15%"></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php } else if(!$isBankAccCreated){ ?>
			<div class="panel panel-default">
				<div class="panel-heading">Host your space on Ulieve</div>
				<div class="panel-body">
					<table>
						<tbody>
						<tr>
							<td style="width: 8%"></td>
							<td style="width: 77%">
								<p class="text-muted" style="margin-bottom: 5px;">As a host, you need to provide your personal information for verification as well as your bank details so that we can transfer</p>
								<p class="text-muted" style="margin-bottom: 5px;">applicant's bond money to your bank account. Lack of bank details results in your listings not being able to be applied by</p>
								<p class="text-muted" style="margin-bottom: 5px;">visitors.</p>
								<a href="/your-profile/account/" type="button" id="beHostBtn" class="btn btn-raised btn-info" style="width: 205px; border-radius: 2px; vertical-align: middle">Add bank account</a>
							</td>
							<td style="width: 15%"></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		<?php }?>
		<div>
			<p id="errorTxt" class="text-danger" style="visibility: hidden"></p>
		</div>
		<p class="tml-submit-wrap">
			<input type="hidden" name="action" value="profile" />
			<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
			<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>" />
			<input type="submit" class="btn btn-raised btn-danger button-primary" value="<?php esc_attr_e( 'Save', 'theme-my-login' ); ?>" name="submit" id="submit" />
		</p>
	</form>
</div>
