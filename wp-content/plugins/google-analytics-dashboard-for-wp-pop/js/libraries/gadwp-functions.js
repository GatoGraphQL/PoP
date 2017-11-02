(function($){
popGADWP = {

	//-------------------------------------------------
	// PUBLIC functions
	//-------------------------------------------------

	// pageSectionFetchSuccess : function(args) {

	// 	var t = this;

	// 	// Only register the Google Analytics call if it is not a silent page
	// 	var options = args.options;
	// 	if (!options.silentDocument) {

	// 		ga('send', 'pageview');
	// 	}
	// },
	stateURLPushed : function(args) {

		var t = this;
		// Provide the path: remove the domain from the URL to track
		// Documentation: https://developers.google.com/analytics/devguides/collection/analyticsjs/single-page-applications
		ga('set', 'page', args.url.substr(M.HOME_DOMAIN.length));
		ga('send', 'pageview');
	},
};
})(jQuery);

//-------------------------------------------------
// Initialize
//-------------------------------------------------
// popJSLibraryManager.register(popGADWP, ['pageSectionFetchSuccess']);
popJSLibraryManager.register(popGADWP, ['stateURLPushed']);
