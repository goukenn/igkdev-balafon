(function(){
    var i = document.scripts[document.scripts.length-2];//.previousSibling;
      if (typeof(i.text) == "undefined"){
        return;
    }

    var s = i.text.trim().substring(2);
  
    function loadXml(s){
    var r = null;
    if ("DOMParser" in window) {
        var g = (new window.DOMParser()).parseFromString(s, "text/xml");
        r = g.firstChild; 
        if (r && r.tagName.toLowerCase() == "parsererror") {
            return null;
        }
    }
    else {
        r = igk.dom.activeXDocument();
        r.load(s);
    }
    return r;
    }
    if (r = loadXml("<data>"+s+"</data>")){
        if (r.lastChild){
            var b = r.lastChild.textContent; 
            try{
                if ( b && (b.length > 0)){
                    (new Function(b)).apply();
                }
            } catch (e) {
                console.error('Error:igk-winui-balafon-js-inc');
                console.debug('message: '+ e.message, e.lineNumber+":"+e.columnNumber);
                // view message rule
                var tab = b.split('\n');
                var msg = ";";
                if (tab.length< e.lineNumber){
                    var o = Math.max(0, e.columnNumber -10);
                    msg = b.substring(o, o + 40);
                }else{
                    msg = tab[e.lineNumber].substr(Math.max(e.columnNumber -10, 0), 20) ;
                }
                console.error('source ... : \n' + msg);
            
            }
        }
    } else {
        console.debug("failed to parse core data")
    }
})();