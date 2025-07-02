<?php
// @file: IGKHtmlCssLink.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com


namespace IGK\System\Html\Dom;


class HtmlCssLinkNode extends HtmlNode{
    protected function _acceptRender($options = null):bool{
        if($this->system && $options && ($options->Context == "mail")){
            return false;
        }
        $uri= null;//igk_html_get_system_uri($this->link, $options);        
        $tr= $uri ? $uri: $this->link;
        $this->ln["href"]= $tr;
        return $tr && $this->IsVisible;
    }
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
    protected function _getRenderingChildren($option=null){
        return array($this->ln);
    }
    public function activate($name, $condition_key=null){
        $this->ln->activate($name, $condition_key);
        return $this;
    }
    public function getCache(){
        return $this->ln->cache;
    }
     
    public function getlink(){
        return $this->ln->link;
    }
    public function getln(){
        return $this->getFlag("csslink");
    }
    public function getRel(){
        return $this->ln["rel"];
    }
    public function getSystem(){
        return $this->ln->system;
    }
    public function getType(){
        return $this->ln["type"];
    }
    protected function innerHTML(& $option=null){
        return null;
    }
    public function setAttribute($name, $value){
        $this->ln->setAttribute($name, $value);
        return $this;
    }
    protected function setln($v){
        $this->setFlag("csslink", $v);
        return $this;
    }
    public function setRel($value){
        $this->ln["rel"]=$value;
    }
    public function setType($value){
        $this->ln["type"]=$value;
        return $this;
    }
}
