<?php
// @author: C.A.D. BONDJE DOUE
// @filename: XmlComment.php
// @date: 20220814 09:19:42
// @desc: 
namespace IGK\System\Html\XML;
use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\HtmlRenderer;
/**
 * xml special comment
 * @package IGK\System\Html\XML
 */
class XmlComment extends HtmlItemBase{
    /**
    * Name of tagname.
    * @var mixed
    */
    protected $tagname = "igk:comment";
    /**
    * .ctr
    * @param null|string $data
    */
    public function __construct(?string $data = null)
    {
        parent::__construct();
        $this->setContent($data);
    }
    /**
    * Returns Can Add Childs.
    */
    public function getCanAddChilds(){
        return false;
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options=null){ 
        if (igk_getv($options, "NoComment"))
            return null;    
        $c = $this->getContent();
        if ($c)
            return "<!-- " .trim($c). " -->";
        return null;
    }
    /**
    * Returns Can Render Tag.
    */
    public function getCanRenderTag()
    {
        return false;
    }
}