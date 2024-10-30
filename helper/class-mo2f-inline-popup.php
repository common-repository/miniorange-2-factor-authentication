<?php
/**
 * This includes UI of Inline Prompts.
 *
 * @package miniOrange-2-factor-authentication/handler
 */

namespace TwoFA\Onprem;

use TwoFA\Helper\Mo2f_Common_Helper;
use TwoFA\Helper\MoWpnsConstants;
use TwoFA\Helper\MocURL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Mo2f_Inline_Popup' ) ) {

	/**
	 * Class for log login transactions
	 */
	class Mo2f_Inline_Popup {


		/**
		 * This user shows popup to select inline method.
		 *
		 * @param string $current_user_id user id of current user of current user.
		 * @param string $login_message Login message.
		 * @param string $redirect_to redirect url.
		 * @param string $session_id session id.
		 * @return void
		 */
		public function prompt_user_to_select_2factor_mthod_inline( $current_user_id, $login_message, $redirect_to, $session_id ) {
			global $mo2fdb_queries;
			$current_user     = get_userdata( $current_user_id );
			$common_helper    = new Mo2f_Common_Helper();
			$selected_methods = $common_helper->fetch_methods( $current_user );
			?>  
		<html>
			<head>
				<meta charset="utf-8"/>
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<?php
					$common_helper->mo2f_inline_css_and_js();
				?>
			</head>
			<body>
				<div class="mo2f_modal1" tabindex="-1" role="dialog" id="myModal51">
					<div class="mo2f-modal-backdrop"></div>
					<div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md">
						<div class="login mo_customer_validation-modal-content">
							<div class="mo2f_modal-header">
								<h3 class="mo2f_modal-title"><button type="button" class="mo2f_close" data-dismiss="modal" aria-label="Close" title="<?php esc_attr_e( 'Back to login', 'miniorange-2-factor-authentication' ); ?>" onclick="mologinback();"><span aria-hidden="true">&times;</span></button>

						<?php esc_html_e( 'New security system has been enabled', 'miniorange-2-factor-authentication' ); ?></h3>
							</div>
							<div class="mo2f_modal-body">
							<b>
						<?php
							esc_html_e( 'Configure a Two-Factor method to protect your account', 'miniorange-2-factor-authentication' );
						?>
							</b>
							<?php
							if ( isset( $login_message ) && ! empty( $login_message ) ) {
								echo '<br><br>';
								?>

								<div  id="otpMessage">
									<p class="mo2fa_display_message_frontend" style="text-align: left !important;"><?php echo wp_kses( $login_message, array( 'b' => array() ) ); ?></p>
								</div>
										<?php
							} else {
								echo '<br>';
							}
							?>

								<br>
								<span class="
								<?php
								if ( ! ( in_array( MoWpnsConstants::GOOGLE_AUTHENTICATOR, $selected_methods, true ) ) ) {
									echo 'mo2f_td_hide';
								} else {
									echo 'mo2f_td_show'; }
								?>
								">
									<label title="<?php esc_attr_e( 'You have to enter 6 digits code generated by Authenticator App to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication' ); ?>">
									<input type="radio"  name="mo2f_selected_2factor_method"  value="<?php echo esc_attr( MoWpnsConstants::GOOGLE_AUTHENTICATOR ); ?>"  />
									<?php
									esc_html_e(
										'Google / Authy / Microsoft Authenticator',
										'miniorange-2-factor-authentication'
									);
									?>
									<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<?php
									esc_html_e(
										'(Any TOTP Based Authenticator App)',
										'miniorange-2-factor-authentication'
									);
									?>
								</label>
								<br>
								</span>
								<span class="
										<?php
										if ( ! ( in_array( MoWpnsConstants::OUT_OF_BAND_EMAIL, $selected_methods, true ) ) ) {
											echo 'mo2f_td_hide';
										} else {
											echo 'mo2f_td_show'; }
										?>
								" >
									<label title="<?php esc_attr_e( 'You will receive an email with link. You have to click the ACCEPT or DENY link to verify your email. Supported in Desktops, Laptops, Smartphones.', 'miniorange-2-factor-authentication' ); ?>">
												<input type="radio"  name="mo2f_selected_2factor_method"  value="<?php echo esc_attr( MoWpnsConstants::OUT_OF_BAND_EMAIL ); ?>"  />
										<?php esc_html_e( MoWpnsConstants::mo2f_convert_method_name( MoWpnsConstants::OUT_OF_BAND_EMAIL, 'cap_to_small' ), 'miniorange-2-factor-authentication' ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- The $text is a single string literal ?>
									</label>
									<br>
								</span> 
								<span class="
								<?php
								if ( ! ( in_array( MoWpnsConstants::OTP_OVER_SMS, $selected_methods, true ) ) ) {
									echo 'mo2f_td_hide';
								} else {
									echo 'mo2f_td_show'; }
								?>
								" >
										<label title="<?php esc_attr_e( 'You will receive a one time passcode via SMS on your phone. You have to enter the otp on your screen to login. Supported in Smartphones, Feature Phones.', 'miniorange-2-factor-authentication' ); ?>">
											<input type="radio"  name="mo2f_selected_2factor_method"  value="<?php echo esc_attr( MoWpnsConstants::OTP_OVER_SMS ); ?>"  />
									<?php esc_html_e( MoWpnsConstants::mo2f_convert_method_name( MoWpnsConstants::OTP_OVER_SMS, 'cap_to_small' ), 'miniorange-2-factor-authentication' ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- The $text is a single string literal ?>
										</label>
									<br>
								</span>
								<span class="
								<?php
								if ( ! ( in_array( MoWpnsConstants::OTP_OVER_TELEGRAM, $selected_methods, true ) ) ) {
									echo 'mo2f_td_hide';
								} else {
									echo 'mo2f_td_show'; }
								?>
								" >
										<label title="<?php esc_attr_e( 'You will get an OTP on your Telegram app from miniOrange Bot.', 'miniorange-2-factor-authentication' ); ?>" >
											<input type="radio"  name="mo2f_selected_2factor_method"  value="<?php esc_html_e( MoWpnsConstants::OTP_OVER_TELEGRAM, 'miniorange-2-factor-authentication' ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- The $text is a single string literal ?>"  />
									<?php esc_html_e( MoWpnsConstants::mo2f_convert_method_name( MoWpnsConstants::OTP_OVER_TELEGRAM, 'cap_to_small' ), 'miniorange-2-factor-authentication' ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- The $text is a single string literal ?>
										</label>
									<br>
								</span>
								<span class="
								<?php
								if ( ! ( in_array( MoWpnsConstants::AUTHY_AUTHENTICATOR, $selected_methods, true ) ) ) {
									echo 'mo2f_td_hide';
								} else {
									echo 'mo2f_td_show'; }
								?>
								">
											<label title="<?php esc_attr_e( 'You have to enter 6 digits code generated by Authy 2-Factor Authentication App to login. Supported in Smartphones only.', 'miniorange-2-factor-authentication' ); ?>">
												<input type="radio"  name="mo2f_selected_2factor_method"  value="<?php echo esc_attr( MoWpnsConstants::AUTHY_AUTHENTICATOR ); ?>"  />
										<?php esc_html_e( MoWpnsConstants::mo2f_convert_method_name( MoWpnsConstants::AUTHY_AUTHENTICATOR, 'cap_to_small' ), 'miniorange-2-factor-authentication' ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- The $text is a single string literal ?>
											</label>
											<br>
								</span>
								<span class="
								<?php
								if ( ! ( in_array( MoWpnsConstants::SECURITY_QUESTIONS, $selected_methods, true ) ) ) {
									echo 'mo2f_td_hide';
								} else {
									echo 'mo2f_td_show'; }
								?>
								">
									<label title="<?php esc_attr_e( 'You have to answers some knowledge based security questions which are only known to you to authenticate yourself. Supported in Desktops,Laptops,Smartphones.', 'miniorange-2-factor-authentication' ); ?>" >
									<input type="radio"  name="mo2f_selected_2factor_method"  value="<?php echo esc_attr( MoWpnsConstants::SECURITY_QUESTIONS ); ?>"  />
										<?php esc_html_e( 'Security Questions ( KBA )', 'miniorange-2-factor-authentication' ); ?>
											</label>
											<br>
								</span>
								<span class="
								<?php
								if ( ! ( in_array( MoWpnsConstants::OTP_OVER_EMAIL, $selected_methods, true ) ) ) {
									echo 'mo2f_td_hide';
								} else {
									echo 'mo2f_td_show'; }
								?>
								">
									<label title="<?php esc_attr_e( 'You will receive a one time passcode on your email. You have to enter the otp on your screen to login. Supported in Smartphones, Feature Phones.', 'miniorange-2-factor-authentication' ); ?>" >
									<input type="radio"  name="mo2f_selected_2factor_method"  value="<?php echo esc_attr( MoWpnsConstants::OTP_OVER_EMAIL ); ?>"  />
										<?php esc_html_e( MoWpnsConstants::mo2f_convert_method_name( MoWpnsConstants::OTP_OVER_EMAIL, 'cap_to_small' ), 'miniorange-2-factor-authentication' ); //phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText -- The $text is a single string literal ?>
											</label>
											<br>
								</span>

								<?php

								$check_grace_period = new Mo2f_Common_Helper();

								if ( get_site_option( 'mo2f_grace_period' ) && ( ! $check_grace_period->mo2f_is_grace_period_expired( $current_user ) ) ) {

									?>
								<br>
										<a href="#skiptwofactor" style="color:#F4D03F ;font-weight:bold;margin-left:35%;"><?php esc_html_e( 'Skip Two Factor', 'miniorange-2-factor-authentication' ); ?></a>
										<br>
										<?php } ?>

										<?php
										$common_helper = new Mo2f_Common_Helper();
										echo wp_kses(
											$common_helper->mo2f_customize_logo(),
											array(
												'div' => array(
													'style' => array(),
												),
												'img' => array(
													'alt' => array(),
													'src' => array(),
												),
											)
										);
										?>
							</div>
						</div>
					</div>
				</div>
				<?php
				$common_helper = new Mo2f_Common_Helper();
				echo wp_kses(
					$common_helper->mo2f_backto_login_form(),
					array(
						'form' => array(
							'name'   => array(),
							'id'     => array(),
							'method' => array(),
							'action' => array(),
							'class'  => array(),
						),
					)
				);
				?>
				<form name="f" method="post" action="" id="mo2f_select_2fa_methods_form" style="display:none;">
					<input type="hidden" name="mo2f_selected_2factor_method" />
					<input type="hidden" name="miniorange_inline_save_2factor_method_nonce" value="<?php echo esc_attr( wp_create_nonce( 'miniorange-2-factor-inline-save-2factor-method-nonce' ) ); ?>" />
					<input type="hidden" name="option" value="miniorange_inline_save_2factor_method" />
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>"/>
					<input type="hidden" name="session_id" value="<?php echo esc_attr( $session_id ); ?>"/>
				</form>

				<form name="f" id="mo2f_skip_loginform" method="post" action="" style="display:none;">
					<input type="hidden" name="option" value="mo2f_skip_2fa_setup" />
					<input type="hidden" name="miniorange_inline_save_2factor_method_nonce" value="<?php echo esc_attr( wp_create_nonce( 'miniorange-2-factor-inline-save-2factor-method-nonce' ) ); ?>" />
					<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>"/>
					<input type="hidden" name="session_id" value="<?php echo esc_attr( $session_id ); ?>"/>
				</form>
			<script>
				function mologinback(){
					jQuery('#mo2f_backto_mo_loginform').submit();
				}
				jQuery('input:radio[name=mo2f_selected_2factor_method]').click(function() {
					var selectedMethod = jQuery(this).val();
					document.getElementById("mo2f_select_2fa_methods_form").elements[0].value = selectedMethod;
					jQuery('#mo2f_select_2fa_methods_form').submit();
				});
				jQuery('a[href="#skiptwofactor"]').click(function(e) {
				jQuery('#mo2f_skip_loginform').submit();
			});
			</script>
			</body>
		</html>
						<?php
		}

		/**
		 * Gets hidden forms for inline.
		 *
		 * @param string $redirect_to Redirection url.
		 * @param string $session_id Session id.
		 * @param int    $current_user_id User id.
		 * @return string
		 */
		public function mo2f_get_inline_hidden_forms( $redirect_to, $session_id, $current_user_id ) {
			$common_helper = new Mo2f_Common_Helper();
			$html          = $common_helper->mo2f_get_validation_success_form( $redirect_to, $session_id, $current_user_id );
			$html         .= $common_helper->mo2f_backto_inline_registration_form( $session_id, $redirect_to );
			$html         .= $common_helper->mo2f_backto_login_form();
			return $html;
		}

		/**
		 * This function shows download backup code popup.
		 *
		 * @param string $redirect_to redirect url.
		 * @param string $session_id_encrypt encrypted session id.
		 * @param array  $codes Backup codes.
		 * @return void
		 */
		public function mo2f_show_generated_backup_codes_inline( $redirect_to, $session_id_encrypt, $codes ) {
			?>
	<html>
		<head>  <meta charset="utf-8"/>
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<?php

			$common_helper = new Mo2f_Common_Helper();
			$common_helper->mo2f_echo_js_css_files();
			wp_register_style( 'mo2f_bootstrap', plugins_url( 'includes/css/bootstrap.min.css', dirname( __FILE__ ) ), array(), MO2F_VERSION, false );
			wp_print_styles( 'mo2f_bootstrap' );
			?>
			<style>
				.mo2f_kba_ques, .mo2f_table_textbox{
					background: whitesmoke none repeat scroll 0% 0%;
				}
			</style>
		</head>
		<body>
			<div class="mo2f_modal" tabindex="-1" role="dialog" id="myModal5">
				<div class="mo2f-modal-backdrop"></div>
				<div class="mo2f_modal-dialog mo2f_modal-lg">
					<div class="login mo_customer_validation-modal-content">
						<div class="mo2f_modal-header">
							<h4 class="mo2f_modal-title">
							<?php esc_html_e( 'Two Factor Setup Complete', 'miniorange-2-factor-authentication' ); ?></h4>
						</div>
						<div class="mo2f_modal-body center">

							<h3> <?php esc_html_e( 'Please download the backup codes for account recovery.', 'miniorange-2-factor-authentication' ); ?></h3>

							<h4> 
								<?php
								esc_html_e(
									'You will receive the backup codes via email if you have your SMTP configured.',
									'miniorange-2-factor-authentication'
								);
								?>
								<br>
										<?php
										esc_html_e(
											'If you have received the codes on your email and do not wish to download the codes, click on Finish.',
											'miniorange-2-factor-authentication'
										);
										?>
									</h4>
							<h4> 
										<?php
										esc_html_e(
											'Backup Codes can be used to login into user account in case you forget your phone or get locked out.',
											'miniorange-2-factor-authentication'
										);
										?>
								<br>
										<?php
										esc_html_e(
											'Please use this carefully as each code can only be used once. Please do not share these codes with anyone.',
											'miniorange-2-factor-authentication'
										);
										?>
									</h4>
							<div>   
								<div style="display: inline-flex;width: 350px; ">
									<div id="clipboard" style="border: solid;width: 55%;float: left;">
										<?php
										$size = count( $codes );
										for ( $x = 0; $x < $size; $x++ ) {
											$str = $codes[ $x ];
											echo( '<br>' . esc_html( $str ) . ' <br>' );
										}

										$str1 = '';
										$size = count( $codes );
										for ( $x = 0; $x < $size; $x++ ) {
											$str   = $codes[ $x ];
											$str1 .= $str;
											if ( 4 !== $x ) {
												$str1 .= ',';
											}
										}
										?>
									</div>
									<div  style="width: 50%;float: right;">
										<form name="f" method="post" id="mo2f_download_backup_codes_inline" action="">
											<input type="hidden" name="option" value="mo2f_download_backup_codes_inline" />
											<input type="hidden" name="mo2f_inline_backup_codes" value="<?php echo esc_attr( $str1 ); ?>" />
											<input type="hidden" name="session_id" value="<?php echo esc_attr( $session_id_encrypt ); ?>"/>
											<input type="hidden" name="miniorange_inline_save_2factor_method_nonce" value="<?php echo esc_attr( wp_create_nonce( 'miniorange-2-factor-inline-save-2factor-method-nonce' ) ); ?>" />
											<input type="submit" name="Generate Codes1" id="codes" style="display:inline;width:100%;margin-left: 20%;margin-bottom: 37%;margin-top: 29%" class="miniorange_button button button-primary button-large" value="<?php esc_attr_e( 'Download Codes', 'miniorange-2-factor-authentication' ); ?>" />
										</form>
									</div>

									<form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo esc_url( wp_login_url() ); ?>" >
										<input type="hidden" name="option" value="mo2f_finish_inline_and_login" />
										<input type="hidden" name="miniorange_inline_save_2factor_method_nonce" value="<?php echo esc_attr( wp_create_nonce( 'miniorange-2-factor-inline-save-2factor-method-nonce' ) ); ?>" />
										<input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_to ); ?>"/>
										<input type="hidden" name="session_id" value="<?php echo esc_attr( $session_id_encrypt ); ?>"/>
										<input type="submit" name="login_page" id="login_page" style="display:inline;margin-left:-198%;margin-top: 289% !important;margin-right: 24% !important;width: 209%" class="miniorange_button button button-primary button-large" value="<?php esc_attr_e( 'Finish', 'miniorange-2-factor-authentication' ); ?>"  /><br>
									</form>
								</div>
							</div>

										<?php
										$common_helper = new Mo2f_Common_Helper();
										$common_helper->mo2f_customize_logo()
										?>
						</div>
					</div>
				</div>
			</div>
			<form name="f" id="mo2f_backto_mo_loginform" method="post" action="<?php echo esc_url( wp_login_url() ); ?>" style="display:none;">
				<input type="hidden" name="miniorange_mobile_validation_failed_nonce" value="<?php echo esc_attr( wp_create_nonce( 'miniorange-2-factor-mobile-validation-failed-nonce' ) ); ?>" />
			</form>
		</body>
		<script>
			function mologinback(){
				jQuery('#mo2f_backto_mo_loginform').submit();
			}
		</script>
	</html>
				<?php
				exit;
		}

	}
	new Mo2f_Inline_Popup();
}
