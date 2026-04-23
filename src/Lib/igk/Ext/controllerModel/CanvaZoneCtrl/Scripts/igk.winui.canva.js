//represent canvas manager
"uses strict";
(function(){
igk.system.createNS("igk.winui.canva",{
	initctrl : function(uri){
		var p = igk.getParentScript();
		if (!p || (p.tagName.toLowerCase() != 'canvas'))
			return;
		/**
		 * evaluate script in canvas context
		 * @param {*} text 
		 */	
		function evalScript(text)
		{
		//eval script
			var canva = p;
			var v_context =canva.getContext ? canva.getContext("2d") : null;
			if (v_context ==null)
				return;
			try{
				(new Function(text)).apply(canva);  
			}
			catch(ex){
				igk.winui.notify.showErrorInfo("Exception - evalScript", "Error : <br />"+ex);
			}
		}
		window.igk.ajx.aget(uri, null, function(xhr){
			if (this.isReady())
			{		
				evalScript.apply(p, new Array(xhr.responseText));
			}
		});
	}
});
})();