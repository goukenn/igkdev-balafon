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
                console.debug('source ... : \n ' + 
                tab[e.lineNumber].substr(max(e.columnNumber -10, 0), 20));
            }
        }
    } else {
        console.debug("failed to parse core data")
    }
})();