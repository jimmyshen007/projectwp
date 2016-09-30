<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div>
	<script type="text/javascript">
		$( "h1" ).hide();
	</script>
	<ul class="nav nav-pills" style="margin-bottom: 35px; margin-left: 0px;margin-top: -15px;">
		<li class="active"><a href="http://localhost/wordpress/?page_id=118">Profile</a></li>
		<li><a href="http://localhost/wordpress/?page_id=138">Wish List</a></li>
		<li><a href="http://localhost/wordpress/?page_id=136">Your Listings</a></li>
		<li><a href="http://localhost/wordpress/?page_id=140 ">Orders</a></li>
	</ul>
</div>
<div class="tml tml-profile" id="theme-my-login<?php $template->the_instance(); ?>">
	<?php $template->the_action_template_message( 'profile' ); ?>
	<?php $template->the_errors(); ?>
	<form id="your-profile" action="<?php $template->the_action_url( 'profile', 'login_post' ); ?>" method="post">
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
										<?php do_action( 'show_user_profile', $profileuser ); ?>
									</td>
									<td style="width: 5%"></td>
									<td style="width: 50%">
										<?php do_action( 'profile_personal_options', $profileuser ); ?>
										<table class="tml-form-table">
											<tr class="form-group">
												<td>
													<label class="control-label" for="user_login"><?php _e( 'Username', 'theme-my-login' ); ?></label>
													<input class="form-control input-lg" type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" />
												</td>
											</tr>

											<tr>
												<td><label class="control-label" for="first_name"><?php _e( 'First Name', 'theme-my-login' ); ?></label>
													<input class="form-control input-lg"  type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profileuser->first_name ); ?>"/>
												</td>
											</tr>

											<tr class="form-group">
												<th><label class="control-label" for="last_name"><?php _e( 'Last Name', 'theme-my-login' ); ?></label></th>
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
								<tr class="pw-weak">
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

		<div class="panel panel-default">
			<div class="panel-heading">Tenant Verification</div>
			<div class="panel-body">
				<table>
					<tbody>
						<tr>
							<td style="width: 8%"></td>
							<td style="width: 77%">
								<table>
									<tbody>
										<tr>
											<td style="width: 40%">
												<label class="control-label">Passport</label>
												<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
													<div>
														<span class="btn btn-raised btn-success btn-file"><span class="fileinput-new">Upload Passport</span><span class="fileinput-exists">Change</span><input type="file" name="..."></span>
														<a href="#" class="btn btn-raised btn-warning fileinput-exists" data-dismiss="fileinput">Remove</a>
													</div>
												</div>
											</td>
											<td style="width: 5%"></td>
											<td style="width: 55%">
												<label class="control-label" for="pp_expiry_date">Expiry Date</label>
												<input class="form-control input-lg"  type="text" name="pp_expiry_date" id="pp_expiry_date"/>
											</td>
										</tr>
										<tr>
											<td style="width: 40%">
												<label class="control-label">Student ID</label>
												<div class="fileinput fileinput-new" data-provides="fileinput">
													<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
													<div>
														<span class="btn btn-raised btn-success btn-file"><span class="fileinput-new">Upload Student ID</span><span class="fileinput-exists">Change</span><input type="file" name="..."></span>
														<a href="#" class="btn btn-raised btn-warning fileinput-exists" data-dismiss="fileinput">Remove</a>
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
																	<div class="col-md-10">
																		<div class="radio radio-primary">
																			<label>
																				<input type="radio" name="optionsRadios" id="optionsRadios1" value="option1" checked="">
																				Student Card
																			</label>
																		</div>
																		<div class="radio radio-primary">
																			<label>
																				<input type="radio" name="optionsRadios" id="optionsRadios2" value="option2">
																				School Offer
																			</label>
																		</div>
																	</div>
																</div>
															</td>
														</tr>
														<tr>
															<td>
																<p></p>
																<div class="form-group">
																	<label class="control-label" for="pp_expiry_date">Expiry Date</label>
																	<input class="form-control input-lg"  type="text" name="pp_expiry_date" id="pp_expiry_date"/>
																</div>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
							<td style="width: 15%"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">Landlord Verification</div>
			<div class="panel-body">
			</div>
		</div>
		<p class="tml-submit-wrap">
			<input type="hidden" name="action" value="profile" />
			<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
			<input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>" />
			<input type="submit" class="btn btn-raised btn-danger button-primary" value="<?php esc_attr_e( 'Save', 'theme-my-login' ); ?>" name="submit" id="submit" />
		</p>
	</form>
</div>
