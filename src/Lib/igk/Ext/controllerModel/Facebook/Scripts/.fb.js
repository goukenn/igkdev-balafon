

//.facebook.loader.js
"use strict";
(function(locale){
	console.error("present");
	return;
	
	var _lib = igk.system.createNS("igk.lib");
	if (typeof(FB)!='undefined'){
		console.debug("already "+FB);		
		// FB.init();	
		FB.XFBML.parse();		
		return;
	}
	if (!locale){
		locale = "en_GB";
	}
	//console.debug("register facebook lib");
	var c= igk.getCurrentScript();
	var l = c.getAttribute("data-lib");
	var lc = c.getAttribute("data-locale");
	var lv = c.getAttribute("data-version");
	console.debug(locale, lc);
	var o = igk.getParentScript();
	var js = document.createElement("script");
	// js.src= l || 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.11';	
	js.src= l || 'https://connect.facebook.net/'+locale+'/sdk.js#xfbml=1&version=v2.11';	
	
	// wait on loading to append data
	igk.ready(function(){
		if (!$igk(".fb-root").first()){
			var d= document.createElement("div");
			d.className="fb-root";
			$igk(document.body).add(d);		
		}		
		$igk(document.body).add(js);
		_lib.fbLib={host:c};
	});	
})();