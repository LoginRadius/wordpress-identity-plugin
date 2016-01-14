// LoginRadius Disqus SSO Custom Interface


jQuery(document).ready(function($) {
	if($('#lr-disqus-enable').is(':checked')){
		$(".lr-option-disabled-hr.disqus").hide();
	}else{
		$(".lr-option-disabled-hr.disqus").show();
	}

	$('#lr-disqus-enable').change(function(){
		if($(this).is(':checked')){
			$(".lr-option-disabled-hr.disqus").hide();
		}else{
			$(".lr-option-disabled-hr.disqus").show();
		}
	});
});
