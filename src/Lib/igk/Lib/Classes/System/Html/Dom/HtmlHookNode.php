<?php
// @file: IGKHtmlHookNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

class HtmlHookNode extends HtmlNode{
    private $eventType, $source;
    ///<summary></summary>
    ///<param name="eventType"></param>
    ///<param name="options" default="null"></param>
    public function __construct($eventType, $options=null){
        parent::__construct("igk-hook-node");
        $this->eventType=$eventType;
        $this->source=$options;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    protected function __getRenderingChildren($options=null){
        return null;
    }
    ///<summary></summary>
    public function getCanRenderTag(){
        return false;
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options=null){
        ob_start();
        igk_hook($this->eventType, ["object"=>$this, "options"=>$options]);
        $s=ob_get_contents();
        ob_end_clean();
        return $s;
    }
}
