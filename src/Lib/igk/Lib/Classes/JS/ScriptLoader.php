<?php
namespace IGK\JS;

///<summary>Represent default script loader </summary>
/**
* Represent default script loader 
*/
class ScriptLoader{
    var $ctrl;
    var $target;
    ///<summary>Represente __construct function</summary>
    ///<param name="controller"></param>
    ///<param name="cachetarget"></param>
    /**
    * Represente __construct function
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
    * Represente Load function
    * @param  $doc
    * @param  $folder
    * @param  $created
    */
    public function Load($doc, $folder, $created){
        $is_prod=igk_environment()->is("production");
        $files=igk_io_getfiles($this->ctrl->getScriptsDir(), "/\.js$/");
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
                igk_io_w2file($file, igk_ob_get_func("igk_zip_output", [$out, 0, 0]));
            }
            $doc->addTempScript($file);
        }
    }
}