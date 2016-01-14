// LoginRadius LiveFyre


jQuery(document).ready(function($) {
	if($('#lr-livefyre-enable').is(':checked')){
		$(".lr-option-disabled-hr.livefyre").hide();
	}else{
		$(".lr-option-disabled-hr.livefyre").show();
	}

	$('#lr-livefyre-enable').change(function(){
		if($(this).is(':checked')){
			$(".lr-option-disabled-hr.livefyre").hide();
		}else{
			$(".lr-option-disabled-hr.livefyre").show();
		}
	});
});