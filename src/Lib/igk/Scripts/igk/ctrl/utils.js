//
"use strict";
(function(){
	//controller and article functions
	var CA = igk.system.createNS("igk.ctrl.ca", {	
	});
	igk.appendProperties(CA, {
		editChange: function(tq,target, uri){
			// console.debug(target);
			// console.debug(uri);
			if (target && uri)
			(function(i){
					var p = $igk('.cnf-edit-view-result').getItemAt(0); 
					var q = window.igk.getParentById(i, target); 
					var q_xhr = window.igk.ajx.post(uri+i.value, null, function(xhr){  
					if (this.isReady()){ 
						if (xhr.getResponseHeader('content-type') == "application/json"){
							var data = JSON.parse(xhr.responseText); 
							p.setHtml(data["select_result"]);
							p.init();
							var n = $igk("#edit_ctrl").first();
							if (n){
								var dummy = $igk(document.createElement('dummy'));
								dummy.setHtml(data["edit_result"]);
								var qn =  dummy.firstChild(); 
								n.o.parentNode.insertBefore( qn.o, n.o);
								qn.init();
								n.remove(); 
							} 
							return;
						}
						this.setResponseTo(p.o);
						var r = $igk($igk(i).o.form).select('.c-opts').getItemAt(0);
						var tp = p.select('.c-opts').first();
						if (r && tp){
							r.setHtml(tp.getHtml()).init();
						}
					}
				});
				// q_xhr.setReponsteType("application/json");
			})(tq);
			return !1;
		}
	});
})();