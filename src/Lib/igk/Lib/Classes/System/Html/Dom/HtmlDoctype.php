<?php
// @file: IGKHtmlDoctype.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;


final class HtmlDoctype extends HtmlItemBase{
    ///<summary></summary>
    ///<param name="value"></param>
    public function __construct($value){
        $this->Content=$value;
    }
   
    ///<summary></summary>
    ///<param name="item"></param>
    ///<param name="index" default="null"></param>
    protected function _addChild($item, $index=null){
        return false;
    }
    ///<summary></summary>
    protected function _acceptRender($options = null):bool{
        return true;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options=null){ 
        $out="<!DOCTYPE ".$this->Content. ">".igk_html_indent_line($options);
        return $out;
    }
}
