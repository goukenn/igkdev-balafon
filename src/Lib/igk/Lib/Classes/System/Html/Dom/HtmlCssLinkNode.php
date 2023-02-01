<?php
// @file: IGKHtmlCssLink.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com


namespace IGK\System\Html\Dom;


///<summary>special css link node</summary>
class HtmlCssLinkNode extends HtmlNode{
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    protected function __AcceptRender($o=null){
        if($this->system && $o && ($o->Context == "mail")){
            return false;
        }
        $uri=igk_html_get_system_uri($this->link, $o);        
        $tr= $uri ? $uri: $this->link;
        $this->ln["href"]= $tr;
        return $tr && $this->IsVisible;
    }
    ///<summary></summary>
    ///<param name="link"></param>
    ///<param name="system" default="false"></param>
    public function __construct($link, $system=false, $defer=0){
        parent::__construct("igk-css-link");
        $ln = self::CreateWebNode("link"); 
         
        $ln["type"]="text/css";
        $ln["rel"]="stylesheet";
        if($defer)
            $ln->activate("defer");
        $ln->link=$link;
        $ln->cache=false;
        $ln->system=$system;
        $this->setln($ln);  
    }
    public function getCanRenderTag()
    {
        return false;
    }
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    protected function __getRenderingChildren($option=null){
        return array($this->ln);
    }
    ///<summary>Represente activate function</summary>
    ///<param name="name"></param>
    public function activate($name){
        $this->ln->activate($name);
        return $this;
    }
    ///<summary></summary>
    public function getCache(){
        return $this->ln->cache;
    }
     
    ///<summary></summary>
    public function getlink(){
        return $this->ln->link;
    }
    ///<summary></summary>
    public function getln(){
        return $this->getFlag("csslink");
    }
    ///<summary></summary>
    public function getRel(){
        return $this->ln["rel"];
    }
    ///<summary></summary>
    public function getSystem(){
        return $this->ln->system;
    }
    ///<summary></summary>
    public function getType(){
        return $this->ln["type"];
    }
    ///<summary></summary>
    ///<param name="option" default="null" ref="true"></param>
    protected function innerHTML(& $option=null){
        return null;
    }
    ///<summary>Represente setAttribute function</summary>
    ///<param name="name"></param>
    ///<param name="value"></param>
    public function setAttribute($name, $value){
        $this->ln->setAttribute($name, $value);
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    protected function setln($v){
        $this->setFlag("csslink", $v);
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setRel($value){
        $this->ln["rel"]=$value;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    public function setType($value){
        $this->ln["type"]=$value;
        return $this;
    }
}
