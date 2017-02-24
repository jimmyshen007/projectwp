<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<script type="text/javascript">
	$( "h1" ).hide();

	function rentCheck() {
		if (document.getElementById('tenantCheckbox').checked || document.getElementById('hostCheckbox').checked) {
			document.getElementById("signupBtn").disabled = false;
		} else {
			document.getElementById("signupBtn").disabled = true;
		}
	}

	function hostCheck() {
		if (document.getElementById('tenantCheckbox').checked || document.getElementById('hostCheckbox').checked) {
			document.getElementById("signupBtn").disabled = false;
		} else {
			document.getElementById("signupBtn").disabled = true;
		}
	}
</script>
<div class="panel panel-default">
	<div class="panel-heading">Sign Up</div>
	<div class="panel-body" style="width: 70%; padding-left: 30px">
		<div class="tml tml-register" id="theme-my-login<?php $template->the_instance(); ?>">
			<?php $template->the_errors(); ?>
			<form name="registerform" id="registerform<?php $template->the_instance(); ?>" action="<?php $template->the_action_url( 'register', 'login_post' ); ?>" method="post">
				<?php if ( 'email' != $theme_my_login->get_option( 'login_type' ) ) : ?>
					<div class="form-group">
						<label class="control-label" for="user_login<?php $template->the_instance(); ?>"><?php _e( 'Username', 'theme-my-login' ); ?></label>
						<input class="form-control input-lg"  type="text" name="user_login" id="user_login<?php $template->the_instance(); ?>" value="<?php $template->the_posted_value( 'user_login' ); ?>" size="20" />
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label class="control-label" for="user_email<?php $template->the_instance(); ?>"><?php _e( 'E-mail', 'theme-my-login' ); ?></label>
					<input class="form-control input-lg" type="text" name="user_email" id="user_email<?php $template->the_instance(); ?>" value="<?php $template->the_posted_value( 'user_email' ); ?>" size="20" />
				</div>

				<?php do_action( 'register_form' ); ?>

				<p class="tml-registration-confirmation" id="reg_passmail<?php $template->the_instance(); ?>"><?php echo apply_filters( 'tml_register_passmail_template_message', __( 'Registration confirmation will be e-mailed to you.', 'theme-my-login' ) ); ?></p>

				<p class="tml-submit-wrap">
					<input class="btn btn-raised btn-danger" style="height: 50px; border-radius: 5px;" type="submit" name="wp-submit" id="signupBtn" value="Create your Ulieve Account" />
					<input type="hidden" name="redirect_to" value="<?php $template->the_redirect_url( 'register' ); ?>" />
					<input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
					<input type="hidden" name="action" value="register" />
				</p>
			</form>
		</div>
	</div>
</div>
