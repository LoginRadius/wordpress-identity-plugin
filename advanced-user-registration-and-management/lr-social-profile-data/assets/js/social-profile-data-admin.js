/*
Author: LoginRadius Team
Version: 1.0
Author URI: http://www.LoginRadius.com
*/

jQuery(function($) {

	function hideAndShowElement(element, inputBoxName) {
		if (element.is(':checked')) {
			$(inputBoxName).show();
		} else {
			$(inputBoxName).hide();
		}
	}

	// Hide/Show Options if enabled/disabled on change
	$('#lr-enable-custom-popup').change(function() {
		hideAndShowElement($(this), '.lr-custom-popup-settings');
	});

	hideAndShowElement($('#lr-enable-custom-popup'), '.lr-custom-popup-settings');

	// Turn Off/On Custom Popup Options
	$('#lr-enable-custom-popup').change(function(){
		if($(this).is(':checked')){
			$('.lr-custom-popup-options').prop('checked', true);
		}else{
			$('.lr-custom-popup-options').prop('checked', false);
		}
	});

	$('.custom_option select').change(function() {
		if (true === $(this).children('option[value="dropdown"]').is(":selected")) {
			$(this).attr("value", "dropdown");
		}
		else {
			$(this).attr("value", "text");
		}
	});

	$( '.custom_option select option:selected' ).each(function() {
	  $( this ).parent().attr("value", $( this ).attr("value"));
	});


});