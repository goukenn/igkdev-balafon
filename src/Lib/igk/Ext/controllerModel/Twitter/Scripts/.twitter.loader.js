// .twitter.loader.js - 
"use strict";
(function(){
	igk.ready(function(){
		var l = igk.getCurrentScript().getAttribute("data-lib");
		var o = igk.getParentScript();
		var js = document.createElement("script");
		js.src= l || "https://platform.twitter.com/widgets.js"; 
		$igk(document.body).add(js);	
		alert('load twitter widgets');
	});
})();