"use strict";
// @component: igk-transitionblock
(function(){
/**
 * handle animation 
 * @param {*} q 
 */
function animateHandle(q){
	var d = function(e){		
		if (this.getisVisible()){
			q.addClass("animate");
		}
	};
	var _po = q.getscrollParent().o;
	if (_po == document.body){
		_po = $igk(document);
	}else{
		_po = $igk(_po);
	}
	_po.reg_event("scroll", function(){
		if (q.getisVisible()){
			q.addClass("animate");
		}
	}); 
	q.on("transitionend", function(e){
		if (e.target == q.o){
			// console.debug("transitionend: "+ e.propertyName);
			if (e.propertyName=="transform")
				_po.unreg_event("scroll", d);
		}
	});
};
igk.winui.initClassControl("igk-transitionblock", function(){
	if (this.getisVisible()){
		this.addClass("animate");
	}else{
		new animateHandle(this);
	} 
}); 
})();