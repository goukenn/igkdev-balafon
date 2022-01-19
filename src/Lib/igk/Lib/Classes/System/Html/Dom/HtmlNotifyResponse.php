<?php

namespace IGK\System\Html\Dom;


class HtmlNotifyResponse extends HtmlNode{
    protected $tagname = "div";
    private $autohide;
    private $name;
    public function __construct($name, $autohide=1)
    {
        $this->autohide = $autohide;
        $this->name = $name;
        parent::__construct();
        
    }
    protected function initialize()
    {
        $this["class"] = "igk-notify-host";
        if ($this->autohide) {
            $this["class"] = "+anim-autohide";
        }
        if (igk_environment()->is("DEV")) {
            $this["title"] = igk_str_ns($this->name);
        } 
    }
    public function getCanAddChilds()
    {
        return false;
    }
    public function getIsVisible()
    {
        return ($not = igk_notifyctrl($this->name)) && ($t = $not->getTab()) && (count($t)> 0);
    }
    public function getRenderedChilds($options = null)
    {
        $not = igk_notifyctrl($this->name);
        $c = [];
        $tab=$not->getTab();
        if(is_array($tab) && (count($tab) > 0)){
            foreach($tab as $inf){
                if(isset($inf["type"]) && isset($inf["msg"])){
                    $d = new HtmlNode("div");
                    $d->setClass("igk-panel ".$inf["type"])->Content=$inf["msg"];
                    $c[] = $d ; 
                }
            }
            $not->clear();            
        }
        return $c;
    }
}