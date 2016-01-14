jQuery(document).ready(function($) {

	//tabs
	$('.lr-options-tab-btns li').click(function(){
		var tab_id = $(this).attr('data-tab');

		$('.lr-options-tab-btns li').removeClass('lr-active');
		$('.lr-tab-frame').removeClass('lr-active');

		$(this).addClass('lr-active');
		$("#"+tab_id).addClass('lr-active');
	});

	var loginRadiusSharingHorizontalSharingProviders;
	var loginRadiusSharingVerticalSharingProviders;
	
	function init(){
		loginRadiusSharingHorizontalSharingProviders = $('.LoginRadius_hz_share_providers');
		loginRadiusSharingVerticalSharingProviders = $('.LoginRadius_ve_share_providers')

		var h_selected = $('input:radio[name="LoginRadius_share_settings[horizontal_share_interface]"]:checked').val();
		var v_selected = $('input:radio[name="LoginRadius_share_settings[vertical_share_interface]"]:checked').val();

		if(h_selected == "32-h" || h_selected == "16-h" || h_selected == "responsive") {
			$('#lr_hz_theme_options').show();
			$('#lr_hz_hz_theme_options').show();
			$('#lr_hz_ve_theme_options').hide();
			$('#login_radius_horizontal_rearrange_container').show();
		}else if (h_selected == "hybrid-h-h" || h_selected == "hybrid-h-v") {
			$('#lr_hz_theme_options').show();
			$('#lr_hz_hz_theme_options').hide();
			$('#lr_hz_ve_theme_options').show();
			$('#login_radius_horizontal_rearrange_container').hide();
		}else{
			$('#lr_hz_theme_options').hide();
			$('#login_radius_horizontal_rearrange_container').hide();
		}

		if(v_selected == "32-v" || v_selected == "16-v") {
			$('#lr_ve_theme_options').show();
			$('#lr_ve_hz_theme_options').show();
			$('#lr_ve_ve_theme_options').hide();
			$('#login_radius_vertical_rearrange_container').show();
		}else if (v_selected == "hybrid-v-h" || v_selected == "hybrid-v-v") {
			$('#lr_ve_theme_options').show();
			$('#lr_ve_hz_theme_options').hide();
			$('#lr_ve_ve_theme_options').show();
			$('#login_radius_vertical_rearrange_container').hide();
		}else{
			$('#lr_ve_theme_options').hide();
			$('#login_radius_vertical_rearrange_container').hide();
		}
	}

	if($('#lr-enable-horizontal').is(':checked')){
		$(".lr-option-disabled-hr").hide();
	}else{
		$(".lr-option-disabled-hr").show();
	}

	if($('#lr-enable-vertical').is(':checked')){
		$(".lr-option-disabled-vr").hide();
	}else{
		$(".lr-option-disabled-vr").show();
	}

	$('input:radio[name="LoginRadius_share_settings[horizontal_share_interface]"]').change( function(){
		if(this.value == "32-h" || this.value == "16-h" || this.value == "responsive") {
			$('#lr_hz_theme_options').show();
			$('#lr_hz_hz_theme_options').show();
			$('#lr_hz_ve_theme_options').hide();
			$('#login_radius_horizontal_rearrange_container').show();
		}else if (this.value == "hybrid-h-h" || this.value == "hybrid-h-v") {
			$('#lr_hz_theme_options').show();
			$('#lr_hz_hz_theme_options').hide();
			$('#lr_hz_ve_theme_options').show();
			$('#login_radius_horizontal_rearrange_container').hide();
		}else{
			$('#lr_hz_theme_options').hide();
			$('#login_radius_horizontal_rearrange_container').hide();
		}
	});

	$('input:radio[name="LoginRadius_share_settings[vertical_share_interface]"]').change( function(){
		if(this.value == "32-v" || this.value == "16-v") {
			$('#lr_ve_theme_options').show();
			$('#lr_ve_hz_theme_options').show();
			$('#lr_ve_ve_theme_options').hide();
			$('#login_radius_vertical_rearrange_container').show();
		}else if (this.value == "hybrid-v-h" || this.value == "hybrid-v-v") {
			$('#lr_ve_theme_options').show();
			$('#lr_ve_hz_theme_options').hide();
			$('#lr_ve_ve_theme_options').show();
			$('#login_radius_vertical_rearrange_container').hide();
		}else{
			$('#lr_ve_theme_options').hide();
			$('#login_radius_vertical_rearrange_container').hide();
		}
	});

	$("#loginRadiusHorizontalSortable").sortable({
		scroll: false,
		revert: true,
		tolerance: 'pointer',
		items: '> li:not(.lr-pin)',
		containment: 'parent',
		axis: 'x'
	});

	$("#loginRadiusVerticalSortable").sortable({
		scroll: false,
		revert: true,
		tolerance: 'pointer',
		items: '> li:not(.lr-pin)',
		containment: 'parent',
		axis: 'y'
	});

	// prepare rearrange provider list
	function loginRadiusRearrangeProviderList(elem, sharingType) {
		$ul = $('#loginRadius' + sharingType + 'Sortable');
		if (elem.checked) {
			$listItem = $('<li />');
			$listItem.attr({
				id: 'loginRadius' + sharingType + 'LI' + elem.value,
				title: elem.value,
				class: 'lrshare_iconsprite32 lr-icon-' + elem.value.toLowerCase()
			});

			// append hidden field
			$provider = $('<input />');
			$provider.attr({
				type: 'hidden',
				name: 'LoginRadius_share_settings[' + sharingType.toLowerCase() + '_rearrange_providers][]',
				value: elem.value
			});
			$listItem.append($provider);
			$ul.append($listItem);
		} else {
			if ($('#loginRadius' + sharingType + 'LI' + elem.value)) {
				$('#loginRadius' + sharingType + 'LI' + elem.value).remove();
			}
		}
	}

	// limit maximum number of providers selected in horizontal sharing
	function loginRadiusHorizontalSharingLimit( elem ) {
		var checkCount = 0;
		for (var i = 0; i < loginRadiusSharingHorizontalSharingProviders.length; i++) {
			if (loginRadiusSharingHorizontalSharingProviders[i].checked) {
				// count checked providers
				checkCount++;
				if (checkCount >= 9) {
					elem.checked = false;
					$('#loginRadiusHorizontalSharingLimit').show().delay(3000).fadeOut();
					return;
				}
			}
		}
	}

	// limit maximum number of providers selected in horizontal sharing
	function loginRadiusHorizontalVerticalSharingLimit( elem ) {
		var checkCount = 0;
		var inputs = document.getElementsByClassName(elem.className);

		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].checked) {
				// count checked providers
				checkCount++;
				if (checkCount >= 9) {
					elem.checked = false;
					$('#loginRadiusHorizontalVerticalSharingLimit').show().delay(3000).fadeOut();
					return;
				}
			}
		}
	}

	// limit maximum number of providers selected in vertical sharing
	function loginRadiusVerticalSharingLimit( elem ) {
		var checkCount = 0;
		for (var i = 0; i < loginRadiusSharingVerticalSharingProviders.length; i++) {
			if (loginRadiusSharingVerticalSharingProviders[i].checked) {
				// count checked providers
				checkCount++;
				if (checkCount >= 9) {
					elem.checked = false;
					$('#loginRadiusVerticalSharingLimit').show().delay(3000).fadeOut();
					return;
				}
			}
		}
	}

	function loginRadiusVerticalVerticalSharingLimit( elem ) {
		var checkCount = 0;
		var inputs = document.getElementsByClassName(elem.className);
		for (var i = 0; i < inputs.length; i++) {
			if (inputs[i].checked) {
				// count checked providers
				checkCount++;
				if (checkCount >= 9) {
					elem.checked = false;
					$('#loginRadiusVerticalVerticalSharingLimit').show().delay(3000).fadeOut();
					return;
				}
			}
		}
	}

	$('.LoginRadius_hz_share_providers').change(function(){
		loginRadiusHorizontalSharingLimit( this );
		loginRadiusRearrangeProviderList( this, 'Horizontal' );
	});

	$('.LoginRadius_hz_ve_share_providers').change(function(){
		loginRadiusHorizontalVerticalSharingLimit( this );
	});

	$('.LoginRadius_ve_share_providers').change(function(){
		loginRadiusVerticalSharingLimit( this );
		loginRadiusRearrangeProviderList( this, 'Vertical' );
	});

	$('.LoginRadius_ve_ve_share_providers').change(function(){
		loginRadiusVerticalVerticalSharingLimit( this );
	});

	$('#lr-clicker-hr-home').change(function(){
		if($(this).is(':checked')){
			$('.lr-clicker-hr-home-options.default').prop('checked', true);
		}else{
			$('.lr-clicker-hr-home-options').prop('checked', false);
		}
	});

	$('#lr-clicker-hr-post').change(function(){
		if($(this).is(':checked')){
			$('.lr-clicker-hr-post-options.default').prop('checked', true);
		}else{
			$('.lr-clicker-hr-post-options').prop('checked', false);
		}
	});

	$('#lr-clicker-hr-static').change(function(){
		if($(this).is(':checked')){
			$('.lr-clicker-hr-static-options.default').prop('checked', true);
		}else{
			$('.lr-clicker-hr-static-options').prop('checked', false);
		}
	});

	$('#lr-clicker-hr-excerpts').change(function(){
		if($(this).is(':checked')){
			$('.lr-clicker-hr-excerpts-options.default').prop('checked', true);
		}else{
			$('.lr-clicker-hr-excerpts-options').prop('checked', false);
		}
	});

	$('#lr-enable-horizontal').change(function(){
		if($(this).is(':checked')){
			$('#lr-clicker-hr-home').prop('checked', true);
			$('.lr-clicker-hr-home-options.default').prop('checked', true);
			$('#lr-clicker-hr-post').prop('checked', true);
			$('.lr-clicker-hr-post-options.default').prop('checked', true);
			$('#lr-clicker-hr-static').prop('checked', true);
			$('.lr-clicker-hr-static-options.default').prop('checked', true);
			$('#lr-clicker-hr-excerpts').prop('checked', true);
			$('.lr-clicker-hr-excerpts-options.default').prop('checked', true);

		}else{
			$('#lr-clicker-hr-home').prop('checked', false);
			$('.lr-clicker-hr-home-options').prop('checked', false);
			$('#lr-clicker-hr-post').prop('checked', false);
			$('.lr-clicker-hr-post-options').prop('checked', false);
			$('#lr-clicker-hr-static').prop('checked', false);
			$('.lr-clicker-hr-static-options').prop('checked', false);
			$('#lr-clicker-hr-excerpts').prop('checked', false);
			$('.lr-clicker-hr-excerpts-options').prop('checked', false);
		}
	});

	$('#lr-clicker-vr-home').change(function(){
		if($(this).is(':checked')){
			$('.lr-clicker-vr-home-options.default').prop('checked', true);
		}else{
			$('.lr-clicker-vr-home-options').prop('checked', false);
		}
	});

	$('#lr-clicker-vr-post').change(function(){
		if($(this).is(':checked')){
			$('.lr-clicker-vr-post-options.default').prop('checked', true);
		}else{
			$('.lr-clicker-vr-post-options').prop('checked', false);
		}
	});

	$('#lr-clicker-vr-static').change(function(){
		if($(this).is(':checked')){
			$('.lr-clicker-vr-static-options.default').prop('checked', true);
		}else{
			$('.lr-clicker-vr-static-options').prop('checked', false);
		}
	});

	$('#lr-enable-vertical').change(function(){
		if($(this).is(':checked')){
			$('#lr-clicker-vr-home').prop('checked', true);
			$('.lr-clicker-vr-home-options.default').prop('checked', true);
			$('#lr-clicker-vr-post').prop('checked', true);
			$('.lr-clicker-vr-post-options.default').prop('checked', true);
			$('#lr-clicker-vr-static').prop('checked', true);
			$('.lr-clicker-vr-static-options.default').prop('checked', true);

		}else{
			$('#lr-clicker-vr-home').prop('checked', false);
			$('.lr-clicker-vr-home-options').prop('checked', false);
			$('#lr-clicker-vr-post').prop('checked', false);
			$('.lr-clicker-vr-post-options').prop('checked', false);
			$('#lr-clicker-vr-static').prop('checked', false);
			$('.lr-clicker-vr-static-options').prop('checked', false);
		}
	});

	$('#lr-enable-horizontal').change(function(){
		if($(this).is(':checked')){
			$(".lr-option-disabled-hr").hide();
		}else{
			$(".lr-option-disabled-hr").show();
		}
	});

	$('#lr-enable-vertical').change(function(){
		if($(this).is(':checked')){
			$(".lr-option-disabled-vr").hide();
		}else{
			$(".lr-option-disabled-vr").show();
		}
	});

	init();
})
