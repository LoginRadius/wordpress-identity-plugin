<!DOCTYPE html>
<html>

<?php
// Exit if called directly
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}
	
global $post, $loginRadiusObject, $loginRadiusSettings, $loginradius_api_settings, $lr_custom_interface_settings, $lr_js_in_footer, $lr_disqus_settings;

// LoginRadius js sdk must be loaded in header.
wp_register_script( 'lr-sdk', LR_CORE_URL . 'js/LoginRadiusSDK.2.0.0.js', array(), '2.0.0', $lr_js_in_footer );

// Custom Interface must be loaded in header.
wp_register_script( 'lr-custom-interface', '//cdn.loginradius.com/hub/prod/js/lr-custom-interface.3.js', array(), '3.0.0', $lr_js_in_footer);
wp_register_script( 'lr-social-login', '//hub.loginradius.com/include/js/LoginRadius.js', array(), LR_PLUGIN_VERSION, $lr_js_in_footer);
wp_register_script( 'lr-custom-interface', '//cdn.loginradius.com/hub/prod/js/lr-custom-interface.3.js', array(), '3.0.0', $lr_js_in_footer);
wp_register_script( 'lr-disqus-custom-interface', LR_DISQUS_URL . 'assets/js/lr-disqus-sso-custom.js', array(), '1.0.0' );
wp_register_style( 'lr-disqus-popup-css', LR_DISQUS_URL . 'assets/css/lr-disqus-sso-popup.css' );
wp_register_style( 'lr-form-style', LR_CORE_URL . 'assets/css/lr-form-style.min.css', array(), LR_PLUGIN_VERSION );
$callback = LR_Common::get_protocol() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$link = '';
if( $callback != NULL || ! empty( $callback ) ) {
	$link = $callback;
}
$popup_title = isset( $lr_disqus_settings['popup_title'] ) ? $lr_disqus_settings['popup_title'] : '';
?>		
<head>
	<title><?php echo $popup_title; ?></title>
	<?php
		wp_print_scripts( 'jquery' );
		wp_print_scripts( 'lr-sdk' );
		wp_print_scripts( 'lr-custom-interface' );
	    wp_print_scripts( 'lr-social-login' );
	    wp_print_scripts( 'lr-custom-interface' );
	    $args = array(
			'siteName'  => isset( $loginradius_api_settings['sitename'] ) ? $loginradius_api_settings['sitename'] : '',
			'apiKey'    => isset( $loginradius_api_settings['LoginRadius_apikey'] ) ? trim( $loginradius_api_settings['LoginRadius_apikey'] ) : '',
			'providers' => isset( $lr_custom_interface_settings['selected_providers'] ) ? $lr_custom_interface_settings['selected_providers'] : 'Providers Not Configured'
		);

	    wp_localize_script( 'lr-disqus-custom-interface', "phpvar", $args);
		wp_print_scripts( 'lr-disqus-custom-interface' );
		wp_print_styles( 'lr-disqus-popup-css' );
		wp_print_styles( 'lr-form-style' );
	?>
	<script type="text/html" id="lr_disqus_sso_tmpl">
		<a class="lr_custom_provider" onclick="return $LRIC.util.openWindow('<%=Endpoint%>&callback=<?php echo $link; ?>&is_access_token=true')" ><img src="<?php echo LR_CUSTOM_INTERFACE_URL; ?>assets/images/custom_interface/<%=Name.toLowerCase() %>.png" /></a>
	</script>
	<script type="text/javascript">
		(function($) {
			function IsEmail(email) {
			  var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
			  return regex.test(email);
			}

			function showEmailForm() {
				
				var token = sessionStorage.getItem("LRTokenKey");
				
				LoginRadiusSDK.getUserprofile( function( profile ) {

					if( '' == profile.Email || undefined == profile.Email ) {
						$('#login').html( '<div class="lr-input-style"><div><?php _e('Please enter your email address to complete login', 'LoginRadius'); ?><span id="lr_disqus_popup_error"></span></div><div><input class="lr_disqus_popup_email" type="text" placeholder="e.g someone@email.com" name="email"/><button class="lr_disqus_popup_submit">Log In</button></div></div>' );
						$('.lr_disqus_popup_submit').click(function(){
							var email = $('.lr_disqus_popup_email').val();

							//validate email
							if( IsEmail(email) ) {
								jQuery('.lr_fade').show();
								loginUser( token, email );
							} else {
								$('#lr_disqus_popup_error').html('<?php _e( 'Email address is not valid please try again', 'LoginRadius' ); ?>');
							}
						});
					} else {
						loginUser( token );
					}
				});
			}

			function loginUser( token, email ) {
				var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
				jQuery('.lr_fade').show();
				$.ajax( {
					type: 'POST',
					url: ajaxurl,
					data: {
						token: token,
						email: email,
						action: 'loginradius_login'
					},
					success: function ( data, textStatus, XMLHttpRequest ) {
						var error, success;
						try {
							data = $.parseJSON( data );
							error = data.Error;
							success = data.Success;
						} catch (e) {
							// error
							error = data;
						}

						if(error != '') {
							jQuery('.lr_fade').hide();
							console.log(error);

							if( "Email is required" == error ) {
								showEmailForm();
							} else {
								$('#lr_disqus_popup_error').html(error);
							}
						}else{
							window.close();
						}
					}
				} );
			}

			function AfterLogin(element) {
				
				var token = sessionStorage.getItem("LRTokenKey");
				loginUser( token );
			};
			
			window.onload = function () {
				jQuery('body').append('<?php echo LR_Core::get_spinner(); ?>');
				jQuery(".lr_fade").click(function () {
					jQuery('.lr_fade').hide();
				});
				if ( window.opener != null && typeof document.getElementById( 'disqus_thread' ) != null ) {
					var loginform = document.getElementById('login');
					
					<?php
					if ( ! ( $loginRadiusObject->loginradius_is_valid_guid( trim( $loginradius_api_settings['LoginRadius_apikey'] ) ) && $loginRadiusObject->loginradius_is_valid_guid( trim( $loginradius_api_settings['LoginRadius_secret'] ) ) ) ){
					?>
						loginform.innerHTML = '<div style="color:red"><?php _e( 'Your LoginRadius API key or secret is not valid, please correct it or contact LoginRadius support at <b><a href ="http://www.loginradius.com" target = "_blank">www.LoginRadius.com</a></b>', 'LoginRadius' ); ?></div>';
					<?php
					} else {
						?>
						loginform.innerHTML = '<div class="lr_disqus_sso_container"><h2 class="lr_disqus_sso_title"><?php echo $popup_title; ?></h2><span id="lr_disqus_popup_error"></span><div class="lr_disqus_sso"></div></div>';

						<?php if( isset( $lr_custom_interface_settings['custom_interface'] ) && $lr_custom_interface_settings['custom_interface'] == '1' ) {
							?>
							
							$LRIC.util.ready(function () {
								var options = {};
								options.apikey = phpvar.apiKey;
								options.appname = phpvar.siteName;
								options.providers = phpvar.providers;
								options.templatename = "lr_disqus_sso_tmpl"; 
								$LRIC.renderInterface("lr_disqus_sso", options);
							});
							LoginRadiusSDK.onlogin = AfterLogin;
						<?php }else { ?>
							var options = {};
							options.login = true;
							LoginRadius_SocialLogin.util.ready( function () {
								$ui = LoginRadius_SocialLogin.lr_login_settings;
								$ui.interfacesize = '';
								$ui.apikey = "<?php echo $loginradius_api_settings['LoginRadius_apikey'] ?>";
								$ui.callback = "<?php echo $link; ?>";
								$ui.lrinterfacecontainer = "lr_disqus_sso";
								$ui.is_access_token = true;
								$ui.interfacesize = "<?php echo isset( $loginRadiusSettings['LoginRadius_interfaceSize'] ) ? trim( $loginRadiusSettings['LoginRadius_interfaceSize'] ) : ''; ?>";
								<?php 
									if ( isset( $loginRadiusSettings['LoginRadius_numColumns'] ) && trim( $loginRadiusSettings['LoginRadius_numColumns']) != '' ) { 
										echo '$ui.noofcolumns = '. trim( $loginRadiusSettings['LoginRadius_numColumns'] ).';'; 
									}
								?>
								$ui.lrinterfacebackground = "<?php echo isset( $loginRadiusSettings['LoginRadius_backgroundColor'] ) ? trim( $loginRadiusSettings['LoginRadius_backgroundColor'] ) : ''; ?>";
								LoginRadius_SocialLogin.init( options );
							} );
							LoginRadiusSDK.onlogin = AfterLogin;
						<?php }
					}
				?>
				}
			};
		})(jQuery);
	</script>
</head>				

<body class="lr_disqus_sso_popup">
	<?php echo '<div id="login"></div>'; ?>
</body>
</html>