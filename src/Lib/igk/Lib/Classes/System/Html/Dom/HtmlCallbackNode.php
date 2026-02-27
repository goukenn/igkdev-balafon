<?php
// @file: IGKHtmlCallbackNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
use IGKOb;

/**
* auto generate doc.
*/
final class HtmlCallbackNode extends HtmlNode{

    /**
    * Callback handler for callback.
    * @var mixed
    */
    var $callback;

    /**
    * Accept render.
    * @param null|mixed $option
    */
    public function _acceptRender($option=null){
        return 1;
    }
    /**
     * render callback constructor
     * @param null|callable $callback 
     * @return void 
     */

    public function __construct(?callable $callback=null){
        parent::__construct('igk:callbacknode');
        $this->callback = $callback;
    }

    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag(){
        return false;
    }

    /**
    * Renders.
    * @param null|mixed $options
    */
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