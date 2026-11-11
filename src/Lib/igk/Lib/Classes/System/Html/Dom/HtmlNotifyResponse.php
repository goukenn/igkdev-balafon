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
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "div";
    /**
    * Property: autohide.
    * @var ?bool
    */
    private $autohide;
    /**
    * Name of name.
    * @var mixed
    */
    private $name;
    /**
    * Type of notifytype.
    * @var mixed
    */
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
    /**
    * Sets Notify Type.
    * @param mixed $type
    */
    public function setNotifyType($type){
        $this->m_notifytype = $type;
        return $this;
    }
    /**
    * .ctr
    * @param mixed $name
    * @param null|bool $autohide
    */
    public function __construct($name, ?bool $autohide=null)
    {
        $this->autohide = $autohide;
        $this->name = $name;
        parent::__construct();
    }
    /**
    * Initializes.
    */
    protected function initialize()
    {
        $this["class"] = "igk-notify-host";
        if ($this->autohide) {
            $this["class"] = "+igk-anim-autohide";
        }
        if (igk_environment()->isDev()) {
            $this["title"] = $this->name;
        } 
    }
    /**
    * auto generate doc.
    * @return false
    */
    public function getCanAddChilds()
    {
        return false;
    }
    /**
    * auto generate doc.
    * @return bool
    */
    public function getIsVisible(): bool
    {
        $not = igk_notifyctrl($this->name);
        return $not && ($t = $not->getTab()) && (count($t)> 0);
    }
    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool{
        if (!$this->getIsVisible()){
            return false;
        }
        $not = igk_notifyctrl($this->name);
        if ((is_null($this->autohide) && !$not->getAutohide()) || (is_bool($this->autohide) &&  !$this->autohide)){ 
            $this["class"] = '-igk-anim-autohide';
        } 
        else 
            $this["class"] = '+igk-anim-autohide'; 
        return true;
    }
    /**
    * Returns Rendered Childs.
    * @param null|mixed $options
    */
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
        if (!$this->autohide){
            $n = igk_create_notagnode();
            $n->a('#')
            ->setClass('')
            ->setAttributes([
                'alt'=>__('remove'),
                'class'=>'posab loc-r-20px loc-t-20px',
                'style'=>'top:8px; right:8px;',
            ])
            ->on('click', 'return ($igk(this.o.parentNode).remove(), !1);')
            ->setContent(__('remove'))
            ->content = igk_svg_use('drop');
            $c[] = $n;
        }
        $tab = [];
        $not->clear();             
        return $c;
    }
}