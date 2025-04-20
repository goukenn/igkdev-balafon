<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ScriptLoader.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\JS;

use IGK\System\IO\HtmlDocument\IDocumentScriptLoader;
use IGKHtmlDoc;

///<summary>Represent default script loader </summary>
/**
* Represent default script loader 
*/
class ScriptLoader implements IDocumentScriptLoader{
    var $ctrl;
    var $target;
    ///<summary>Represente __construct function</summary>
    ///<param name="controller"></param>
    ///<param name="cachetarget"></param>
    /**
    * Represent __construct function
    * @param  $controller
    * @param  $cachetarget
    */
    public function __construct($controller, $cachetarget){
        $this->ctrl=$controller;
        $this->target=$cachetarget;
    }
    ///<summary>Represente Load function</summary>
    ///<param name="doc"></param>
    ///<param name="folder"></param>
    ///<param name="created"></param>
    /**
    * load cached document
    * @param  $doc
    * @param  $folder
    * @param  $created
    */
    public function loadScripts(IGKHtmlDoc $doc, ?string $folder=null, bool $created=false){

        $is_prod=igk_environment()->isOPS();
        $files=igk_io_getfiles($this->ctrl->getScriptsDir(), '/\.js$/i');
        if(!$is_prod){
            $cache_js=array();
            foreach($files as $f){
                $doc->addTempScript($f);
                $cache_js[$f]=$f;
            }
        }
        else{
            $cachedir=igk_io_cacheddist_jsdir();
            $file=$cachedir.$this->target;
            if(!file_exists($file)){
                $out=igk_js_dist_scripts($files);
                igk_io_w2file($file,  $out); 
            }
            $doc->addTempScript($file);
        }
    }
}