<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlFormNode.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlUtils;
use IGK\System\Html\Traits\HostableItemTrait;

///<summary>igk framework form</summary>
/**
 * igk framework form
 */
final class HtmlFormNode extends HtmlNode
{
    use HostableItemTrait; 
    const URLEncoded = "application/x-www-form-urlencoded";
    private $bodydiv;
    private $footdiv;
    private $m_definition;
    private $m_encType;
    private $m_nofoot;
    private $m_notitle;
    private $topdiv;
    public function multipart(){
        $this['enctype'] = 'multipart/form-data';
        return $this;
    }
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    /**
     * 
     * @param mixed $o the default value is null
     */
    protected function _acceptRender($options = null):bool
    {
        $e = $this->topdiv->Content;
        $this->topdiv->setIsVisible(!empty($e) && !$this->m_notitle);
        $this->footdiv->setIsVisible($this->footdiv->gethasContent() && !$this->m_nofoot);
        return true;
    }
    ///<summary></summary>
    ///<param name="notitle" default="false"></param>
    ///<param name="nofoot" default="true"></param>
    /**
     * 
     * @param mixed $notitle the default value is false
     * @param mixed $nofoot the default value is true
     */
    public function __construct($action = ".", $method = "POST", $notitle = false, $nofoot = true)
    {
        parent::__construct("form");
        $this->Method = $method;
        $this->Action = $action;
        $this->m_encType = true;
        $this->m_notitle = $notitle;
        $this->m_nofoot = $nofoot;
        $this["class"] = "igk-form";
        $this->topdiv = new HtmlFormTitleNode();
        $this->bodydiv = igk_create_node("div")->setAttributes(["class" => 'content']);
        $this->footdiv = igk_create_node("div")->setAttributes(["class" => "foot"]);
        $this->m_definition = new HtmlFormInnerNode($this);
        $this->m_definition->Add($this->topdiv);
        $this->m_definition->Add($this->bodydiv);
        $this->m_definition->Add($this->footdiv);

        parent::_Add($this->m_definition);
    }
    ///<summary></summary>
    ///<param name="item"></param>
    ///<param name="index" default="null"></param>
    /**
     * 
     * @param mixed $item
     * @param mixed $index the default value is null
     */
    protected function _Add($item, $index = null):bool
    {
        return $this->bodydiv->_Add($item);
    }
    ///<summary></summary>
    ///<param name="nameoritem"></param>
    ///<param name="attributes" default="null"></param>
    ///<param name="index" default="null"></param>
    /**
     * 
     * @param mixed $nameoritem
     * @param mixed $attributes the default value is null
     * @param mixed $index the default value is null
     */
    public function add($nameoritem, $attributes = null, $index = null)
    {
        return $this->bodydiv->add($nameoritem, $attributes, $index);
    }
    ///<summary>input environement confirmation</summary>
    /**
     * input environement confirmation
     */
    public function addConfirm($v = 1)
    {
        return $this->addInput("confirm", "hidden", $v);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $n
     * @param mixed $v
     */
    public function addHidden($n, $v)
    {
        return $this->addInput($n, "hidden", $v);
    }
    ///<summary></summary>
    /**
     * 
     */
    public function addToken()
    {
        $tokenid = igk_html_form_tokenid();
        $i = $this->add('input');
        $i["name"] = $tokenid;
        $i["value"] = 1;
        $i["type"] = "hidden";
        return $i;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function ClearChilds()
    {
        $this->bodydiv->clearChilds();
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getAction()
    {
        return $this["action"];
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getBox()
    {
        return $this->bodydiv;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getContent()
    {
        return null;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getEncType()
    {
        return $this->m_encType;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getFooter()
    {
        return $this->footdiv;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getMethod()
    {
        return $this["method"];
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getNoFoot()
    {
        return $this->m_nofoot;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getNoTitle()
    {
        return $this->m_notitle;
    }
    ///<summary></summary>
    /**
     * 
     */
    public function getTitle()
    {
        return $this->topdiv->Content;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $value
     */
    public function setAction($value)
    {
        $this->setAttribute("action", $value);
        return $this;
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
     * 
     * @param mixed $v
     */
    public function setContent($v)
    {
        $this->bodydiv->setContent($v);
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $value
     */
    public function setEncType($value)
    {
        $this->m_encType = $value;
        $this['enctype'] = $value;
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $value
     */
    public function setMethod($value)
    {
        $this->setAttribute("method", $value);
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $value
     */
    public function setNoFoot($value)
    {
        $this->m_nofoot = $value;
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * 
     * @param mixed $value
     */
    public function setNoTitle($value)
    {
        $this->m_notitle = $value;
        return $this;
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
     * set for mtitle
     * @param mixed $value
     */
    public function setTitle($value)
    {
        $this->topdiv->Content = $value;
        return $this;
    }
    
}
