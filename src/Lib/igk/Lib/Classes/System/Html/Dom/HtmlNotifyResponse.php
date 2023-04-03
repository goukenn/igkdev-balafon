<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlNotifyResponse.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

/**
 * return a notify response
 * @package IGK\System\Html\Dom
 */
class HtmlNotifyResponse extends HtmlNode{
    protected $tagname = "div";
    private $autohide;
    private $name;
    private $m_notifytype;
    /**
     * set the autohide 
     * @param null|bool $autohide 
     * @return void 
     */
    public function setAutohide(?bool $autohide){
        $this->autohide = $autohide;
        return $this;
    }
    public function setNotifyType($type){
        $this->m_notifytype = $type;
        return $this;
    }
    public function __construct($name, ?bool $autohide=null)
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
        if (igk_environment()->isDev()) {
            $this["title"] = $this->name;
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
    protected function _acceptRender($options = null):bool{
        if (!$this->getIsVisible()){
            return false;
        }
        $not = igk_notifyctrl($this->name);
        if ((is_null($this->autohide) && !$not->getAutohide()) || (is_bool($this->autohide) &&  !$this->autohide)){ 
            $this["class"] = '-anim-autohide';
        } 
        else 
            $this["class"] = '+anim-autohide'; 
        return true;
    }
    public function getRenderedChilds($options = null)
    {
        $not = igk_notifyctrl($this->name);
        $c = [];
        $tab = & $not->getTab();
        if(is_array($tab) && (count($tab) > 0)){
            foreach($tab as $inf){
                if(isset($inf["type"]) && isset($inf["msg"])){
                    $d = igk_create_node("div");
                    $d->setClass("igk-panel ".$inf["type"])->Content=$inf["msg"];
                    $c[] = $d; 
                }
            }
        }
        $tab = [];
        $not->clear();             
        return $c;
    }
}