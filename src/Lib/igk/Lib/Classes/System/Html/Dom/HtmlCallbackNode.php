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
    public function _acceptRender($option=null){
        return 1;
    }
    ///<summary></summary>
    /**
     * render callback constructor
     * @param null|callable $callback 
     * @return void 
     */
    public function __construct(?callable $callback=null){
        parent::__construct('igk:callbacknode');
        $this->callback = $callback;
    }
    ///<summary></summary>
    public function getCanRenderTag(){
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options=null){
        IGKOb::Start();
        $r = "";
        if ($fc=$this->callback){
            $r = $fc($options);
        }
        $s=IGKOb::Content();
        IGKOb::Clear();
        return $r.$s;
    }
}
