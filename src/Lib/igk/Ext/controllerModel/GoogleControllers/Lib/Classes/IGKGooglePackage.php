<?php
// @file: IGKGooglePackage.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Represent IGK\Core\Ext\Google namespace
*/
namespace IGK\Core\Ext\Google;
// DIRECT RENDERING
/**
* use for google package
*/
class IGKGooglePackage{
    /**
    * 
    */
    public function Button(){
        $n=igk_create_node("div");
        $n["class"]="igk-google-button";
        $n["curx"]="10px";
        $n["cury"]="10px";
        $n->Content="Google Button";
        return $n;
    }
    /**
    * 
    * @param mixed $name
    */
    public function CreateNode($name){
        if(method_exists($this, $name)){
            return call_user_func_array([$this, $name], array_slice(func_get_args(), 1));
        }
        return igk_create_node("div");
    }
}
