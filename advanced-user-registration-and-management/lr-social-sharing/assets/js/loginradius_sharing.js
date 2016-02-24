var islrsharing = true; 
var islrsocialcounter = true;
var hybridsharing = true;

(function($) {
	$(window).load(function() {
		if( $("body").hasClass("admin-bar") ) {
			$("body.admin-bar").find(".lr-share-vertical-fix[style*='top:']").css("top","32px");
		}
	});
})(jQuery);
