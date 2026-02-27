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

    /**
    * Constant: urlencoded.
    * @var mixed
    */
    const URLEncoded = "application/x-www-form-urlencoded";

    /**
    * Property: bodydiv.
    * @var mixed
    */
    private $m_bodydiv;

    /**
    * Property: footdiv.
    * @var mixed
    */
    private $m_footdiv;

    /**
    * Property: definition.
    * @var mixed
    */
    private $m_definition;

    /**
    * Type of enc type.
    * @var mixed
    */
    private $m_encType;

    /**
    * Property: nofoot.
    * @var mixed
    */
    private $m_nofoot;

    /**
    * Property: notitle.
    * @var mixed
    */
    private $m_notitle;

    /**
    * Property: topdiv.
    * @var mixed
    */
    private $m_topdiv;

    /**
    * Property: max file size.
    * @var mixed
    */
    private $_max_file_size;

    /**
    * Property: max upload file.
    * @var mixed
    */
    private $_max_upload_file;

    /**
    * Property: prevent max file upload.
    * @var mixed
    */
    private $_prevent_max_file_upload;

    /**
    * auto generate doc.
    * @return HtmlItemBase
    */

    public function getBodyContent(): HtmlItemBase{
        return $this->m_bodydiv;
    }

    /**
    * auto generate doc.
    * @return HtmlItemBase
    */

    public function getFooterContent(): HtmlItemBase{
        return $this->m_footdiv;
    }

    /**
    * auto generate doc.
    * @return $this
    */

    public function multipart()
    {
        $this['enctype'] = IGK_HTML_ENCTYPE;
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $o the default value is null
    */

    protected function _acceptRender($options = null): bool
    {
        $e = $this->m_topdiv->Content;
        $this->m_topdiv->setIsVisible(!empty($e) && !$this->m_notitle);
        $this->m_footdiv->setIsVisible($this->m_footdiv->gethasContent() && !$this->m_nofoot);
        return true;
    }

    /**
    * auto generate doc.
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
        $this->m_topdiv = new HtmlFormTitleNode();
        $this->m_bodydiv = igk_create_node("div")->setAttributes(["class" => 'content']);
        $this->m_footdiv = igk_create_node("div")->setAttributes(["class" => "foot"]);
        $this->m_definition = new HtmlFormInnerNode($this);
        $this->m_definition->Add($this->m_topdiv);
        $this->m_definition->Add($this->m_bodydiv);
        $this->m_definition->Add($this->m_footdiv);
        parent::_Add($this->m_definition);
    }

    /**
    * Sets MAX FILE SIZE.
    * @param mixed $size
    */
    public function setMAX_FILE_SIZE($size)
    {
        return $this->_update_node($this->_max_file_size, $size,'MAX_FILE_SIZE');
    }

    /**
    * Sets MAX UPLOAD FILE.
    * @param null|int $count
    */
    public function setMAX_UPLOAD_FILE(?int $count=null){
        return $this->_update_node($this->_max_upload_file, $count,'MAX_UPLOAD_FILE');
    }

    /**
    * Prevent max file upload.
    */
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
    * auto generate doc.
    * @param mixed $index the default value is null
    */

    protected function _Add($item, $index = null): bool
    {
        return $this->m_bodydiv->_Add($item);
    }

    /**
    * auto generate doc.
    * @param mixed $index the default value is null
    */

    public function add($nameoritem, $attributes = null, $index = null)
    {
        return $this->m_bodydiv->add($nameoritem, $attributes, $index);
    }
    /**
     * input environement confirmation
     */

    public function addConfirm($v = 1)
    {
        return $this->addInput("confirm", "hidden", $v);
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function addHidden($n, $v)
    {
        return $this->addInput($n, "hidden", $v);
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
    */
    public function ClearChilds()
    {
        $this->m_bodydiv->clearChilds();
    }

    /**
    * auto generate doc.
    */
    public function getAction()
    {
        return $this["action"];
    }

    /**
    * auto generate doc.
    */
    public function getBox()
    {
        return $this->m_bodydiv;
    }

    /**
    * auto generate doc.
    */
    public function getContent()
    {
        return null;
    }

    /**
    * auto generate doc.
    */
    public function getEncType()
    {
        return $this->m_encType;
    }

    /**
    * auto generate doc.
    */
    public function getFooter()
    {
        return $this->m_footdiv;
    }

    /**
    * auto generate doc.
    */
    public function getMethod()
    {
        return $this["method"];
    }

    /**
    * auto generate doc.
    */
    public function getNoFoot()
    {
        return $this->m_nofoot;
    }

    /**
    * auto generate doc.
    */
    public function getNoTitle()
    {
        return $this->m_notitle;
    }

    /**
    * auto generate doc.
    */
    public function getTitle()
    {
        return $this->m_topdiv->Content;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setAction($value)
    {
        $this->setAttribute("action", $value);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setContent($v)
    {
        $this->m_bodydiv->setContent($v);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setEncType($value)
    {
        $this->m_encType = $value;
        $this['enctype'] = $value;
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setMethod($value)
    {
        $this->setAttribute("method", $value);
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setNoFoot($value)
    {
        $this->m_nofoot = $value;
        return $this;
    }

    /**
    * auto generate doc.
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
        $this->m_topdiv->Content = $value;
        return $this;
    }
}