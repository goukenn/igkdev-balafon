<?php
// @file: IGKHtmlCallbackNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

use IGKOb;

/** @package  */
final class HtmlCallbackNode extends HtmlNode{
    var $callback;
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    public function __AcceptRender($option=null){
        return 1;
    }
    ///<summary></summary>
    public function __construct(){
        parent::__construct('igk:callbacknode');
    }
    ///<summary></summary>
    public function getIsRenderTagName(){
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options=null){
        IGKOb::Start();
        $fc=$this->callback;
        $fc($options);
        $s=IGKOb::Content();
        IGKOb::Clear();
        return $s;
    }
}
