<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlFormNode.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html\Dom;
use IGK\Helper\ViewHelper;
use IGK\System\Html\HtmlUtils;
use IGK\System\Html\Traits\HostableItemTrait;
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
    private $_max_file_size;
    private $_max_upload_file;
    private $_prevent_max_file_upload;
    /**
     * 
     * @return $this 
     */
    public function multipart()
    {
        $this['enctype'] = IGK_HTML_ENCTYPE;
        return $this;
    }
    /**
     * 
     * @param mixed $o the default value is null
     */
    protected function _acceptRender($options = null): bool
    {
        $e = $this->topdiv->Content;
        $this->topdiv->setIsVisible(!empty($e) && !$this->m_notitle);
        $this->footdiv->setIsVisible($this->footdiv->gethasContent() && !$this->m_nofoot);
        return true;
    }
    /**
     * 
     * @param mixed $notitle the default value is false
     * @param mixed $nofoot the default value is true
     */
    public function __construct($action = ".", $method = "POST", $notitle = false, $nofoot = true)
    {
        parent::__construct("form");
        if (($action == '.') && (ViewHelper::InViewContext())) {
            $ctrl = ViewHelper::CurrentCtrl();
            $fname = ViewHelper::GetViewArgs('fname');
            if(is_array($fname)){
                igk_die("dkjf");
            }
            $action = $fname ? $ctrl::uri($fname) : 'index';
        }
        $this->method = $method;
        $this->action = $action;
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
    public function setMAX_FILE_SIZE($size)
    {
        return $this->_update_node($this->_max_file_size, $size,'MAX_FILE_SIZE');
    }
    public function setMAX_UPLOAD_FILE(?int $count=null){
        return $this->_update_node($this->_max_upload_file, $count,'MAX_UPLOAD_FILE');
    }
    public function prevent_max_file_upload(){
        $n = & $this->_prevent_max_file_upload;
        if (!$n){
            $n = igk_create_node_arg('div.form-prevent-max-upload');
            $this->add($n);
        }
        return $n;
    }
    private function _update_node(& $prop, $value, $id){
        $n = & $prop;
        if ($value === null) {
            $n && $n->remove();
            $n = null;
        } else {
            if (!$n) { 
                $n = igk_create_node('input')
                ->setAttributes([
                    'type' => 'hidden', 
                    'id' => $id,
                ]); 
                $this->add($n);
            }
            $n['value'] = $value;
        }
        return $n;
    }
    /**
     * 
     * @param mixed $item
     * @param mixed $index the default value is null
     */
    protected function _Add($item, $index = null): bool
    {
        return $this->bodydiv->_Add($item);
    }
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
    /**
     * input environement confirmation
     */
    public function addConfirm($v = 1)
    {
        return $this->addInput("confirm", "hidden", $v);
    }
    /**
     * 
     * @param mixed $n
     * @param mixed $v
     */
    public function addHidden($n, $v)
    {
        return $this->addInput($n, "hidden", $v);
    }
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
    /**
     * 
     */
    public function ClearChilds()
    {
        $this->bodydiv->clearChilds();
    }
    /**
     * 
     */
    public function getAction()
    {
        return $this["action"];
    }
    /**
     * 
     */
    public function getBox()
    {
        return $this->bodydiv;
    }
    /**
     * 
     */
    public function getContent()
    {
        return null;
    }
    /**
     * 
     */
    public function getEncType()
    {
        return $this->m_encType;
    }
    /**
     * 
     */
    public function getFooter()
    {
        return $this->footdiv;
    }
    /**
     * 
     */
    public function getMethod()
    {
        return $this["method"];
    }
    /**
     * 
     */
    public function getNoFoot()
    {
        return $this->m_nofoot;
    }
    /**
     * 
     */
    public function getNoTitle()
    {
        return $this->m_notitle;
    }
    /**
     * 
     */
    public function getTitle()
    {
        return $this->topdiv->Content;
    }
    /**
     * 
     * @param mixed $value
     */
    public function setAction($value)
    {
        $this->setAttribute("action", $value);
        return $this;
    }
    /**
     * 
     * @param mixed $v
     */
    public function setContent($v)
    {
        $this->bodydiv->setContent($v);
        return $this;
    }
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
    /**
     * 
     * @param mixed $value
     */
    public function setMethod($value)
    {
        $this->setAttribute("method", $value);
        return $this;
    }
    /**
     * 
     * @param mixed $value
     */
    public function setNoFoot($value)
    {
        $this->m_nofoot = $value;
        return $this;
    }
    /**
     * 
     * @param mixed $value
     */
    public function setNoTitle($value)
    {
        $this->m_notitle = $value;
        return $this;
    }
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