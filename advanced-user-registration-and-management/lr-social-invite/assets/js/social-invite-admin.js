/*
Author: LoginRadius Team
Version: 1.0
Author URI: http://www.LoginRadius.com
*/

jQuery( function( $ ) {

	function hideAndShowElement(element, inputBoxName) {
		if (element.is(':checked')) {
			jQuery(inputBoxName).show();
		} else {
			jQuery(inputBoxName).hide();
		}
	}

	jQuery('#lr-enable-custom-email').change(function() {
		hideAndShowElement( jQuery(this), '.lr-custom-email-settings' );
	});

	hideAndShowElement(jQuery('#lr-enable-custom-email'), '.lr-custom-email-settings');

	if($('#lr-social-invite-enable').is(':checked')){
		$(".lr-option-disabled-hr.lr-social-invite").hide();
	}else{
		$(".lr-option-disabled-hr.lr-social-invite").show();
	}

	$('#lr-social-invite-enable').change(function(){
		if($(this).is(':checked')){
			$(".lr-option-disabled-hr.lr-social-invite").hide();
		}else{
			$(".lr-option-disabled-hr.lr-social-invite").show();
		}
	});

});